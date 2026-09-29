namespace MEA.Server.DTO.BrCertificate;

public class CreateBrCertificateProductDto
{
    public string? NameOfTheProduct { get; set; }
    public string? ScientificName { get; set; }
    public string? TypeOfPackaging { get; set; }
    public int? NumberOfPackages { get; set; }
    public decimal? NetWeight { get; set; }
}

