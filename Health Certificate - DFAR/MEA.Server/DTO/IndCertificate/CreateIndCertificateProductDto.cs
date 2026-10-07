namespace MEA.Server.DTO.IndCertificate
{
    public class CreateIndCertificateProductDto
    {
        public string? NameOfProduct { get; set; }
        public string? LotNo { get; set; }
        public string? TypeOfPackaging { get; set; }
        public int? NumberOfPackages { get; set; }
        public decimal? NetWeight { get; set; }
    }
}

