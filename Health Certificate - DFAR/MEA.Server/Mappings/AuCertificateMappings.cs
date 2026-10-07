using MEA.Server.DTO.AuCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class AuCertificateMappings
{
    public static AuCertificate ToEntity(this CreateAuCertificateDto dto, string companyUserId) => new()
    {
        CertificateRequestId            = dto.CertificateRequestId,
        CompanyUserId                   = companyUserId,
        CreatedAt                       = DateTime.UtcNow,
        ConsignorName                   = dto.ConsignorName,
        ConsignorAddress                = dto.ConsignorAddress,
        ConsignorPostal                 = dto.ConsignorPostal,
        ConsignorTel                    = dto.ConsignorTel,
        CertRefNumber                   = dto.CertRefNumber,
        CertRefNumberA                  = dto.CertRefNumberA,
        CentralCompetentAuthority       = dto.CentralCompetentAuthority,
        LocalCompetentAuthority         = dto.LocalCompetentAuthority,
        ConsigneeName                   = dto.ConsigneeName,
        ConsigneeAddress                = dto.ConsigneeAddress,
        ConsigneePostal                 = dto.ConsigneePostal,
        ConsigneeTel                    = dto.ConsigneeTel,
        Consignee6                      = dto.Consignee6,
        CountryOrigin                   = dto.CountryOrigin,
        CountryOriginISO                = dto.CountryOriginISO,
        RegionOrigin                    = dto.RegionOrigin,
        RegionOriginISO                 = dto.RegionOriginISO,
        CountryDestination              = dto.CountryDestination,
        CountryDestinationISO           = dto.CountryDestinationISO,
        CountryDestination110           = dto.CountryDestination110,
        PlaceOfOriginName               = dto.PlaceOfOriginName,
        PlaceOfOriginAddress            = dto.PlaceOfOriginAddress,
        PlaceOfOriginApprovalNo         = dto.PlaceOfOriginApprovalNo,
        CountryDestination112           = dto.CountryDestination112,
        PlaceOfLoading                  = dto.PlaceOfLoading,
        DateOfDeparture                 = dto.DateOfDeparture,
        TransportAeroPlane              = dto.TransportAeroPlane,
        TransportShip                   = dto.TransportShip,
        TransportRailwayWagon           = dto.TransportRailwayWagon,
        TransportRoadVehicle            = dto.TransportRoadVehicle,
        TransportOther                  = dto.TransportOther,
        DocReferences                   = dto.DocReferences,
        EntryBIP                        = dto.EntryBIP,
        Field117                        = dto.Field117,
        DescCommon                      = dto.DescCommon,
        HsCode                          = dto.HsCode,
        Quantity                        = dto.Quantity,
        TemperatureAmbient              = dto.TemperatureAmbient,
        TemperatureChilled              = dto.TemperatureChilled,
        TemperatureFrozen               = dto.TemperatureFrozen,
        NumPackages                     = dto.NumPackages,
        ContainerId                     = dto.ContainerId,
        PackagingType                   = dto.PackagingType,
        ForHumanConsumption             = dto.ForHumanConsumption,
        Field126                        = dto.Field126,
        ForImportEU                     = dto.ForImportEU,
        HealthCertNo                    = dto.HealthCertNo,
        HealthCertNoB                   = dto.HealthCertNoB,
        ExportApprovalNumber            = dto.ExportApprovalNumber,
        SignatoryUserId                 = dto.SignatoryUserId,
        SignatoryName                   = dto.SignatoryName,
        Qualification                   = dto.Qualification,
        SignatureDate                   = dto.SignatureDate,
        Stamp                           = dto.Stamp,
        Signature                       = dto.Signature,
        CertificateType                 = dto.CertificateType,
        Products = dto.Products.Select(p => new AuCertificateProduct
        {
            SpeciesScientificName           = p.SpeciesScientificName,
            NatureOfCommodity               = p.NatureOfCommodity,
            TreatmentType                   = p.TreatmentType,
            ApprovalNumberOfEstablishments  = p.ApprovalNumberOfEstablishments,
            ManufacturingPlant              = p.ManufacturingPlant,
            NumberOfPackages                = p.NumberOfPackages,
            NetWeight                       = p.NetWeight
        }).ToList()
    };
}

