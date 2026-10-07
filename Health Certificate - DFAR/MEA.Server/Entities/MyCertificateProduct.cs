using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class MyCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int MyCertificateId { get; set; }
        public MyCertificate? MyCertificate { get; set; }

        public string? HsCode { get; set; }
        public string? Description { get; set; }
        public string? ScientificName { get; set; }
        public string? BatchCode { get; set; }
        
        public int NumberOfPackages { get; set; }
        public decimal NetWeight { get; set; }
    }
}

