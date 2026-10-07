using System.ComponentModel.DataAnnotations;

namespace MEA.Server.DTO.IndCertificate
{
    public class CreateIndCertificateDto
    {
        public int? CertificateRequestId { get; set; }

        public string? CertificateType { get; set; }
        public string? CountryOfDispatch { get; set; }
        public string? CertificateNumber { get; set; }
        public string? MyRef { get; set; }
        public string? YourRef { get; set; }

        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        public string? ConsignorTel { get; set; }

        public string? CompetentAuthorityDetails { get; set; }

        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        public string? ConsigneeTel { get; set; }

        public string? CountryOfOrigin { get; set; }
        public string? CountryOfOriginIso { get; set; }
        public string? CountryOfDestination { get; set; }
        public string? CountryOfDestinationIso { get; set; }
        public string? PlaceOfLoading { get; set; }

        public string? MeansOfTransport { get; set; }
        public string? DeclaredPointOfEntry { get; set; }
        public string? ConditionsForTransportStorage { get; set; }
        public string? TotalQuantity { get; set; }
        public string? InvoiceNoDate { get; set; }

        public string? FoodDescription { get; set; }
        public string? IntendedPurpose { get; set; }
        public string? ProducerNameAddress { get; set; }
        public string? ApprovalNumberDetails { get; set; }

        public List<CreateIndCertificateProductDto> Products { get; set; } = new();

        public DateTime? DateOfManufacture { get; set; }
        public DateTime? BestBefore { get; set; }
        public DateTime? DateOfExpiry { get; set; }

        public string? ItemDescription { get; set; }
        public string? NumberOfPackagesStr { get; set; }
        public string? NetWeightStr { get; set; }
        public string? ProcessingPlantNameAddress { get; set; }
        public string? ProcessingPlantRegNo { get; set; }
        public string? DispatchFrom { get; set; }
        public string? DispatchTo { get; set; }
        public string? ModeOfTransport { get; set; }
        public string? HsCode { get; set; }
        public string? SpeciesName { get; set; }
        public string? PreviousCertRef { get; set; }
        public string? ConsignmentIdentificationDetails { get; set; }
        public string? ProductDescription { get; set; }

        public string? AttestationPlace { get; set; }
        public DateTime? AttestationDate { get; set; }

        public string? SignatoryUserId { get; set; }
        public string? SignatoryName { get; set; }
        public string? Qualification { get; set; }
        public DateTime? AuthorizedOfficialDate { get; set; }
        public string? AuthorizedOfficialSignature { get; set; }
        public string? OfficialStamp { get; set; }
    }
}


