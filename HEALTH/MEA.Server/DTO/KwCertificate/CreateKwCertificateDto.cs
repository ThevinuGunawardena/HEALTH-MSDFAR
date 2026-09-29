using System.Collections.Generic;

namespace MEA.Server.DTO.KwCertificate
{
    public class CreateKwCertificateDto
    {
        public int? CertificateRequestId { get; set; }

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
        
        public bool? TransportByAir { get; set; }
        public bool? TransportBySea { get; set; }
        public string? VehicleIdentificationNo { get; set; }
        
        public bool? TempChilled { get; set; }
        public bool? TempFrozen { get; set; }
        
        public bool? CommoditiesOther { get; set; }
        public bool? CommoditiesAfterFurtherProcess { get; set; }
        public bool? CommoditiesHumanConsumption { get; set; }
        
        public List<CreateKwCertificateProductDto>? Products { get; set; }

        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? OfficialStamp { get; set; }
        public string? OfficialSignature { get; set; }
        public DateTime? SignatureDate { get; set; }
        public string? CertificateType { get; set; }
    }
}

