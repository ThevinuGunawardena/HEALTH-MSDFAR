namespace MEA.Server.DTO.AuCertificate;

public class CreateAuCertificateProductDto
{
    public string? SpeciesScientificName { get; set; }
    public string? NatureOfCommodity { get; set; }
    public string? TreatmentType { get; set; }
    public string? ApprovalNumberOfEstablishments { get; set; }
    public string? ManufacturingPlant { get; set; }
    public int? NumberOfPackages { get; set; }
    public decimal? NetWeight { get; set; }
}

