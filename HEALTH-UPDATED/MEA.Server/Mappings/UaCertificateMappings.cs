using MEA.Server.DTO.UaCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class UaCertificateMappings
{
    public static UaCertificate ToEntity(this CreateUaCertificateDto dto, string companyUserId)
    {
        return new UaCertificate
        {
            CompanyUserId = companyUserId,
            CertificateRequestId = dto.CertificateRequestId,
            CreatedAt = DateTime.UtcNow,

            ConsignorName = dto.ConsignorName,
            ConsignorAddress = dto.ConsignorAddress,
            ConsignorPostalCode = dto.ConsignorPostalCode,
            ConsignorTelNo = dto.ConsignorTelNo,
            CertificateReferenceNumber = dto.CertificateReferenceNumber,
            CentralCompetentAuthority = dto.CentralCompetentAuthority,
            LocalCompetentAuthority = dto.LocalCompetentAuthority,
            ConsigneeName = dto.ConsigneeName,
            ConsigneeAddress = dto.ConsigneeAddress,
            ConsigneePostalCode = dto.ConsigneePostalCode,
            ConsigneeTel = dto.ConsigneeTel,
            PersonResponsibleName = dto.PersonResponsibleName,
            PersonResponsibleAddress = dto.PersonResponsibleAddress,
            PersonResponsiblePostalCode = dto.PersonResponsiblePostalCode,
            PersonResponsibleTel = dto.PersonResponsibleTel,
            CountryOfOriginName = dto.CountryOfOriginName,
            CountryOfOriginISO = dto.CountryOfOriginISO,
            CountryOfOriginISOCode = dto.CountryOfOriginISOCode,
            CountryOfOriginZone = dto.CountryOfOriginZone,
            ZoneOrigin = dto.ZoneOrigin,
            ZoneOriginCode = dto.ZoneOriginCode,
            CountryDestinationName = dto.CountryDestinationName,
            CountryDestinationISO = dto.CountryDestinationISO,
            CountryDestinationISOCode = dto.CountryDestinationISOCode,
            CountryDestinationZone = dto.CountryDestinationZone,
            ZoneDestination = dto.ZoneDestination,
            ZoneDestinationCode = dto.ZoneDestinationCode,
            PlaceOriginName = dto.PlaceOriginName,
            PlaceOriginApprovalNumber = dto.PlaceOriginApprovalNumber,
            PlaceOriginAddress = dto.PlaceOriginAddress,
            Field112 = dto.Field112,
            PlaceLoadingAddress = dto.PlaceLoadingAddress,
            DateOfDeparture = dto.DateOfDeparture,
            TransportAeroplane = dto.TransportAeroplane,
            TransportShip = dto.TransportShip,
            TransportRailwayWagon = dto.TransportRailwayWagon,
            TransportRoadVehicle = dto.TransportRoadVehicle,
            TransportOther = dto.TransportOther,
            TransportIdentification = dto.TransportIdentification,
            TransportDocumentReferences = dto.TransportDocumentReferences,
            EntryBIPUkraine = dto.EntryBIPUkraine,
            DescriptionOfCommodity = dto.DescriptionOfCommodity,
            CommodityCodeHS = dto.CommodityCodeHS,
            Quantity = dto.Quantity,
            TemperatureAmbient = dto.TemperatureAmbient,
            TemperatureChilled = dto.TemperatureChilled,
            TemperatureFrozen = dto.TemperatureFrozen,
            NumberOfPackages = dto.NumberOfPackages,
            SealContainerNo = dto.SealContainerNo,
            TypeOfPackaging = dto.TypeOfPackaging,
            CommoditiesHumanConsumption = dto.CommoditiesHumanConsumption,
            Field126 = dto.Field126,
            ForImportIntoUkraine = dto.ForImportIntoUkraine,
            HealthInfoNotes = dto.HealthInfoNotes,
            HealthCertificateReferenceNumber = dto.HealthCertificateReferenceNumber,
            AdditionalInformation = dto.AdditionalInformation,
            SignatoryUserId = dto.SignatoryUserId,
            SignatoryName = dto.SignatoryName,
            Qualification = dto.Qualification,
            OfficialStamp = dto.OfficialStamp,
            OfficialSignature = dto.OfficialSignature,
            CertifiedDate = dto.CertifiedDate,
            CertificateType = dto.CertificateType ?? "attachment",

            Products = dto.Products.Select(p => p.ToEntity()).ToList()
        };
    }

    public static UaCertificateProduct ToEntity(this CreateUaCertificateProductDto dto)
    {
        return new UaCertificateProduct
        {
            Species = dto.Species,
            NatureOfCommodity = dto.NatureOfCommodity,
            TreatmentApprovalNumber = dto.TreatmentApprovalNumber,
            ManufacturingPlant = dto.ManufacturingPlant,
            NumberOfPackaging = dto.NumberOfPackaging,
            TypeOfPackaging = dto.TypeOfPackaging,
            NetWeight = dto.NetWeight
        };
    }
}

