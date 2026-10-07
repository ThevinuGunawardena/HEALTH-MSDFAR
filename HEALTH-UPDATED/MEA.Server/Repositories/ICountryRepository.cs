using MEA.Server.Entities;

namespace MEA.Server.Repositories
{
    public interface ICountryRepository
    {
        Task<IEnumerable<Country>> GetAllAsync();
        Task<Country?> GetByIdAsync(int id);
        Task<bool> ExistsAsync(int id);
    }
}

