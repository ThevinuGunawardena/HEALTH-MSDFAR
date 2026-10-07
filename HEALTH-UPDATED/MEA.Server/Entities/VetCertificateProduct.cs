using System.ComponentModel.DataAnnotations;

namespace MEA.Server.Entities
{
    public class VetCertificateProduct
    {
        [Key]
        public int Id { get; set; }

        public int VetCertificateFormId { get; set; }
        public VetCertificateForm? VetCertificateForm { get; set; }

        public int ProductOrder { get; set; }

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
    }
}
