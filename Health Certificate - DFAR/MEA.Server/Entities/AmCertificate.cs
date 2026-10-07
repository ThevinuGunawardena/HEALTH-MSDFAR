using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class AmCertificate
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
        public string? TransportId { get; set; }
        public string? ProcessingEstName { get; set; }
        public string? ProcessingEstAddress { get; set; }
        public string? ProcessingEstRegNo { get; set; }
        public ICollection<AmPreExportCertificate> PreExportCertificates { get; set; } = new List<AmPreExportCertificate>();
        public ICollection<AmAttachment> Attachments { get; set; } = new List<AmAttachment>();
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
        public string? CertificateNo { get; set; }
        public string? CountryIssuing { get; set; }
        public string? CompetentAuthorityExporting { get; set; }
        public string? OrganizationIssuing { get; set; }
        public string? CountryOfTransit { get; set; }
        public string? PointOfCrossingBorder { get; set; }

        public string? ProductName { get; set; }
        public DateTime? ProductionDate { get; set; }
        public string? NetWeight { get; set; }
        public string? NumberOfSeal { get; set; }
        public string? IdentificationMarks { get; set; }
        public string? StorageConditions { get; set; }

        public string? FactoryVessel { get; set; }
        public string? ColdStore { get; set; }
        public string? AdministrativeUnit { get; set; }

        public string? PlaceOfIssue { get; set; }
        public DateTime? DateOfIssue { get; set; }
        public DateTime? DateOfAttachment { get; set; }
        public string? IdentificationMarksAttachment { get; set; }

        public string? ExportApprovalNumber { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public DateTime? SignatureDate { get; set; }
        public string? Stamp { get; set; }
        public string? Signature { get; set; }
        public CertificateRequest? CertificateRequest { get; set; }
    }

    public class AmAttachment
    {
        [Key]
        public int Id { get; set; }
        public int AmCertificateId { get; set; }
        [ForeignKey("AmCertificateId")]
        public AmCertificate? AmCertificate { get; set; }
        public string? Product { get; set; }
        public decimal? NumberOfKgs { get; set; }
        public int? NumberOfBoxes { get; set; }
    }

    public class AmPreExportCertificate
    {
        [Key]
        public int Id { get; set; }
        public int AmCertificateId { get; set; }
        [ForeignKey("AmCertificateId")]
        public AmCertificate? AmCertificate { get; set; }
        public DateTime? Date { get; set; }
        public string? Number { get; set; }
        public string? CountryOfOrigin { get; set; }
        public string? AdministrativeTerritory { get; set; }
        public string? ApprovalNumber { get; set; }
        public string? ProductNameAndQuantity { get; set; }
    }
}

