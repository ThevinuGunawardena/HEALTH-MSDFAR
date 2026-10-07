using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities;

public class CaCertificate
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

    public string? MyRef { get; set; }
    public string? YourRef { get; set; }
    public DateTime? Date { get; set; }
    public string? CertificateNumber { get; set; }
    public string? CompetentAuthority { get; set; } = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES";
    public string? CertifyingBody { get; set; } = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES";
    public string? ConsignorName { get; set; }
    public string? ConsignorAddress { get; set; }
    public string? ConsigneeName { get; set; }
    public string? ConsigneeAddress { get; set; }
    public string? CountryOfOrigin { get; set; } = "SRI LANKA";
    public string? CountryOfOriginISO { get; set; } = "LK";
    public string? CountryOfDestination { get; set; } = "CANADA";
    public string? CountryOfDestinationISO { get; set; } = "CA";
    public string? PlaceOfLoading { get; set; }
    
    public bool TransportAeroPlane { get; set; }
    public bool TransportShip { get; set; }
    public bool TransportRailway { get; set; }
    public bool TransportRoad { get; set; }
    public bool TransportOther { get; set; }
    
    public string? DespatchFrom { get; set; }
    public string? DespatchTo { get; set; }
    public string? DespatchByShip { get; set; }
    
    public string? ItemName { get; set; }
    public string? NumberOfPackages { get; set; }
    public string? NetWeight { get; set; }
    public string? ProcessingPlantName { get; set; }
    public string? ProcessingPlantAddress { get; set; }
    public string? CompetentAuthorityRegNo { get; set; }
    
    public string? PointsOfEntry { get; set; }
    public string? ConditionsOfStorage { get; set; }
    public string? TotalQuantity { get; set; }
    public string? SealNumber { get; set; }
    public string? TotalNumberOfPackages { get; set; }
    public string? ApprovalNumberOfEstablishments { get; set; }
    public string? DescriptionOfCommodity { get; set; }

    public string? SignatoryUserId { get; set; }
    [ForeignKey("SignatoryUserId")]
    public virtual AppUser? SignatoryUser { get; set; }

    public string? SignatoryName { get; set; }
    public string? Designation { get; set; }
    public string? Qualification { get; set; }
    public string? CompanyRegistrationNo { get; set; }
    public string? OfficialStamp { get; set; }
    public string? OfficialSignature { get; set; }
    public string? CertificateType { get; set; } = "letter";

    public virtual ICollection<CaCertificateProductAttachment> ProductsAttachment { get; set; } = new List<CaCertificateProductAttachment>();
}

public class CaCertificateProductAttachment
{
    [Key]
    public int Id { get; set; }

    public int CaCertificateId { get; set; }
    [ForeignKey("CaCertificateId")]
    public virtual CaCertificate? CaCertificate { get; set; }

    public string? Product { get; set; }
    public string? LotIdentifier { get; set; }
    public string? TypeOfPackaging { get; set; }
    public string? NumberOfKgs { get; set; }
    public int? NumberOfBoxes { get; set; }
}
