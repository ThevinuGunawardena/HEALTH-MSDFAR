using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class IlCertificate
    {
        [Key]
        public int Id { get; set; }

        public int? CertificateRequestId { get; set; }

        public string CompanyUserId { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        public string? CertificateType { get; set; } = "attachment";
        public string? CertificationNo { get; set; }
        public string? CentralCompetentAuthority { get; set; }
        public string? CentralCompetentAuthorityEmail { get; set; }
        public string? LocalCompetentAuthority { get; set; }
        public string? CountryOfOrigin { get; set; }
        public string? PlaceOfOriginName { get; set; }
        public string? PlaceOfOriginAddress { get; set; }
        public string? PlaceOfOriginApprovalNo { get; set; }
        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        public string? PostalCodeConsignor { get; set; }
        public string? TelNoConsignor { get; set; }
        public string? EmailConsignor { get; set; }
        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        public string? PostalCodeConsignee { get; set; }
        public string? TelNoConsignee { get; set; }
        public string? EmailConsignee { get; set; }

        public string? PlaceOfLoading { get; set; }
        public string? PortOfEntry { get; set; }
        
        public DateTime? DateOfArrival { get; set; }
        public string? PlaceOfArrival { get; set; }
        public string? PlaceOfArrivalAddress { get; set; }
        public string? PlaceOfDestinationName { get; set; }
        public string? PlaceOfDestinationAddress { get; set; }
        public string? PlaceOfDestinationApprovalNo { get; set; }
        
        public DateTime? DateOfContainerization { get; set; }
        public DateTime? DateOfDeparture { get; set; }
        
        // Transport Checkboxes
        public bool? TransportSea { get; set; }
        public bool? TransportAir { get; set; }
        public bool? TransportRail { get; set; }
        public bool? TransportRoad { get; set; }
        public bool? TransportOther { get; set; }
        public string? BillOfLading { get; set; }
        public string? Awb { get; set; }
        
        public string? MeansOfTransportIdentification { get; set; }
        public string? ContainerNo { get; set; }
        public string? SealNo { get; set; }
        public string? MeansOfTransportReference { get; set; }
        public string? EntryBIP { get; set; }

        // Page 2 Compliance
        public string? ReadyToEat { get; set; }
        public string? NonReadyToEat { get; set; }
        public string? ShipmentNumber { get; set; }
        public string? Remarks { get; set; }

        // Page 4 - Signature
        public string? PlaceOfIssue { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public DateTime? SignatureDate { get; set; }
        public string? Stamp { get; set; }
        public string? Signature { get; set; }
        public string? SignatoryUserId { get; set; }
        [ForeignKey("SignatoryUserId")]
        public virtual AppUser? SignatoryUser { get; set; }

        public CertificateRequest? CertificateRequest { get; set; }
        public ICollection<IlCertificateProduct> Products { get; set; } = new List<IlCertificateProduct>();
    }

    public class IlCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int IlCertificateId { get; set; }
        [ForeignKey("IlCertificateId")]
        public IlCertificate? IlCertificate { get; set; }

        public string? DescriptionOfCommodity { get; set; }
        public string? SpeciesScientificName { get; set; }
        public string? NatureOfCommodity { get; set; }
        public string? TreatmentType { get; set; }
        public string? ApprovalNo { get; set; }
        public int? NumberOfPackages { get; set; }
        public decimal? NetWeight { get; set; }
        public DateTime? HarvestingDate { get; set; }
        public DateTime? ProductionDate { get; set; }
        public DateTime? BestBefore { get; set; }
        public string? LotNo { get; set; }
    }
}
