using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class KwCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int KwCertificateId { get; set; }
        public KwCertificate? KwCertificate { get; set; }

        public string? NameDescription { get; set; }
        public string? HsCodes { get; set; }
        public string? TreatmentDerivedFrom { get; set; }
        public string? BrandName { get; set; }
        
        public DateTime? ProductionDate { get; set; }
        public DateTime? ExpiryDate { get; set; }
        
        public int NumberPackages { get; set; }
        public string? BatchLotNo { get; set; }
        public decimal TotalWeight { get; set; }
    }
}

