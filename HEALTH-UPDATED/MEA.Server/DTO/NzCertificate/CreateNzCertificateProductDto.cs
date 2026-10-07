namespace MEA.Server.DTO.NzCertificate
{
    public class CreateNzCertificateProductDto
    {
        public string? ProductName { get; set; }
        public string? AquaticAnimalSpecies { get; set; }
        public DateTime? ProductionDate { get; set; }
        public int? NumberOfPackages { get; set; }
        public decimal? NetWeightKg { get; set; }
        public string? HsCode { get; set; }
    }
}

