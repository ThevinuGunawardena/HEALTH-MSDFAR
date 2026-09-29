using MEA.Server.Entities;
using MEA.Server.Repositories;
using MEA.Server.Data;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [Authorize]
    public class CompanyController : ControllerBase
    {
        private readonly ICompanyRepository _companyRepository;
        private readonly AppDbContext _dbContext;

        public CompanyController(
            ICompanyRepository companyRepository,
            AppDbContext dbContext)
        {
            _companyRepository = companyRepository;
            _dbContext = dbContext;
        }

        [HttpGet("companies")]
        [Authorize(Roles = "Admin,Company,User")]
        public async Task<IActionResult> GetCompanies()
        {
            var companies = await _companyRepository.GetAllAsync();
            return Ok(companies);
        }

        [HttpGet("listed-countries")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> GetListedCountries()
        {
            var listedCountries = await _dbContext.ListedCountries
                .AsNoTracking()
                .OrderBy(x => x.Name)
                .Select(x => new { x.Id, x.Name })
                .ToListAsync();

            return Ok(listedCountries);
        }

        [HttpGet("product-certificates")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> GetProductCertificates()
        {
            var certificates = await _dbContext.ProductCertificates
                .AsNoTracking()
                .OrderBy(x => x.Name)
                .Select(x => new { x.Id, x.Name })
                .ToListAsync();

            return Ok(certificates);
        }

        [HttpGet("company-statuses")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> GetCompanyStatuses()
        {
            var statuses = await _dbContext.CompanyStatuses
                .AsNoTracking()
                .OrderBy(x => x.Name)
                .Select(x => new { x.Id, x.Name })
                .ToListAsync();

            return Ok(statuses);
        }

        [HttpPost("savecompanies")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> CreateCompany([FromBody] CompanyCreateModel model)
        {
            var (companyStatusId, listedCountryId, productCertificateId, validationError) = await ResolveCompanyLookupIdsAsync(model);
            if (!string.IsNullOrWhiteSpace(validationError))
            {
                return BadRequest(new { Message = validationError });
            }

            var existingCompany = await _companyRepository.ExistsByEmailOrRegistrationNoAsync(
                model.CompanyEmail, 
                model.RegistrationNo);
            if (existingCompany)
            {
                return BadRequest(new { Message = "Company already exists" });
            }

            var company = new Company
            {
                CompanyName = model.CompanyName,
                CompanyEmail = model.CompanyEmail,
                CompanyPhone = model.CompanyPhone,
                CompanyAddress = model.CompanyAddress,
                RegistrationNo = model.RegistrationNo,
                CompanyStatusId = companyStatusId,
                ListedCountryId = listedCountryId,
                ProductCertificateId = productCertificateId,
                UserId = null
            };

            await _companyRepository.CreateAsync(company);

            return Ok(new
            {
                Company = company
            });
        }

        [HttpPut("updatecompanies/{id}")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> UpdateCompany(int id, [FromBody] CompanyCreateModel model)
        {
            var (companyStatusId, listedCountryId, productCertificateId, validationError) = await ResolveCompanyLookupIdsAsync(model);
            if (!string.IsNullOrWhiteSpace(validationError))
            {
                return BadRequest(new { Message = validationError });
            }

            var company = await _companyRepository.GetByIdAsync(id);
            if (company == null)
            {
                return NotFound(new { Message = "Company not found" });
            }

            var duplicateEmail = await _companyRepository.ExistsByEmailOrRegistrationNoAsync(
                model.CompanyEmail, 
                model.RegistrationNo, 
                id);
            if (duplicateEmail)
            {
                return BadRequest(new { Message = "Email or Registration number already exists" });
            }

            company.CompanyName = model.CompanyName;
            company.CompanyEmail = model.CompanyEmail;
            company.CompanyPhone = model.CompanyPhone;
            company.CompanyAddress = model.CompanyAddress;
            company.RegistrationNo = model.RegistrationNo;
            company.CompanyStatusId = companyStatusId;
            company.ListedCountryId = listedCountryId;
            company.ProductCertificateId = productCertificateId;

            await _companyRepository.UpdateAsync(company);

            return Ok(new { Company = company });
        }

        private async Task<(int companyStatusId, int listedCountryId, int? productCertificateId, string? error)> ResolveCompanyLookupIdsAsync(CompanyCreateModel model)
        {
            var companyStatus = await _dbContext.CompanyStatuses
                .AsNoTracking()
                .FirstOrDefaultAsync(x => x.Id == model.Status);

            if (companyStatus == null)
            {
                return (0, 0, null, "Invalid company status");
            }

            var listedCountry = await _dbContext.ListedCountries
                .AsNoTracking()
                .FirstOrDefaultAsync(x => x.Id == model.ListedCountry);

            if (listedCountry == null)
            {
                return (0, 0, null, "Invalid listed country");
            }

            int? productCertificateId = null;
            if (model.ProductCertificate.HasValue)
            {
                var productCertificate = await _dbContext.ProductCertificates
                    .AsNoTracking()
                    .FirstOrDefaultAsync(x => x.Id == model.ProductCertificate.Value);

                if (productCertificate == null)
                {
                    return (0, 0, null, "Invalid product certificate");
                }

                productCertificateId = productCertificate.Id;
            }

            return (companyStatus.Id, listedCountry.Id, productCertificateId, null);
        }

        [HttpDelete("deletecompanies/{id}")]
        [Authorize(Roles = "Admin")]
        public async Task<IActionResult> DeleteCompany(int id)
        {
            var company = await _companyRepository.GetByIdAsync(id);
            if (company == null)
            {
                return NotFound(new { Message = "Company not found" });
            }

            await _companyRepository.DeleteAsync(id);

            return Ok(new { Message = "Company deleted successfully" });
        }
    }

    public class CompanyCreateModel
    {
        public required string CompanyName { get; set; }
        public required string CompanyEmail { get; set; }
        public required string CompanyPhone { get; set; }
        public required string CompanyAddress { get; set; }
        public required string RegistrationNo { get; set; }
        public int? ProductCertificate { get; set; }
        public required int Status { get; set; }
        public required int ListedCountry { get; set; }
    }
}

