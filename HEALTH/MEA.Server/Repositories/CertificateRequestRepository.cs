using System.Security.Cryptography;
using MEA.Server.Data;
using MEA.Server.Entities;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Repositories
{
    public class CertificateRequestRepository : ICertificateRequestRepository
    {
        private readonly AppDbContext _context;

        public CertificateRequestRepository(AppDbContext context)
        {
            _context = context;
        }

        public async Task<IEnumerable<CertificateRequest>> GetAllAsync()
        {
            return await _context.CertificateRequests
                .AsNoTracking()
                .OrderByDescending(r => r.CreatedAt)
                .ToListAsync();
        }

        public async Task<IEnumerable<CertificateRequest>> GetByUserIdAsync(string userId)
        {
            return await _context.CertificateRequests
                .AsNoTracking()
                .Where(r => r.CompanyUserId == userId)
                .OrderByDescending(r => r.CreatedAt)
                .ToListAsync();
        }

        public async Task<CertificateRequest?> GetByIdAsync(int id)
        {
            return await _context.CertificateRequests.FindAsync(id);
        }

        public async Task<bool> ExistsByReferenceNumberAsync(string referenceNumber)
        {
            return await _context.CertificateRequests
                .AsNoTracking()
                .AnyAsync(r => r.ReferenceNumber == referenceNumber);
        }

        private static readonly SemaphoreSlim _sequenceLock = new SemaphoreSlim(1, 1);

        public static string GetCountryCode(string? countryName)
        {
            if (string.IsNullOrWhiteSpace(countryName)) return "GEN";
            var normalized = countryName.Trim().ToLowerInvariant();

            if (normalized == "european union" || normalized == "eu") return "EU";
            if (normalized == "hong kong" || normalized == "hongkong") return "HK";
            if (normalized == "china") return "CH";
            if (normalized == "united states" || normalized == "united states of america" || normalized == "usa") return "USA";
            if (normalized == "united kingdom" || normalized == "uk" || normalized == "great britain") return "UK";
            if (normalized == "australia") return "AU";
            if (normalized == "canada") return "CA";
            if (normalized == "russia" || normalized == "russian federation") return "RU";
            if (normalized == "japan") return "JP";
            if (normalized == "maldives") return "MV";
            if (normalized == "malaysia") return "MY";
            if (normalized == "armenia") return "AM";
            if (normalized == "brazil") return "BR";
            if (normalized == "india") return "IND";
            if (normalized == "indonesia") return "ID";
            if (normalized == "israel") return "IL";
            if (normalized == "kazakhstan" || normalized == "republic of kazakhstan") return "KZ";
            if (normalized == "kuwait") return "KW";
            if (normalized == "new zealand") return "NZ";
            if (normalized == "saudi arabia") return "SA";
            if (normalized == "south africa") return "ZA";
            if (normalized == "taiwan") return "TW";
            if (normalized == "ukraine") return "UA";

            var words = countryName.Split(new[] { ' ', '-', '_' }, StringSplitOptions.RemoveEmptyEntries);
            if (words.Length > 1)
            {
                var initials = string.Concat(words.Select(w => char.ToUpperInvariant(w[0])));
                if (initials.Length >= 2) return initials.Substring(0, Math.Min(4, initials.Length));
            }
            return countryName.Length >= 3 ? countryName.Substring(0, 3).ToUpperInvariant() : countryName.ToUpperInvariant();
        }

        public async Task<string> GenerateUniqueReferenceNumberAsync(CertificateType type, int? countryId = null, string? countryCode = null)
        {
            await _sequenceLock.WaitAsync();
            try
            {
                string code = "GEN";

                if (!string.IsNullOrWhiteSpace(countryCode))
                {
                    code = countryCode.Trim().ToUpperInvariant();
                }
                else if (type == CertificateType.EU)
                {
                    code = "EU";
                }
                else if (countryId.HasValue)
                {
                    var country = await _context.Countries.FindAsync(countryId.Value);
                    if (country != null && !string.IsNullOrWhiteSpace(country.Name))
                    {
                        code = GetCountryCode(country.Name);
                    }
                    else
                    {
                        code = "NON-EU";
                    }
                }
                else
                {
                    code = "NON-EU";
                }

                int currentYear = DateTime.UtcNow.Year;
                string prefix = $"HC-{currentYear}-{code}-";

                var existingRefs = await _context.CertificateRequests
                    .AsNoTracking()
                    .Where(r => r.ReferenceNumber != null && r.ReferenceNumber.StartsWith(prefix))
                    .Select(r => r.ReferenceNumber)
                    .ToListAsync();

                int maxSeq = 0;
                foreach (var r in existingRefs)
                {
                    if (r != null && r.Length > prefix.Length)
                    {
                        var suffix = r.Substring(prefix.Length);
                        if (int.TryParse(suffix, out int parsedSeq))
                        {
                            if (parsedSeq > maxSeq) maxSeq = parsedSeq;
                        }
                    }
                }

                int nextSeq = maxSeq + 1;
                string referenceNumber = $"{prefix}{nextSeq:D3}";

                return referenceNumber;
            }
            finally
            {
                _sequenceLock.Release();
            }
        }

        private static string IncrementLetters(string letters)
        {
            char[] chars = letters.ToCharArray();
            for (int i = chars.Length - 1; i >= 0; i--)
            {
                if (chars[i] < 'Z')
                {
                    chars[i]++;
                    return new string(chars);
                }
                else
                {
                    chars[i] = 'A';
                }
            }
            return "A" + new string(chars);
        }

        public async Task<CertificateRequest> CreateAsync(CertificateRequest request)
        {
            _context.CertificateRequests.Add(request);
            await _context.SaveChangesAsync();
            return request;
        }

        public async Task<CertificateRequest> UpdateAsync(CertificateRequest request)
        {
            _context.CertificateRequests.Update(request);
            await _context.SaveChangesAsync();
            return request;
        }

        public async Task<bool> BelongsToUserAsync(int id, string userId)
        {
            return await _context.CertificateRequests
                .AsNoTracking()
                .AnyAsync(r => r.Id == id && r.CompanyUserId == userId);
        }
    }
}

