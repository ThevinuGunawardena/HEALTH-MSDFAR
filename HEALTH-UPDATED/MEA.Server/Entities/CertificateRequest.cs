namespace MEA.Server.Entities
{
    public class CertificateRequest
    {
        public int Id { get; set; }

        public string ReferenceNumber { get; set; } = string.Empty;

        public CertificateType CertificateType { get; set; }
        
        public string CompanyUserId { get; set; } = string.Empty;

        public int? CountryId { get; set; }

        public CertificateStatus Status { get; set; } = CertificateStatus.Pending;

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        public string? CancelsAndReplacesRef { get; set; }

        public DateTime? CancelsAndReplacesDate { get; set; }

        public int? ReplacedCertificateRequestId { get; set; }
    }
}

