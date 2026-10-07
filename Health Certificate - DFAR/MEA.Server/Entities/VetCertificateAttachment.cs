namespace MEA.Server.Entities
{
    public class VetCertificateAttachment
    {
        public int Id { get; set; }
        public int VetCertificateFormId { get; set; }
        public int FileOrder { get; set; }
        public string? OriginalFileName { get; set; }
        public string? SecondaryFileName { get; set; }
        public string? ContentType { get; set; }
        public byte[] FileContent { get; set; } = Array.Empty<byte>();

        public VetCertificateForm? VetCertificateForm { get; set; }
    }
}