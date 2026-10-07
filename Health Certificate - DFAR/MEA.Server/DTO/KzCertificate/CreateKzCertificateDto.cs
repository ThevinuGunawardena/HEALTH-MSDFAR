using System;
using System.Collections.Generic;

namespace MEA.Server.DTO.KzCertificate
{
    public class CreateKzCertificateDto
    {
        public int? CertificateRequestId { get; set; }

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

        public string? ProductName { get; set; }
        public DateTime? ProductionDate { get; set; }
        public string? TypeOfPackage { get; set; }
        public string? NumberOfPackages { get; set; }
        public string? NetWeight { get; set; }
        public string? NumberOfSeal { get; set; }
        public string? IdentificationMarks { get; set; }
        public string? StorageConditions { get; set; }

        public string? EstablishmentNameAddressRegNo { get; set; }
        public string? FactoryVessel { get; set; }
        public string? ColdStore { get; set; }
        public string? AdministrativeUnit { get; set; }

        public List<CreateKzPreExportCertificateDto> PreExportCertificates { get; set; } = new();

        public string? PlaceOfIssue { get; set; }
        public DateTime? DateOfIssue { get; set; }
        public string? OfficialStamp { get; set; }
        public string? OfficialSignature { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? CertificateType { get; set; }

        public DateTime? DateOfAttachment { get; set; }
        public string? IdentificationMarksAttachment { get; set; }
        public List<CreateKzAttachmentDto> Attachments { get; set; } = new();
    }

    public class CreateKzPreExportCertificateDto
    {
        public string? Date { get; set; }
        public string? Number { get; set; }
        public string? CountryOfOrigin { get; set; }
        public string? AdministrativeTerritory { get; set; }
        public string? ApprovalNumber { get; set; }
        public string? ProductNameAndQuantity { get; set; }
    }

    public class CreateKzAttachmentDto
    {
        public string? Product { get; set; }
        public decimal? NumberOfKgs { get; set; }
        public int? NumberOfBoxes { get; set; }
    }
}
