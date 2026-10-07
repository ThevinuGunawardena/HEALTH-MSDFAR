using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class RuCertificate
    {
        [Key]
        public int Id { get; set; }
        public int? CertificateRequestId { get; set; }
        public string CompanyUserId { get; set; } = string.Empty;
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        // Section 1: Shipment details
        public string? ConsignorNameAddress { get; set; }
        public string? ConsigneeNameAddress { get; set; }
        public string? MeansOfTransport { get; set; }
        public string? CountryOfTransit { get; set; }
        public string? CertificateNo { get; set; }
        public string? CountryOfOrigin { get; set; }
        public string? CountryIssuing { get; set; }
        public string? CompetentAuthorityExporting { get; set; }
        public string? OrganizationIssuing { get; set; }
        public string? PointOfCrossingBorder { get; set; }

        // Section 2: Identification of goods
        public string? ProductName { get; set; }
        public DateTime? ProductionDate { get; set; }
        public string? TypeOfPackage { get; set; }
        public string? NumberOfPackages { get; set; }
        public string? NetWeight { get; set; }
        public string? NumberOfSeal { get; set; }
        public string? IdentificationMarks { get; set; }
        public string? StorageConditions { get; set; }

        // Section 3: Origin of products
        public string? EstablishmentNameAddressRegNo { get; set; }
        public string? FactoryVessel { get; set; }
        public string? ColdStore { get; set; }
        public string? AdministrativeUnit { get; set; }

        // Section 4: Pre-export certificates
        public ICollection<RuPreExportCertificate> PreExportCertificates { get; set; } = new List<RuPreExportCertificate>();

        // Issue details
        public string? PlaceOfIssue { get; set; }
        public DateTime? DateOfIssue { get; set; }
        public string? OfficialStamp { get; set; }
        public string? OfficialSignature { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? CertificateType { get; set; } = "attachment";

        // Attachment details
        public DateTime? DateOfAttachment { get; set; }
        public string? IdentificationMarksAttachment { get; set; }
        public ICollection<RuAttachment> Attachments { get; set; } = new List<RuAttachment>();

        public CertificateRequest? CertificateRequest { get; set; }
    }

    public class RuPreExportCertificate
    {
        [Key]
        public int Id { get; set; }
        public int RuCertificateId { get; set; }
        [ForeignKey("RuCertificateId")]
        public RuCertificate? RuCertificate { get; set; }

        public string? Date { get; set; }
        public string? Number { get; set; }
        public string? CountryOfOrigin { get; set; }
        public string? AdministrativeTerritory { get; set; }
        public string? ApprovalNumber { get; set; }
        public string? ProductNameAndQuantity { get; set; }
    }

    public class RuAttachment
    {
        [Key]
        public int Id { get; set; }
        public int RuCertificateId { get; set; }
        [ForeignKey("RuCertificateId")]
        public RuCertificate? RuCertificate { get; set; }

        public string? Product { get; set; }
        public decimal? NumberOfKgs { get; set; }
        public int? NumberOfBoxes { get; set; }
    }
}

