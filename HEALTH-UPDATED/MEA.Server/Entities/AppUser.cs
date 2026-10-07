using Microsoft.AspNetCore.Identity;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class AppUser : IdentityUser
    {
        [PersonalData]
        [Column(TypeName = "nvarchar(110)")]
        public string FullName { get; set; } = string.Empty;

        [PersonalData]
        public int? CompanyId { get; set; }

        [PersonalData]
        [Column(TypeName = "nvarchar(1000)")]
        public string? Qualification { get; set; }

        public ICollection<CertificateRequest> CertificateRequests { get; set; } = new List<CertificateRequest>();
    }
}
