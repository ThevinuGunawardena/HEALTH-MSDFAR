using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities;

public class UkCertificate
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

    public string? CertificateReferenceNo { get; set; }

    public string? ConsignorName { get; set; }
    public string? ConsignorAddress { get; set; }
    public string? ConsignorTel { get; set; }

    public string? ConsigneeName { get; set; }
    public string? ConsigneeAddress { get; set; }
    public string? ConsigneeTel { get; set; }

    public string? OperatorName { get; set; }
    public string? OperatorAddress { get; set; }
    public string? OperatorTel { get; set; }

    public string? CountryOfOrigin { get; set; }
    public string? CountryOfOriginISO { get; set; }
    public string? RegionOfOrigin { get; set; }
    public string? RegionOfOriginCode { get; set; }

    public string? CountryOfDestination { get; set; }
    public string? CountryOfDestinationISO { get; set; }
    public string? RegionOfDestination { get; set; }
    public string? RegionOfDestinationCode { get; set; }

    public string? PlaceOfDispatchName { get; set; }
    public string? PlaceOfDispatchApprovalNo { get; set; }
    public string? PlaceOfDispatchAddress { get; set; }

    public string? PlaceOfDestinationName { get; set; }
    public string? PlaceOfDestinationAddress { get; set; }

    public string? PlaceOfLoading { get; set; }
    public DateTime? DateOfDeparture { get; set; }
    public string? TimeOfDeparture { get; set; }

    public bool TransportAeroplane { get; set; }
    public bool TransportVessel { get; set; }
    public bool TransportRailway { get; set; }
    public bool TransportRoadVehicle { get; set; }
    public bool TransportOther { get; set; }

    public string? TransportIdentification { get; set; }
    public string? EntryBCP { get; set; }

    public string? AccompDocType { get; set; }
    public string? AccompDocNo { get; set; }

    public bool TempAmbient { get; set; }
    public bool TempChilled { get; set; }
    public bool TempFrozen { get; set; }

    public string? ContainerSealNo { get; set; }

    public bool GoodsCanningIndustry { get; set; }
    public bool GoodsHumanConsumption { get; set; }

    public string? Field21 { get; set; }
    public string? Field22 { get; set; }

    public string? TotalNumberOfPackages { get; set; }
    public string? TotalNetWeight { get; set; }
    public string? TotalGrossWeight { get; set; }

    public bool FinalConsumer { get; set; }

    // Animal Health Attestations Strikethrough Fields
    public bool StrikeAnimalHealthAll { get; set; } = true;
    public bool StrikeAhT153 { get; set; } = true;
    public bool StrikeAhT154 { get; set; } = true;
    public bool StrikeAhT155 { get; set; } = true;
    public bool StrikeAhT155_Either { get; set; } = true;
    public bool StrikeAhT155_D_Bkd { get; set; } = true;
    public bool StrikeAhT155_D_SvcGs { get; set; } = true;
    public bool StrikeAhT155_D_SvcBkd { get; set; } = true;
    public bool StrikeAhT155_GsSalinity { get; set; } = true;
    public bool StrikeAhT155_GsEggs { get; set; } = true;
    public bool StrikeAhP502 { get; set; } = true;

    public string? SignatoryUserId { get; set; }
    [ForeignKey("SignatoryUserId")]
    public virtual AppUser? SignatoryUser { get; set; }

    public string? SignatoryName { get; set; }
    public string? Qualification { get; set; }
    public DateTime? CertifiedDate { get; set; }

    public virtual ICollection<UkCertificateProduct> Products { get; set; } = new List<UkCertificateProduct>();
}

public class UkCertificateProduct
{
    [Key]
    public int Id { get; set; }

    public int UkCertificateId { get; set; }
    [ForeignKey("UkCertificateId")]
    public virtual UkCertificate? UkCertificate { get; set; }

    public string? Species { get; set; }
    public string? NatureOfCommodity { get; set; }
    public string? TreatmentType { get; set; }
    public string? VesselPlant { get; set; }
    public string? NumberOfPackages { get; set; }
    public string? NetWeight { get; set; }
    public string? BatchNo { get; set; }
    public string? TypeOfPackaging { get; set; }
}
