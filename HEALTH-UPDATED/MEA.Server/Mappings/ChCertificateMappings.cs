using System.Linq;
using MEA.Server.DTO.ChCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class ChCertificateMappings
{
    public static ChCertificate ToEntity(this CreateChCertificateDto dto, string companyUserId) => new()
    {
        CertificateRequestId                = dto.CertificateRequestId,
        CompanyUserId                       = companyUserId,
        CreatedAt                           = DateTime.UtcNow,
        CertificateType                     = dto.CertificateType ?? "attachment",
        RefNumber                           = dto.RefNumber,

        CountryOfExport                     = dto.CountryOfExport,
        CountryOfProduction                 = dto.CountryOfProduction,
        CompetentAuthority                  = dto.CompetentAuthority,
        DepartmentOfIssuance                = dto.DepartmentOfIssuance,

        CommodityName                       = dto.CommodityName,
        ScientificName                      = dto.ScientificName,
        LatinName                           = dto.LatinName,
        Number                              = dto.Number,
        NumberOfPackages                    = dto.NumberOfPackages,
        NetWeight                           = dto.NetWeight,
        ProductionDate                      = dto.ProductionDate,
        LotNumber                           = dto.LotNumber,

        OriginRawMaterialsCountry           = dto.OriginRawMaterialsCountry,
        ProcessingType                      = dto.ProcessingType,
        ProductionMode                      = dto.ProductionMode,
        Aquacultured                        = dto.Aquacultured,
        WildCaughtBool                      = dto.WildCaughtBool,
        ProductiveWaterArea                 = dto.ProductiveWaterArea,
        AquacultureArea                     = dto.AquacultureArea,
        CatchArea                           = dto.CatchArea,
        ArtificialCulture                   = dto.ArtificialCulture,
        WildCaught                          = dto.WildCaught,

        AquacultureFarmApprovedReg          = dto.AquacultureFarmApprovedReg,
        FishingVessel                       = dto.FishingVessel,
        FishingAndFactoryVessel             = dto.FishingAndFactoryVessel,
        TransportFishingVessel              = dto.TransportFishingVessel,
        ProcessingPlantNameAddress          = dto.ProcessingPlantNameAddress,
        ProcessingPlantRegNo                = dto.ProcessingPlantRegNo,
        ColdStorageRawMaterials             = dto.ColdStorageRawMaterials,
        ColdStorageProducts                 = dto.ColdStorageProducts,

        PackagingEnterpriseName             = dto.PackagingEnterpriseName,
        PackagingEnterpriseAddress          = dto.PackagingEnterpriseAddress,
        PackagingEnterpriseRegNumber        = dto.PackagingEnterpriseRegNumber,

        ConsignorName                       = dto.ConsignorName,
        ConsignorAddress                    = dto.ConsignorAddress,
        ConsigneeName                       = dto.ConsigneeName,
        ConsigneeAddress                    = dto.ConsigneeAddress,
        PlaceOfDispatch                     = dto.PlaceOfDispatch,
        PlaceOfDestination                  = dto.PlaceOfDestination,
        MeansOfTransport                    = dto.MeansOfTransport,
        NameOfVessel                        = dto.NameOfVessel,
        FlightNumber                        = dto.FlightNumber,
        OtherTransportMeans                 = dto.OtherTransportMeans,
        ContainerNumber                     = dto.ContainerNumber,
        SealNumber                          = dto.SealNumber,
        DateOfDeparture                     = dto.DateOfDeparture,
        PortOfDeparture                     = dto.PortOfDeparture,

        TransportAeroPlane                  = dto.TransportAeroPlane,
        TransportShip                       = dto.TransportShip,
        TransportRailwayWagon               = dto.TransportRailwayWagon,
        TransportRoadVehicle                = dto.TransportRoadVehicle,
        TransportOther                      = dto.TransportOther,

        IdentificationDocumentReferences    = dto.IdentificationDocumentReferences,

        ExporterName                        = dto.ExporterName,
        ExporterAddress                     = dto.ExporterAddress,
        ImporterName                        = dto.ImporterName,
        ImporterAddress                     = dto.ImporterAddress,

        PlaceOfIssue                        = dto.PlaceOfIssue,
        DateOfIssue                         = dto.DateOfIssue,
        OfficialStamp                       = dto.OfficialStamp,
        SignatoryUserId                     = dto.SignatoryUserId,
        SignatoryName                       = dto.SignatoryName,
        Qualification                       = dto.Qualification,

        DateOfAttachment                    = dto.DateOfAttachment,
        IdentificationMarksAttachment       = dto.IdentificationMarksAttachment,
        Attachments                         = dto.Attachments.Select(a => new ChAttachment
        {
            Product      = a.Product,
            NetWeight    = a.NetWeight,
            NumberOfBoxes = a.NumberOfBoxes
        }).ToList()
    };
}


