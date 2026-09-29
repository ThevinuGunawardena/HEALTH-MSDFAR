using MEA.Server.Controllers;
using MEA.Server.Extensions;
using MEA.Server.Data;
using MEA.Server.Repositories;
using MEA.Server.Services;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;

var builder = WebApplication.CreateBuilder(args);

builder.Services.AddControllers()
    .AddJsonOptions(options =>
    {
        options.JsonSerializerOptions.ReferenceHandler = System.Text.Json.Serialization.ReferenceHandler.IgnoreCycles;
    });
builder.Services.AddSwaggerExplorer()
                .InjectDbContext(builder.Configuration)
                .AddAppConfig(builder.Configuration)
                .AddIdentityHandlersAndStores()
                .ConfigureIdentityOption()                
                .AddIdentityAuth(builder.Configuration);

// Register Repositories
builder.Services.AddScoped<ICompanyRepository, CompanyRepository>();
builder.Services.AddScoped<ICertificateRequestRepository, CertificateRequestRepository>();
builder.Services.AddScoped<ICountryRepository, CountryRepository>();
builder.Services.AddScoped<IVetCertificateFormRepository, VetCertificateFormRepository>();

// Register Services
builder.Services.AddScoped<ICertificateService, CertificateService>();

var app = builder.Build();

// Seed database roles and users
using (var scope = app.Services.CreateScope())
{
    var services = scope.ServiceProvider;
    var context = services.GetRequiredService<AppDbContext>();
    var roleManager = services.GetRequiredService<RoleManager<IdentityRole>>();
    var userManager = services.GetRequiredService<UserManager<MEA.Server.Entities.AppUser>>();
    
    await ApplicationDbContextSeed.SeedAsync(context, roleManager, userManager);
}

app.ConfigureCORS(builder.Configuration);
app.UseHttpsRedirection();
app.ConfigureSwaggerExplorer()
    .AddIdentityAuthMiddlewares();

app.UseDefaultFiles();
app.UseStaticFiles();
app.MapStaticAssets();

app.MapControllers();
app.MapFallbackToFile("index.html");

app.Run();
