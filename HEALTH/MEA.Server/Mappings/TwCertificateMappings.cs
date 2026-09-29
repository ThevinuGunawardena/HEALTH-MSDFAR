using MEA.Server.DTO.TwCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class TwCertificateMappings
{
    public static TwCertificate ToEntity(this CreateTwCertificateDto dto, string companyUserId)
    {
        return new TwCertificate
        {
            CompanyUserId = companyUserId,
            CertificateRequestId = dto.CertificateRequestId,
            CreatedAt = DateTime.UtcNow,
            ReferenceNo = dto.ReferenceNo,
            CountryOfExport = dto.CountryOfExport,
            CountryOfProduction = dto.CountryOfProduction,
            CompetentAuthority = dto.CompetentAuthority,
            DepartmentIssuance = dto.DepartmentIssuance,

            ProductionPlace = dto.ProductionPlace,
            ProcessingType = dto.ProcessingType,
            ProductionMode = dto.ProductionMode,
            AquaculturedYes = dto.AquaculturedYes,
            AquaculturedNo = dto.AquaculturedNo,
            WildCaughtYes = dto.WildCaughtYes,
            WildCaughtNo = dto.WildCaughtNo,
            AquacultureArea = dto.AquacultureArea,
            CatchArea = dto.CatchArea,
            HarvestingArea = dto.HarvestingArea,
            VesselName = dto.VesselName,
            EnterpriseName = dto.EnterpriseName,
            EnterpriseRegistrationNo = dto.EnterpriseRegistrationNo,
            ProductionDate = dto.ProductionDate,

            ConsignorName = dto.ConsignorName,
            ConsignorAddress = dto.ConsignorAddress,
            ConsigneeName = dto.ConsigneeName,
            ConsigneeAddress = dto.ConsigneeAddress,
            PlaceOfDispatch = dto.PlaceOfDispatch,
            PlaceOfDestination = dto.PlaceOfDestination,
            MeansOfTransport = dto.MeansOfTransport,
            VesselNameTransport = dto.VesselNameTransport,
            FlightNumber = dto.FlightNumber,
            OtherTransportMeans = dto.OtherTransportMeans,
            ContainerNumber = dto.ContainerNumber,
            SealNumber = dto.SealNumber,

            PlaceOfIssue = dto.PlaceOfIssue,
            DateOfIssue = dto.DateOfIssue,
            OfficialStamp = dto.OfficialStamp,
            OfficialSignature = dto.OfficialSignature,
            SignatoryUserId = dto.SignatoryUserId,
            SignatoryName = dto.SignatoryName,
            Qualification = dto.Qualification,
            CertificateType = dto.CertificateType ?? "single",

            Products = dto.Products.Select(p => p.ToEntity()).ToList()
        };
    }

    public static TwCertificateProduct ToEntity(this CreateTwCertificateProductDto dto)
    {
        return new TwCertificateProduct
        {
            CommodityName = dto.CommodityName,
            HsCode = dto.HsCode,
            ScientificName = dto.ScientificName,
            NumberOfPackages = dto.NumberOfPackages,
            NetWeight = dto.NetWeight
        };
    }
}

