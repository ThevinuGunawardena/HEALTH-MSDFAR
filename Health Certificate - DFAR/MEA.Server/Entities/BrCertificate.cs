using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MEA.Server.Entities
{
    public class BrCertificate
    {
        [Key]
        public int Id { get; set; }
        public string? RefNumber { get; set; }
        public string? CountryOfExport { get; set; } = "SRI LANKA";
        public string? CertificateNo { get; set; }
        public string? CompetentAuthority { get; set; } = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES";
        public string? LocalCompetentAuthority { get; set; } = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES";
        public string? ExporterName { get; set; }
        public string? ExporterAddress { get; set; }
        public string? ImporterName { get; set; }
        public string? ImporterAddress { get; set; }
        public string? CountryOrigin { get; set; } = "SRI LANKA";
        public string? CountryOriginISO { get; set; } = "LK";
        public string? CountryOfDestination { get; set; } = "BRAZIL";
        public string? CountryDestinationISO { get; set; } = "BR";
        public string? PlaceOfLoading { get; set; }
        public bool TransportAeroPlane { get; set; }
        public bool TransportShip { get; set; }
        public bool TransportRailwayWagon { get; set; }
        public bool TransportRoadVehicle { get; set; }
        public bool TransportOther { get; set; }
        public string? DeclaredPointOfEntry { get; set; }
        public string? ConditionsForTransportStorage { get; set; }
        public string? IdentificationOfContainers { get; set; }
        public string? IdentificationOfFoodProducts { get; set; }
        public string? ProducerDetails { get; set; }
        public string? HsCode { get; set; }
        public string? IntendedPurpose { get; set; }
        public decimal TotalNetWeight { get; set; }
        public string? PlaceAndDate { get; set; }
        public DateTime DateOfIssue { get; set; } = DateTime.UtcNow;
        public string? OfficialStamp { get; set; }
        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public int? CertificateRequestId { get; set; }
        public string? CompanyUserId { get; set; }
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
        public string? ModeloConformeCircularNo { get; set; }
        public string? SanitaryCertification { get; set; }
        
        public CertificateRequest? CertificateRequest { get; set; }
        public ICollection<BrCertificateProduct> Products { get; set; } = new List<BrCertificateProduct>();
    }

    public class BrCertificateProduct
    {
        [Key]
        public int Id { get; set; }
        public int BrCertificateId { get; set; }
        [ForeignKey("BrCertificateId")]
        public BrCertificate? BrCertificate { get; set; }
        public string? NameOfTheProduct { get; set; }
        public string? ScientificName { get; set; }
        public string? TypeOfPackaging { get; set; }
        public int NumberOfPackages { get; set; }
        public decimal NetWeight { get; set; }
    }
}
