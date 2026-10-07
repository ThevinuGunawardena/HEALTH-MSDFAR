using MEA.Server.DTO.BrCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class BrCertificateMappings
{
    public static BrCertificate ToEntity(this CreateBrCertificateDto dto, string companyUserId) => new()
    {
        CertificateRequestId            = dto.CertificateRequestId,
        CompanyUserId                   = companyUserId,
        CreatedAt                       = DateTime.UtcNow,
        RefNumber                       = dto.RefNumber,
        CountryOfExport                 = dto.CountryOfExport,
        CertificateNo                   = dto.CertificateNo,
        CompetentAuthority              = dto.CompetentAuthority,
        LocalCompetentAuthority         = dto.LocalCompetentAuthority,
        ExporterName                    = dto.ExporterName,
        ExporterAddress                 = dto.ExporterAddress,
        ImporterName                    = dto.ImporterName,
        ImporterAddress                 = dto.ImporterAddress,
        CountryOrigin                   = dto.CountryOrigin,
        CountryOriginISO                = dto.CountryOriginISO,
        CountryOfDestination            = dto.CountryOfDestination,
        CountryDestinationISO           = dto.CountryDestinationISO,
        PlaceOfLoading                  = dto.PlaceOfLoading,
        TransportAeroPlane              = dto.TransportAeroPlane ?? false,
        TransportShip                   = dto.TransportShip ?? false,
        TransportRailwayWagon           = dto.TransportRailwayWagon ?? false,
        TransportRoadVehicle            = dto.TransportRoadVehicle ?? false,
        TransportOther                  = dto.TransportOther ?? false,
        DeclaredPointOfEntry            = dto.DeclaredPointOfEntry,
        ConditionsForTransportStorage   = dto.ConditionsForTransportStorage,
        IdentificationOfContainers      = dto.IdentificationOfContainers,
        IdentificationOfFoodProducts    = dto.IdentificationOfFoodProducts,
        ProducerDetails                 = dto.ProducerDetails,
        HsCode                          = dto.HsCode,
        IntendedPurpose                 = dto.IntendedPurpose,
        TotalNetWeight                  = dto.TotalNetWeight ?? 0,
        PlaceAndDate                    = dto.PlaceAndDate,
        DateOfIssue                     = dto.DateOfIssue ?? DateTime.UtcNow,
        OfficialStamp                   = dto.OfficialStamp,
        SignatoryUserId                 = string.IsNullOrWhiteSpace(dto.SignatoryUserId) ? null : dto.SignatoryUserId,
        SignatoryName                   = dto.SignatoryName,
        Qualification                   = dto.Qualification,
        ModeloConformeCircularNo        = dto.ModeloConformeCircularNo,
        SanitaryCertification           = dto.SanitaryCertification,
        Products = dto.Products.Select(p => new BrCertificateProduct
        {
            NameOfTheProduct = p.NameOfTheProduct,
            ScientificName   = p.ScientificName,
            TypeOfPackaging  = p.TypeOfPackaging,
            NumberOfPackages = p.NumberOfPackages ?? 0,
            NetWeight        = p.NetWeight ?? 0
        }).ToList()
    };
}

