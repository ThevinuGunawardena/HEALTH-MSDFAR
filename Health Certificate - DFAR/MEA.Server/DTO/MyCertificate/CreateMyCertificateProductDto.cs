namespace MEA.Server.DTO.MyCertificate
{
    public class CreateMyCertificateProductDto
    {
        public string? HsCode { get; set; }
        public string? Description { get; set; }
        public string? ScientificName { get; set; }
        public string? BatchCode { get; set; }
        
        public int? NumberOfPackages { get; set; }
        public decimal? NetWeight { get; set; }
    }
}

