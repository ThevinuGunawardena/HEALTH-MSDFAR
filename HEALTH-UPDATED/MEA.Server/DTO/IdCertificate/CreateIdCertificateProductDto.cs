namespace MEA.Server.DTO.IdCertificate;

public class CreateIdCertificateProductDto
{
    public string? No { get; set; }
    public string? CommonName { get; set; }
    public string? ScientificName { get; set; }
    public string? HsCode { get; set; }
    public decimal? Quantity { get; set; }
    public string? Unit { get; set; }
}

