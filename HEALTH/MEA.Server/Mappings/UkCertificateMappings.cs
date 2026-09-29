using MEA.Server.DTO.UkCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class UkCertificateMappings
{
    public static UkCertificate ToEntity(this CreateUkCertificateDto dto, string companyUserId)
    {
        return new UkCertificate
        {
            CompanyUserId = companyUserId,
            CertificateRequestId = dto.CertificateRequestId,
            CreatedAt = DateTime.UtcNow,

            CertificateReferenceNo = dto.CertificateReferenceNo,

            ConsignorName = dto.ConsignorName,
            ConsignorAddress = dto.ConsignorAddress,
            ConsignorTel = dto.ConsignorTel,

            ConsigneeName = dto.ConsigneeName,
            ConsigneeAddress = dto.ConsigneeAddress,
            ConsigneeTel = dto.ConsigneeTel,

            OperatorName = dto.OperatorName,
            OperatorAddress = dto.OperatorAddress,
            OperatorTel = dto.OperatorTel,

            CountryOfOrigin = dto.CountryOfOrigin,
            CountryOfOriginISO = dto.CountryOfOriginISO,
            RegionOfOrigin = dto.RegionOfOrigin,
            RegionOfOriginCode = dto.RegionOfOriginCode,

            CountryOfDestination = dto.CountryOfDestination,
            CountryOfDestinationISO = dto.CountryOfDestinationISO,
            RegionOfDestination = dto.RegionOfDestination,
            RegionOfDestinationCode = dto.RegionOfDestinationCode,

            PlaceOfDispatchName = dto.PlaceOfDispatchName,
            PlaceOfDispatchApprovalNo = dto.PlaceOfDispatchApprovalNo,
            PlaceOfDispatchAddress = dto.PlaceOfDispatchAddress,

            PlaceOfDestinationName = dto.PlaceOfDestinationName,
            PlaceOfDestinationAddress = dto.PlaceOfDestinationAddress,

            PlaceOfLoading = dto.PlaceOfLoading,
            DateOfDeparture = dto.DateOfDeparture,
            TimeOfDeparture = dto.TimeOfDeparture,

            TransportAeroplane = dto.TransportAeroplane,
            TransportVessel = dto.TransportVessel,
            TransportRailway = dto.TransportRailway,
            TransportRoadVehicle = dto.TransportRoadVehicle,
            TransportOther = dto.TransportOther,

            TransportIdentification = dto.TransportIdentification,
            EntryBCP = dto.EntryBCP,

            AccompDocType = dto.AccompDocType,
            AccompDocNo = dto.AccompDocNo,

            TempAmbient = dto.TempAmbient,
            TempChilled = dto.TempChilled,
            TempFrozen = dto.TempFrozen,

            ContainerSealNo = dto.ContainerSealNo,

            GoodsCanningIndustry = dto.GoodsCanningIndustry,
            GoodsHumanConsumption = dto.GoodsHumanConsumption,

            Field21 = dto.Field21,
            Field22 = dto.Field22,

            TotalNumberOfPackages = dto.TotalNumberOfPackages,
            TotalNetWeight = dto.TotalNetWeight,
            TotalGrossWeight = dto.TotalGrossWeight,

            FinalConsumer = dto.FinalConsumer,

            StrikeAnimalHealthAll = dto.StrikeAnimalHealthAll,
            StrikeAhT153 = dto.StrikeAhT153,
            StrikeAhT154 = dto.StrikeAhT154,
            StrikeAhT155 = dto.StrikeAhT155,
            StrikeAhT155_Either = dto.StrikeAhT155_Either,
            StrikeAhT155_D_Bkd = dto.StrikeAhT155_D_Bkd,
            StrikeAhT155_D_SvcGs = dto.StrikeAhT155_D_SvcGs,
            StrikeAhT155_D_SvcBkd = dto.StrikeAhT155_D_SvcBkd,
            StrikeAhT155_GsSalinity = dto.StrikeAhT155_GsSalinity,
            StrikeAhT155_GsEggs = dto.StrikeAhT155_GsEggs,
            StrikeAhP502 = dto.StrikeAhP502,

            SignatoryUserId = dto.SignatoryUserId,
            SignatoryName = dto.SignatoryName,
            Qualification = dto.Qualification,
            CertifiedDate = dto.CertifiedDate,

            Products = dto.Products.Select(p => p.ToEntity()).ToList()
        };
    }

    public static UkCertificateProduct ToEntity(this CreateUkCertificateProductDto dto)
    {
        return new UkCertificateProduct
        {
            Species = dto.Species,
            NatureOfCommodity = dto.NatureOfCommodity,
            TreatmentType = dto.TreatmentType,
            VesselPlant = dto.VesselPlant,
            NumberOfPackages = dto.NumberOfPackages,
            NetWeight = dto.NetWeight,
            BatchNo = dto.BatchNo,
            TypeOfPackaging = dto.TypeOfPackaging
        };
    }
}
