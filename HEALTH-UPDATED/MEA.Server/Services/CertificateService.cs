using MEA.Server.Data;
using MEA.Server.DTO.AmCertificate;
using MEA.Server.DTO.AuCertificate;
using MEA.Server.DTO.BrCertificate;
using MEA.Server.DTO.ChCertificate;
using MEA.Server.DTO.HkCertificate;
using MEA.Server.DTO.IdCertificate;
using MEA.Server.DTO.IndCertificate;
using MEA.Server.DTO.JpCertificate;
using MEA.Server.DTO.KwCertificate;
using MEA.Server.DTO.MyCertificate;
using MEA.Server.DTO.NzCertificate;
using MEA.Server.DTO.RuCertificate;
using MEA.Server.DTO.KzCertificate;
using MEA.Server.DTO.TwCertificate;
using MEA.Server.DTO.UaCertificate;
using MEA.Server.DTO.UkCertificate;
using MEA.Server.DTO.UsaCertificate;
using MEA.Server.DTO.IlCertificate;
using MEA.Server.DTO.MvCertificate;
using MEA.Server.DTO.CaCertificate;
using MEA.Server.DTO.SaCertificate;
using MEA.Server.DTO.ZaCertificate;
using MEA.Server.Mappings;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Services
{
    public class CertificateService : ICertificateService
    {
        private readonly AppDbContext _context;

        public CertificateService(AppDbContext context)
        {
            _context = context;
        }

        public async Task<string?> ResolveCompanyUserId(string fallbackUserId, int? certificateRequestId)
        {
            if (!certificateRequestId.HasValue) return fallbackUserId;

            var request = await _context.CertificateRequests
                .AsNoTracking()
                .FirstOrDefaultAsync(r => r.Id == certificateRequestId.Value);

            return request?.CompanyUserId;
        }

        public async Task<Entities.AuCertificate> CreateAuCertificateAsync(CreateAuCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.AuCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.AuCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.AuCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.AmCertificate> CreateAmCertificateAsync(CreateAmCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.AmCertificates.Include(c => c.PreExportCertificates).Include(c => c.Attachments).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.AmCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.AmCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.BrCertificate> CreateBrCertificateAsync(CreateBrCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.BrCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.BrCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.BrCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.ChCertificate> CreateChCertificateAsync(CreateChCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.ChCertificates.FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.ChCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.ChCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.HkCertificate> CreateHkCertificateAsync(CreateHkCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.HkCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.HkCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.HkCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.IdCertificate> CreateIdCertificateAsync(CreateIdCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.IdCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.IdCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.IdCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.IndCertificate> CreateIndCertificateAsync(CreateIndCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.IndCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.IndCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.IndCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.JpCertificate> CreateJpCertificateAsync(CreateJpCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.JpCertificates.FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.JpCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.JpCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.KwCertificate> CreateKwCertificateAsync(CreateKwCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.KwCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.KwCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.KwCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.MyCertificate> CreateMyCertificateAsync(CreateMyCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.MyCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.MyCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.MyCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.NzCertificate> CreateNzCertificateAsync(CreateNzCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.NzCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.NzCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.NzCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.RuCertificate> CreateRuCertificateAsync(CreateRuCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.RuCertificates.FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.RuCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.RuCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.KzCertificate> CreateKzCertificateAsync(CreateKzCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.KzCertificates.FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.KzCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.KzCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.TwCertificate> CreateTwCertificateAsync(CreateTwCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.TwCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.TwCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.TwCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.UaCertificate> CreateUaCertificateAsync(CreateUaCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.UaCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.UaCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.UaCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.UkCertificate> CreateUkCertificateAsync(CreateUkCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.UkCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.UkCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.UkCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.UsaCertificate> CreateUsaCertificateAsync(CreateUsaCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.UsaCertificates.FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.UsaCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.UsaCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.IlCertificate> CreateIlCertificateAsync(CreateIlCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.IlCertificates.Include(c => c.Products).FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.IlCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.IlCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.MvCertificate> CreateMvCertificateAsync(MvCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.MvCertificates
                    .Include(c => c.Products)
                    .Include(c => c.ProductsSecond)
                    .Include(c => c.ProductsAttachment)
                    .FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.MvCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.MvCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.CaCertificate> CreateCaCertificateAsync(CaCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.CaCertificates
                    .Include(c => c.ProductsAttachment)
                    .FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.CaCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.CaCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.SaCertificate> CreateSaCertificateAsync(SaCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.SaCertificates
                    .Include(c => c.ProductsAttachment)
                    .FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.SaCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.SaCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }

        public async Task<Entities.ZaCertificate> CreateZaCertificateAsync(ZaCertificateDto dto, string companyUserId)
        {
            if (dto.CertificateRequestId.HasValue)
            {
                var existing = await _context.ZaCertificates
                    .Include(c => c.ProductsAttachment)
                    .FirstOrDefaultAsync(c => c.CertificateRequestId == dto.CertificateRequestId.Value);
                if (existing != null)
                {
                    _context.ZaCertificates.Remove(existing);
                    await _context.SaveChangesAsync();
                }
            }
            var entity = dto.ToEntity(companyUserId);
            _context.ZaCertificates.Add(entity);
            await _context.SaveChangesAsync();
            return entity;
        }
    }
}

