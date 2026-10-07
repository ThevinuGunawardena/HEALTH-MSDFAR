using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities;

public class TwCertificate
{
    [Key]
    public int Id { get; set; }

    public string? CompanyUserId { get; set; }
    [ForeignKey("CompanyUserId")]
    public virtual AppUser? CompanyUser { get; set; }

    public int? CertificateRequestId { get; set; }
    [ForeignKey("CertificateRequestId")]
    public virtual CertificateRequest? CertificateRequest { get; set; }

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public string? ReferenceNo { get; set; }
    public string? CountryOfExport { get; set; }
    public string? CountryOfProduction { get; set; }
    public string? CompetentAuthority { get; set; }
    public string? DepartmentIssuance { get; set; }

    public string? ProductionPlace { get; set; }
    public string? ProcessingType { get; set; }
    public string? ProductionMode { get; set; }
    public bool AquaculturedYes { get; set; }
    public bool AquaculturedNo { get; set; }
    public bool WildCaughtYes { get; set; }
    public bool WildCaughtNo { get; set; }
    public string? AquacultureArea { get; set; }
    public string? CatchArea { get; set; }
    public string? HarvestingArea { get; set; }
    public string? VesselName { get; set; }
    public string? EnterpriseName { get; set; }
    public string? EnterpriseRegistrationNo { get; set; }
    public DateTime? ProductionDate { get; set; }

    public string? ConsignorName { get; set; }
    public string? ConsignorAddress { get; set; }
    public string? ConsigneeName { get; set; }
    public string? ConsigneeAddress { get; set; }
    public string? PlaceOfDispatch { get; set; }
    public string? PlaceOfDestination { get; set; }
    public string? MeansOfTransport { get; set; }
    public string? VesselNameTransport { get; set; }
    public string? FlightNumber { get; set; }
    public string? OtherTransportMeans { get; set; }
    public string? ContainerNumber { get; set; }
    public string? SealNumber { get; set; }

    public string? PlaceOfIssue { get; set; }
    public DateTime? DateOfIssue { get; set; }
    public string? OfficialStamp { get; set; }
    public string? OfficialSignature { get; set; }
    public string? SignatoryUserId { get; set; }
    public string? SignatoryName { get; set; }
    public string? Qualification { get; set; }
    public string? CertificateType { get; set; } = "single";

    public virtual ICollection<TwCertificateProduct> Products { get; set; } = new List<TwCertificateProduct>();
}

public class TwCertificateProduct
{
    [Key]
    public int Id { get; set; }

    public int TwCertificateId { get; set; }
    [ForeignKey("TwCertificateId")]
    public virtual TwCertificate? TwCertificate { get; set; }

    public string? CommodityName { get; set; }
    public string? HsCode { get; set; }
    public string? ScientificName { get; set; }
    public int NumberOfPackages { get; set; }
    public decimal NetWeight { get; set; }
}

