using MEA.Server.Entities;

namespace MEA.Server.Repositories
{
    public interface ICertificateRequestRepository
    {
        Task<IEnumerable<CertificateRequest>> GetAllAsync();
        Task<IEnumerable<CertificateRequest>> GetByUserIdAsync(string userId);
        Task<CertificateRequest?> GetByIdAsync(int id);
        Task<bool> ExistsByReferenceNumberAsync(string referenceNumber);
        Task<string> GenerateUniqueReferenceNumberAsync(CertificateType type, int? countryId = null, string? countryCode = null);
        Task<CertificateRequest> CreateAsync(CertificateRequest request);
        Task<CertificateRequest> UpdateAsync(CertificateRequest request);
        Task<bool> BelongsToUserAsync(int id, string userId);
    }
}

