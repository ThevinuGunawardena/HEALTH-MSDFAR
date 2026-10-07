using MEA.Server.Entities;

namespace MEA.Server.Repositories
{
    public interface ICompanyRepository
    {
        Task<IEnumerable<Company>> GetAllAsync();
        Task<Company?> GetByIdAsync(int id);
        Task<Company?> GetByEmailAsync(string email);
        Task<bool> ExistsByEmailOrRegistrationNoAsync(string email, string registrationNo, int? excludeId = null);
        Task<Company> CreateAsync(Company company);
        Task<Company> UpdateAsync(Company company);
        Task DeleteAsync(int id);
    }
}

