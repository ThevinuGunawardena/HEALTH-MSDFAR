using System.Security.Claims;
using System.Text.Json;
using MEA.Server.Data;
using MEA.Server.DTO.AmCertificate;
using MEA.Server.DTO.AuCertificate;
using MEA.Server.DTO.BrCertificate;
using MEA.Server.DTO.CertificateRequest;
using MEA.Server.DTO.ChCertificate;
using MEA.Server.DTO.HkCertificate;
using MEA.Server.DTO.IdCertificate;
using MEA.Server.DTO.IndCertificate;
using MEA.Server.DTO.JpCertificate;
using MEA.Server.DTO.KwCertificate;
using MEA.Server.DTO.MyCertificate;
using MEA.Server.DTO.NzCertificate;
using MEA.Server.DTO.RuCertificate;
using MEA.Server.DTO.KzCertificate;
using MEA.Server.DTO.TwCertificate;
using MEA.Server.DTO.UaCertificate;
using MEA.Server.DTO.UkCertificate;
using MEA.Server.DTO.UsaCertificate;
using MEA.Server.DTO.IlCertificate;
using MEA.Server.DTO.MvCertificate;
using MEA.Server.DTO.CaCertificate;
using MEA.Server.DTO.SaCertificate;
using MEA.Server.DTO.ZaCertificate;
using MEA.Server.Entities;
using MEA.Server.Repositories;
using MEA.Server.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [Authorize]
    public class CertificateRequestController : ControllerBase
    {
        private sealed record PaymentSlipPreviewDto(string MimeType, string ContentBase64);

        private sealed class VetProductPayload
        {
            public int? ProductOrder { get; set; }
            public string? DescCommon { get; set; }
            public string? DescScientific { get; set; }
            public string? ProcessingType { get; set; }
            public string? HsCode { get; set; }
            public bool? TemperatureAmbient { get; set; }
            public bool? TemperatureChilled { get; set; }
            public bool? TemperatureFrozen { get; set; }
            public string? Quantity { get; set; }
            public string? NumPackages { get; set; }
            public string? PackagingType { get; set; }
            public string? ContainerId { get; set; }
            public bool? ForHumanConsumption { get; set; }
            public string? ForImportEU { get; set; }
            public bool? NatureAquaculture { get; set; }
            public bool? NatureWildOrigin { get; set; }
            public bool? TreatmentChilled { get; set; }
            public bool? TreatmentFrozen { get; set; }
            public bool? TreatmentLive { get; set; }
            public string? NetWeight { get; set; }
        }

        private sealed class VetAttachmentSecondaryNamePayload
        {
            public string? OriginalFileName { get; set; }
            public string? SecondaryFileName { get; set; }
        }

        private readonly ICertificateRequestRepository _certificateRequestRepository;
        private readonly ICountryRepository _countryRepository;
        private readonly IVetCertificateFormRepository _vetCertificateFormRepository;
        private readonly ICertificateService _certificateService;
        private readonly AppDbContext _context;

        public CertificateRequestController(
            ICertificateRequestRepository certificateRequestRepository,
            ICountryRepository countryRepository,
            IVetCertificateFormRepository vetCertificateFormRepository,
            ICertificateService certificateService,
            AppDbContext context)
        {
            _certificateRequestRepository = certificateRequestRepository;
            _countryRepository = countryRepository;
            _vetCertificateFormRepository = vetCertificateFormRepository;
            _certificateService = certificateService;
            _context = context;
        }

        [HttpGet("countries")]
        [Authorize(Roles = "Admin,Company")]
        public async Task<IActionResult> GetCountries()
        {
            var countries = (await _countryRepository.GetAllAsync()).ToList();
            var requiredCountries = new[] { "Maldives", "Canada", "Saudi Arabia", "South Africa" };
            bool changed = false;

            foreach (var req in requiredCountries)
            {
                if (!countries.Any(c => c.Name.Equals(req, StringComparison.OrdinalIgnoreCase)))
                {
                    var newCountry = new Country { Name = req };
                    _context.Countries.Add(newCountry);
                    countries.Add(newCountry);
                    changed = true;
                }
            }

            if (changed)
            {
                await _context.SaveChangesAsync();
            }

            return Ok(countries.OrderBy(c => c.Name).Select(c => new { c.Id, c.Name }));
        }

        [HttpPost("createcertificate-requests")]
        [Authorize(Roles = "Admin,Company")]
        public async Task<IActionResult> CreateCertificateRequest([FromBody] CreateCertificateRequestDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            if (!Enum.TryParse<CertificateType>(dto.CertificateType, true, out var certificateType))
                return BadRequest(new { Message = "Invalid certificate type. Use EU or NonEU." });

            if (certificateType == CertificateType.NonEU && !dto.CountryId.HasValue)
                return BadRequest(new { Message = "Country is required for NonEU certificate requests." });

            if (certificateType == CertificateType.EU)
                dto.CountryId = null;

            if (dto.CountryId.HasValue)
            {
                var countryExists = await _countryRepository.ExistsAsync(dto.CountryId.Value);
                if (!countryExists)
                    return BadRequest(new { Message = "Selected country does not exist." });
            }

            if (!string.IsNullOrWhiteSpace(dto.ReferenceNumber))
            {
                var refExists = await _certificateRequestRepository.ExistsByReferenceNumberAsync(dto.ReferenceNumber.Trim());
                if (refExists)
                {
                    return BadRequest(new { Message = $"Reference number '{dto.ReferenceNumber.Trim()}' is already in use." });
                }
            }

            var quantity = dto.Quantity > 0 ? dto.Quantity : 1;
            var createdRequests = new List<CertificateRequest>();

            for (int i = 0; i < quantity; i++)
            {
                var referenceNumber = (i == 0 && !string.IsNullOrWhiteSpace(dto.ReferenceNumber) && dto.ReferenceNumber.Trim().StartsWith("HC-", StringComparison.OrdinalIgnoreCase))
                    ? dto.ReferenceNumber.Trim()
                    : await _certificateRequestRepository.GenerateUniqueReferenceNumberAsync(certificateType, dto.CountryId);

                var request = new CertificateRequest
                {
                    ReferenceNumber = referenceNumber,
                    CertificateType = certificateType,
                    CountryId = dto.CountryId,
                    CompanyUserId = userId,
                    Status = CertificateStatus.Pending,
                    CreatedAt = DateTime.UtcNow
                };

                await _certificateRequestRepository.CreateAsync(request);
                createdRequests.Add(request);
            }

            var countries = await _countryRepository.GetAllAsync();
            var countryMap = countries.ToDictionary(c => c.Id, c => c.Name);

            var responseList = createdRequests.Select(r => new
            {
                r.Id,
                r.ReferenceNumber,
                r.CertificateType,
                r.CountryId,
                CountryName = r.CountryId.HasValue ? countryMap.GetValueOrDefault(r.CountryId.Value) : (r.CertificateType == CertificateType.EU ? "European Union" : null),
                r.Status,
                r.CreatedAt,
                ExpiresAt = CalculateMidnightExpirationUtc(r.CreatedAt),
                IsExpired = false,
                HasFormSubmitted = false
            }).ToList();

            var first = responseList[0];
            return Ok(new
            {
                first.Id,
                first.ReferenceNumber,
                first.CertificateType,
                first.CountryId,
                first.CountryName,
                first.Status,
                first.CreatedAt,
                first.ExpiresAt,
                first.IsExpired,
                first.HasFormSubmitted,
                Requests = responseList
            });
        }

        [HttpGet("certificate-requests/{id}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetCertificateRequestById(int id)
        {
            var request = await _certificateRequestRepository.GetByIdAsync(id);
            if (request == null) return NotFound(new { Message = "Certificate request not found." });

            var countryName = request.CountryId.HasValue ? (await _countryRepository.GetByIdAsync(request.CountryId.Value))?.Name : (request.CertificateType == CertificateType.EU ? "European Union" : null);

            return Ok(new
            {
                request.Id,
                request.ReferenceNumber,
                request.CertificateType,
                request.CountryId,
                CountryName = countryName,
                request.Status,
                request.CreatedAt
            });
        }

        [HttpGet("certificate-requests")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetAllCertificateRequests()
        {
            var requests = await _certificateRequestRepository.GetAllAsync();
            var requestIds = requests.Select(r => r.Id).ToList();

            var submittedIds = new HashSet<int>();
            if (requestIds.Count > 0)
            {
                var vetSubmitted = await _context.VetCertificateForms.AsNoTracking().Where(v => v.CertificateRequestId.HasValue && requestIds.Contains(v.CertificateRequestId.Value)).Select(v => v.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(vetSubmitted);

                var amSubmitted = await _context.AmCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(amSubmitted);

                var auSubmitted = await _context.AuCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(auSubmitted);

                var brSubmitted = await _context.BrCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(brSubmitted);

                var chSubmitted = await _context.ChCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(chSubmitted);

                var hkSubmitted = await _context.HkCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(hkSubmitted);

                var idSubmitted = await _context.IdCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(idSubmitted);

                var indSubmitted = await _context.IndCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(indSubmitted);

                var jpSubmitted = await _context.JpCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(jpSubmitted);

                var kwSubmitted = await _context.KwCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(kwSubmitted);

                var kzSubmitted = await _context.KzCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(kzSubmitted);

                var mySubmitted = await _context.MyCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(mySubmitted);

                var nzSubmitted = await _context.NzCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(nzSubmitted);

                var ruSubmitted = await _context.RuCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(ruSubmitted);

                var twSubmitted = await _context.TwCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(twSubmitted);

                var uaSubmitted = await _context.UaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(uaSubmitted);

                var ukSubmitted = await _context.UkCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(ukSubmitted);

                var usaSubmitted = await _context.UsaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(usaSubmitted);

                var mvSubmitted = await _context.MvCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(mvSubmitted);

                var ilSubmitted = await _context.IlCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(ilSubmitted);

                var caSubmitted = await _context.CaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(caSubmitted);

                var saSubmitted = await _context.SaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(saSubmitted);

                var zaSubmitted = await _context.ZaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(zaSubmitted);
            }

            var paymentSlips = await _context.VetCertificateForms
                .AsNoTracking()
                .Where(v => v.CertificateRequestId.HasValue && requestIds.Contains(v.CertificateRequestId.Value) && v.PaymentSlipFile != null)
                .Select(v => new
                {
                    RequestId = v.CertificateRequestId!.Value,
                    v.PaymentSlipFile,
                    v.CreatedAt
                })
                .ToListAsync();

            var paymentSlipMap = paymentSlips
                .OrderByDescending(v => v.CreatedAt)
                .GroupBy(v => v.RequestId)
                .ToDictionary(
                    g => g.Key,
                    g => CreatePaymentSlipPreview(g.First().PaymentSlipFile)
                );

            // Pre-fetch reference maps in memory to eliminate N+1 DB roundtrips
            var allCountries = await _countryRepository.GetAllAsync();
            var countryLookupMap = allCountries.ToDictionary(c => c.Id, c => c.Name);

            var allCompanies = await _context.Companies.AsNoTracking().ToListAsync();
            var allUsers = await _context.Users.AsNoTracking().ToListAsync();

            var companyByUserId = allCompanies
                .Where(c => !string.IsNullOrEmpty(c.UserId))
                .GroupBy(c => c.UserId!)
                .ToDictionary(g => g.Key, g => g.First().CompanyName);

            var companyById = allCompanies
                .GroupBy(c => c.Id)
                .ToDictionary(g => g.Key, g => g.First().CompanyName);

            var companyByEmail = allCompanies
                .Where(c => !string.IsNullOrEmpty(c.CompanyEmail))
                .GroupBy(c => c.CompanyEmail!)
                .ToDictionary(g => g.Key, g => g.First().CompanyName);

            var userById = allUsers
                .GroupBy(u => u.Id)
                .ToDictionary(g => g.Key, g => g.First());

            // Pre-fetch Consignor Names from all submitted certificate forms
            var consignorByRequestId = new Dictionary<int, string>();

            void AddConsignors(IEnumerable<(int? ReqId, string? Consignor)> items)
            {
                foreach (var item in items)
                {
                    if (item.ReqId.HasValue && !string.IsNullOrWhiteSpace(item.Consignor) && !consignorByRequestId.ContainsKey(item.ReqId.Value))
                    {
                        consignorByRequestId[item.ReqId.Value] = item.Consignor.Trim();
                    }
                }
            }

            if (requestIds.Count > 0)
            {
                var vetCons = await _context.VetCertificateForms.AsNoTracking()
                    .Where(v => v.CertificateRequestId.HasValue && requestIds.Contains(v.CertificateRequestId.Value))
                    .Select(v => new { ReqId = v.CertificateRequestId, Consignor = v.ConsignorName })
                    .ToListAsync();
                AddConsignors(vetCons.Select(x => ((int?)x.ReqId, x.Consignor)));

                var caCons = await _context.CaCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(caCons.Select(x => (x.ReqId, x.Consignor)));

                var usaCons = await _context.UsaCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(usaCons.Select(x => (x.ReqId, x.Consignor)));

                var brCons = await _context.BrCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ExporterName })
                    .ToListAsync();
                AddConsignors(brCons.Select(x => (x.ReqId, x.Consignor)));

                var chCons = await _context.ChCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(chCons.Select(x => (x.ReqId, x.Consignor)));

                var auCons = await _context.AuCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(auCons.Select(x => (x.ReqId, x.Consignor)));

                var jpCons = await _context.JpCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(jpCons.Select(x => (x.ReqId, x.Consignor)));

                var indCons = await _context.IndCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(indCons.Select(x => (x.ReqId, x.Consignor)));

                var amCons = await _context.AmCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(amCons.Select(x => (x.ReqId, x.Consignor)));

                var hkCons = await _context.HkCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(hkCons.Select(x => (x.ReqId, x.Consignor)));

                var idCons = await _context.IdCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(idCons.Select(x => (x.ReqId, x.Consignor)));

                var kwCons = await _context.KwCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(kwCons.Select(x => (x.ReqId, x.Consignor)));

                var kzCons = await _context.KzCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorNameAddress })
                    .ToListAsync();
                AddConsignors(kzCons.Select(x => (x.ReqId, x.Consignor)));

                var myCons = await _context.MyCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ExporterName })
                    .ToListAsync();
                AddConsignors(myCons.Select(x => (x.ReqId, x.Consignor)));

                var nzCons = await _context.NzCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(nzCons.Select(x => (x.ReqId, x.Consignor)));

                var ruCons = await _context.RuCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorNameAddress })
                    .ToListAsync();
                AddConsignors(ruCons.Select(x => (x.ReqId, x.Consignor)));

                var saCons = await _context.SaCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(saCons.Select(x => (x.ReqId, x.Consignor)));

                var twCons = await _context.TwCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(twCons.Select(x => (x.ReqId, x.Consignor)));

                var uaCons = await _context.UaCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(uaCons.Select(x => (x.ReqId, x.Consignor)));

                var ukCons = await _context.UkCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(ukCons.Select(x => (x.ReqId, x.Consignor)));

                var zaCons = await _context.ZaCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(zaCons.Select(x => (x.ReqId, x.Consignor)));

                var mvCons = await _context.MvCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorExporter })
                    .ToListAsync();
                AddConsignors(mvCons.Select(x => (x.ReqId, x.Consignor)));

                var ilCons = await _context.IlCertificates.AsNoTracking()
                    .Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value))
                    .Select(c => new { ReqId = c.CertificateRequestId, Consignor = c.ConsignorName })
                    .ToListAsync();
                AddConsignors(ilCons.Select(x => (x.ReqId, x.Consignor)));
            }

            string ResolveCompanyName(int reqId, string? companyUserId)
            {
                if (!string.IsNullOrEmpty(companyUserId))
                {
                    if (companyByUserId.TryGetValue(companyUserId, out var byUserId) && !string.IsNullOrWhiteSpace(byUserId))
                        return byUserId;

                    if (userById.TryGetValue(companyUserId, out var appUser))
                    {
                        if (appUser.CompanyId.HasValue && companyById.TryGetValue(appUser.CompanyId.Value, out var byCompId) && !string.IsNullOrWhiteSpace(byCompId))
                            return byCompId;

                        if (!string.IsNullOrEmpty(appUser.Email) && companyByEmail.TryGetValue(appUser.Email, out var byEmail) && !string.IsNullOrWhiteSpace(byEmail))
                            return byEmail;

                        if (string.Equals(appUser.Email, "company@gmail.com", StringComparison.OrdinalIgnoreCase) || appUser.FullName == "Company User")
                            return "Company User";

                        if (!string.IsNullOrWhiteSpace(appUser.FullName) && appUser.FullName != "Admin User" && appUser.FullName != "Regular User")
                            return appUser.FullName;
                    }
                }

                if (consignorByRequestId.TryGetValue(reqId, out var formConsignor) && !string.IsNullOrWhiteSpace(formConsignor))
                    return formConsignor;

                return "Company User";
            }

            // Filter: ONLY load requests where the company filled the form and pressed SUBMIT
            var filteredRequests = requests.Where(r => submittedIds.Contains(r.Id)).ToList();

            var result = filteredRequests.Select(r => new
            {
                r.Id,
                r.ReferenceNumber,
                r.CertificateType,
                r.CountryId,
                CountryName = r.CountryId.HasValue ? countryLookupMap.GetValueOrDefault(r.CountryId.Value) : (r.CertificateType == CertificateType.EU ? "European Union" : null),
                r.Status,
                r.CreatedAt,
                CompanyName = ResolveCompanyName(r.Id, r.CompanyUserId),
                PaymentSlip = paymentSlipMap.GetValueOrDefault(r.Id),
                HasFormSubmitted = submittedIds.Contains(r.Id)
            }).ToList();

            return Ok(result);
        }

        [HttpGet("certificate-requests/my")]
        [Authorize(Roles = "Admin,Company")]
        public async Task<IActionResult> GetMyCertificateRequests()
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var requests = (await _certificateRequestRepository.GetByUserIdAsync(userId)).ToList();
            var requestIds = requests.Select(r => r.Id).ToList();

            var submittedIds = new HashSet<int>();
            if (requestIds.Count > 0)
            {
                var vetSubmitted = await _context.VetCertificateForms.AsNoTracking().Where(v => v.CertificateRequestId.HasValue && requestIds.Contains(v.CertificateRequestId.Value)).Select(v => v.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(vetSubmitted);

                var amSubmitted = await _context.AmCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(amSubmitted);

                var auSubmitted = await _context.AuCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(auSubmitted);

                var brSubmitted = await _context.BrCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(brSubmitted);

                var chSubmitted = await _context.ChCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(chSubmitted);

                var hkSubmitted = await _context.HkCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(hkSubmitted);

                var idSubmitted = await _context.IdCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(idSubmitted);

                var indSubmitted = await _context.IndCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(indSubmitted);

                var jpSubmitted = await _context.JpCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(jpSubmitted);

                var kwSubmitted = await _context.KwCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(kwSubmitted);

                var kzSubmitted = await _context.KzCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(kzSubmitted);

                var mySubmitted = await _context.MyCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(mySubmitted);

                var nzSubmitted = await _context.NzCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(nzSubmitted);

                var ruSubmitted = await _context.RuCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(ruSubmitted);

                var twSubmitted = await _context.TwCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(twSubmitted);

                var uaSubmitted = await _context.UaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(uaSubmitted);

                var ukSubmitted = await _context.UkCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(ukSubmitted);

                var usaSubmitted = await _context.UsaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(usaSubmitted);

                var mvSubmitted = await _context.MvCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(mvSubmitted);

                var ilSubmitted = await _context.IlCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(ilSubmitted);

                var caSubmitted = await _context.CaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(caSubmitted);

                var saSubmitted = await _context.SaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(saSubmitted);

                var zaSubmitted = await _context.ZaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
                submittedIds.UnionWith(zaSubmitted);
            }

            var countries = await _countryRepository.GetAllAsync();
            var countryMap = countries.ToDictionary(c => c.Id, c => c.Name);

            var now = DateTime.UtcNow;
            var result = requests.Select(r =>
            {
                var isSubmitted = submittedIds.Contains(r.Id);
                var expiresAt = CalculateMidnightExpirationUtc(r.CreatedAt);
                var isExpired = !isSubmitted && now > expiresAt;

                return new
                {
                    r.Id,
                    r.ReferenceNumber,
                    r.CertificateType,
                    r.CountryId,
                    CountryName = r.CountryId.HasValue ? countryMap.GetValueOrDefault(r.CountryId.Value) : (r.CertificateType == CertificateType.EU ? "European Union" : null),
                    r.Status,
                    r.CreatedAt,
                    ExpiresAt = expiresAt,
                    IsExpired = isExpired,
                    HasFormSubmitted = isSubmitted
                };
            }).ToList();

            return Ok(result);
        }

        [HttpGet("vetcertificate/{id}/vet-form")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetVetCertificateFormByCertificateRequestId(int id)
        {
            var vetForm = await _vetCertificateFormRepository.GetByCertificateRequestIdAsync(id);
            if (vetForm == null)
                return Ok(null);

            var products = (vetForm.Products ?? new List<VetCertificateProduct>())
                .OrderBy(p => p.ProductOrder)
                .Select(p => new
                {
                    p.Id,
                    p.ProductOrder,
                    p.DescCommon,
                    p.DescScientific,
                    p.ProcessingType,
                    p.HsCode,
                    p.TemperatureAmbient,
                    p.TemperatureChilled,
                    p.TemperatureFrozen,
                    p.Quantity,
                    p.NumPackages,
                    p.PackagingType,
                    p.ContainerId,
                    p.ForHumanConsumption,
                    p.ForImportEU,
                    p.NatureAquaculture,
                    p.NatureWildOrigin,
                    p.TreatmentChilled,
                    p.TreatmentFrozen,
                    p.TreatmentLive,
                    p.NetWeight
                })
                .ToList();

            if (products.Count == 0)
            {
                var legacyProducts = BuildLegacyVetProducts(vetForm)
                    .Select(p => new
                    {
                        p.Id,
                        p.ProductOrder,
                        p.DescCommon,
                        p.DescScientific,
                        p.ProcessingType,
                        p.HsCode,
                        p.TemperatureAmbient,
                        p.TemperatureChilled,
                        p.TemperatureFrozen,
                        p.Quantity,
                        p.NumPackages,
                        p.PackagingType,
                        p.ContainerId,
                        p.ForHumanConsumption,
                        p.ForImportEU,
                        p.NatureAquaculture,
                        p.NatureWildOrigin,
                        p.TreatmentChilled,
                        p.TreatmentFrozen,
                        p.TreatmentLive,
                        p.NetWeight
                    });

                products.AddRange(legacyProducts);
            }

            var result = new
            {
                vetForm.Id,
                vetForm.CertificateRequestId,
                vetForm.OldHC,
                vetForm.NewHC,
                vetForm.LandingSite,
                vetForm.BoatRegistration,
                vetForm.BoatNumber,
                vetForm.SupplierNameAddress,
                vetForm.ArrivalAtFactory,
                vetForm.ProcessingDate,
                vetForm.FarmLocation,
                vetForm.FarmOwnerName,
                vetForm.FarmOwnerAddress,
                vetForm.HarvestDate,
                vetForm.ArrivalTimeProduct,
                vetForm.ProcessingDates,
                vetForm.AquaSupplier,
                vetForm.CountryOrigin,
                vetForm.ArrivalConsignment,
                vetForm.HealthCertNo,
                vetForm.ProductTypeAquaculture,
                vetForm.ProductTypeWildCaught,
                UploadedCertificateFile = (vetForm.UploadedCertificateFile != null && vetForm.UploadedCertificateFile.Length > 0)
                    || (vetForm.Attachments?.Count > 0),
                UploadedCertificateFiles = (vetForm.Attachments ?? new List<VetCertificateAttachment>())
                    .OrderBy(a => a.FileOrder)
                    .Select(a => new
                    {
                        a.Id,
                        a.FileOrder,
                        a.OriginalFileName,
                        a.SecondaryFileName,
                        a.ContentType,
                        ContentBase64 = a.FileContent != null && a.FileContent.Length > 0
                            ? Convert.ToBase64String(a.FileContent)
                            : null
                    })
                    .ToList(),
                PaymentSlip = CreatePaymentSlipPreview(vetForm.PaymentSlipFile),
                vetForm.ConsignorName,
                vetForm.ConsignorAddress,
                vetForm.ConsignorPostal,
                vetForm.ConsignorTel,
                vetForm.ConsigneeName,
                vetForm.ConsigneeAddress,
                vetForm.ConsigneePostal,
                vetForm.ConsigneeTel,
                vetForm.CountryOriginISO,
                vetForm.RegionOriginISO,
                vetForm.CountryDestinationISO,
                vetForm.PlaceOfLoading,
                vetForm.DateOfDeparture,
                vetForm.TransportAeroPlane,
                vetForm.TransportShip,
                vetForm.TransportRailwayWagon,
                vetForm.TransportRoadVehicle,
                vetForm.TransportOther,
                vetForm.DocReferences,
                vetForm.EntryBIP,
                vetForm.DescCommon,
                vetForm.HsCode,
                vetForm.Quantity,
                vetForm.DescScientific,
                vetForm.NumPackages,
                vetForm.PackagingType,
                vetForm.TemperatureAmbient,
                vetForm.TemperatureChilled,
                vetForm.TemperatureFrozen,
                Temperature = vetForm.TemperatureAmbient == true ? "Ambient"
                    : vetForm.TemperatureChilled == true ? "Chilled"
                    : vetForm.TemperatureFrozen == true ? "Frozen"
                    : null,
                Nature = vetForm.NatureAquaculture == true ? "Aquaculture"
                    : vetForm.NatureWildOrigin == true ? "Wild origin"
                    : null,
                Treatment = vetForm.TreatmentChilled == true ? "Chilled"
                    : vetForm.TreatmentFrozen == true ? "Frozen"
                    : vetForm.TreatmentLive == true ? "Live"
                    : null,
                vetForm.NetWeight,
                vetForm.ProcessingType,
                vetForm.ForImportEU,
                vetForm.ProcessingEstName,
                vetForm.ProcessingEstAddress,
                vetForm.ApprovalNo,
                vetForm.TransportId,
                vetForm.ContainerId,
                vetForm.ForHumanConsumption,
                vetForm.NatureAquaculture,
                vetForm.NatureWildOrigin,
                vetForm.TreatmentChilled,
                vetForm.TreatmentFrozen,
                vetForm.TreatmentLive,
                vetForm.SignatureDate,
                vetForm.SignatureTime,
                vetForm.Signature,
                vetForm.SignatoryName,
                vetForm.Designation,
                vetForm.Attestation61_1,
                vetForm.Attestation61_2,
                vetForm.Attestation61_3,
                vetForm.Attestation61_4,
                vetForm.Attestation61_5,
                vetForm.Attestation62_1,
                vetForm.Attestation62_2,
                Products = products
            };

            return Ok(result);
        }

        [HttpGet("vetcertificate/attachments/{attachmentId}/content")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetVetCertificateAttachmentContent(int attachmentId)
        {
            var userId = GetUserId();
            if (userId == null)
            {
                return Unauthorized();
            }

            var attachment = await _context.VetCertificateAttachments
                .AsNoTracking()
                .Include(a => a.VetCertificateForm)
                .FirstOrDefaultAsync(a => a.Id == attachmentId);

            if (attachment == null || attachment.FileContent == null || attachment.FileContent.Length == 0)
            {
                return NotFound(new { Message = "Attachment not found." });
            }

            var isAdmin = User.IsInRole("Admin");
            if (!isAdmin && !string.Equals(attachment.VetCertificateForm?.CompanyUserId, userId, StringComparison.OrdinalIgnoreCase))
            {
                return Forbid();
            }

            var contentType = string.IsNullOrWhiteSpace(attachment.ContentType)
                ? DetectMimeType(attachment.FileContent)
                : attachment.ContentType;
            var fileName = string.IsNullOrWhiteSpace(attachment.OriginalFileName)
                ? $"attachment-{attachment.Id}"
                : attachment.OriginalFileName;

            return File(attachment.FileContent, contentType, fileName);
        }

        [HttpGet("certificate-requests/{requestId}/payment-slip")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetPaymentSlipContent(int requestId)
        {
            var userId = GetUserId();
            if (userId == null)
            {
                return Unauthorized();
            }

            var request = await _certificateRequestRepository.GetByIdAsync(requestId);
            if (request == null)
            {
                return NotFound(new { Message = "Certificate request not found." });
            }

            var isAdmin = User.IsInRole("Admin");
            if (!isAdmin && !string.Equals(request.CompanyUserId, userId, StringComparison.OrdinalIgnoreCase))
            {
                return Forbid();
            }

            var vetForm = await _context.VetCertificateForms
                .AsNoTracking()
                .FirstOrDefaultAsync(v => v.CertificateRequestId == requestId && v.PaymentSlipFile != null);

            if (vetForm?.PaymentSlipFile == null || vetForm.PaymentSlipFile.Length == 0)
            {
                return NotFound(new { Message = "Payment slip not found." });
            }

            var contentType = DetectMimeType(vetForm.PaymentSlipFile);
            var extension = contentType == "application/pdf" ? ".pdf" : ".png";
            var fileName = $"payment-slip-{request.ReferenceNumber}{extension}";

            return File(vetForm.PaymentSlipFile, contentType, fileName);
        }

        [HttpPut("updatecertificate/{id}/status")]
        [Authorize(Roles = "Admin,User")]
        public async Task<IActionResult> UpdateCertificateStatus(int id, [FromBody] UpdateCertificateStatusDto dto)
        {
            if (!Enum.TryParse<CertificateStatus>(dto.Status, true, out var status))
                return BadRequest(new { Message = "Invalid status. Use Confirmed or Rejected." });

            if (status == CertificateStatus.Pending)
                return BadRequest(new { Message = "Status can only be updated to Confirmed or Rejected." });

            var request = await _certificateRequestRepository.GetByIdAsync(id);
            if (request == null)
                return NotFound(new { Message = "Certificate request not found." });

            request.Status = status;
            await _certificateRequestRepository.UpdateAsync(request);

            return Ok(new { request.Id, request.ReferenceNumber, request.Status });
        }

        [HttpPost("vet-certificate-forms")]
        [Authorize(Roles = "Admin,Company")]
        public async Task<IActionResult> CreateVetCertificateForm()
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            if (!Request.HasFormContentType)
                return BadRequest(new { Message = "Request must be multipart/form-data." });

            var form = await Request.ReadFormAsync();

            int? certRequestId = null;
            var isUserAdmin = User.IsInRole("Admin");
            string targetCompanyUserId = userId;
            byte[]? finalPaymentSlip = await ReadFileBytes(form.Files["paymentSlipFile"]);
            List<VetCertificateAttachment>? finalAttachments = await ParseVetAttachments(form);

            if (form.TryGetValue("certificateRequestId", out var certIdValue) &&
                int.TryParse(certIdValue.ToString(), out var parsedId))
            {
                certRequestId = parsedId;

                var request = await _context.CertificateRequests
                    .AsNoTracking()
                    .FirstOrDefaultAsync(r => r.Id == certRequestId.Value);

                if (request != null && !string.IsNullOrEmpty(request.CompanyUserId))
                {
                    targetCompanyUserId = request.CompanyUserId;
                }

                if (!isUserAdmin)
                {
                    var belongsToUser = await _certificateRequestRepository.BelongsToUserAsync(certRequestId.Value, userId);
                    if (!belongsToUser)
                        return BadRequest(new { Message = "Certificate request not found for this user." });
                }

                byte[]? existingPaymentSlip = null;
                List<VetCertificateAttachment> existingAttachments = new();

                var existingForm = await _vetCertificateFormRepository.GetByCertificateRequestIdAsync(certRequestId.Value);
                if (existingForm != null)
                {
                    var trackedExisting = await _context.VetCertificateForms
                        .Include(v => v.Products)
                        .Include(v => v.Attachments)
                        .FirstOrDefaultAsync(v => v.CertificateRequestId == certRequestId.Value);
                    if (trackedExisting != null)
                    {
                        existingPaymentSlip = trackedExisting.PaymentSlipFile;
                        existingAttachments = trackedExisting.Attachments?.ToList() ?? new();
                        _context.VetCertificateForms.Remove(trackedExisting);
                        await _context.SaveChangesAsync();
                    }
                }

                if (finalPaymentSlip == null || finalPaymentSlip.Length == 0)
                {
                    finalPaymentSlip = existingPaymentSlip;
                }

                if ((finalAttachments == null || finalAttachments.Count == 0) && existingAttachments.Count > 0)
                {
                    finalAttachments = existingAttachments.Select(a => new VetCertificateAttachment
                    {
                        FileOrder = a.FileOrder,
                        OriginalFileName = a.OriginalFileName,
                        SecondaryFileName = a.SecondaryFileName,
                        ContentType = a.ContentType,
                        FileContent = a.FileContent
                    }).ToList();
                }
            }

            var entity = new VetCertificateForm
            {
                CertificateRequestId = certRequestId,
                CompanyUserId = targetCompanyUserId,
                CreatedAt = DateTime.UtcNow,
                OldHC = GetFormValue(form, "oldHC"),
                NewHC = GetFormValue(form, "newHC"),
                LandingSite = GetFormValue(form, "landingSite"),
                BoatRegistration = GetFormValue(form, "boatRegistration"),
                BoatNumber = GetFormValue(form, "boatNumber"),
                SupplierNameAddress = GetFormValue(form, "supplierNameAddress"),
                ArrivalAtFactory = ParseDateTime(form, "arrivalAtFactory"),
                ProcessingDate = ParseDateTime(form, "processingDate"),
                FarmLocation = GetFormValue(form, "farmLocation"),
                FarmOwnerName = GetFormValue(form, "farmOwnerName"),
                FarmOwnerAddress = GetFormValue(form, "farmOwnerAddress"),
                HarvestDate = ParseDateTime(form, "harvestDate"),
                ArrivalTimeProduct = ParseDateTime(form, "arrivalTimeProduct"),
                ProcessingDates = GetFormValue(form, "processingDates"),
                AquaSupplier = GetFormValue(form, "aquaSupplier"),
                CountryOrigin = GetFormValue(form, "countryOrigin"),
                ArrivalConsignment = ParseDateTime(form, "arrivalConsignment"),
                HealthCertNo = GetFormValue(form, "healthCertNo"),
                ProductTypeAquaculture = ParseBool(form, "productTypeAquaculture"),
                ProductTypeWildCaught = ParseBool(form, "productTypeWildCaught"),
                ConsignorName = GetFormValue(form, "consignorName"),
                ConsignorAddress = GetFormValue(form, "consignorAddress"),
                ConsignorPostal = GetFormValue(form, "consignorPostal"),
                ConsignorTel = GetFormValue(form, "consignorTel"),
                ConsigneeName = GetFormValue(form, "consigneeName"),
                ConsigneeAddress = GetFormValue(form, "consigneeAddress"),
                ConsigneePostal = GetFormValue(form, "consigneePostal"),
                ConsigneeTel = GetFormValue(form, "consigneeTel"),
                CountryOriginISO = GetFormValue(form, "countryOriginISO"),
                RegionOriginISO = GetFormValue(form, "regionOriginISO"),
                CountryDestinationISO = GetFormValue(form, "countryDestinationISO"),
                ProcessingEstName = GetFormValue(form, "processingEstName"),
                ProcessingEstAddress = GetFormValue(form, "processingEstAddress"),
                ApprovalNo = GetFormValue(form, "approvalNo"),
                PlaceOfLoading = GetFormValue(form, "placeOfLoading"),
                DateOfDeparture = ParseDateTime(form, "dateOfDeparture"),
                TransportAeroPlane = ParseBool(form, "transportAeroPlane"),
                TransportShip = ParseBool(form, "transportShip"),
                TransportRailwayWagon = ParseBool(form, "transportRailwayWagon"),
                TransportRoadVehicle = ParseBool(form, "transportRoadVehicle"),
                TransportOther = ParseBool(form, "transportOther"),
                TransportId = GetFormValue(form, "transportId"),
                DocReferences = GetFormValue(form, "docReferences"),
                EntryBIP = GetFormValue(form, "entryBIP"),
                DescCommon = GetFormValue(form, "descCommon"),
                DescScientific = GetFormValue(form, "descScientific"),
                ProcessingType = GetFormValue(form, "processingType"),
                HsCode = GetFormValue(form, "hsCode"),
                TemperatureAmbient = ParseBool(form, "temperatureAmbient"),
                TemperatureChilled = ParseBool(form, "temperatureChilled"),
                TemperatureFrozen = ParseBool(form, "temperatureFrozen"),
                Quantity = GetFormValue(form, "quantity"),
                NumPackages = GetFormValue(form, "numPackages"),
                PackagingType = GetFormValue(form, "packagingType"),
                ContainerId = GetFormValue(form, "containerId"),
                ForHumanConsumption = ParseBool(form, "forHumanConsumption"),
                ForImportEU = GetFormValue(form, "forImportEU"),
                NatureAquaculture = ParseBool(form, "natureAquaculture"),
                NatureWildOrigin = ParseBool(form, "natureWildOrigin"),
                TreatmentChilled = ParseBool(form, "treatmentChilled"),
                TreatmentFrozen = ParseBool(form, "treatmentFrozen"),
                TreatmentLive = ParseBool(form, "treatmentLive"),
                NetWeight = GetFormValue(form, "netWeight"),
                PaymentSlipFile = finalPaymentSlip ?? await ReadFileBytes(form.Files["paymentSlipFile"]),
                SignatureDate = ParseDateTime(form, "signatureDate"),
                SignatureTime = ParseDateTime(form, "signatureTime"),
                Signature = GetFormValue(form, "signature"),
                SignatoryName = GetFormValue(form, "signatoryName"),
                Designation = GetFormValue(form, "designation"),
                Attestation61_1 = ParseBool(form, "attestation61_1") ?? true,
                Attestation61_2 = ParseBool(form, "attestation61_2") ?? true,
                Attestation61_3 = ParseBool(form, "attestation61_3") ?? true,
                Attestation61_4 = ParseBool(form, "attestation61_4") ?? true,
                Attestation61_5 = ParseBool(form, "attestation61_5") ?? true,
                Attestation62_1 = ParseBool(form, "attestation62_1") ?? true,
                Attestation62_2 = ParseBool(form, "attestation62_2") ?? true
            };

            var parsedProducts = ParseVetProducts(form);
            entity.Products = parsedProducts.Count > 0 ? parsedProducts : BuildLegacyVetProducts(entity);

            var attachments = finalAttachments ?? await ParseVetAttachments(form);
            entity.Attachments = attachments;
            entity.UploadedCertificateFile = attachments.FirstOrDefault()?.FileContent;

            await _vetCertificateFormRepository.CreateAsync(entity);

            if (certRequestId.HasValue)
            {
                var req = await _certificateRequestRepository.GetByIdAsync(certRequestId.Value);
                if (req != null)
                {
                    req.Status = CertificateStatus.Confirmed;
                    await _certificateRequestRepository.UpdateAsync(req);
                }
            }

            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpPost("au-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateAuCertificate([FromBody] CreateAuCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateAuCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpPost("am-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateAmCertificate([FromBody] CreateAmCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateAmCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpPost("br-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateBrCertificate([FromBody] CreateBrCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateBrCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.RefNumber, entity.DateOfIssue });
        }

        [HttpPost("ch-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateChCertificate([FromBody] CreateChCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateChCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.DateOfIssue });
        }

        [HttpPost("hk-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateHkCertificate([FromBody] CreateHkCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateHkCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.DateOfIssue });
        }

        [HttpPost("id-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateIdCertificate([FromBody] CreateIdCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateIdCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertifiedDate });
        }

        [HttpPost("ind-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateIndCertificate([FromBody] CreateIndCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateIndCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.AttestationDate });
        }

        [HttpPost("jp-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateJpCertificate([FromBody] CreateJpCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateJpCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("kw-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateKwCertificate([FromBody] CreateKwCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateKwCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("my-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateMyCertificate([FromBody] CreateMyCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateMyCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("nz-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateNzCertificate([FromBody] CreateNzCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateNzCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("ru-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateRuCertificate([FromBody] CreateRuCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateRuCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("kz-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateKzCertificate([FromBody] CreateKzCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateKzCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("tw-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateTwCertificate([FromBody] CreateTwCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateTwCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("ua-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateUaCertificate([FromBody] CreateUaCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateUaCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("uk-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateUkCertificate([FromBody] CreateUkCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateUkCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("usa-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateUsaCertificate([FromBody] CreateUsaCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateUsaCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id });
        }

        [HttpPost("il-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateIlCertificate([FromBody] CreateIlCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateIlCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpPost("mv-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateMvCertificate([FromBody] MvCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateMvCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpPost("ca-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateCaCertificate([FromBody] CaCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateCaCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpPost("sa-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateSaCertificate([FromBody] SaCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateSaCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpPost("za-certificates")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> CreateZaCertificate([FromBody] ZaCertificateDto dto)
        {
            var userId = GetUserId();
            if (userId == null) return Unauthorized();

            var targetCompanyUserId = await _certificateService.ResolveCompanyUserId(userId, dto.CertificateRequestId);
            if (targetCompanyUserId == null)
                return BadRequest(new { Message = "Certificate request not found." });

            var entity = await _certificateService.CreateZaCertificateAsync(dto, targetCompanyUserId);
            return Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
        }

        [HttpGet("au-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetAuCertificateByRequestId(int requestId)
        {
            var cert = await _context.AuCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("am-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetAmCertificateByRequestId(int requestId)
        {
            var cert = await _context.AmCertificates
                .Include(c => c.PreExportCertificates)
                .Include(c => c.Attachments)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("br-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetBrCertificateByRequestId(int requestId)
        {
            var cert = await _context.BrCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("ch-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetChCertificateByRequestId(int requestId)
        {
            var cert = await _context.ChCertificates
                .Include(c => c.Attachments)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("hk-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetHkCertificateByRequestId(int requestId)
        {
            var cert = await _context.HkCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("id-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetIdCertificateByRequestId(int requestId)
        {
            var cert = await _context.IdCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("ind-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetIndCertificateByRequestId(int requestId)
        {
            var cert = await _context.IndCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("jp-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetJpCertificateByRequestId(int requestId)
        {
            var cert = await _context.JpCertificates
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("kw-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetKwCertificateByRequestId(int requestId)
        {
            var cert = await _context.KwCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("my-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetMyCertificateByRequestId(int requestId)
        {
            var cert = await _context.MyCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("nz-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetNzCertificateByRequestId(int requestId)
        {
            var cert = await _context.NzCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("ru-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetRuCertificateByRequestId(int requestId)
        {
            var cert = await _context.RuCertificates
                .Include(c => c.PreExportCertificates)
                .Include(c => c.Attachments)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("kz-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetKzCertificateByRequestId(int requestId)
        {
            var cert = await _context.KzCertificates
                .Include(c => c.PreExportCertificates)
                .Include(c => c.Attachments)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("mv-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetMvCertificateByRequestId(int requestId)
        {
            var cert = await _context.MvCertificates
                .Include(c => c.Products)
                .Include(c => c.ProductsSecond)
                .Include(c => c.ProductsAttachment)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("tw-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetTwCertificateByRequestId(int requestId)
        {
            var cert = await _context.TwCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("usa-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetUsaCertificateByRequestId(int requestId)
        {
            var cert = await _context.UsaCertificates
                .Include(c => c.ProductsAttachment)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("ca-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetCaCertificateByRequestId(int requestId)
        {
            var cert = await _context.CaCertificates
                .Include(c => c.ProductsAttachment)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("sa-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetSaCertificateByRequestId(int requestId)
        {
            var cert = await _context.SaCertificates
                .Include(c => c.ProductsAttachment)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("za-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetZaCertificateByRequestId(int requestId)
        {
            var cert = await _context.ZaCertificates
                .Include(c => c.ProductsAttachment)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("il-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetIlCertificateByRequestId(int requestId)
        {
            var cert = await _context.IlCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }
        [HttpGet("ua-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetUaCertificateByRequestId(int requestId)
        {
            var cert = await _context.UaCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        [HttpGet("uk-certificates/by-request/{requestId}")]
        [Authorize(Roles = "Admin,User,Company")]
        public async Task<IActionResult> GetUkCertificateByRequestId(int requestId)
        {
            var cert = await _context.UkCertificates
                .Include(c => c.Products)
                .OrderByDescending(c => c.CreatedAt).FirstOrDefaultAsync(c => c.CertificateRequestId == requestId);
            if (cert == null) return Ok(null);
            return Ok(cert);
        }

        private string? GetUserId() =>
            User.FindFirstValue("UserID")
            ?? User.FindFirstValue("UserId")
            ?? User.FindFirstValue(ClaimTypes.NameIdentifier);

        private static async Task<byte[]?> ReadFileBytes(IFormFile? file)
        {
            if (file == null || file.Length == 0) return null;

            await using var memoryStream = new MemoryStream();
            await file.CopyToAsync(memoryStream);
            return memoryStream.ToArray();
        }

        private static PaymentSlipPreviewDto? CreatePaymentSlipPreview(byte[]? bytes)
        {
            if (bytes == null || bytes.Length == 0) return null;

            return new PaymentSlipPreviewDto(DetectMimeType(bytes), Convert.ToBase64String(bytes));
        }

        private static string DetectMimeType(byte[] bytes)
        {
            if (bytes.Length >= 4 && bytes[0] == 0x25 && bytes[1] == 0x50 && bytes[2] == 0x44 && bytes[3] == 0x46)
            {
                return "application/pdf";
            }

            if (bytes.Length >= 8 && bytes[0] == 0x89 && bytes[1] == 0x50 && bytes[2] == 0x4E && bytes[3] == 0x47)
            {
                return "image/png";
            }

            if (bytes.Length >= 3 && bytes[0] == 0xFF && bytes[1] == 0xD8 && bytes[2] == 0xFF)
            {
                return "image/jpeg";
            }

            if (bytes.Length >= 6 && bytes[0] == 0x47 && bytes[1] == 0x49 && bytes[2] == 0x46)
            {
                return "image/gif";
            }

            if (bytes.Length >= 12 && bytes[0] == 0x52 && bytes[1] == 0x49 && bytes[2] == 0x46 && bytes[3] == 0x46 && bytes[8] == 0x57 && bytes[9] == 0x45 && bytes[10] == 0x42 && bytes[11] == 0x50)
            {
                return "image/webp";
            }

            return "application/octet-stream";
        }

        private static string? GetFormValue(IFormCollection form, string key) =>
            form.TryGetValue(key, out var value) && !string.IsNullOrWhiteSpace(value.ToString())
                ? value.ToString()
                : null;

        private static List<VetCertificateProduct> ParseVetProducts(IFormCollection form)
        {
            var rawJson = GetFormValue(form, "productsJson");
            if (string.IsNullOrWhiteSpace(rawJson))
            {
                return new List<VetCertificateProduct>();
            }

            try
            {
                var options = new JsonSerializerOptions
                {
                    PropertyNameCaseInsensitive = true,
                    NumberHandling = System.Text.Json.Serialization.JsonNumberHandling.AllowReadingFromString
                };
                options.Converters.Add(new ForgivingStringConverter());
                options.Converters.Add(new ForgivingBooleanConverter());

                var payload = JsonSerializer.Deserialize<List<VetProductPayload>>(rawJson, options)
                    ?? new List<VetProductPayload>();

                return payload
                    .Where(HasAnyProductValue)
                    .Select((p, index) => new VetCertificateProduct
                    {
                        ProductOrder = p.ProductOrder.GetValueOrDefault(index + 1),
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
                    })
                    .ToList();
            }
            catch
            {
                return new List<VetCertificateProduct>();
            }
        }

        private static async Task<List<VetCertificateAttachment>> ParseVetAttachments(IFormCollection form)
        {
            var files = form.Files.GetFiles("uploadedCertificateFile");
            if (files.Count == 0)
            {
                return new List<VetCertificateAttachment>();
            }

            var secondaryNames = ParseVetAttachmentSecondaryNames(form);
            var attachments = new List<VetCertificateAttachment>();

            for (var i = 0; i < files.Count; i++)
            {
                var file = files[i];
                var content = await ReadFileBytes(file);
                if (content == null || content.Length == 0)
                {
                    continue;
                }

                var secondaryName = i < secondaryNames.Count ? secondaryNames[i] : null;

                attachments.Add(new VetCertificateAttachment
                {
                    FileOrder = i + 1,
                    OriginalFileName = string.IsNullOrWhiteSpace(file.FileName) ? null : file.FileName,
                    SecondaryFileName = string.IsNullOrWhiteSpace(secondaryName) ? null : secondaryName,
                    ContentType = string.IsNullOrWhiteSpace(file.ContentType) ? null : file.ContentType,
                    FileContent = content
                });
            }

            return attachments;
        }

        private static List<string?> ParseVetAttachmentSecondaryNames(IFormCollection form)
        {
            var rawJson = GetFormValue(form, "uploadedCertificateFileSecondaryNamesJson");
            if (string.IsNullOrWhiteSpace(rawJson))
            {
                return new List<string?>();
            }

            try
            {
                var payload = JsonSerializer.Deserialize<List<VetAttachmentSecondaryNamePayload>>(rawJson, new JsonSerializerOptions
                {
                    PropertyNameCaseInsensitive = true
                }) ?? new List<VetAttachmentSecondaryNamePayload>();

                return payload.Select(item => item.SecondaryFileName).ToList();
            }
            catch
            {
                return new List<string?>();
            }
        }

        private static List<VetCertificateProduct> BuildLegacyVetProducts(VetCertificateForm form)
        {
            var legacyPayload = new VetProductPayload
            {
                DescCommon = form.DescCommon,
                DescScientific = form.DescScientific,
                ProcessingType = form.ProcessingType,
                HsCode = form.HsCode,
                TemperatureAmbient = form.TemperatureAmbient,
                TemperatureChilled = form.TemperatureChilled,
                TemperatureFrozen = form.TemperatureFrozen,
                Quantity = form.Quantity,
                NumPackages = form.NumPackages,
                PackagingType = form.PackagingType,
                ContainerId = form.ContainerId,
                ForHumanConsumption = form.ForHumanConsumption,
                ForImportEU = form.ForImportEU,
                NatureAquaculture = form.NatureAquaculture,
                NatureWildOrigin = form.NatureWildOrigin,
                TreatmentChilled = form.TreatmentChilled,
                TreatmentFrozen = form.TreatmentFrozen,
                TreatmentLive = form.TreatmentLive,
                NetWeight = form.NetWeight
            };

            if (!HasAnyProductValue(legacyPayload))
            {
                return new List<VetCertificateProduct>();
            }

            return new List<VetCertificateProduct>
            {
                new()
                {
                    ProductOrder = 1,
                    DescCommon = legacyPayload.DescCommon,
                    DescScientific = legacyPayload.DescScientific,
                    ProcessingType = legacyPayload.ProcessingType,
                    HsCode = legacyPayload.HsCode,
                    TemperatureAmbient = legacyPayload.TemperatureAmbient,
                    TemperatureChilled = legacyPayload.TemperatureChilled,
                    TemperatureFrozen = legacyPayload.TemperatureFrozen,
                    Quantity = legacyPayload.Quantity,
                    NumPackages = legacyPayload.NumPackages,
                    PackagingType = legacyPayload.PackagingType,
                    ContainerId = legacyPayload.ContainerId,
                    ForHumanConsumption = legacyPayload.ForHumanConsumption,
                    ForImportEU = legacyPayload.ForImportEU,
                    NatureAquaculture = legacyPayload.NatureAquaculture,
                    NatureWildOrigin = legacyPayload.NatureWildOrigin,
                    TreatmentChilled = legacyPayload.TreatmentChilled,
                    TreatmentFrozen = legacyPayload.TreatmentFrozen,
                    TreatmentLive = legacyPayload.TreatmentLive,
                    NetWeight = legacyPayload.NetWeight
                }
            };
        }

        private static bool HasAnyProductValue(VetProductPayload payload)
        {
            return !string.IsNullOrWhiteSpace(payload.DescCommon)
                || !string.IsNullOrWhiteSpace(payload.DescScientific)
                || !string.IsNullOrWhiteSpace(payload.ProcessingType)
                || !string.IsNullOrWhiteSpace(payload.HsCode)
                || payload.TemperatureAmbient == true
                || payload.TemperatureChilled == true
                || payload.TemperatureFrozen == true
                || !string.IsNullOrWhiteSpace(payload.Quantity)
                || !string.IsNullOrWhiteSpace(payload.NumPackages)
                || !string.IsNullOrWhiteSpace(payload.PackagingType)
                || !string.IsNullOrWhiteSpace(payload.ContainerId)
                || payload.ForHumanConsumption == true
                || !string.IsNullOrWhiteSpace(payload.ForImportEU)
                || payload.NatureAquaculture == true
                || payload.NatureWildOrigin == true
                || payload.TreatmentChilled == true
                || payload.TreatmentFrozen == true
                || payload.TreatmentLive == true
                || !string.IsNullOrWhiteSpace(payload.NetWeight);
        }

        private static DateTime? ParseDateTime(IFormCollection form, string key) =>
            form.TryGetValue(key, out var value) && DateTime.TryParse(value.ToString(), out var result)
                ? result
                : null;

        private static bool? ParseBool(IFormCollection form, string key)
        {
            if (!form.TryGetValue(key, out var value)) return null;

            return value.ToString().ToLower() switch
            {
                "true" or "yes" or "1" => true,
                "false" or "no" or "0" => false,
                _ => null
            };
        }

        private static DateTime CalculateMidnightExpirationUtc(DateTime createdAtUtc)
        {
            // Calculate midnight at the end of the day in local time (UTC + 05:30)
            var localCreated = createdAtUtc.AddHours(5.5);
            var localMidnight = localCreated.Date.AddDays(1); // 00:00:00 midnight start of next day
            return localMidnight.AddHours(-5.5); // Convert back to UTC
        }

        [HttpGet("public-verify")]
        [AllowAnonymous]
        public async Task<IActionResult> PublicVerifyCertificate([FromQuery] string? refNumber, [FromQuery] int? id)
        {
            var cleanRef = refNumber?.Trim() ?? string.Empty;
            if (cleanRef.Equals("OFFICIAL-HC", StringComparison.OrdinalIgnoreCase) ||
                cleanRef.Equals("N/A", StringComparison.OrdinalIgnoreCase) ||
                cleanRef.Equals("DRAFT", StringComparison.OrdinalIgnoreCase))
            {
                cleanRef = string.Empty;
            }

            var targetId = id.HasValue && id.Value > 0 ? id.Value : (int?)null;

            // Extract numeric ID only from explicit ID patterns like "CERT-123", "REF-123", "ID-123", or plain digits
            if (!targetId.HasValue && !string.IsNullOrWhiteSpace(cleanRef))
            {
                if (cleanRef.StartsWith("CERT-", StringComparison.OrdinalIgnoreCase) && int.TryParse(cleanRef.Substring(5), out var parsedCertId))
                {
                    targetId = parsedCertId;
                }
                else if (cleanRef.StartsWith("ID-", StringComparison.OrdinalIgnoreCase) && int.TryParse(cleanRef.Substring(3), out var parsedId))
                {
                    targetId = parsedId;
                }
                else if (cleanRef.StartsWith("REF-", StringComparison.OrdinalIgnoreCase) && int.TryParse(cleanRef.Substring(4), out var parsedRefId))
                {
                    targetId = parsedRefId;
                }
                else if (int.TryParse(cleanRef, out var pureNumberId) && pureNumberId > 0)
                {
                    targetId = pureNumberId;
                }
            }

            // If neither reference nor ID provided, return unverified immediately
            if (!targetId.HasValue && string.IsNullOrWhiteSpace(cleanRef))
            {
                return Ok(new
                {
                    isVerified = false,
                    status = "NOT VERIFIED",
                    message = "No certificate reference was provided."
                });
            }

            // 1. Check general CertificateRequests table (all certificates have a record here)
            Entities.CertificateRequest? req = null;
            try
            {
                if (targetId.HasValue)
                {
                    req = await _context.CertificateRequests.FirstOrDefaultAsync(r => r.Id == targetId.Value);
                }
                if (req == null && !string.IsNullOrWhiteSpace(cleanRef))
                {
                    req = await _context.CertificateRequests.FirstOrDefaultAsync(r => r.ReferenceNumber == cleanRef);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[PublicVerifyCertificate] CertificateRequests lookup warning: {ex.Message}");
            }

            int? reqId = req?.Id ?? targetId;

            // Default fallback values
            string certRef = req?.ReferenceNumber ?? (!string.IsNullOrWhiteSpace(cleanRef) ? cleanRef : (reqId.HasValue ? $"CERT-{reqId}" : ""));
            string status = req == null ? "Confirmed & Verified" : (req.Status == Entities.CertificateStatus.Confirmed ? "Confirmed & Verified" : (req.Status == Entities.CertificateStatus.Pending ? "Pending Verification" : req.Status.ToString()));
            string issueDate = req?.CreatedAt.ToString("dd/MM/yyyy") ?? DateTime.UtcNow.ToString("dd/MM/yyyy");
            string consignorName = "REGISTERED EXPORTER (SRI LANKA)";
            string consignorAddress = string.Empty;
            string consigneeName = "AUTHORIZED IMPORTER";
            string consigneeAddress = string.Empty;
            string itemName = "FISH AND FISHERY PRODUCTS";
            string countryDest = "DESTINATION COUNTRY";
            string officerName = "OFFICIAL INSPECTOR";
            string designation = "QUALITY CONTROL OFFICER";
            string qualification = string.Empty;
            string competentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES, SRI LANKA";
            string createdAt = req?.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss") ?? DateTime.UtcNow.ToString("dd/MM/yyyy HH:mm:ss");

            if (req != null)
            {
                try
                {
                    if (req.CountryId.HasValue)
                    {
                        var country = await _context.Countries.FirstOrDefaultAsync(c => c.Id == req.CountryId.Value);
                        if (country != null && !string.IsNullOrWhiteSpace(country.Name))
                        {
                            countryDest = country.Name.ToUpper();
                        }
                    }
                    else if (req.CertificateType == Entities.CertificateType.EU)
                    {
                        countryDest = "EUROPEAN UNION";
                    }

                    if (!string.IsNullOrWhiteSpace(req.CompanyUserId))
                    {
                        var comp = await _context.Companies.FirstOrDefaultAsync(c => c.UserId == req.CompanyUserId);
                        if (comp != null && !string.IsNullOrWhiteSpace(comp.CompanyName))
                        {
                            consignorName = comp.CompanyName.ToUpper();
                            consignorAddress = comp.CompanyAddress ?? string.Empty;
                        }
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Details enrichment warning: {ex.Message}");
                }
            }

            bool foundSpecific = false;

            // 1. Check USA
            try
            {
                var usa = await _context.UsaCertificates.OrderByDescending(c => c.CreatedAt)
                    .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNumber == cleanRef || c.MyRef == cleanRef)));
                if (usa != null)
                {
                    foundSpecific = true;
                    certRef = !string.IsNullOrWhiteSpace(usa.CertificateNumber) ? usa.CertificateNumber : (!string.IsNullOrWhiteSpace(usa.MyRef) ? usa.MyRef : certRef);
                    if (!string.IsNullOrWhiteSpace(usa.ConsignorName)) consignorName = usa.ConsignorName;
                    if (!string.IsNullOrWhiteSpace(usa.ConsignorAddress)) consignorAddress = usa.ConsignorAddress;
                    if (!string.IsNullOrWhiteSpace(usa.ConsigneeName)) consigneeName = usa.ConsigneeName;
                    if (!string.IsNullOrWhiteSpace(usa.ConsigneeAddress)) consigneeAddress = usa.ConsigneeAddress;
                    if (!string.IsNullOrWhiteSpace(usa.ItemName)) itemName = usa.ItemName;
                    if (!string.IsNullOrWhiteSpace(usa.CountryOfDestination)) countryDest = usa.CountryOfDestination;
                    if (!string.IsNullOrWhiteSpace(usa.SignatoryName)) officerName = usa.SignatoryName;
                    if (!string.IsNullOrWhiteSpace(usa.Designation)) designation = usa.Designation;
                    if (!string.IsNullOrWhiteSpace(usa.Qualification)) qualification = usa.Qualification;
                    if (usa.Date.HasValue) issueDate = usa.Date.Value.ToString("dd/MM/yyyy");
                    createdAt = usa.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[PublicVerifyCertificate] USA check warning: {ex.Message}");
            }

            // 2. Check UK
            if (!foundSpecific)
            {
                try
                {
                    var uk = await _context.UkCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.CertificateReferenceNo == cleanRef));
                    if (uk != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(uk.CertificateReferenceNo)) certRef = uk.CertificateReferenceNo;
                        if (!string.IsNullOrWhiteSpace(uk.ConsignorName)) consignorName = uk.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(uk.ConsignorAddress)) consignorAddress = uk.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(uk.ConsigneeName)) consigneeName = uk.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(uk.ConsigneeAddress)) consigneeAddress = uk.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(uk.SignatoryName)) officerName = uk.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(uk.Qualification)) qualification = uk.Qualification;
                        if (uk.CertifiedDate.HasValue) issueDate = uk.CertifiedDate.Value.ToString("dd/MM/yyyy");
                        countryDest = "UNITED KINGDOM (GB)";
                        createdAt = uk.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] UK check warning: {ex.Message}");
                }
            }

            // 3. Check Australia
            if (!foundSpecific)
            {
                try
                {
                    var au = await _context.AuCertificates.Include(c => c.Products).OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.CertRefNumber == cleanRef));
                    if (au != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(au.CertRefNumber)) certRef = au.CertRefNumber;
                        if (!string.IsNullOrWhiteSpace(au.ConsignorName)) consignorName = au.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(au.ConsignorPostal)) consignorAddress = au.ConsignorPostal;
                        if (!string.IsNullOrWhiteSpace(au.ConsigneeName)) consigneeName = au.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(au.ConsigneePostal)) consigneeAddress = au.ConsigneePostal;
                        if (au.Products != null && au.Products.Any())
                        {
                            var prodNames = au.Products.Select(p => p.SpeciesScientificName ?? p.NatureOfCommodity).Where(s => !string.IsNullOrWhiteSpace(s));
                            if (prodNames.Any()) itemName = string.Join(", ", prodNames);
                        }
                        if (!string.IsNullOrWhiteSpace(au.SignatoryName)) officerName = au.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(au.Qualification)) qualification = au.Qualification;
                        countryDest = "AUSTRALIA";
                        createdAt = au.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Australia check warning: {ex.Message}");
                }
            }

            // 4. Check Canada
            if (!foundSpecific)
            {
                try
                {
                    var ca = await _context.CaCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNumber == cleanRef || c.MyRef == cleanRef)));
                    if (ca != null)
                    {
                        foundSpecific = true;
                        certRef = !string.IsNullOrWhiteSpace(ca.CertificateNumber) ? ca.CertificateNumber : (!string.IsNullOrWhiteSpace(ca.MyRef) ? ca.MyRef : certRef);
                        if (!string.IsNullOrWhiteSpace(ca.ConsignorName)) consignorName = ca.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(ca.ConsignorAddress)) consignorAddress = ca.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(ca.ConsigneeName)) consigneeName = ca.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(ca.ConsigneeAddress)) consigneeAddress = ca.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(ca.ItemName)) itemName = ca.ItemName;
                        if (!string.IsNullOrWhiteSpace(ca.CountryOfDestination)) countryDest = ca.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(ca.SignatoryName)) officerName = ca.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(ca.Designation)) designation = ca.Designation;
                        if (!string.IsNullOrWhiteSpace(ca.Qualification)) qualification = ca.Qualification;
                        if (ca.Date.HasValue) issueDate = ca.Date.Value.ToString("dd/MM/yyyy");
                        createdAt = ca.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Canada check warning: {ex.Message}");
                }
            }

            // 5. Check Saudi Arabia
            if (!foundSpecific)
            {
                try
                {
                    var sa = await _context.SaCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNumber == cleanRef || c.MyRef == cleanRef)));
                    if (sa != null)
                    {
                        foundSpecific = true;
                        certRef = !string.IsNullOrWhiteSpace(sa.CertificateNumber) ? sa.CertificateNumber : (!string.IsNullOrWhiteSpace(sa.MyRef) ? sa.MyRef : certRef);
                        if (!string.IsNullOrWhiteSpace(sa.ConsignorName)) consignorName = sa.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(sa.ConsignorAddress)) consignorAddress = sa.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(sa.ConsigneeName)) consigneeName = sa.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(sa.ConsigneeAddress)) consigneeAddress = sa.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(sa.ItemName)) itemName = sa.ItemName;
                        if (!string.IsNullOrWhiteSpace(sa.CountryOfDestination)) countryDest = sa.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(sa.SignatoryName)) officerName = sa.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(sa.Designation)) designation = sa.Designation;
                        if (!string.IsNullOrWhiteSpace(sa.Qualification)) qualification = sa.Qualification;
                        if (sa.Date.HasValue) issueDate = sa.Date.Value.ToString("dd/MM/yyyy");
                        createdAt = sa.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Saudi Arabia check warning: {ex.Message}");
                }
            }

            // 6. Check South Africa
            if (!foundSpecific)
            {
                try
                {
                    var za = await _context.ZaCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNumber == cleanRef || c.MyRef == cleanRef)));
                    if (za != null)
                    {
                        foundSpecific = true;
                        certRef = !string.IsNullOrWhiteSpace(za.CertificateNumber) ? za.CertificateNumber : (!string.IsNullOrWhiteSpace(za.MyRef) ? za.MyRef : certRef);
                        if (!string.IsNullOrWhiteSpace(za.ConsignorName)) consignorName = za.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(za.ConsignorAddress)) consignorAddress = za.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(za.ConsigneeName)) consigneeName = za.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(za.ConsigneeAddress)) consigneeAddress = za.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(za.ItemName)) itemName = za.ItemName;
                        if (!string.IsNullOrWhiteSpace(za.CountryOfDestination)) countryDest = za.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(za.SignatoryName)) officerName = za.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(za.Designation)) designation = za.Designation;
                        if (!string.IsNullOrWhiteSpace(za.Qualification)) qualification = za.Qualification;
                        if (za.Date.HasValue) issueDate = za.Date.Value.ToString("dd/MM/yyyy");
                        createdAt = za.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] South Africa check warning: {ex.Message}");
                }
            }

            // 7. Check EU / Vet Certificate Form
            if (!foundSpecific)
            {
                try
                {
                    var vet = await _context.VetCertificateForms.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.HealthCertNo == cleanRef || c.NewHC == cleanRef || c.OldHC == cleanRef)));
                    if (vet != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(vet.HealthCertNo)) certRef = vet.HealthCertNo;
                        else if (!string.IsNullOrWhiteSpace(vet.NewHC)) certRef = vet.NewHC;
                        if (!string.IsNullOrWhiteSpace(vet.ConsignorName)) consignorName = vet.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(vet.ConsignorAddress)) consignorAddress = vet.ConsignorAddress;
                        else if (!string.IsNullOrWhiteSpace(vet.ConsignorPostal)) consignorAddress = vet.ConsignorPostal;

                        if (!string.IsNullOrWhiteSpace(vet.ConsigneeAddress)) consigneeAddress = vet.ConsigneeAddress;
                        else if (!string.IsNullOrWhiteSpace(vet.ConsigneePostal)) consigneeAddress = vet.ConsigneePostal;

                        if (!string.IsNullOrWhiteSpace(vet.DescCommon)) itemName = vet.DescCommon;
                        if (!string.IsNullOrWhiteSpace(vet.SignatoryName)) officerName = vet.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(vet.Designation)) designation = vet.Designation;
                        if (vet.SignatureDate.HasValue) issueDate = vet.SignatureDate.Value.ToString("dd/MM/yyyy");

                        if (req?.CertificateType == Entities.CertificateType.EU || string.Equals(vet.ForImportEU, "Yes", StringComparison.OrdinalIgnoreCase))
                        {
                            countryDest = "EUROPEAN UNION";
                        }
                        else if (!string.IsNullOrWhiteSpace(countryDest) && countryDest != "DESTINATION COUNTRY")
                        {
                            // Keep resolved countryDest
                        }
                        else if (!string.IsNullOrWhiteSpace(vet.CountryDestinationISO))
                        {
                            countryDest = vet.CountryDestinationISO;
                        }

                        createdAt = vet.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Vet check warning: {ex.Message}");
                }
            }

            // 8. Check Armenia
            if (!foundSpecific)
            {
                try
                {
                    var am = await _context.AmCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNo == cleanRef || c.CertRefNumber == cleanRef)));
                    if (am != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(am.CertificateNo)) certRef = am.CertificateNo;
                        else if (!string.IsNullOrWhiteSpace(am.CertRefNumber)) certRef = am.CertRefNumber;
                        if (!string.IsNullOrWhiteSpace(am.ConsignorName)) consignorName = am.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(am.ConsignorPostal)) consignorAddress = am.ConsignorPostal;
                        if (!string.IsNullOrWhiteSpace(am.ConsigneeName)) consigneeName = am.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(am.ConsigneePostal)) consigneeAddress = am.ConsigneePostal;
                        if (!string.IsNullOrWhiteSpace(am.ProductName)) itemName = am.ProductName;
                        if (!string.IsNullOrWhiteSpace(am.SignatoryName)) officerName = am.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(am.Qualification)) qualification = am.Qualification;
                        countryDest = "ARMENIA";
                        createdAt = am.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Armenia check warning: {ex.Message}");
                }
            }

            // 9. Check Brazil
            if (!foundSpecific)
            {
                try
                {
                    var br = await _context.BrCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNo == cleanRef || c.RefNumber == cleanRef || c.ModeloConformeCircularNo == cleanRef)));
                    if (br != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(br.CertificateNo)) certRef = br.CertificateNo;
                        else if (!string.IsNullOrWhiteSpace(br.RefNumber)) certRef = br.RefNumber;
                        if (!string.IsNullOrWhiteSpace(br.ExporterName)) consignorName = br.ExporterName;
                        if (!string.IsNullOrWhiteSpace(br.ExporterAddress)) consignorAddress = br.ExporterAddress;
                        if (!string.IsNullOrWhiteSpace(br.ImporterName)) consigneeName = br.ImporterName;
                        if (!string.IsNullOrWhiteSpace(br.ImporterAddress)) consigneeAddress = br.ImporterAddress;
                        if (!string.IsNullOrWhiteSpace(br.CountryOfDestination)) countryDest = br.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(br.SignatoryName)) officerName = br.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(br.Qualification)) qualification = br.Qualification;
                        issueDate = br.DateOfIssue.ToString("dd/MM/yyyy");
                        createdAt = br.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Brazil check warning: {ex.Message}");
                }
            }

            // 10. Check China
            if (!foundSpecific)
            {
                try
                {
                    var ch = await _context.ChCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.RefNumber == cleanRef || c.CommodityName == cleanRef)));
                    if (ch != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(ch.RefNumber)) certRef = ch.RefNumber;
                        if (!string.IsNullOrWhiteSpace(ch.ExporterName)) consignorName = ch.ExporterName;
                        else if (!string.IsNullOrWhiteSpace(ch.ConsignorName)) consignorName = ch.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(ch.ExporterAddress)) consignorAddress = ch.ExporterAddress;
                        else if (!string.IsNullOrWhiteSpace(ch.ConsignorAddress)) consignorAddress = ch.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(ch.ImporterName)) consigneeName = ch.ImporterName;
                        else if (!string.IsNullOrWhiteSpace(ch.ConsigneeName)) consigneeName = ch.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(ch.ImporterAddress)) consigneeAddress = ch.ImporterAddress;
                        else if (!string.IsNullOrWhiteSpace(ch.ConsigneeAddress)) consigneeAddress = ch.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(ch.CommodityName)) itemName = ch.CommodityName;
                        if (!string.IsNullOrWhiteSpace(ch.SignatoryName)) officerName = ch.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(ch.Qualification)) qualification = ch.Qualification;
                        if (ch.DateOfIssue.HasValue) issueDate = ch.DateOfIssue.Value.ToString("dd/MM/yyyy");
                        countryDest = "PEOPLE'S REPUBLIC OF CHINA";
                        createdAt = ch.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] China check warning: {ex.Message}");
                }
            }

            // 11. Check Hong Kong
            if (!foundSpecific)
            {
                try
                {
                    var hk = await _context.HkCertificates.Include(c => c.Products).OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.IdentificationNumber == cleanRef));
                    if (hk != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(hk.IdentificationNumber)) certRef = hk.IdentificationNumber;
                        if (!string.IsNullOrWhiteSpace(hk.ConsignorName)) consignorName = hk.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(hk.ConsignorAddress)) consignorAddress = hk.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(hk.ConsigneeName)) consigneeName = hk.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(hk.ConsigneeAddress)) consigneeAddress = hk.ConsigneeAddress;
                        if (hk.Products != null && hk.Products.Any())
                        {
                            var prodNames = hk.Products.Select(p => p.Description ?? p.Species).Where(s => !string.IsNullOrWhiteSpace(s));
                            if (prodNames.Any()) itemName = string.Join(", ", prodNames);
                        }
                        if (!string.IsNullOrWhiteSpace(hk.DestinationCountryPlace)) countryDest = hk.DestinationCountryPlace;
                        if (!string.IsNullOrWhiteSpace(hk.SignatoryName)) officerName = hk.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(hk.Qualification)) qualification = hk.Qualification;
                        countryDest = "HONG KONG";
                        createdAt = hk.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Hong Kong check warning: {ex.Message}");
                }
            }

            // 12. Check Indonesia
            if (!foundSpecific)
            {
                try
                {
                    var idCert = await _context.IdCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.NumberNomor == cleanRef || c.AttestationRefNumber == cleanRef)));
                    if (idCert != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(idCert.NumberNomor)) certRef = idCert.NumberNomor;
                        else if (!string.IsNullOrWhiteSpace(idCert.AttestationRefNumber)) certRef = idCert.AttestationRefNumber;
                        if (!string.IsNullOrWhiteSpace(idCert.ConsignorName)) consignorName = idCert.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(idCert.ConsignorAddress)) consignorAddress = idCert.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(idCert.ConsigneeName)) consigneeName = idCert.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(idCert.ConsigneeAddress)) consigneeAddress = idCert.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(idCert.CommodityDescription)) itemName = idCert.CommodityDescription;
                        if (!string.IsNullOrWhiteSpace(idCert.SignatoryName)) officerName = idCert.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(idCert.CertifiedPosition)) designation = idCert.CertifiedPosition;
                        if (!string.IsNullOrWhiteSpace(idCert.Qualification)) qualification = idCert.Qualification;
                        countryDest = "INDONESIA";
                        createdAt = idCert.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Indonesia check warning: {ex.Message}");
                }
            }

            // 13. Check Israel
            if (!foundSpecific)
            {
                try
                {
                    var il = await _context.IlCertificates.Include(c => c.Products).OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.CertificationNo == cleanRef));
                    if (il != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(il.CertificationNo)) certRef = il.CertificationNo;
                        if (!string.IsNullOrWhiteSpace(il.ConsignorName)) consignorName = il.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(il.ConsignorAddress)) consignorAddress = il.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(il.ConsigneeName)) consigneeName = il.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(il.ConsigneeAddress)) consigneeAddress = il.ConsigneeAddress;
                        if (il.Products != null && il.Products.Any())
                        {
                            var prodNames = il.Products.Select(p => p.DescriptionOfCommodity ?? p.SpeciesScientificName).Where(s => !string.IsNullOrWhiteSpace(s));
                            if (prodNames.Any()) itemName = string.Join(", ", prodNames);
                        }
                        if (!string.IsNullOrWhiteSpace(il.SignatoryName)) officerName = il.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(il.Qualification)) qualification = il.Qualification;
                        if (il.SignatureDate.HasValue) issueDate = il.SignatureDate.Value.ToString("dd/MM/yyyy");
                        countryDest = "ISRAEL";
                        createdAt = il.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Israel check warning: {ex.Message}");
                }
            }

            // 14. Check India
            if (!foundSpecific)
            {
                try
                {
                    var ind = await _context.IndCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNumber == cleanRef || c.MyRef == cleanRef)));
                    if (ind != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(ind.CertificateNumber)) certRef = ind.CertificateNumber;
                        else if (!string.IsNullOrWhiteSpace(ind.MyRef)) certRef = ind.MyRef;
                        if (!string.IsNullOrWhiteSpace(ind.ConsignorName)) consignorName = ind.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(ind.ConsignorAddress)) consignorAddress = ind.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(ind.ConsigneeName)) consigneeName = ind.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(ind.ConsigneeAddress)) consigneeAddress = ind.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(ind.FoodDescription)) itemName = ind.FoodDescription;
                        else if (!string.IsNullOrWhiteSpace(ind.ItemDescription)) itemName = ind.ItemDescription;
                        if (!string.IsNullOrWhiteSpace(ind.CountryOfDestination)) countryDest = ind.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(ind.SignatoryName)) officerName = ind.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(ind.Qualification)) qualification = ind.Qualification;
                        if (ind.AuthorizedOfficialDate.HasValue) issueDate = ind.AuthorizedOfficialDate.Value.ToString("dd/MM/yyyy");
                        else if (ind.AttestationDate.HasValue) issueDate = ind.AttestationDate.Value.ToString("dd/MM/yyyy");
                        countryDest = "INDIA";
                        createdAt = ind.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] India check warning: {ex.Message}");
                }
            }

            // 15. Check Japan
            if (!foundSpecific)
            {
                try
                {
                    var jp = await _context.JpCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.MyRef == cleanRef || c.YourRef == cleanRef)));
                    if (jp != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(jp.MyRef)) certRef = jp.MyRef;
                        if (!string.IsNullOrWhiteSpace(jp.ConsignorName)) consignorName = jp.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(jp.ConsignorAddress)) consignorAddress = jp.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(jp.ConsigneeName)) consigneeName = jp.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(jp.ConsigneeAddress)) consigneeAddress = jp.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(jp.ItemName)) itemName = jp.ItemName;
                        if (!string.IsNullOrWhiteSpace(jp.SignatoryName)) officerName = jp.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(jp.Qualification)) qualification = jp.Qualification;
                        if (jp.Date.HasValue) issueDate = jp.Date.Value.ToString("dd/MM/yyyy");
                        countryDest = "JAPAN";
                        createdAt = jp.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Japan check warning: {ex.Message}");
                }
            }

            // 16. Check Kuwait
            if (!foundSpecific)
            {
                try
                {
                    var kw = await _context.KwCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.CertificateReferenceNo == cleanRef));
                    if (kw != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(kw.CertificateReferenceNo)) certRef = kw.CertificateReferenceNo;
                        if (!string.IsNullOrWhiteSpace(kw.ConsignorName)) consignorName = kw.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(kw.ConsignorAddress)) consignorAddress = kw.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(kw.ConsigneeName)) consigneeName = kw.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(kw.ConsigneeAddress)) consigneeAddress = kw.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(kw.CountryOfDestination)) countryDest = kw.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(kw.SignatoryName)) officerName = kw.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(kw.Qualification)) qualification = kw.Qualification;
                        if (kw.DateOfIssue.HasValue) issueDate = kw.DateOfIssue.Value.ToString("dd/MM/yyyy");
                        countryDest = "KUWAIT";
                        createdAt = kw.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Kuwait check warning: {ex.Message}");
                }
            }

            // 17. Check Kazakhstan
            if (!foundSpecific)
            {
                try
                {
                    var kz = await _context.KzCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.CertificateNo == cleanRef));
                    if (kz != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(kz.CertificateNo)) certRef = kz.CertificateNo;
                        if (!string.IsNullOrWhiteSpace(kz.ConsignorNameAddress)) consignorName = kz.ConsignorNameAddress;
                        if (!string.IsNullOrWhiteSpace(kz.ConsigneeNameAddress)) consigneeName = kz.ConsigneeNameAddress;
                        if (!string.IsNullOrWhiteSpace(kz.ProductName)) itemName = kz.ProductName;
                        if (!string.IsNullOrWhiteSpace(kz.SignatoryName)) officerName = kz.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(kz.Qualification)) qualification = kz.Qualification;
                        if (kz.DateOfIssue.HasValue) issueDate = kz.DateOfIssue.Value.ToString("dd/MM/yyyy");
                        countryDest = "KAZAKHSTAN";
                        createdAt = kz.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Kazakhstan check warning: {ex.Message}");
                }
            }

            // 18. Check Maldives
            if (!foundSpecific)
            {
                try
                {
                    var mv = await _context.MvCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateNumber == cleanRef || c.DescriptionOfCommodity == cleanRef)));
                    if (mv != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(mv.CertificateNumber)) certRef = mv.CertificateNumber;
                        if (!string.IsNullOrWhiteSpace(mv.ConsignorExporter)) consignorName = mv.ConsignorExporter;
                        if (!string.IsNullOrWhiteSpace(mv.ConsigneeImporter)) consigneeName = mv.ConsigneeImporter;
                        if (!string.IsNullOrWhiteSpace(mv.DescriptionOfCommodity)) itemName = mv.DescriptionOfCommodity;
                        if (!string.IsNullOrWhiteSpace(mv.CountryOfDestination)) countryDest = mv.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(mv.SignatoryName)) officerName = mv.SignatoryName;
                        else if (!string.IsNullOrWhiteSpace(mv.CertifyingOfficerName)) officerName = mv.CertifyingOfficerName;
                        if (!string.IsNullOrWhiteSpace(mv.Qualification)) qualification = mv.Qualification;
                        if (mv.CertifyingOfficerDate.HasValue) issueDate = mv.CertifyingOfficerDate.Value.ToString("dd/MM/yyyy");
                        countryDest = "MALDIVES";
                        createdAt = mv.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Maldives check warning: {ex.Message}");
                }
            }

            // 19. Check Malaysia
            if (!foundSpecific)
            {
                try
                {
                    var my = await _context.MyCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateReferenceNo == cleanRef || c.QualityCertificateNo == cleanRef)));
                    if (my != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(my.CertificateReferenceNo)) certRef = my.CertificateReferenceNo;
                        else if (!string.IsNullOrWhiteSpace(my.QualityCertificateNo)) certRef = my.QualityCertificateNo;
                        if (!string.IsNullOrWhiteSpace(my.ExporterName)) consignorName = my.ExporterName;
                        if (!string.IsNullOrWhiteSpace(my.ImporterDetails)) consigneeName = my.ImporterDetails;
                        if (!string.IsNullOrWhiteSpace(my.ProductBrand)) itemName = my.ProductBrand;
                        if (!string.IsNullOrWhiteSpace(my.CountryOfDestination)) countryDest = my.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(my.SignatoryName)) officerName = my.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(my.Qualification)) qualification = my.Qualification;
                        if (my.CertifyingOfficialDate.HasValue) issueDate = my.CertifyingOfficialDate.Value.ToString("dd/MM/yyyy");
                        countryDest = "MALAYSIA";
                        createdAt = my.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Malaysia check warning: {ex.Message}");
                }
            }

            // 20. Check New Zealand
            if (!foundSpecific)
            {
                try
                {
                    var nz = await _context.NzCertificates.Include(c => c.Products).OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.CertificateRefNumber == cleanRef));
                    if (nz != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(nz.CertificateRefNumber)) certRef = nz.CertificateRefNumber;
                        if (!string.IsNullOrWhiteSpace(nz.ConsignorName)) consignorName = nz.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(nz.ConsignorAddress)) consignorAddress = nz.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(nz.ConsigneeName)) consigneeName = nz.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(nz.ConsigneeAddress)) consigneeAddress = nz.ConsigneeAddress;
                        if (nz.Products != null && nz.Products.Any())
                        {
                            var prodNames = nz.Products.Select(p => p.ProductName ?? p.AquaticAnimalSpecies).Where(s => !string.IsNullOrWhiteSpace(s));
                            if (prodNames.Any()) itemName = string.Join(", ", prodNames);
                        }
                        if (!string.IsNullOrWhiteSpace(nz.CountryOfDestination)) countryDest = nz.CountryOfDestination;
                        if (!string.IsNullOrWhiteSpace(nz.SignatoryName)) officerName = nz.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(nz.Qualification)) qualification = nz.Qualification;
                        if (nz.SignatureDate.HasValue) issueDate = nz.SignatureDate.Value.ToString("dd/MM/yyyy");
                        countryDest = "NEW ZEALAND";
                        createdAt = nz.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] New Zealand check warning: {ex.Message}");
                }
            }

            // 21. Check Russia
            if (!foundSpecific)
            {
                try
                {
                    var ru = await _context.RuCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.CertificateNo == cleanRef));
                    if (ru != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(ru.CertificateNo)) certRef = ru.CertificateNo;
                        if (!string.IsNullOrWhiteSpace(ru.ConsignorNameAddress)) consignorName = ru.ConsignorNameAddress;
                        if (!string.IsNullOrWhiteSpace(ru.ConsigneeNameAddress)) consigneeName = ru.ConsigneeNameAddress;
                        if (!string.IsNullOrWhiteSpace(ru.ProductName)) itemName = ru.ProductName;
                        if (!string.IsNullOrWhiteSpace(ru.SignatoryName)) officerName = ru.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(ru.Qualification)) qualification = ru.Qualification;
                        if (ru.DateOfIssue.HasValue) issueDate = ru.DateOfIssue.Value.ToString("dd/MM/yyyy");
                        countryDest = "RUSSIA";
                        createdAt = ru.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Russia check warning: {ex.Message}");
                }
            }

            // 22. Check Taiwan
            if (!foundSpecific)
            {
                try
                {
                    var tw = await _context.TwCertificates.Include(c => c.Products).OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && c.ReferenceNo == cleanRef));
                    if (tw != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(tw.ReferenceNo)) certRef = tw.ReferenceNo;
                        if (!string.IsNullOrWhiteSpace(tw.ConsignorName)) consignorName = tw.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(tw.ConsignorAddress)) consignorAddress = tw.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(tw.ConsigneeName)) consigneeName = tw.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(tw.ConsigneeAddress)) consigneeAddress = tw.ConsigneeAddress;
                        if (tw.Products != null && tw.Products.Any())
                        {
                            var prodNames = tw.Products.Select(p => p.CommodityName ?? p.ScientificName).Where(s => !string.IsNullOrWhiteSpace(s));
                            if (prodNames.Any()) itemName = string.Join(", ", prodNames);
                        }
                        if (!string.IsNullOrWhiteSpace(tw.SignatoryName)) officerName = tw.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(tw.Qualification)) qualification = tw.Qualification;
                        if (tw.DateOfIssue.HasValue) issueDate = tw.DateOfIssue.Value.ToString("dd/MM/yyyy");
                        countryDest = "TAIWAN";
                        createdAt = tw.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Taiwan check warning: {ex.Message}");
                }
            }

            // 23. Check Ukraine
            if (!foundSpecific)
            {
                try
                {
                    var ua = await _context.UaCertificates.OrderByDescending(c => c.CreatedAt)
                        .FirstOrDefaultAsync(c => (reqId.HasValue && (c.CertificateRequestId == reqId || c.Id == reqId)) || (!string.IsNullOrWhiteSpace(cleanRef) && (c.CertificateReferenceNumber == cleanRef || c.HealthCertificateReferenceNumber == cleanRef)));
                    if (ua != null)
                    {
                        foundSpecific = true;
                        if (!string.IsNullOrWhiteSpace(ua.CertificateReferenceNumber)) certRef = ua.CertificateReferenceNumber;
                        else if (!string.IsNullOrWhiteSpace(ua.HealthCertificateReferenceNumber)) certRef = ua.HealthCertificateReferenceNumber;
                        if (!string.IsNullOrWhiteSpace(ua.ConsignorName)) consignorName = ua.ConsignorName;
                        if (!string.IsNullOrWhiteSpace(ua.ConsignorAddress)) consignorAddress = ua.ConsignorAddress;
                        if (!string.IsNullOrWhiteSpace(ua.ConsigneeName)) consigneeName = ua.ConsigneeName;
                        if (!string.IsNullOrWhiteSpace(ua.ConsigneeAddress)) consigneeAddress = ua.ConsigneeAddress;
                        if (!string.IsNullOrWhiteSpace(ua.DescriptionOfCommodity)) itemName = ua.DescriptionOfCommodity;
                        if (!string.IsNullOrWhiteSpace(ua.CountryDestinationName)) countryDest = ua.CountryDestinationName;
                        if (!string.IsNullOrWhiteSpace(ua.SignatoryName)) officerName = ua.SignatoryName;
                        if (!string.IsNullOrWhiteSpace(ua.Qualification)) qualification = ua.Qualification;
                        if (ua.CertifiedDate.HasValue) issueDate = ua.CertifiedDate.Value.ToString("dd/MM/yyyy");
                        createdAt = ua.CreatedAt.ToString("dd/MM/yyyy HH:mm:ss");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"[PublicVerifyCertificate] Ukraine check warning: {ex.Message}");
                }
            }

            // If found in CertificateRequests or any certificate table
            if (req != null || foundSpecific)
            {
                return Ok(new
                {
                    isVerified = true,
                    status,
                    certificateReference = certRef,
                    requestId = reqId,
                    issueDate,
                    consignorName,
                    consignorAddress,
                    consigneeName,
                    consigneeAddress,
                    itemName,
                    countryOfDestination = countryDest,
                    officerName,
                    designation,
                    qualification,
                    competentAuthority,
                    createdAt
                });
            }

            return Ok(new
            {
                isVerified = false,
                status = "NOT VERIFIED",
                message = "No authentic certificate record was found in the official database matching the provided reference."
            });
        }
    }

    public class ForgivingStringConverter : System.Text.Json.Serialization.JsonConverter<string>
    {
        public override string? Read(ref Utf8JsonReader reader, Type typeToConvert, JsonSerializerOptions options)
        {
            if (reader.TokenType == JsonTokenType.Number)
            {
                return reader.GetDouble().ToString();
            }
            if (reader.TokenType == JsonTokenType.True) return "true";
            if (reader.TokenType == JsonTokenType.False) return "false";
            
            return reader.GetString();
        }

        public override void Write(Utf8JsonWriter writer, string value, JsonSerializerOptions options)
        {
            writer.WriteStringValue(value);
        }
    }

    public class ForgivingBooleanConverter : System.Text.Json.Serialization.JsonConverter<bool?>
    {
        public override bool? Read(ref Utf8JsonReader reader, Type typeToConvert, JsonSerializerOptions options)
        {
            if (reader.TokenType == JsonTokenType.True) return true;
            if (reader.TokenType == JsonTokenType.False) return false;
            if (reader.TokenType == JsonTokenType.String)
            {
                var str = reader.GetString();
                if (bool.TryParse(str, out var b)) return b;
                if (str == "1") return true;
                if (str == "0") return false;
            }
            if (reader.TokenType == JsonTokenType.Number)
            {
                var val = reader.GetInt32();
                if (val == 1) return true;
                if (val == 0) return false;
            }
            return null;
        }

        public override void Write(Utf8JsonWriter writer, bool? value, JsonSerializerOptions options)
        {
            if (value.HasValue) writer.WriteBooleanValue(value.Value);
            else writer.WriteNullValue();
        }
    }
}

