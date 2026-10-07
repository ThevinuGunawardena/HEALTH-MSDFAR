using System;

namespace MEA.Server.DTO.KwCertificate
{
    public class CreateKwCertificateProductDto
    {
        public string? NameDescription { get; set; }
        public string? HsCodes { get; set; }
        public string? TreatmentDerivedFrom { get; set; }
        public string? BrandName { get; set; }
        
        public DateTime? ProductionDate { get; set; }
        public DateTime? ExpiryDate { get; set; }
        
        public int? NumberPackages { get; set; }
        public string? BatchLotNo { get; set; }
        public decimal? TotalWeight { get; set; }
    }
}

