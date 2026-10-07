using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class KwCertificate
    {
        [Key]
        public int Id { get; set; }

        public int? CertificateRequestId { get; set; }
        public CertificateRequest? CertificateRequest { get; set; }

        public string? CompanyUserId { get; set; }
        public AppUser? CompanyUser { get; set; }

        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        
        public string? CertificateReferenceNo { get; set; }
        public string? PlaceOfIssue { get; set; }
        public DateTime? DateOfIssue { get; set; }
        
        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        
        public string? CompetentAuthority { get; set; }
        public string? CompetentAuthorityAddress { get; set; }
        
        public string? CountryOfOrigin { get; set; }
        public string? CountryOfOriginIso { get; set; }
        
        public string? CountryOfDestination { get; set; }
        public string? CountryOfDestinationIso { get; set; }
        
        public string? ProducerName { get; set; }
        public string? ProducerAddress { get; set; }
        
        public string? PackingEstName { get; set; }
        public string? PackingEstAddress { get; set; }
        public string? PackingEstApprovalNo { get; set; }
        
        public string? BorderOfEntry { get; set; }
        public string? BorderLoadingCountry { get; set; }
        public string? BorderLoadingPlace { get; set; }
        
        public bool TransportByAir { get; set; }
        public bool TransportBySea { get; set; }
        public string? VehicleIdentificationNo { get; set; }
        
        public bool TempChilled { get; set; }
        public bool TempFrozen { get; set; }
        
        public bool CommoditiesOther { get; set; }
        public bool CommoditiesAfterFurtherProcess { get; set; }
        public bool CommoditiesHumanConsumption { get; set; }
        
        public ICollection<KwCertificateProduct> Products { get; set; } = new List<KwCertificateProduct>();

        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? OfficialStamp { get; set; }
        public string? OfficialSignature { get; set; }
        public DateTime? SignatureDate { get; set; }
        public string? CertificateType { get; set; } = "single";

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    }
}

