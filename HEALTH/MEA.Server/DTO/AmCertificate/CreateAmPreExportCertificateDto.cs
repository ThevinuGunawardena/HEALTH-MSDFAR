namespace MEA.Server.DTO.AmCertificate;

public class CreateAmPreExportCertificateDto
{
    public DateTime? Date { get; set; }
    public string? Number { get; set; }
    public string? CountryOfOrigin { get; set; }
    public string? AdministrativeTerritory { get; set; }
    public string? ApprovalNumber { get; set; }
    public string? ProductNameAndQuantity { get; set; }
}

