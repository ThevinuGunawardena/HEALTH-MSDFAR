using System;

namespace MEA.Server.DTO.JpCertificate
{
    public class CreateJpCertificateDto
    {
        public int? CertificateRequestId { get; set; }

        public string? MyRef { get; set; }
        public string? YourRef { get; set; }
        public DateTime? Date { get; set; }
        
        public string? ItemName { get; set; }
        public string? NumberOfPackages { get; set; }
        public string? NetWeight { get; set; }

        public string? ProcessingPlantName { get; set; }
        public string? ProcessingPlantAddress { get; set; }
        public string? CompetentAuthorityRegNo { get; set; }
        
        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        
        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        
        public string? DespatchFrom { get; set; }
        public string? DespatchTo { get; set; }
        public string? DespatchByShip { get; set; }
        
        public string? OfficialStamp { get; set; }
        public string? OfficialSignature { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? CertificateType { get; set; }
    }
}

