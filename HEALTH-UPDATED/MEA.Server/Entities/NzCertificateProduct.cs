using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class NzCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int NzCertificateId { get; set; }
        public NzCertificate? NzCertificate { get; set; }

        public string? ProductName { get; set; }
        public string? AquaticAnimalSpecies { get; set; }
        public DateTime? ProductionDate { get; set; }
        public int NumberOfPackages { get; set; }
        public decimal NetWeightKg { get; set; }
        public string? HsCode { get; set; }
    }
}

