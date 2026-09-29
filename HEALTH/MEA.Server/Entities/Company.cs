using System.ComponentModel.DataAnnotations;

namespace MEA.Server.Entities
{
    public class Company
    {
        public int Id { get; set; }

        [Required]
        public string CompanyName { get; set; } = string.Empty;

        [Required]
        public string CompanyEmail { get; set; } = string.Empty;

        [Required]
        public string CompanyPhone { get; set; } = string.Empty;

        [Required]
        public string CompanyAddress { get; set; } = string.Empty;

        [Required]
        public string RegistrationNo { get; set; } = string.Empty;

        public int? ProductCertificateId { get; set; }
        public ProductCertificate? ProductCertificate { get; set; }

        [Required]
        public int CompanyStatusId { get; set; }
        public CompanyStatus? CompanyStatus { get; set; }

        [Required]
        public int ListedCountryId { get; set; }
        public ListedCountry? ListedCountry { get; set; }

        public string? UserId { get; set; }

        public AppUser? User { get; set; }

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    }
}

