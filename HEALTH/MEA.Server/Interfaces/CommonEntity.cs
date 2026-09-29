namespace MEA.Server.Interfaces
{
    public class CommonEntity
    {
        public int ID { get; set; }
        public string? CreatedByName { get; set; }
        public string? CreatedBy { get; set; }
        public string? UpdatedByName { get; set; }
        public string? UpdatedBy { get; set; }
        public DateTime? UpdatedDate { get; set; }
        public DateTime? CreatedDate { get; set; }
        public bool IsActive { get; set; }
    }
}
