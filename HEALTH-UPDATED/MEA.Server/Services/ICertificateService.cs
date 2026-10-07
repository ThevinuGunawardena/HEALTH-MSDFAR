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

namespace MEA.Server.Services
{
    public interface ICertificateService
    {
        Task<string?> ResolveCompanyUserId(string fallbackUserId, int? certificateRequestId);
        Task<Entities.AuCertificate> CreateAuCertificateAsync(CreateAuCertificateDto dto, string companyUserId);
        Task<Entities.AmCertificate> CreateAmCertificateAsync(CreateAmCertificateDto dto, string companyUserId);
        Task<Entities.BrCertificate> CreateBrCertificateAsync(CreateBrCertificateDto dto, string companyUserId);
        Task<Entities.ChCertificate> CreateChCertificateAsync(CreateChCertificateDto dto, string companyUserId);
        Task<Entities.HkCertificate> CreateHkCertificateAsync(CreateHkCertificateDto dto, string companyUserId);
        Task<Entities.IdCertificate> CreateIdCertificateAsync(CreateIdCertificateDto dto, string companyUserId);
        Task<Entities.IndCertificate> CreateIndCertificateAsync(CreateIndCertificateDto dto, string companyUserId);
        Task<Entities.JpCertificate> CreateJpCertificateAsync(CreateJpCertificateDto dto, string companyUserId);
        Task<Entities.KwCertificate> CreateKwCertificateAsync(CreateKwCertificateDto dto, string companyUserId);
        Task<Entities.MyCertificate> CreateMyCertificateAsync(CreateMyCertificateDto dto, string companyUserId);
        Task<Entities.NzCertificate> CreateNzCertificateAsync(CreateNzCertificateDto dto, string companyUserId);
        Task<Entities.RuCertificate> CreateRuCertificateAsync(CreateRuCertificateDto dto, string companyUserId);
        Task<Entities.KzCertificate> CreateKzCertificateAsync(CreateKzCertificateDto dto, string companyUserId);
        Task<Entities.TwCertificate> CreateTwCertificateAsync(CreateTwCertificateDto dto, string companyUserId);
        Task<Entities.UaCertificate> CreateUaCertificateAsync(CreateUaCertificateDto dto, string companyUserId);
        Task<Entities.UkCertificate> CreateUkCertificateAsync(CreateUkCertificateDto dto, string companyUserId);
        Task<Entities.UsaCertificate> CreateUsaCertificateAsync(CreateUsaCertificateDto dto, string companyUserId);
        Task<Entities.IlCertificate> CreateIlCertificateAsync(CreateIlCertificateDto dto, string companyUserId);
        Task<Entities.MvCertificate> CreateMvCertificateAsync(MvCertificateDto dto, string companyUserId);
        Task<Entities.CaCertificate> CreateCaCertificateAsync(DTO.CaCertificate.CaCertificateDto dto, string companyUserId);
        Task<Entities.SaCertificate> CreateSaCertificateAsync(DTO.SaCertificate.SaCertificateDto dto, string companyUserId);
        Task<Entities.ZaCertificate> CreateZaCertificateAsync(DTO.ZaCertificate.ZaCertificateDto dto, string companyUserId);
    }
}

