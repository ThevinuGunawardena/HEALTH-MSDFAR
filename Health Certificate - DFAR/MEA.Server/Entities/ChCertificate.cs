using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class ChCertificate
    {
        [Key]
        public int Id { get; set; }

        public int? CertificateRequestId { get; set; }

        public string CompanyUserId { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        public string? CertificateType { get; set; } = "attachment";
        public string? RefNumber { get; set; }

        // Section I
        public string? CountryOfExport { get; set; }
        public string? CountryOfProduction { get; set; }
        public string? CompetentAuthority { get; set; }
        public string? DepartmentOfIssuance { get; set; }

        // Section II
        public string? CommodityName { get; set; }
        public string? ScientificName { get; set; }
        public string? LatinName { get; set; }
        public string? Number { get; set; }
        public string? NumberOfPackages { get; set; }
        public string? NetWeight { get; set; }
        public DateTime? ProductionDate { get; set; }
        public string? LotNumber { get; set; }

        // Section III
        public string? OriginRawMaterialsCountry { get; set; }
        public string? ProcessingType { get; set; }
        public string? ProductionMode { get; set; }
        public bool? Aquacultured { get; set; }
        public bool? WildCaughtBool { get; set; }
        public string? ProductiveWaterArea { get; set; }
        public string? AquacultureArea { get; set; }
        public string? CatchArea { get; set; }
        public string? ArtificialCulture { get; set; }
        public string? WildCaught { get; set; }

        // Section IV (Establishments)
        public string? AquacultureFarmApprovedReg { get; set; }
        public string? FishingVessel { get; set; }
        public string? FishingAndFactoryVessel { get; set; }
        public string? TransportFishingVessel { get; set; }
        public string? ProcessingPlantNameAddress { get; set; }
        public string? ProcessingPlantRegNo { get; set; }
        public string? ColdStorageRawMaterials { get; set; }
        public string? ColdStorageProducts { get; set; }

        // Packaging enterprise
        public string? PackagingEnterpriseName { get; set; }
        public string? PackagingEnterpriseAddress { get; set; }
        public string? PackagingEnterpriseRegNumber { get; set; }

        // Section V (Transport & Exporter/Importer)
        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        public string? PlaceOfDispatch { get; set; }
        public string? PlaceOfDestination { get; set; }
        public string? MeansOfTransport { get; set; }
        public string? NameOfVessel { get; set; }
        public string? FlightNumber { get; set; }
        public string? OtherTransportMeans { get; set; }
        public string? ContainerNumber { get; set; }
        public string? SealNumber { get; set; }
        public DateTime? DateOfDeparture { get; set; }
        public string? PortOfDeparture { get; set; }

        public bool? TransportAeroPlane { get; set; }
        public bool? TransportShip { get; set; }
        public bool? TransportRailwayWagon { get; set; }
        public bool? TransportRoadVehicle { get; set; }
        public bool? TransportOther { get; set; }

        public string? IdentificationDocumentReferences { get; set; }

        public string? ExporterName { get; set; }
        public string? ExporterAddress { get; set; }
        public string? ImporterName { get; set; }
        public string? ImporterAddress { get; set; }

        // Signatory & Issue
        public string? PlaceOfIssue { get; set; }
        public DateTime? DateOfIssue { get; set; }
        public string? OfficialStamp { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }

        // Attachment (Page 4)
        public DateTime? DateOfAttachment { get; set; }
        public string? IdentificationMarksAttachment { get; set; }
        public ICollection<ChAttachment> Attachments { get; set; } = new List<ChAttachment>();

        [ForeignKey("CertificateRequestId")]
        public CertificateRequest? CertificateRequest { get; set; }
    }

    public class ChAttachment
    {
        [Key]
        public int Id { get; set; }
        public int ChCertificateId { get; set; }
        [ForeignKey("ChCertificateId")]
        public ChCertificate? ChCertificate { get; set; }
        public string? Product { get; set; }
        public decimal? NetWeight { get; set; }
        public int? NumberOfBoxes { get; set; }
    }
}

