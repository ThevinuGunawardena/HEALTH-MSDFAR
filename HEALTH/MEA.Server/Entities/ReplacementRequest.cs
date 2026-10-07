using System;

namespace MEA.Server.Entities
{
    public enum ReplacementStatus
    {
        Pending = 0,
        Approved = 1,
        Rejected = 2
    }

    public class ReplacementRequest
    {
        public int Id { get; set; }
        public int? OriginalCertificateRequestId { get; set; }
        public string OriginalReferenceNumber { get; set; } = string.Empty;
        public string ReplacementReferenceNumber { get; set; } = string.Empty;
        public string CompanyUserId { get; set; } = string.Empty;
        public string CompanyName { get; set; } = string.Empty;
        public string Country { get; set; } = string.Empty;
        public string CertificateType { get; set; } = string.Empty;
        public string Reason { get; set; } = string.Empty;
        public string? Remarks { get; set; }
        public string? RejectionReason { get; set; }
        public ReplacementStatus Status { get; set; } = ReplacementStatus.Pending;
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
        public DateTime? ProcessedAt { get; set; }
        public string? ProcessedByUserId { get; set; }
    }
}
