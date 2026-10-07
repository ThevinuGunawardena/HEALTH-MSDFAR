using System;
using System.Collections.Generic;

namespace MEA.Server.DTO.NzCertificate
{
    public class CreateNzCertificateDto
    {
        public int? CertificateRequestId { get; set; }

        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        public string? CertificateRefNumber { get; set; }
        
        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        
        public string? CountryOfOrigin { get; set; }
        public string? CountryOfDestination { get; set; }
        
        public string? ProcessorName { get; set; }
        public string? ProcessorAddress { get; set; }
        public string? ProcessorEstablishmentNumber { get; set; }
        
        public string? PortDispatchedFrom { get; set; }
        public DateTime? DateOfDeparture { get; set; }
        
        public string? CompetentAuthority { get; set; }
        public string? MeansOfTransport { get; set; }
        
        public bool? TransportAeroplan { get; set; }
        public bool? TransportShip { get; set; }
        
        public string? TemperatureOfCommodities { get; set; }
        
        public string? ContainerNumber { get; set; }
        public string? OfficialSealNumber { get; set; }
        public string? OfficialStamp { get; set; }
        public string? OfficialSignature { get; set; }
        
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public DateTime? SignatureDate { get; set; }
        public string? CertificateType { get; set; }

        public List<CreateNzCertificateProductDto>? Products { get; set; }
    }
}

