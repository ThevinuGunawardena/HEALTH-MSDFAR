namespace MEA.Server.DTO.HkCertificate;

public class CreateHkCertificateDto
{
    public int? CertificateRequestId { get; set; }
    public string? CertificateType { get; set; }
    public string? IdentificationNumber { get; set; }
    public string? CountryOfDispatch { get; set; }
    public string? CompetentAuthority { get; set; }
    public string? CertifyingBody { get; set; }
    public string? ContainerNumber { get; set; }
    public string? SealNumber { get; set; }
    public string? SealIdentificationNumber { get; set; }
    public string? StorageTemperature { get; set; }
    public string? ApprovalNumber { get; set; }
    public string? ProcessingEstablishment { get; set; }
    public string? ProvenanceDetails { get; set; }
    public string? ConsignorName { get; set; }
    public string? ConsignorAddress { get; set; }
    public string? PlaceOfDispatch { get; set; }
    public string? DestinationCountryPlace { get; set; }
    public string? MeansOfTransport { get; set; }
    public string? ConsigneeName { get; set; }
    public string? ConsigneeAddress { get; set; }
    public DateTime? DateOfAttachment { get; set; }
    public string? AttachmentRegNo { get; set; }
    public string? PlaceOfIssue { get; set; }
    public DateTime? DateOfIssue { get; set; }
    public string? SignatoryUserId { get; set; }
    public string? SignatoryName { get; set; }
    public string? Qualification { get; set; }
    public string? OfficialSignature { get; set; }
    public string? OfficerTel { get; set; }
    public string? OfficerFax { get; set; }
    public string? OfficerEmail { get; set; }
    public List<CreateHkCertificateProductDto> Products { get; set; } = new();
}

