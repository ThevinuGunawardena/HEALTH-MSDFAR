namespace MEA.Server.DTO.UaCertificate;

public class CreateUaCertificateDto
{
    public int? CertificateRequestId { get; set; }

    public string? ConsignorName { get; set; }
    public string? ConsignorAddress { get; set; }
    public string? ConsignorPostalCode { get; set; }
    public string? ConsignorTelNo { get; set; }
    public string? CertificateReferenceNumber { get; set; }
    public string? CentralCompetentAuthority { get; set; }
    public string? LocalCompetentAuthority { get; set; }
    public string? ConsigneeName { get; set; }
    public string? ConsigneeAddress { get; set; }
    public string? ConsigneePostalCode { get; set; }
    public string? ConsigneeTel { get; set; }
    public string? PersonResponsibleName { get; set; }
    public string? PersonResponsibleAddress { get; set; }
    public string? PersonResponsiblePostalCode { get; set; }
    public string? PersonResponsibleTel { get; set; }
    public string? CountryOfOriginName { get; set; }
    public string? CountryOfOriginISO { get; set; }
    public string? CountryOfOriginISOCode { get; set; }
    public string? CountryOfOriginZone { get; set; }
    public string? ZoneOrigin { get; set; }
    public string? ZoneOriginCode { get; set; }
    public string? CountryDestinationName { get; set; }
    public string? CountryDestinationISO { get; set; }
    public string? CountryDestinationISOCode { get; set; }
    public string? CountryDestinationZone { get; set; }
    public string? ZoneDestination { get; set; }
    public string? ZoneDestinationCode { get; set; }
    public string? PlaceOriginName { get; set; }
    public string? PlaceOriginApprovalNumber { get; set; }
    public string? PlaceOriginAddress { get; set; }
    public string? Field112 { get; set; }
    public string? PlaceLoadingAddress { get; set; }
    public DateTime? DateOfDeparture { get; set; }
    public bool TransportAeroplane { get; set; }
    public bool TransportShip { get; set; }
    public bool TransportRailwayWagon { get; set; }
    public bool TransportRoadVehicle { get; set; }
    public bool TransportOther { get; set; }
    public string? TransportIdentification { get; set; }
    public string? TransportDocumentReferences { get; set; }
    public string? EntryBIPUkraine { get; set; }
    public string? DescriptionOfCommodity { get; set; }
    public string? CommodityCodeHS { get; set; }
    public string? Quantity { get; set; }
    public bool TemperatureAmbient { get; set; }
    public bool TemperatureChilled { get; set; }
    public bool TemperatureFrozen { get; set; }
    public string? NumberOfPackages { get; set; }
    public string? SealContainerNo { get; set; }
    public string? TypeOfPackaging { get; set; }
    public bool CommoditiesHumanConsumption { get; set; }
    public string? Field126 { get; set; }
    public string? ForImportIntoUkraine { get; set; }
    public string? HealthInfoNotes { get; set; }
    public string? HealthCertificateReferenceNumber { get; set; }
    public string? AdditionalInformation { get; set; }
    public string? SignatoryUserId { get; set; }
    public string? SignatoryName { get; set; }
    public string? Qualification { get; set; }
    public string? OfficialStamp { get; set; }
    public string? OfficialSignature { get; set; }
    public DateTime? CertifiedDate { get; set; }
    public string? CertificateType { get; set; }

    public List<CreateUaCertificateProductDto> Products { get; set; } = new();
}

public class CreateUaCertificateProductDto
{
    public string? Species { get; set; }
    public string? NatureOfCommodity { get; set; }
    public string? TreatmentApprovalNumber { get; set; }
    public string? ManufacturingPlant { get; set; }
    public string? NumberOfPackaging { get; set; }
    public string? TypeOfPackaging { get; set; }
    public string? NetWeight { get; set; }
}

