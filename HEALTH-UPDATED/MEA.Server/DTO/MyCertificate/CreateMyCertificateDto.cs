using System;
using System.Collections.Generic;

namespace MEA.Server.DTO.MyCertificate
{
    public class CreateMyCertificateDto
    {
        public int? CertificateRequestId { get; set; }

        public string? ExporterName { get; set; }
        public string? CertificateReferenceNo { get; set; }
        public string? QualityCertificateNo { get; set; }
        
        public string? CompetentAuthority { get; set; }
        public string? LocalAuthority { get; set; }
        
        public string? ImporterDetails { get; set; }
        
        public string? CountryOfOrigin { get; set; }
        public string? CountryOfOriginIso { get; set; }
        
        public string? CountryOfDestination { get; set; }
        public string? CountryOfDestinationIso { get; set; }
        
        public string? ProcessingEstablishment { get; set; }
        public string? AuthorizationNo { get; set; }
        
        public string? PlaceOfLoading { get; set; }
        
        public bool? TransportAir { get; set; }
        public bool? TransportShip { get; set; }
        public bool? TransportRail { get; set; }
        public bool? TransportRoad { get; set; }
        public bool? TransportOther { get; set; }
        
        public string? PortOfEntry { get; set; }
        public string? TransportCompany { get; set; }

        public bool? ConditionAmbient { get; set; }
        public bool? ConditionChilled { get; set; }
        public bool? ConditionFrozen { get; set; }

        public string? ContainerSealIdentification { get; set; }
        public string? InvoiceNo { get; set; }
        public string? TransitCountry { get; set; }
        public DateTime? DepartureDate { get; set; }
        public DateTime? CertifyingOfficialDate { get; set; }

        public string? CertificateReferenceNoPage2 { get; set; }
        public string? ProductBrand { get; set; }
        
        public bool? OriginFisheries { get; set; }
        public bool? OriginAquaculture { get; set; }
        public string? CertifiedProductFor { get; set; }
        public string? TreatmentType { get; set; }

        public List<CreateMyCertificateProductDto>? Products { get; set; }

        public string? CertificateReferenceNoPage3 { get; set; }
        public string? AdditionalInformation { get; set; }

        public string? OfficialStamp { get; set; }
        public string? OfficialSignature { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? CertificateType { get; set; }
    }
}

