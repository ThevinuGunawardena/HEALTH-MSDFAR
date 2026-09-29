using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class AuCertificate
    {
        [Key]
        public int Id { get; set; }

        public int? CertificateRequestId { get; set; }

        public string CompanyUserId { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        public string? ConsignorPostal { get; set; }
        public string? ConsignorTel { get; set; }
        public string? CertRefNumber { get; set; }
        public string? CertRefNumberA { get; set; }
        public string? CentralCompetentAuthority { get; set; }
        public string? LocalCompetentAuthority { get; set; }
        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        public string? ConsigneePostal { get; set; }
        public string? ConsigneeTel { get; set; }
        public string? Consignee6 { get; set; }
        public string? CountryOrigin { get; set; }
        public string? CountryOriginISO { get; set; }
        public string? RegionOrigin { get; set; }
        public string? RegionOriginISO { get; set; }
        public string? CountryDestination { get; set; }
        public string? CountryDestinationISO { get; set; }
        public string? CountryDestination110 { get; set; }
        public string? PlaceOfOriginName { get; set; }
        public string? PlaceOfOriginAddress { get; set; }
        public string? PlaceOfOriginApprovalNo { get; set; }
        public string? CountryDestination112 { get; set; }
        public string? PlaceOfLoading { get; set; }
        public DateTime? DateOfDeparture { get; set; }
        public bool? TransportAeroPlane { get; set; }
        public bool? TransportShip { get; set; }
        public bool? TransportRailwayWagon { get; set; }
        public bool? TransportRoadVehicle { get; set; }
        public bool? TransportOther { get; set; }
        public string? DocReferences { get; set; }
        public string? EntryBIP { get; set; }
        public string? Field117 { get; set; }
        public string? DescCommon { get; set; }
        public string? HsCode { get; set; }
        public string? Quantity { get; set; }
        public bool? TemperatureAmbient { get; set; }
        public bool? TemperatureChilled { get; set; }
        public bool? TemperatureFrozen { get; set; }
        public string? NumPackages { get; set; }
        public string? ContainerId { get; set; }
        public string? PackagingType { get; set; }
        public bool? ForHumanConsumption { get; set; }
        public string? Field126 { get; set; }
        public string? ForImportEU { get; set; }
        public string? HealthCertNo { get; set; }
        public string? HealthCertNoB { get; set; }
        public string? ExportApprovalNumber { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public DateTime? SignatureDate { get; set; }
        public string? Stamp { get; set; }
        public string? Signature { get; set; }
        public string? CertificateType { get; set; }

        public CertificateRequest? CertificateRequest { get; set; }
        public ICollection<AuCertificateProduct> Products { get; set; } = new List<AuCertificateProduct>();
    }

    public class AuCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int AuCertificateId { get; set; }
        [ForeignKey("AuCertificateId")]
        public AuCertificate? AuCertificate { get; set; }

        public string? SpeciesScientificName { get; set; }
        public string? NatureOfCommodity { get; set; }
        public string? TreatmentType { get; set; }
        public string? ApprovalNumberOfEstablishments { get; set; }
        public string? ManufacturingPlant { get; set; }
        public int? NumberOfPackages { get; set; }
        public decimal? NetWeight { get; set; }
    }
}

