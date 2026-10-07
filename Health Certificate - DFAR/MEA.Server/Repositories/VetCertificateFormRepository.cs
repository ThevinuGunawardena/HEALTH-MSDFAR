using MEA.Server.Data;
using MEA.Server.Entities;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Repositories
{
    public class VetCertificateFormRepository : IVetCertificateFormRepository
    {
        private readonly AppDbContext _context;

        public VetCertificateFormRepository(AppDbContext context)
        {
            _context = context;
        }

        public async Task<VetCertificateForm?> GetByCertificateRequestIdAsync(int certificateRequestId)
        {
            return await _context.VetCertificateForms
                .AsNoTracking()
                .Include(v => v.Products)
                .Include(v => v.Attachments)
                .FirstOrDefaultAsync(v => v.CertificateRequestId == certificateRequestId);
        }

        public async Task<VetCertificateForm> CreateAsync(VetCertificateForm form)
        {
            _context.VetCertificateForms.Add(form);
            await _context.SaveChangesAsync();
            return form;
        }
    }
}

