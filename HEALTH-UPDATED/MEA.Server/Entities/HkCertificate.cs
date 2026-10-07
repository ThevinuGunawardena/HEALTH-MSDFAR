using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class HkCertificate
    {
        [Key]
        public int Id { get; set; }

        public int? CertificateRequestId { get; set; }

        public string CompanyUserId { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        public string? CertificateType { get; set; } = "attachment";
        public string? IdentificationNumber { get; set; }
        public string? CountryOfDispatch { get; set; }
        public string? CompetentAuthority { get; set; }
        public string? CertifyingBody { get; set; }

        public string? ContainerNumber { get; set; }
        public string? SealNumber { get; set; }
        public string? SealIdentificationNumber { get; set; }
        public string? StorageTemperature { get; set; }

        public string? ApprovalNumber { get; set; }
        public string? ProcessingEstablishment { get; set; }
        public string? ProvenanceDetails { get; set; }
        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }

        public string? PlaceOfDispatch { get; set; }
        public string? DestinationCountryPlace { get; set; }
        public string? MeansOfTransport { get; set; }
        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }

        public DateTime? DateOfAttachment { get; set; }
        public string? AttachmentRegNo { get; set; }

        public string? PlaceOfIssue { get; set; }
        public DateTime? DateOfIssue { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? OfficialSignature { get; set; }
        public string? OfficerTel { get; set; }
        public string? OfficerFax { get; set; }
        public string? OfficerEmail { get; set; }

        [ForeignKey("CertificateRequestId")]
        public CertificateRequest? CertificateRequest { get; set; }

        public ICollection<HkCertificateProduct> Products { get; set; } = new List<HkCertificateProduct>();
    }

    public class HkCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int HkCertificateId { get; set; }

        [ForeignKey("HkCertificateId")]
        public HkCertificate? HkCertificate { get; set; }

        public string? Description { get; set; }
        public string? Species { get; set; }
        public string? ProcessingType { get; set; }
        public string? PackagingType { get; set; }
        public string? LotCode { get; set; }
        public int? NumberOfPackages { get; set; }
        public string? PackagesUnit { get; set; }
        public decimal? NetWeight { get; set; }
        public string? NetWeightUnit { get; set; }
    }
}

