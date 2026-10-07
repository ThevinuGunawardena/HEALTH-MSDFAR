using System;
using System.Collections.Generic;
using System.Linq;
using System.Security.Claims;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using MEA.Server.Data;
using MEA.Server.Entities;
using MEA.Server.Repositories;

namespace MEA.Server.Controllers
{
    public class CreateReplacementRequestDto
    {
        public int? OriginalCertificateRequestId { get; set; }
        public string? OriginalReferenceNumber { get; set; }
        public string Reason { get; set; } = string.Empty;
        public string? Remarks { get; set; }
    }

    public class RejectReplacementRequestDto
    {
        public string Reason { get; set; } = string.Empty;
    }

    [Route("api/replacement-requests")]
    [ApiController]
    public class ReplacementRequestController : ControllerBase
    {
        private readonly AppDbContext _context;
        private readonly ICertificateRequestRepository _certificateRequestRepository;

        public ReplacementRequestController(AppDbContext context, ICertificateRequestRepository certificateRequestRepository)
        {
            _context = context;
            _certificateRequestRepository = certificateRequestRepository;
        }

        private string? GetUserId() => User.FindFirstValue(ClaimTypes.NameIdentifier);
        private string? GetUserRole() => User.FindFirstValue(ClaimTypes.Role);

        [HttpGet]
        [Authorize(Roles = "Admin,Company,User")]
        public async Task<IActionResult> GetAll()
        {
            var userId = GetUserId();
            var role = (GetUserRole() ?? "").ToLower();

            var query = _context.ReplacementRequests.AsNoTracking();

            if (role == "company" && !string.IsNullOrEmpty(userId))
            {
                query = query.Where(r => r.CompanyUserId == userId);
            }

            var list = await query
                .OrderByDescending(r => r.CreatedAt)
                .ToListAsync();

            return Ok(list);
        }

        [HttpGet("eligible-certificates")]
        [Authorize(Roles = "Admin,Company,User")]
        public async Task<IActionResult> GetEligibleCertificates()
        {
            var userId = GetUserId();
            var role = (GetUserRole() ?? "").ToLower();

            var query = _context.CertificateRequests.AsNoTracking();
            if (role == "company" && !string.IsNullOrEmpty(userId))
            {
                query = query.Where(r => r.CompanyUserId == userId);
            }

            var countries = await _context.Countries.AsNoTracking().ToDictionaryAsync(c => c.Id, c => c.Name);
            var companies = await _context.Companies.AsNoTracking()
                .Where(c => c.UserId != null)
                .ToDictionaryAsync(c => c.UserId!, c => c.CompanyName);

            var requests = await query
                .Where(r => r.Status == CertificateStatus.Confirmed || r.Status == CertificateStatus.Pending)
                .OrderByDescending(r => r.CreatedAt)
                .Select(r => new
                {
                    r.Id,
                    r.ReferenceNumber,
                    CertificateType = r.CertificateType.ToString(),
                    Country = r.CountryId.HasValue && countries.ContainsKey(r.CountryId.Value)
                        ? countries[r.CountryId.Value]
                        : (r.CertificateType == CertificateType.EU ? "European Union" : "N/A"),
                    CompanyName = companies.ContainsKey(r.CompanyUserId) ? companies[r.CompanyUserId] : "N/A",
                    r.CreatedAt
                })
                .ToListAsync();

            return Ok(requests);
        }

        [HttpGet("{id}")]
        [Authorize(Roles = "Admin,Company,User")]
        public async Task<IActionResult> GetById(int id)
        {
            var item = await _context.ReplacementRequests.FindAsync(id);
            if (item == null) return NotFound(new { Message = "Replacement request not found." });

            var userId = GetUserId();
            var role = (GetUserRole() ?? "").ToLower();
            if (role == "company" && item.CompanyUserId != userId)
            {
                return Forbid();
            }

            return Ok(item);
        }

        [HttpPost]
        [Authorize(Roles = "Admin,Company,User")]
        public async Task<IActionResult> Create([FromBody] CreateReplacementRequestDto dto)
        {
            if (string.IsNullOrWhiteSpace(dto.Reason))
            {
                return BadRequest(new { Message = "Reason for replacement is required." });
            }

            var userId = GetUserId() ?? string.Empty;
            var role = (GetUserRole() ?? "").ToLower();

            CertificateRequest? certReq = null;
            if (dto.OriginalCertificateRequestId.HasValue)
            {
                certReq = await _context.CertificateRequests.FindAsync(dto.OriginalCertificateRequestId.Value);
            }
            else if (!string.IsNullOrWhiteSpace(dto.OriginalReferenceNumber))
            {
                certReq = await _context.CertificateRequests.FirstOrDefaultAsync(r => r.ReferenceNumber == dto.OriginalReferenceNumber.Trim());
            }

            string originalRef = dto.OriginalReferenceNumber?.Trim() ?? certReq?.ReferenceNumber ?? "N/A";
            string country = "N/A";
            string certType = certReq?.CertificateType.ToString() ?? "NonEU";
            string companyName = "Exporter";
            string companyUserId = certReq?.CompanyUserId ?? userId;

            if (certReq?.CountryId.HasValue == true)
            {
                var c = await _context.Countries.FindAsync(certReq.CountryId.Value);
                if (c != null) country = c.Name;
            }
            else if (certReq?.CertificateType == CertificateType.EU)
            {
                country = "European Union";
            }

            var company = await _context.Companies.FirstOrDefaultAsync(c => c.UserId == companyUserId);
            if (company != null && !string.IsNullOrWhiteSpace(company.CompanyName))
            {
                companyName = company.CompanyName;
            }
            else
            {
                var user = await _context.AppUsers.FindAsync(companyUserId);
                if (user != null && !string.IsNullOrWhiteSpace(user.FullName))
                {
                    companyName = user.FullName;
                }
            }

            var request = new ReplacementRequest
            {
                OriginalCertificateRequestId = certReq?.Id,
                OriginalReferenceNumber = originalRef,
                ReplacementReferenceNumber = string.Empty,
                CompanyUserId = companyUserId,
                CompanyName = companyName,
                Country = country,
                CertificateType = certType,
                Reason = dto.Reason.Trim(),
                Remarks = dto.Remarks?.Trim(),
                Status = ReplacementStatus.Pending,
                CreatedAt = DateTime.UtcNow
            };

            _context.ReplacementRequests.Add(request);
            await _context.SaveChangesAsync();

            return Ok(request);
        }

        [HttpPost("{id}/approve")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> Approve(int id)
        {
            var item = await _context.ReplacementRequests.FindAsync(id);
            if (item == null) return NotFound(new { Message = "Replacement request not found." });

            if (item.Status == ReplacementStatus.Approved)
            {
                return BadRequest(new { Message = "This replacement request has already been approved." });
            }

            // Generate replacement reference number with star sign '*'
            string baseRef = item.OriginalReferenceNumber.Trim();
            string replacementRef;
            if (item.OriginalCertificateRequestId.HasValue)
            {
                var origReq = await _certificateRequestRepository.GetByIdAsync(item.OriginalCertificateRequestId.Value);
                if (origReq != null)
                {
                    var newSeq = await _certificateRequestRepository.GenerateUniqueReferenceNumberAsync(origReq.CertificateType, origReq.CountryId);
                    replacementRef = newSeq.StartsWith("*") ? newSeq : $"*{newSeq}";
                }
                else
                {
                    replacementRef = baseRef.StartsWith("*") ? $"{baseRef}-R1" : $"*{baseRef}-R1";
                }
            }
            else
            {
                replacementRef = baseRef.StartsWith("*") ? $"{baseRef}-R1" : $"*{baseRef}-R1";
            }

            // Ensure unique reference number
            int rCount = 1;
            while (await _context.ReplacementRequests.AnyAsync(r => r.ReplacementReferenceNumber == replacementRef && r.Id != item.Id))
            {
                rCount++;
                replacementRef = baseRef.StartsWith("*") ? $"{baseRef}-R{rCount}" : $"*{baseRef}-R{rCount}";
            }

            item.ReplacementReferenceNumber = replacementRef;
            item.Status = ReplacementStatus.Approved;
            item.ProcessedAt = DateTime.UtcNow;
            item.ProcessedByUserId = GetUserId();

            if (item.OriginalCertificateRequestId.HasValue)
            {
                var origReq = await _certificateRequestRepository.GetByIdAsync(item.OriginalCertificateRequestId.Value);
                if (origReq != null)
                {
                    var existingRepReq = await _context.CertificateRequests.FirstOrDefaultAsync(r => r.ReferenceNumber == replacementRef);
                    if (existingRepReq == null)
                    {
                        var newReq = new CertificateRequest
                        {
                            ReferenceNumber = replacementRef,
                            CertificateType = origReq.CertificateType,
                            CountryId = origReq.CountryId,
                            CompanyUserId = origReq.CompanyUserId,
                            Status = CertificateStatus.Confirmed,
                            CreatedAt = DateTime.UtcNow,
                            CancelsAndReplacesRef = origReq.ReferenceNumber,
                            CancelsAndReplacesDate = origReq.CreatedAt,
                            ReplacedCertificateRequestId = origReq.Id
                        };
                        await _certificateRequestRepository.CreateAsync(newReq);

                        var origVetForm = await _context.VetCertificateForms
                            .Include(v => v.Products)
                            .Include(v => v.Attachments)
                            .FirstOrDefaultAsync(v => v.CertificateRequestId == origReq.Id);

                        if (origVetForm != null)
                        {
                            var newVetForm = new VetCertificateForm
                            {
                                CertificateRequestId = newReq.Id,
                                CompanyUserId = origVetForm.CompanyUserId,
                                HealthCertNo = replacementRef,
                                OldHC = origVetForm.HealthCertNo ?? origVetForm.OldHC,
                                NewHC = replacementRef,
                                LandingSite = origVetForm.LandingSite,
                                BoatRegistration = origVetForm.BoatRegistration,
                                BoatNumber = origVetForm.BoatNumber,
                                SupplierNameAddress = origVetForm.SupplierNameAddress,
                                ArrivalAtFactory = origVetForm.ArrivalAtFactory,
                                ProcessingDate = origVetForm.ProcessingDate,
                                FarmLocation = origVetForm.FarmLocation,
                                FarmOwnerName = origVetForm.FarmOwnerName,
                                FarmOwnerAddress = origVetForm.FarmOwnerAddress,
                                HarvestDate = origVetForm.HarvestDate,
                                ArrivalTimeProduct = origVetForm.ArrivalTimeProduct,
                                ProcessingDates = origVetForm.ProcessingDates,
                                AquaSupplier = origVetForm.AquaSupplier,
                                CountryOrigin = origVetForm.CountryOrigin,
                                ArrivalConsignment = origVetForm.ArrivalConsignment,
                                ProductTypeAquaculture = origVetForm.ProductTypeAquaculture,
                                ProductTypeWildCaught = origVetForm.ProductTypeWildCaught,
                                UploadedCertificateFile = origVetForm.UploadedCertificateFile,
                                ConsignorName = origVetForm.ConsignorName,
                                ConsignorAddress = origVetForm.ConsignorAddress,
                                ConsignorPostal = origVetForm.ConsignorPostal,
                                ConsignorTel = origVetForm.ConsignorTel,
                                ConsigneeName = origVetForm.ConsigneeName,
                                ConsigneeAddress = origVetForm.ConsigneeAddress,
                                ConsigneePostal = origVetForm.ConsigneePostal,
                                ConsigneeTel = origVetForm.ConsigneeTel,
                                CountryOriginISO = origVetForm.CountryOriginISO,
                                RegionOriginISO = origVetForm.RegionOriginISO,
                                CountryDestinationISO = origVetForm.CountryDestinationISO,
                                ProcessingEstName = origVetForm.ProcessingEstName,
                                ProcessingEstAddress = origVetForm.ProcessingEstAddress,
                                ApprovalNo = origVetForm.ApprovalNo,
                                PlaceOfLoading = origVetForm.PlaceOfLoading,
                                DateOfDeparture = origVetForm.DateOfDeparture,
                                TransportAeroPlane = origVetForm.TransportAeroPlane,
                                TransportShip = origVetForm.TransportShip,
                                TransportRailwayWagon = origVetForm.TransportRailwayWagon,
                                TransportRoadVehicle = origVetForm.TransportRoadVehicle,
                                TransportOther = origVetForm.TransportOther,
                                TransportId = origVetForm.TransportId,
                                DocReferences = origVetForm.DocReferences,
                                EntryBIP = origVetForm.EntryBIP,
                                DescCommon = origVetForm.DescCommon,
                                DescScientific = origVetForm.DescScientific,
                                ProcessingType = origVetForm.ProcessingType,
                                HsCode = origVetForm.HsCode,
                                TemperatureAmbient = origVetForm.TemperatureAmbient,
                                TemperatureChilled = origVetForm.TemperatureChilled,
                                TemperatureFrozen = origVetForm.TemperatureFrozen,
                                Quantity = origVetForm.Quantity,
                                NumPackages = origVetForm.NumPackages,
                                PackagingType = origVetForm.PackagingType,
                                ContainerId = origVetForm.ContainerId,
                                ForHumanConsumption = origVetForm.ForHumanConsumption,
                                ForImportEU = origVetForm.ForImportEU,
                                NatureAquaculture = origVetForm.NatureAquaculture,
                                NatureWildOrigin = origVetForm.NatureWildOrigin,
                                TreatmentChilled = origVetForm.TreatmentChilled,
                                TreatmentFrozen = origVetForm.TreatmentFrozen,
                                TreatmentLive = origVetForm.TreatmentLive,
                                NetWeight = origVetForm.NetWeight,
                                PaymentSlipFile = origVetForm.PaymentSlipFile,
                                Signature = origVetForm.Signature,
                                SignatoryName = origVetForm.SignatoryName,
                                Designation = origVetForm.Designation,
                                SignatureDate = origVetForm.SignatureDate,
                                SignatureTime = origVetForm.SignatureTime,
                                Attestation61_1 = origVetForm.Attestation61_1,
                                Attestation61_2 = origVetForm.Attestation61_2,
                                Attestation61_3 = origVetForm.Attestation61_3,
                                Attestation61_4 = origVetForm.Attestation61_4,
                                Attestation61_5 = origVetForm.Attestation61_5,
                                Attestation62_1 = origVetForm.Attestation62_1,
                                Attestation62_2 = origVetForm.Attestation62_2,
                                CreatedAt = DateTime.UtcNow,
                                Products = origVetForm.Products?.Select(p => new VetCertificateProduct
                                {
                                    ProductOrder = p.ProductOrder,
                                    DescCommon = p.DescCommon,
                                    DescScientific = p.DescScientific,
                                    ProcessingType = p.ProcessingType,
                                    HsCode = p.HsCode,
                                    TemperatureAmbient = p.TemperatureAmbient,
                                    TemperatureChilled = p.TemperatureChilled,
                                    TemperatureFrozen = p.TemperatureFrozen,
                                    Quantity = p.Quantity,
                                    NumPackages = p.NumPackages,
                                    PackagingType = p.PackagingType,
                                    ContainerId = p.ContainerId,
                                    ForHumanConsumption = p.ForHumanConsumption,
                                    ForImportEU = p.ForImportEU,
                                    NatureAquaculture = p.NatureAquaculture,
                                    NatureWildOrigin = p.NatureWildOrigin,
                                    TreatmentChilled = p.TreatmentChilled,
                                    TreatmentFrozen = p.TreatmentFrozen,
                                    TreatmentLive = p.TreatmentLive,
                                    NetWeight = p.NetWeight
                                }).ToList() ?? new List<VetCertificateProduct>(),
                                Attachments = origVetForm.Attachments?.Select(a => new VetCertificateAttachment
                                {
                                    OriginalFileName = a.OriginalFileName,
                                    SecondaryFileName = a.SecondaryFileName,
                                    ContentType = a.ContentType,
                                    FileContent = a.FileContent,
                                    FileOrder = a.FileOrder
                                }).ToList() ?? new List<VetCertificateAttachment>()
                            };
                            _context.VetCertificateForms.Add(newVetForm);
                        }
                    }
                }
            }

            await _context.SaveChangesAsync();

            return Ok(new
            {
                Message = "Replacement request approved successfully.",
                Item = item
            });
        }

        [HttpPost("{id}/reject")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> Reject(int id, [FromBody] RejectReplacementRequestDto dto)
        {
            var item = await _context.ReplacementRequests.FindAsync(id);
            if (item == null) return NotFound(new { Message = "Replacement request not found." });

            if (item.Status != ReplacementStatus.Pending)
            {
                return BadRequest(new { Message = "Only pending replacement requests can be rejected." });
            }

            item.Status = ReplacementStatus.Rejected;
            item.RejectionReason = !string.IsNullOrWhiteSpace(dto.Reason) ? dto.Reason.Trim() : "Rejected by Administrator.";
            item.ProcessedAt = DateTime.UtcNow;
            item.ProcessedByUserId = GetUserId();

            await _context.SaveChangesAsync();

            return Ok(new
            {
                Message = "Replacement request rejected.",
                Item = item
            });
        }
    }
}
