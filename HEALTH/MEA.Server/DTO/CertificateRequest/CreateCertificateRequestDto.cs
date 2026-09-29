namespace MEA.Server.DTO.CertificateRequest;

public class CreateCertificateRequestDto
{
    public string CertificateType { get; set; } = string.Empty;
    public int? CountryId { get; set; }
    public string? ReferenceNumber { get; set; }
    public int Quantity { get; set; } = 1;
}

