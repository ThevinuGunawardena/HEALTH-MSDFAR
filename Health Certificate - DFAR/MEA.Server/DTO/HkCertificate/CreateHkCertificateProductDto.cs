namespace MEA.Server.DTO.HkCertificate;

public class CreateHkCertificateProductDto
{
    public string? Description { get; set; }
    public string? Species { get; set; }
    public string? ProcessingType { get; set; }
    public string? PackagingType { get; set; }
    public string? LotCode { get; set; }
    public int? NumberOfPackages { get; set; }
    public string? PackagesUnit { get; set; }
    public decimal? NetWeight { get; set; }
    public string? NetWeightUnit { get; set; }
}

