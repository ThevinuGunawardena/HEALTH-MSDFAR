using System.Security.Claims;
using System.Security.Cryptography;
using System.Text.Json;
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
using MEA.Server.DTO.TwCertificate;
using MEA.Server.DTO.UaCertificate;
using MEA.Server.DTO.UsaCertificate;
using MEA.Server.Mappings;
using MEA.Server.Entities;
using MEA.Server.Data;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Endpoints;

public static class CertificateRequestEndpoints
{
    private sealed class VetAttachmentSecondaryNamePayload
    {
        public string? OriginalFileName { get; set; }
        public string? SecondaryFileName { get; set; }
    }

    public static IEndpointRouteBuilder MapCertificateRequestEndpoints(this IEndpointRouteBuilder app)
    {
        app.MapGet("/countries", GetCountries)
            .RequireAuthorization();

        app.MapPost("/certificate-requests", CreateCertificateRequest)
            .RequireAuthorization(policy => policy.RequireRole("Company", "Admin", "User"));

        app.MapGet("/certificate-requests", GetAllCertificateRequests)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapGet("/certificate-requests/my", GetMyCertificateRequests)
            .RequireAuthorization(policy => policy.RequireRole("Company", "Admin", "User"));

        app.MapGet("/certificate-requests/{id:int}/vet-form", GetVetCertificateFormByCertificateRequestId)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPut("/certificate-requests/{id:int}/status", UpdateCertificateStatus)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User"));

        app.MapPost("/vet-certificate-forms", CreateVetCertificateForm)
            .RequireAuthorization(policy => policy.RequireRole("Company", "Admin", "User"));

        app.MapPost("/au-certificates", CreateAuCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/am-certificates", CreateAmCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/br-certificates", CreateBrCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/ch-certificates", CreateChCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/hk-certificates", CreateHkCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/id-certificates", CreateIdCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/ind-certificates", CreateIndCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/jp-certificates", CreateJpCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/kw-certificates", CreateKwCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/my-certificates", CreateMyCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/nz-certificates", CreateNzCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/ru-certificates", CreateRuCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/tw-certificates", CreateTwCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/ua-certificates", CreateUaCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        app.MapPost("/usa-certificates", CreateUsaCertificate)
            .RequireAuthorization(policy => policy.RequireRole("Admin", "User", "Company"));

        return app;
    }

    private static async Task<IResult> GetCountries(AppDbContext dbContext)
    {
        var countries = await dbContext.Countries
            .AsNoTracking()
            .OrderBy(c => c.Name)
            .Select(c => new { c.Id, c.Name })
            .ToListAsync();

        return Results.Ok(countries);
    }

    private static async Task<IResult> GetAllCertificateRequests(AppDbContext dbContext)
    {
        var requests = await dbContext.CertificateRequests
            .AsNoTracking()
            .OrderByDescending(r => r.CreatedAt)
            .ToListAsync();
        var requestIds = requests.Select(r => r.Id).ToList();

        var submittedIds = new HashSet<int>();
        if (requestIds.Count > 0)
        {
            var vetSubmitted = await dbContext.VetCertificateForms.AsNoTracking().Where(v => v.CertificateRequestId.HasValue && requestIds.Contains(v.CertificateRequestId.Value)).Select(v => v.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(vetSubmitted);

            var amSubmitted = await dbContext.AmCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(amSubmitted);

            var auSubmitted = await dbContext.AuCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(auSubmitted);

            var brSubmitted = await dbContext.BrCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(brSubmitted);

            var chSubmitted = await dbContext.ChCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(chSubmitted);

            var hkSubmitted = await dbContext.HkCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(hkSubmitted);

            var idSubmitted = await dbContext.IdCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(idSubmitted);

            var indSubmitted = await dbContext.IndCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(indSubmitted);

            var jpSubmitted = await dbContext.JpCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(jpSubmitted);

            var kwSubmitted = await dbContext.KwCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(kwSubmitted);

            var kzSubmitted = await dbContext.KzCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(kzSubmitted);

            var mySubmitted = await dbContext.MyCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(mySubmitted);

            var nzSubmitted = await dbContext.NzCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(nzSubmitted);

            var ruSubmitted = await dbContext.RuCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(ruSubmitted);

            var twSubmitted = await dbContext.TwCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(twSubmitted);

            var uaSubmitted = await dbContext.UaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(uaSubmitted);

            var ukSubmitted = await dbContext.UkCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(ukSubmitted);

            var usaSubmitted = await dbContext.UsaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(usaSubmitted);

            var mvSubmitted = await dbContext.MvCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(mvSubmitted);

            var ilSubmitted = await dbContext.IlCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(ilSubmitted);

            var caSubmitted = await dbContext.CaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(caSubmitted);

            var saSubmitted = await dbContext.SaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(saSubmitted);

            var zaSubmitted = await dbContext.ZaCertificates.AsNoTracking().Where(c => c.CertificateRequestId.HasValue && requestIds.Contains(c.CertificateRequestId.Value)).Select(c => c.CertificateRequestId!.Value).ToListAsync();
            submittedIds.UnionWith(zaSubmitted);
        }

        // Filter: ONLY load requests where the company filled the form and pressed SUBMIT
        var filteredRequests = requests.Where(r => submittedIds.Contains(r.Id)).ToList();

        var result = filteredRequests.Select(r => new
        {
            r.Id,
            r.ReferenceNumber,
            r.CertificateType,
            r.CountryId,
            CountryName = r.CountryId.HasValue ? dbContext.Countries
                .Where(c => c.Id == r.CountryId.Value)
                .Select(c => c.Name)
                .FirstOrDefault() : (r.CertificateType == CertificateType.EU ? "European Union" : null),
            r.Status,
            r.CreatedAt,
            CompanyName = dbContext.Companies
                .Where(c => c.UserId == r.CompanyUserId)
                .Select(c => c.CompanyName)
                .FirstOrDefault() ?? dbContext.Users
                .Where(u => u.Id == r.CompanyUserId)
                .Select(u => dbContext.Companies
                    .Where(c => c.Id == u.CompanyId)
                    .Select(c => c.CompanyName)
                    .FirstOrDefault())
                .FirstOrDefault() ?? dbContext.Users
                .Where(u => u.Id == r.CompanyUserId)
                .Select(u => dbContext.Companies
                    .Where(c => c.CompanyEmail == u.Email)
                    .Select(c => c.CompanyName)
                    .FirstOrDefault())
                .FirstOrDefault() ?? dbContext.Users
                    .Where(u => u.Id == r.CompanyUserId && u.FullName != "Admin User" && u.FullName != "Regular User")
                    .Select(u => u.FullName)
                    .FirstOrDefault() ?? "Company User",
            HasFormSubmitted = submittedIds.Contains(r.Id)
        }).ToList();

        return Results.Ok(result);
    }

    private static async Task<IResult> GetMyCertificateRequests(ClaimsPrincipal user, AppDbContext dbContext)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var requests = await dbContext.CertificateRequests
            .AsNoTracking()
            .Where(r => r.CompanyUserId == userId)
            .OrderByDescending(r => r.CreatedAt)
            .Select(r => new
            {
                r.Id,
                r.ReferenceNumber,
                r.CertificateType,
                r.CountryId,
                r.Status,
                r.CreatedAt
            })
            .ToListAsync();

        return Results.Ok(requests);
    }

    private static async Task<IResult> GetVetCertificateFormByCertificateRequestId(int id, AppDbContext dbContext)
    {
        var vetForm = await dbContext.VetCertificateForms
            .AsNoTracking()
            .Where(v => v.CertificateRequestId == id)
            .Select(v => new
            {
                v.Id,
                v.CertificateRequestId,
                v.ConsignorName,
                v.ConsignorAddress,
                v.ConsignorPostal,
                v.ConsignorTel,
                v.ConsigneeName,
                v.ConsigneeAddress,
                v.ConsigneePostal,
                v.ConsigneeTel,
                v.CountryOriginISO,
                v.RegionOriginISO,
                v.CountryDestinationISO,
                v.PlaceOfLoading,
                v.DateOfDeparture,
                v.TransportAeroPlane,
                v.TransportShip,
                v.TransportRailwayWagon,
                v.TransportRoadVehicle,
                v.TransportOther,
                v.DocReferences,
                v.EntryBIP,
                v.DescCommon,
                v.HsCode,
                v.Quantity,
                v.LandingSite,
                UploadedCertificateFile = (v.UploadedCertificateFile != null && v.UploadedCertificateFile.Length > 0)
                    || v.Attachments.Any(),
                UploadedCertificateFiles = v.Attachments
                    .OrderBy(a => a.FileOrder)
                    .Select(a => new
                    {
                        a.Id,
                        a.FileOrder,
                        a.OriginalFileName,
                        a.SecondaryFileName,
                        a.ContentType
                    }),
                v.DescScientific,
                v.NumPackages,
                v.PackagingType,
                v.TemperatureAmbient,
                v.TemperatureChilled,
                v.TemperatureFrozen,
                v.ForHumanConsumption,
                Temperature = v.TemperatureAmbient == true ? "Ambient"
                    : v.TemperatureChilled == true ? "Chilled"
                    : v.TemperatureFrozen == true ? "Frozen"
                    : null,
                Nature = v.NatureAquaculture == true ? "Aquaculture"
                    : v.NatureWildOrigin == true ? "Wild origin"
                    : null,
                Treatment = v.TreatmentChilled == true ? "Chilled"
                    : v.TreatmentFrozen == true ? "Frozen"
                    : v.TreatmentLive == true ? "Live"
                    : null,
                v.NetWeight,
                v.CountryOrigin,
                v.ProcessingType,
                v.ForImportEU,
                v.ProcessingEstName,
                v.ProcessingEstAddress,
                v.ApprovalNo,
                v.TransportId,
                v.ContainerId,
                v.ProcessingDate
            })
            .FirstOrDefaultAsync();

        if (vetForm is null)
            return Results.NotFound(new { Message = "Vet certificate form not found for this certificate request." });

        return Results.Ok(vetForm);
    }

    private static async Task<IResult> CreateCertificateRequest(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateCertificateRequestDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        if (!Enum.TryParse<CertificateType>(dto.CertificateType, true, out var certificateType))
            return Results.BadRequest(new { Message = "Invalid certificate type. Use EU or NonEU." });

        if (certificateType == CertificateType.NonEU && !dto.CountryId.HasValue)
            return Results.BadRequest(new { Message = "Country is required for NonEU certificate requests." });

        if (certificateType == CertificateType.EU)
            dto.CountryId = null;

        if (dto.CountryId.HasValue)
        {
            var countryExists = await dbContext.Countries
                .AsNoTracking()
                .AnyAsync(c => c.Id == dto.CountryId.Value);

            if (!countryExists)
                return Results.BadRequest(new { Message = "Selected country does not exist." });
        }

        var referenceNumber = (!string.IsNullOrWhiteSpace(dto.ReferenceNumber) && dto.ReferenceNumber.Trim().StartsWith("HC-", StringComparison.OrdinalIgnoreCase))
            ? dto.ReferenceNumber.Trim()
            : await GenerateUniqueReferenceNumber(dbContext, certificateType, dto.CountryId);

        var request = new CertificateRequest
        {
            ReferenceNumber = referenceNumber,
            CertificateType = certificateType,
            CountryId       = dto.CountryId,
            CompanyUserId   = userId,
            Status          = CertificateStatus.Pending,
            CreatedAt       = DateTime.UtcNow
        };

        dbContext.CertificateRequests.Add(request);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new
        {
            request.Id,
            request.ReferenceNumber,
            request.CertificateType,
            request.CountryId,
            request.Status,
            request.CreatedAt
        });
    }

    private static async Task<IResult> UpdateCertificateStatus(
        int id,
        AppDbContext dbContext,
        [FromBody] UpdateCertificateStatusDto dto)
    {
        if (!Enum.TryParse<CertificateStatus>(dto.Status, true, out var status))
            return Results.BadRequest(new { Message = "Invalid status. Use Confirmed or Rejected." });

        if (status == CertificateStatus.Pending)
            return Results.BadRequest(new { Message = "Status can only be updated to Confirmed or Rejected." });

        var request = await dbContext.CertificateRequests.FindAsync(id);
        if (request is null)
            return Results.NotFound(new { Message = "Certificate request not found." });

        request.Status = status;
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { request.Id, request.ReferenceNumber, request.Status });
    }

    private static async Task<IResult> CreateVetCertificateForm(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        HttpRequest request)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        if (!request.HasFormContentType)
            return Results.BadRequest(new { Message = "Request must be multipart/form-data." });

        var form = await request.ReadFormAsync();

        int? certRequestId = null;
        if (form.TryGetValue("certificateRequestId", out var certIdValue) &&
            int.TryParse(certIdValue.ToString(), out var parsedId))
        {
            certRequestId = parsedId;

            var belongsToUser = await dbContext.CertificateRequests
                .AsNoTracking()
                .AnyAsync(r => r.Id == certRequestId.Value && r.CompanyUserId == userId);

            if (!belongsToUser)
                return Results.BadRequest(new { Message = "Certificate request not found for this user." });
        }

        var entity = new VetCertificateForm
        {
            CertificateRequestId    = certRequestId,
            CompanyUserId           = userId,
            CreatedAt               = DateTime.UtcNow,
            OldHC                   = GetFormValue(form, "oldHC"),
            NewHC                   = GetFormValue(form, "newHC"),
            LandingSite             = GetFormValue(form, "landingSite"),
            BoatRegistration        = GetFormValue(form, "boatRegistration"),
            BoatNumber              = GetFormValue(form, "boatNumber"),
            SupplierNameAddress     = GetFormValue(form, "supplierNameAddress"),
            ArrivalAtFactory        = ParseDateTime(form, "arrivalAtFactory"),
            ProcessingDate          = ParseDateTime(form, "processingDate"),
            FarmLocation            = GetFormValue(form, "farmLocation"),
            FarmOwnerName           = GetFormValue(form, "farmOwnerName"),
            FarmOwnerAddress        = GetFormValue(form, "farmOwnerAddress"),
            HarvestDate             = ParseDateTime(form, "harvestDate"),
            ArrivalTimeProduct      = ParseDateTime(form, "arrivalTimeProduct"),
            ProcessingDates         = GetFormValue(form, "processingDates"),
            AquaSupplier            = GetFormValue(form, "aquaSupplier"),
            CountryOrigin           = GetFormValue(form, "countryOrigin"),
            ArrivalConsignment      = ParseDateTime(form, "arrivalConsignment"),
            HealthCertNo            = GetFormValue(form, "healthCertNo"),
            ProductTypeAquaculture  = ParseBool(form, "productTypeAquaculture"),
            ProductTypeWildCaught   = ParseBool(form, "productTypeWildCaught"),
            ConsignorName           = GetFormValue(form, "consignorName"),
            ConsignorAddress        = GetFormValue(form, "consignorAddress"),
            ConsignorPostal         = GetFormValue(form, "consignorPostal"),
            ConsignorTel            = GetFormValue(form, "consignorTel"),
            ConsigneeName           = GetFormValue(form, "consigneeName"),
            ConsigneeAddress        = GetFormValue(form, "consigneeAddress"),
            ConsigneePostal         = GetFormValue(form, "consigneePostal"),
            ConsigneeTel            = GetFormValue(form, "consigneeTel"),
            CountryOriginISO        = GetFormValue(form, "countryOriginISO"),
            RegionOriginISO         = GetFormValue(form, "regionOriginISO"),
            CountryDestinationISO   = GetFormValue(form, "countryDestinationISO"),
            ProcessingEstName       = GetFormValue(form, "processingEstName"),
            ProcessingEstAddress    = GetFormValue(form, "processingEstAddress"),
            ApprovalNo              = GetFormValue(form, "approvalNo"),
            PlaceOfLoading          = GetFormValue(form, "placeOfLoading"),
            DateOfDeparture         = ParseDateTime(form, "dateOfDeparture"),
            TransportAeroPlane      = ParseBool(form, "transportAeroPlane"),
            TransportShip           = ParseBool(form, "transportShip"),
            TransportRailwayWagon   = ParseBool(form, "transportRailwayWagon"),
            TransportRoadVehicle    = ParseBool(form, "transportRoadVehicle"),
            TransportOther          = ParseBool(form, "transportOther"),
            TransportId             = GetFormValue(form, "transportId"),
            DocReferences           = GetFormValue(form, "docReferences"),
            EntryBIP                = GetFormValue(form, "entryBIP"),
            DescCommon              = GetFormValue(form, "descCommon"),
            DescScientific          = GetFormValue(form, "descScientific"),
            ProcessingType          = GetFormValue(form, "processingType"),
            HsCode                  = GetFormValue(form, "hsCode"),
            TemperatureAmbient      = ParseBool(form, "temperatureAmbient"),
            TemperatureChilled      = ParseBool(form, "temperatureChilled"),
            TemperatureFrozen       = ParseBool(form, "temperatureFrozen"),
            Quantity                = GetFormValue(form, "quantity"),
            NumPackages             = GetFormValue(form, "numPackages"),
            PackagingType           = GetFormValue(form, "packagingType"),
            ContainerId             = GetFormValue(form, "containerId"),
            ForHumanConsumption     = ParseBool(form, "forHumanConsumption"),
            ForImportEU             = GetFormValue(form, "forImportEU"),
            NatureAquaculture       = ParseBool(form, "natureAquaculture"),
            NatureWildOrigin        = ParseBool(form, "natureWildOrigin"),
            TreatmentChilled        = ParseBool(form, "treatmentChilled"),
            TreatmentFrozen         = ParseBool(form, "treatmentFrozen"),
            TreatmentLive           = ParseBool(form, "treatmentLive"),
            NetWeight               = GetFormValue(form, "netWeight"),
            PaymentSlipFile         = await ReadFileBytes(form.Files["paymentSlipFile"]),
            SignatureDate           = ParseDateTime(form, "signatureDate"),
            SignatureTime           = ParseDateTime(form, "signatureTime"),
            Signature               = GetFormValue(form, "signature"),
            SignatoryName           = GetFormValue(form, "signatoryName"),
            Designation             = GetFormValue(form, "designation")
        };

        var attachments = await ParseVetAttachments(form);
        entity.Attachments = attachments;
        entity.UploadedCertificateFile = attachments.FirstOrDefault()?.FileContent;

        dbContext.VetCertificateForms.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
    }

    private static async Task<IResult> CreateAuCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateAuCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.AuCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
    }

    private static async Task<IResult> CreateAmCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateAmCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.AmCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.CertificateRequestId, entity.CreatedAt });
    }

    private static async Task<IResult> CreateBrCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateBrCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.BrCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.RefNumber, entity.DateOfIssue });
    }

    private static async Task<IResult> CreateChCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateChCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.ChCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.DateOfIssue });
    }

    private static async Task<IResult> CreateHkCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateHkCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.HkCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.DateOfIssue });
    }

    private static async Task<IResult> CreateIdCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateIdCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.IdCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.CertifiedDate });
    }

    private static async Task<IResult> CreateIndCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateIndCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.IndCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id, entity.AttestationDate });
    }

    private static async Task<IResult> CreateJpCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateJpCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.JpCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }

    private static async Task<IResult> CreateKwCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateKwCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.KwCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }

    private static async Task<IResult> CreateMyCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateMyCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.MyCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }

    private static async Task<IResult> CreateNzCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateNzCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.NzCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }

    private static async Task<IResult> CreateRuCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateRuCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.RuCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }

    private static async Task<IResult> CreateTwCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateTwCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.TwCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }

    private static async Task<IResult> CreateUaCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateUaCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.UaCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }

    private static async Task<IResult> CreateUsaCertificate(
        ClaimsPrincipal user,
        AppDbContext dbContext,
        [FromBody] CreateUsaCertificateDto dto)
    {
        var userId = GetUserId(user);
        if (userId is null) return Results.Unauthorized();

        var targetCompanyUserId = await ResolveCompanyUserId(dbContext, userId, dto.CertificateRequestId);
        if (targetCompanyUserId is null)
            return Results.BadRequest(new { Message = "Certificate request not found." });

        var entity = dto.ToEntity(targetCompanyUserId);
        dbContext.UsaCertificates.Add(entity);
        await dbContext.SaveChangesAsync();

        return Results.Ok(new { entity.Id });
    }


    private static string? GetUserId(ClaimsPrincipal user) =>
        user.FindFirstValue("UserID")
        ?? user.FindFirstValue("UserId")
        ?? user.FindFirstValue(ClaimTypes.NameIdentifier);

    private static async Task<string?> ResolveCompanyUserId(
        AppDbContext dbContext, string fallbackUserId, int? certificateRequestId)
    {
        if (!certificateRequestId.HasValue) return fallbackUserId;

        var request = await dbContext.CertificateRequests
            .AsNoTracking()
            .FirstOrDefaultAsync(r => r.Id == certificateRequestId.Value);

        return request?.CompanyUserId;
    }

    private static async Task<string> GenerateUniqueReferenceNumber(AppDbContext dbContext, CertificateType type, int? countryId = null)
    {
        string code = "GEN";

        if (type == CertificateType.EU)
        {
            code = "EU";
        }
        else if (countryId.HasValue)
        {
            var country = await dbContext.Countries.FindAsync(countryId.Value);
            if (country != null && !string.IsNullOrWhiteSpace(country.Name))
            {
                code = MEA.Server.Repositories.CertificateRequestRepository.GetCountryCode(country.Name);
            }
            else
            {
                code = "NON-EU";
            }
        }
        else
        {
            code = "NON-EU";
        }

        int currentYear = DateTime.UtcNow.Year;
        string prefix = $"HC-{currentYear}-{code}-";

        var existingRefs = await dbContext.CertificateRequests
            .AsNoTracking()
            .Where(r => r.ReferenceNumber != null && r.ReferenceNumber.StartsWith(prefix))
            .Select(r => r.ReferenceNumber)
            .ToListAsync();

        int maxSeq = 0;
        foreach (var r in existingRefs)
        {
            if (r != null && r.Length > prefix.Length)
            {
                var suffix = r.Substring(prefix.Length);
                if (int.TryParse(suffix, out int parsedSeq))
                {
                    if (parsedSeq > maxSeq) maxSeq = parsedSeq;
                }
            }
        }

        int nextSeq = maxSeq + 1;
        string referenceNumber = $"{prefix}{nextSeq:D3}";

        return referenceNumber;
    }

    private static string IncrementLetters(string letters)
    {
        char[] chars = letters.ToCharArray();
        for (int i = chars.Length - 1; i >= 0; i--)
        {
            if (chars[i] < 'Z')
            {
                chars[i]++;
                return new string(chars);
            }
            else
            {
                chars[i] = 'A';
            }
        }
        return "A" + new string(chars);
    }

    private static async Task<byte[]?> ReadFileBytes(IFormFile? file)
    {
        if (file is null || file.Length == 0) return null;

        await using var memoryStream = new MemoryStream();
        await file.CopyToAsync(memoryStream);
        return memoryStream.ToArray();
    }

    private static string? GetFormValue(IFormCollection form, string key) =>
        form.TryGetValue(key, out var value) && !string.IsNullOrWhiteSpace(value.ToString())
            ? value.ToString()
            : null;

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
}

