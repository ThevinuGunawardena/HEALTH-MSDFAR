using MEA.Server.Data;
using MEA.Server.Entities;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Repositories
{
    public class CompanyRepository : ICompanyRepository
    {
        private readonly AppDbContext _context;

        public CompanyRepository(AppDbContext context)
        {
            _context = context;
        }

        public async Task<IEnumerable<Company>> GetAllAsync()
        {
            return await _context.Companies
                .Include(c => c.ProductCertificate)
                .Include(c => c.CompanyStatus)
                .Include(c => c.ListedCountry)
                .AsNoTracking()
                .OrderBy(c => c.Id)
                .ToListAsync();
        }

        public async Task<Company?> GetByIdAsync(int id)
        {
            return await _context.Companies.FindAsync(id);
        }

        public async Task<Company?> GetByEmailAsync(string email)
        {
            return await _context.Companies
                .AsNoTracking()
                .FirstOrDefaultAsync(c => c.CompanyEmail == email);
        }

        public async Task<bool> ExistsByEmailOrRegistrationNoAsync(string email, string registrationNo, int? excludeId = null)
        {
            var query = _context.Companies.AsNoTracking();
            
            if (excludeId.HasValue)
            {
                query = query.Where(c => c.Id != excludeId.Value);
            }

            return await query.AnyAsync(c => c.CompanyEmail == email || c.RegistrationNo == registrationNo);
        }

        public async Task<Company> CreateAsync(Company company)
        {
            _context.Companies.Add(company);
            await _context.SaveChangesAsync();
            return company;
        }

        public async Task<Company> UpdateAsync(Company company)
        {
            _context.Companies.Update(company);
            await _context.SaveChangesAsync();
            return company;
        }

        public async Task DeleteAsync(int id)
        {
            var company = await _context.Companies.FindAsync(id);
            if (company != null)
            {
                _context.Companies.Remove(company);
                await _context.SaveChangesAsync();
            }
        }
    }
}

