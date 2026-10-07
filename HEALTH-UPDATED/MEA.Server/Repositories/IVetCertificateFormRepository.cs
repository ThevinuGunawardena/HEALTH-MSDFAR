using MEA.Server.Entities;

namespace MEA.Server.Repositories
{
    public interface IVetCertificateFormRepository
    {
        Task<VetCertificateForm?> GetByCertificateRequestIdAsync(int certificateRequestId);
        Task<VetCertificateForm> CreateAsync(VetCertificateForm form);
    }
}

