namespace MEA.Server.Entities
{
    public class VetCertificateForm
    {
        public int Id { get; set; }

        public int? CertificateRequestId { get; set; }

        public string CompanyUserId { get; set; } = string.Empty;

        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        public string? OldHC { get; set; }
        public string? NewHC { get; set; }
        public string? LandingSite { get; set; }
        public string? BoatRegistration { get; set; }
        public string? BoatNumber { get; set; }
        public string? SupplierNameAddress { get; set; }
        public DateTime? ArrivalAtFactory { get; set; }
        public DateTime? ProcessingDate { get; set; }

        public string? FarmLocation { get; set; }
        public string? FarmOwnerName { get; set; }
        public string? FarmOwnerAddress { get; set; }
        public DateTime? HarvestDate { get; set; }
        public DateTime? ArrivalTimeProduct { get; set; }
        public string? ProcessingDates { get; set; }
        public string? AquaSupplier { get; set; }

        public string? CountryOrigin { get; set; }
        public DateTime? ArrivalConsignment { get; set; }
        public string? HealthCertNo { get; set; }
        public bool? ProductTypeAquaculture { get; set; }
        public bool? ProductTypeWildCaught { get; set; }
        public byte[]? UploadedCertificateFile { get; set; }

        public string? ConsignorName { get; set; }
        public string? ConsignorAddress { get; set; }
        public string? ConsignorPostal { get; set; }
        public string? ConsignorTel { get; set; }

        public string? ConsigneeName { get; set; }
        public string? ConsigneeAddress { get; set; }
        public string? ConsigneePostal { get; set; }
        public string? ConsigneeTel { get; set; }

        public string? CountryOriginISO { get; set; }
        public string? RegionOriginISO { get; set; }
        public string? CountryDestinationISO { get; set; }

        public string? ProcessingEstName { get; set; }
        public string? ProcessingEstAddress { get; set; }
        public string? ApprovalNo { get; set; }
        public string? PlaceOfLoading { get; set; }
        public DateTime? DateOfDeparture { get; set; }

        public bool? TransportAeroPlane { get; set; }
        public bool? TransportShip { get; set; }
        public bool? TransportRailwayWagon { get; set; }
        public bool? TransportRoadVehicle { get; set; }
        public bool? TransportOther { get; set; }
        public string? TransportId { get; set; }
        public string? DocReferences { get; set; }
        public string? EntryBIP { get; set; }

        public string? DescCommon { get; set; }
        public string? DescScientific { get; set; }
        public string? ProcessingType { get; set; }
        public string? HsCode { get; set; }
        public bool? TemperatureAmbient { get; set; }
        public bool? TemperatureChilled { get; set; }
        public bool? TemperatureFrozen { get; set; }
        public string? Quantity { get; set; }
        public string? NumPackages { get; set; }
        public string? PackagingType { get; set; }
        public string? ContainerId { get; set; }
        public bool? ForHumanConsumption { get; set; }
        public string? ForImportEU { get; set; }
        public bool? NatureAquaculture { get; set; }
        public bool? NatureWildOrigin { get; set; }
        public bool? TreatmentChilled { get; set; }
        public bool? TreatmentFrozen { get; set; }
        public bool? TreatmentLive { get; set; }
        public string? NetWeight { get; set; }

        public byte[]? PaymentSlipFile { get; set; }
        public DateTime? SignatureDate { get; set; }
        public DateTime? SignatureTime { get; set; }
        public string? Signature { get; set; }
        public string? SignatoryName { get; set; }
        public string? Designation { get; set; }

        public bool? Attestation61_1 { get; set; } = true;
        public bool? Attestation61_2 { get; set; } = true;
        public bool? Attestation61_3 { get; set; } = true;
        public bool? Attestation61_4 { get; set; } = true;
        public bool? Attestation61_5 { get; set; } = true;
        public bool? Attestation62_1 { get; set; } = true;
        public bool? Attestation62_2 { get; set; } = true;

        public CertificateRequest? CertificateRequest { get; set; }

        public ICollection<VetCertificateProduct> Products { get; set; } = new List<VetCertificateProduct>();
        public ICollection<VetCertificateAttachment> Attachments { get; set; } = new List<VetCertificateAttachment>();
    }
}

