namespace MEA.Server.Entities
{
    public class Country
    {
        public int Id { get; set; }

        public string Name { get; set; } = string.Empty;

        public ICollection<CertificateRequest> CertificateRequests { get; set; } = new List<CertificateRequest>();
    }
}

