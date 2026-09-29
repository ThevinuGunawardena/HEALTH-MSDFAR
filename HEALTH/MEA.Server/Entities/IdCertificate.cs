using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class IdCertificate
    {
        [Key]
        public int Id { get; set; }

        public int? CertificateRequestId { get; set; }

        [ForeignKey("CertificateRequestId")]
        public CertificateRequest? CertificateRequest { get; set; }

        public string CompanyUserId { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        // Form Fields
        public string? NumberNomor { get; set; }

        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }

        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }

        public string? CompetentAuthority { get; set; }

        public bool EstablishmentAquaculture { get; set; }
        public bool EstablishmentProcessing { get; set; }
        public bool EstablishmentOther { get; set; }
        public string? EstablishmentName { get; set; }
        public string? EstablishmentRegNo { get; set; }
        public string? EstablishmentAddress { get; set; }

        public string? CountryRegionOrigin { get; set; }
        public bool SourceFarmRaised { get; set; }
        public bool SourceWildCaught { get; set; }

        public string? PortOfShipment { get; set; }
        public bool TransportAir { get; set; }
        public bool TransportSea { get; set; }
        public bool TransportRoad { get; set; }

        public string? CommodityDescription { get; set; }
        public bool TempAmbient { get; set; }
        public bool TempFrozen { get; set; }
        public bool TempChilled { get; set; }

        public bool IntendedHumanConsumption { get; set; }
        public bool IntendedCultureBreeding { get; set; }
        public bool IntendedTrade { get; set; }
        public bool IntendedResearch { get; set; }
        public bool IntendedFishFeed { get; set; }
        public bool IntendedExhibition { get; set; }
        public bool IntendedOther { get; set; }

        public string? TotalPackages { get; set; }
        public string? PackagingType { get; set; }
        public string? TotalQuantityKg { get; set; }
        public string? ContainerSealNumber { get; set; }
        public string? PortOfDestination { get; set; }
        public string? TransportVesselName { get; set; }
        public string? TransportVoyageNumber { get; set; }
        public DateTime? DateOfDeparture { get; set; }
        public string? TestingLaboratory { get; set; }
        public string? LaboratoryAddress { get; set; }
        public string? ApprovingOfficerName { get; set; }
        public string? TestResultNumber { get; set; }

        public string? AttestationRefNumber { get; set; }

        // Attestation checkboxes
        public bool AttestFinfish { get; set; }
        public bool AttestMollusca { get; set; }
        public bool AttestCrustacea { get; set; }
        public bool AttestFisheryProducts { get; set; }
        public bool AttestOther { get; set; }

        public bool AttestClauseA { get; set; }
        public bool AttestClauseB { get; set; }
        public bool AttestClauseC { get; set; }
        public bool AttestClauseCCrustacean { get; set; }
        public bool AttestClauseCCyprinidae { get; set; }
        public bool AttestClauseCTilapia { get; set; }
        public bool AttestClauseCCatfish { get; set; }
        public bool AttestClauseCOtherFish { get; set; }
        public bool AttestClauseCVisibleSigns { get; set; }
        public bool AttestClauseCPackagedContainers { get; set; }
        public bool AttestClauseD { get; set; }
        public bool AttestClauseE { get; set; }

        public string? AdditionalInformation { get; set; }

        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public string? CertifiedIssuedAt { get; set; }
        public DateTime? CertifiedDate { get; set; }
        public string? CertifiedPosition { get; set; }
        public string? CertifiedPhone { get; set; }
        public string? CertifiedFax { get; set; }
        public string? CertifiedEmail { get; set; }
        public string? CertifiedAddress { get; set; }

        // Relationships
        public ICollection<IdCertificateProduct> Products { get; set; } = new List<IdCertificateProduct>();
    }

    public class IdCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int IdCertificateId { get; set; }

        [ForeignKey("IdCertificateId")]
        public IdCertificate? IdCertificate { get; set; }

        public string? No { get; set; }
        public string? CommonName { get; set; }
        public string? ScientificName { get; set; }
        public string? HsCode { get; set; }
        public decimal? Quantity { get; set; }
        public string? Unit { get; set; }
    }
}

