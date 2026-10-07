namespace MEA.Server.DTO.BrCertificate;

public class CreateBrCertificateDto
{
    public int? CertificateRequestId { get; set; }
    public string? RefNumber { get; set; }
    public string? CountryOfExport { get; set; }
    public string? CertificateNo { get; set; }
    public string? CompetentAuthority { get; set; }
    public string? LocalCompetentAuthority { get; set; }
    public string? ExporterName { get; set; }
    public string? ExporterAddress { get; set; }
    public string? ImporterName { get; set; }
    public string? ImporterAddress { get; set; }
    public string? CountryOrigin { get; set; }
    public string? CountryOriginISO { get; set; }
    public string? CountryOfDestination { get; set; }
    public string? CountryDestinationISO { get; set; }
    public string? PlaceOfLoading { get; set; }
    public bool? TransportAeroPlane { get; set; }
    public bool? TransportShip { get; set; }
    public bool? TransportRailwayWagon { get; set; }
    public bool? TransportRoadVehicle { get; set; }
    public bool? TransportOther { get; set; }
    public string? DeclaredPointOfEntry { get; set; }
    public string? ConditionsForTransportStorage { get; set; }
    public string? IdentificationOfContainers { get; set; }
    public string? IdentificationOfFoodProducts { get; set; }
    public string? ProducerDetails { get; set; }
    public string? HsCode { get; set; }
    public string? IntendedPurpose { get; set; }
    public decimal? TotalNetWeight { get; set; }
    public string? PlaceAndDate { get; set; }
    public DateTime? DateOfIssue { get; set; }
    public string? OfficialStamp { get; set; }
    public string? SignatoryUserId { get; set; }
    public string? SignatoryName { get; set; }
    public string? Qualification { get; set; }
    public string? ModeloConformeCircularNo { get; set; }
    public string? SanitaryCertification { get; set; }
    public List<CreateBrCertificateProductDto> Products { get; set; } = new();
}

