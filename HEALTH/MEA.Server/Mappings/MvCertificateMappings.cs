using MEA.Server.DTO.MvCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class MvCertificateMappings
{
    public static MvCertificate ToEntity(this MvCertificateDto dto, string companyUserId)
    {
        return new MvCertificate
        {
            CompanyUserId = companyUserId,
            CertificateRequestId = dto.CertificateRequestId,
            CreatedAt = DateTime.UtcNow,

            ConsignorExporter = dto.ConsignorExporter,
            CertificateNumber = dto.CertificateNumber,
            CompetentAuthority = dto.CompetentAuthority ?? "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
            CertifyingBody = dto.CertifyingBody ?? "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
            ConsigneeImporter = dto.ConsigneeImporter,
            CountryOfOrigin = dto.CountryOfOrigin ?? "SRI LANKA",
            CountryOfOriginISO = dto.CountryOfOriginISO ?? "LK",
            CountryOfDestination = dto.CountryOfDestination ?? "MALDIVES",
            CountryOfDestinationISO = dto.CountryOfDestinationISO ?? "MV",
            PlaceOfLoading = dto.PlaceOfLoading,
            
            TransportAeroPlane = dto.TransportAeroPlane,
            TransportShip = dto.TransportShip,
            TransportRailway = dto.TransportRailway,
            TransportRoad = dto.TransportRoad,
            TransportOther = dto.TransportOther,
            
            PointsOfEntry = dto.PointsOfEntry,
            ConditionsOfStorage = dto.ConditionsOfStorage,
            TotalQuantity = dto.TotalQuantity,
            SealNumber = dto.SealNumber,
            TotalNumberOfPackages = dto.TotalNumberOfPackages,
            ApprovalNumberOfEstablishments = dto.ApprovalNumberOfEstablishments,
            DescriptionOfCommodity = dto.DescriptionOfCommodity,
            
            CertifyingOfficerName = dto.CertifyingOfficerName,
            CertifyingOfficerDate = dto.CertifyingOfficerDate,

            SignatoryUserId = string.IsNullOrWhiteSpace(dto.SignatoryUserId) ? null : dto.SignatoryUserId,
            SignatoryName = dto.SignatoryName ?? string.Empty,
            Qualification = dto.Qualification ?? string.Empty,
            CompanyRegistrationNo = dto.CompanyRegistrationNo ?? string.Empty,
            OfficialStamp = dto.OfficialStamp,
            OfficialSignature = dto.OfficialSignature,
            CertificateType = dto.CertificateType ?? "generic",

            Products = dto.Products.Select(p => p.ToEntity()).ToList(),
            ProductsSecond = dto.ProductsSecond.Select(p => p.ToEntity()).ToList(),
            ProductsAttachment = dto.ProductsAttachment.Select(p => p.ToEntity()).ToList()
        };
    }

    public static MvCertificateProduct ToEntity(this MvCertificateProductDto dto)
    {
        return new MvCertificateProduct
        {
            No = dto.No,
            NatureOfCommodity = dto.NatureOfCommodity,
            Species = dto.Species,
            PurposeOfUse = dto.PurposeOfUse
        };
    }

    public static MvCertificateProductSecond ToEntity(this MvCertificateProductSecondDto dto)
    {
        return new MvCertificateProductSecond
        {
            No = dto.No,
            NameOfTheProduct = dto.NameOfTheProduct,
            LotIdentifier = dto.LotIdentifier,
            TypeOfPackaging = dto.TypeOfPackaging,
            NumberOfPackages = dto.NumberOfPackages,
            NetWeight = dto.NetWeight
        };
    }

    public static MvCertificateProductAttachment ToEntity(this MvCertificateProductAttachmentDto dto)
    {
        return new MvCertificateProductAttachment
        {
            Product = dto.Product,
            LotIdentifier = dto.LotIdentifier,
            TypeOfPackaging = dto.TypeOfPackaging,
            NumberOfKgs = dto.NumberOfKgs,
            NumberOfBoxes = dto.NumberOfBoxes
        };
    }

    public static MvCertificateDto ToDto(this MvCertificate entity)
    {
        return new MvCertificateDto
        {
            Id = entity.Id,
            CertificateRequestId = entity.CertificateRequestId,

            ConsignorExporter = entity.ConsignorExporter,
            CertificateNumber = entity.CertificateNumber,
            CompetentAuthority = entity.CompetentAuthority,
            CertifyingBody = entity.CertifyingBody,
            ConsigneeImporter = entity.ConsigneeImporter,
            CountryOfOrigin = entity.CountryOfOrigin,
            CountryOfOriginISO = entity.CountryOfOriginISO,
            CountryOfDestination = entity.CountryOfDestination,
            CountryOfDestinationISO = entity.CountryOfDestinationISO,
            PlaceOfLoading = entity.PlaceOfLoading,
            
            TransportAeroPlane = entity.TransportAeroPlane,
            TransportShip = entity.TransportShip,
            TransportRailway = entity.TransportRailway,
            TransportRoad = entity.TransportRoad,
            TransportOther = entity.TransportOther,
            
            PointsOfEntry = entity.PointsOfEntry,
            ConditionsOfStorage = entity.ConditionsOfStorage,
            TotalQuantity = entity.TotalQuantity,
            SealNumber = entity.SealNumber,
            TotalNumberOfPackages = entity.TotalNumberOfPackages,
            ApprovalNumberOfEstablishments = entity.ApprovalNumberOfEstablishments,
            DescriptionOfCommodity = entity.DescriptionOfCommodity,
            
            CertifyingOfficerName = entity.CertifyingOfficerName,
            CertifyingOfficerDate = entity.CertifyingOfficerDate,

            SignatoryUserId = entity.SignatoryUserId,
            SignatoryName = entity.SignatoryName,
            Qualification = entity.Qualification,
            CompanyRegistrationNo = entity.CompanyRegistrationNo,
            OfficialStamp = entity.OfficialStamp,
            OfficialSignature = entity.OfficialSignature,
            CertificateType = entity.CertificateType,

            Products = entity.Products.Select(p => new MvCertificateProductDto().ToDto(p)).ToList(),
            ProductsSecond = entity.ProductsSecond.Select(p => new MvCertificateProductSecondDto().ToDto(p)).ToList(),
            ProductsAttachment = entity.ProductsAttachment.Select(p => new MvCertificateProductAttachmentDto().ToDto(p)).ToList()
        };
    }
}
