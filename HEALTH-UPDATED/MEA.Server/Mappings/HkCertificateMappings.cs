using MEA.Server.DTO.HkCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class HkCertificateMappings
{
    public static HkCertificate ToEntity(this CreateHkCertificateDto dto, string companyUserId) => new()
    {
        CertificateRequestId        = dto.CertificateRequestId,
        CompanyUserId               = companyUserId,
        CreatedAt                   = DateTime.UtcNow,
        CertificateType             = dto.CertificateType ?? "attachment",
        IdentificationNumber        = dto.IdentificationNumber,
        CountryOfDispatch           = dto.CountryOfDispatch,
        CompetentAuthority          = dto.CompetentAuthority,
        CertifyingBody              = dto.CertifyingBody,
        ContainerNumber             = dto.ContainerNumber,
        SealNumber                  = dto.SealNumber,
        SealIdentificationNumber    = dto.SealIdentificationNumber,
        StorageTemperature          = dto.StorageTemperature,
        ApprovalNumber              = dto.ApprovalNumber,
        ProcessingEstablishment     = dto.ProcessingEstablishment,
        ProvenanceDetails           = dto.ProvenanceDetails,
        ConsignorName               = dto.ConsignorName,
        ConsignorAddress            = dto.ConsignorAddress,
        PlaceOfDispatch             = dto.PlaceOfDispatch,
        DestinationCountryPlace     = dto.DestinationCountryPlace,
        MeansOfTransport            = dto.MeansOfTransport,
        ConsigneeName               = dto.ConsigneeName,
        ConsigneeAddress            = dto.ConsigneeAddress,
        DateOfAttachment            = dto.DateOfAttachment,
        AttachmentRegNo             = dto.AttachmentRegNo,
        PlaceOfIssue                = dto.PlaceOfIssue,
        DateOfIssue                 = dto.DateOfIssue,
        SignatoryUserId             = dto.SignatoryUserId,
        SignatoryName               = dto.SignatoryName,
        Qualification               = dto.Qualification,
        OfficialSignature           = dto.OfficialSignature,
        OfficerTel                  = dto.OfficerTel,
        OfficerFax                  = dto.OfficerFax,
        OfficerEmail                = dto.OfficerEmail,
        Products = dto.Products.Select(p => new HkCertificateProduct
        {
            Description      = p.Description,
            Species          = p.Species,
            ProcessingType   = p.ProcessingType,
            PackagingType    = p.PackagingType,
            LotCode          = p.LotCode,
            NumberOfPackages = p.NumberOfPackages,
            PackagesUnit     = p.PackagesUnit,
            NetWeight        = p.NetWeight,
            NetWeightUnit    = p.NetWeightUnit
        }).ToList()
    };
}

