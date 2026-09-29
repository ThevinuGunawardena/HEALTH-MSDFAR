namespace MEA.Server.Entities
{
    public class CompanyStatus
    {
        public int Id { get; set; }

        public string Name { get; set; } = string.Empty;

        public ICollection<Company> Companies { get; set; } = new List<Company>();
    }
}
