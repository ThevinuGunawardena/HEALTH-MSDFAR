using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities;

public class MvCertificate
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

    public string? ConsignorExporter { get; set; }
    public string? CertificateNumber { get; set; }
    public string? CompetentAuthority { get; set; } = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES";
    public string? CertifyingBody { get; set; } = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES";
    public string? ConsigneeImporter { get; set; }
    public string? CountryOfOrigin { get; set; } = "SRI LANKA";
    public string? CountryOfOriginISO { get; set; } = "LK";
    public string? CountryOfDestination { get; set; } = "MALDIVES";
    public string? CountryOfDestinationISO { get; set; } = "MV";
    public string? PlaceOfLoading { get; set; }
    
    public bool TransportAeroPlane { get; set; }
    public bool TransportShip { get; set; }
    public bool TransportRailway { get; set; }
    public bool TransportRoad { get; set; }
    public bool TransportOther { get; set; }
    
    public string? PointsOfEntry { get; set; }
    public string? ConditionsOfStorage { get; set; }
    public string? TotalQuantity { get; set; }
    public string? SealNumber { get; set; }
    public string? TotalNumberOfPackages { get; set; }
    public string? ApprovalNumberOfEstablishments { get; set; }
    public string? DescriptionOfCommodity { get; set; }
    
    public string? CertifyingOfficerName { get; set; }
    public DateTime? CertifyingOfficerDate { get; set; }

    public string? SignatoryUserId { get; set; }
    [ForeignKey("SignatoryUserId")]
    public virtual AppUser? SignatoryUser { get; set; }

    public string? SignatoryName { get; set; }
    public string? Qualification { get; set; }
    public string? CompanyRegistrationNo { get; set; }
    public string? OfficialStamp { get; set; }
    public string? OfficialSignature { get; set; }
    public string? CertificateType { get; set; } = "generic";

    public virtual ICollection<MvCertificateProduct> Products { get; set; } = new List<MvCertificateProduct>();
    public virtual ICollection<MvCertificateProductSecond> ProductsSecond { get; set; } = new List<MvCertificateProductSecond>();
    public virtual ICollection<MvCertificateProductAttachment> ProductsAttachment { get; set; } = new List<MvCertificateProductAttachment>();
}

public class MvCertificateProduct
{
    [Key]
    public int Id { get; set; }

    public int MvCertificateId { get; set; }
    [ForeignKey("MvCertificateId")]
    public virtual MvCertificate? MvCertificate { get; set; }

    public string? No { get; set; }
    public string? NatureOfCommodity { get; set; }
    public string? Species { get; set; }
    public string? PurposeOfUse { get; set; }
}

public class MvCertificateProductSecond
{
    [Key]
    public int Id { get; set; }

    public int MvCertificateId { get; set; }
    [ForeignKey("MvCertificateId")]
    public virtual MvCertificate? MvCertificate { get; set; }

    public string? No { get; set; }
    public string? NameOfTheProduct { get; set; }
    public string? LotIdentifier { get; set; }
    public string? TypeOfPackaging { get; set; }
    public int? NumberOfPackages { get; set; }
    public string? NetWeight { get; set; }
}

public class MvCertificateProductAttachment
{
    [Key]
    public int Id { get; set; }

    public int MvCertificateId { get; set; }
    [ForeignKey("MvCertificateId")]
    public virtual MvCertificate? MvCertificate { get; set; }

    public string? Product { get; set; }
    public string? LotIdentifier { get; set; }
    public string? TypeOfPackaging { get; set; }
    public string? NumberOfKgs { get; set; }
    public int? NumberOfBoxes { get; set; }
}
