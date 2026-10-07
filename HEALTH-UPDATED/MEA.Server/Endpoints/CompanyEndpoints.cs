using MEA.Server.Entities;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using MEA.Server.Data;

namespace MEA.Server.Endpoints
{
    public class CompanyCreateModel
    {
        public required string CompanyName { get; set; }
        public required string CompanyEmail { get; set; }
        public required string CompanyPhone { get; set; }
        public required string CompanyAddress { get; set; }
        public required string RegistrationNo { get; set; }
    }

    public static class CompanyEndpoints
    {
        public static IEndpointRouteBuilder MapCompanyEndpoints(this IEndpointRouteBuilder app)
        {
            app.MapPost("/companies", CreateCompany)
                .RequireAuthorization(policy => policy.RequireRole("Admin"));
            app.MapGet("/companies", GetCompanies)
                .RequireAuthorization(policy => policy.RequireRole("Admin"));
            app.MapPut("/companies/{id}", UpdateCompany)
                .RequireAuthorization(policy => policy.RequireRole("Admin"));
            app.MapDelete("/companies/{id}", DeleteCompany)
                .RequireAuthorization(policy => policy.RequireRole("Admin"));
            return app;
        }

        private static async Task<IResult> GetCompanies(AppDbContext dbContext)
        {
            var companies = await dbContext.Companies
                .AsNoTracking()
                .OrderBy(c => c.Id)
                .ToListAsync();

            return Results.Ok(companies);
        }

        private static async Task<IResult> CreateCompany(
            AppDbContext dbContext,
            [FromBody] CompanyCreateModel model)
        {
            var existingCompany = await dbContext.Companies
                .AsNoTracking()
                .AnyAsync(c => c.CompanyEmail == model.CompanyEmail || c.RegistrationNo == model.RegistrationNo);
            if (existingCompany)
            {
                return Results.BadRequest(new { Message = "Company already exists" });
            }

            var company = new Company
            {
                CompanyName = model.CompanyName,
                CompanyEmail = model.CompanyEmail,
                CompanyPhone = model.CompanyPhone,
                CompanyAddress = model.CompanyAddress,
                RegistrationNo = model.RegistrationNo,
                UserId = null
            };

            dbContext.Companies.Add(company);
            await dbContext.SaveChangesAsync();

            return Results.Ok(new
            {
                Company = company
            });
        }

        private static async Task<IResult> UpdateCompany(
            int id,
            AppDbContext dbContext,
            [FromBody] CompanyCreateModel model)
        {
            var company = await dbContext.Companies.FindAsync(id);
            if (company == null)
            {
                return Results.NotFound(new { Message = "Company not found" });
            }

            var duplicateEmail = await dbContext.Companies
                .AsNoTracking()
                .AnyAsync(c => c.CompanyEmail == model.CompanyEmail && c.Id != id);
            if (duplicateEmail)
            {
                return Results.BadRequest(new { Message = "Email already exists" });
            }

            var duplicateReg = await dbContext.Companies
                .AsNoTracking()
                .AnyAsync(c => c.RegistrationNo == model.RegistrationNo && c.Id != id);
            if (duplicateReg)
            {
                return Results.BadRequest(new { Message = "Registration number already exists" });
            }

            company.CompanyName = model.CompanyName;
            company.CompanyEmail = model.CompanyEmail;
            company.CompanyPhone = model.CompanyPhone;
            company.CompanyAddress = model.CompanyAddress;
            company.RegistrationNo = model.RegistrationNo;

            await dbContext.SaveChangesAsync();

            return Results.Ok(new { Company = company });
        }

        private static async Task<IResult> DeleteCompany(
            int id,
            AppDbContext dbContext)
        {
            var company = await dbContext.Companies.FindAsync(id);
            if (company == null)
            {
                return Results.NotFound(new { Message = "Company not found" });
            }

            dbContext.Companies.Remove(company);
            await dbContext.SaveChangesAsync();

            return Results.Ok(new { Message = "Company deleted successfully" });
        }
    }
}

