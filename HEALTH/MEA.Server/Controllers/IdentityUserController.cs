using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;
using MEA.Server.Data;
using MEA.Server.Entities;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;
using Microsoft.IdentityModel.Tokens;

namespace MEA.Server.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class IdentityUserController : ControllerBase
    {
        private readonly UserManager<AppUser> _userManager;
        private readonly RoleManager<IdentityRole> _roleManager;
        private readonly IOptions<AppSettings> _appSettings;
        private readonly AppDbContext _dbContext;

        public IdentityUserController(
            UserManager<AppUser> userManager,
            RoleManager<IdentityRole> roleManager,
            IOptions<AppSettings> appSettings,
            AppDbContext dbContext)
        {
            _userManager = userManager;
            _roleManager = roleManager;
            _appSettings = appSettings;
            _dbContext = dbContext;
        }

        [HttpPost("signup")]
        [AllowAnonymous]
        public async Task<IActionResult> CreateUser([FromBody] UserRegistrationModel model)
        {
            var user = new AppUser()
            {
                FullName = model.FullName,
                Email = model.Email,
                UserName = model.Email
            };

            var result = await _userManager.CreateAsync(user, model.Password);
            if (!result.Succeeded)
            {
                return BadRequest(result.Errors);
            }

            var assignedRole = string.IsNullOrWhiteSpace(model.Role) ? "User" : model.Role;
            await _userManager.AddToRoleAsync(user, assignedRole);

            return Ok(new { Message = "User created successfully" });
        }

        [HttpPost("signin")]
        [AllowAnonymous]
        public async Task<IActionResult> SignIn([FromBody] LoginModel loginModel)
        {
            var user = await _userManager.FindByEmailAsync(loginModel.Email);
            if (user != null && await _userManager.CheckPasswordAsync(user, loginModel.Password))
            {
                var roles = await _userManager.GetRolesAsync(user);
                var role = roles.FirstOrDefault() ?? string.Empty;
                var signInKey = new SymmetricSecurityKey(
                    Encoding.UTF8.GetBytes(_appSettings.Value.JWTSecret)
                );
                ClaimsIdentity claims = new ClaimsIdentity(new Claim[]
                {
                    new Claim("UserId", user.Id.ToString()),
                    new Claim(ClaimTypes.Email, user.Email ?? string.Empty),
                    new Claim(ClaimTypes.Name, user.FullName ?? string.Empty),
                    new Claim(ClaimTypes.Role, role),
                    new Claim("role", role), // Add simpler role claim for easier frontend access
                });
                var tokenDescriptor = new SecurityTokenDescriptor
                {
                    Subject = claims,
                    Expires = DateTime.UtcNow.AddHours(24),
                    SigningCredentials = new SigningCredentials(
                        signInKey,
                        SecurityAlgorithms.HmacSha256Signature
                    )
                };
                var tokenHandler = new JwtSecurityTokenHandler();
                var securityToken = tokenHandler.CreateToken(tokenDescriptor);
                var token = tokenHandler.WriteToken(securityToken);
                
                return Ok(new 
                { 
                    token,
                    email = user.Email ?? string.Empty,
                    userId = user.Id,
                    role = role,
                    name = user.FullName ?? string.Empty
                });
            }
            else
            {
                return BadRequest(new { Message = "Invalid login attempt" });
            }
        }

        [HttpPost("sso-login")]
        [AllowAnonymous]
        public async Task<IActionResult> SsoLogin([FromBody] SsoLoginModel model)
        {
            if (string.IsNullOrWhiteSpace(model?.Token))
            {
                return BadRequest(new { Message = "SSO token is required" });
            }

            try
            {
                var tokenHandler = new JwtSecurityTokenHandler();
                var key = Encoding.UTF8.GetBytes(_appSettings.Value.JWTSecret);

                var validationParameters = new TokenValidationParameters
                {
                    ValidateIssuerSigningKey = true,
                    IssuerSigningKey = new SymmetricSecurityKey(key),
                    ValidateIssuer = false,
                    ValidateAudience = false,
                    ValidateLifetime = true,
                    ClockSkew = TimeSpan.FromMinutes(5)
                };

                ClaimsPrincipal principal;
                try
                {
                    principal = tokenHandler.ValidateToken(model.Token, validationParameters, out var validatedToken);
                }
                catch (Exception ex)
                {
                    return Unauthorized(new { Message = "Invalid or expired SSO token: " + ex.Message });
                }

                var email = principal.FindFirst(ClaimTypes.Email)?.Value 
                    ?? principal.FindFirst("email")?.Value 
                    ?? principal.FindFirst(JwtRegisteredClaimNames.Sub)?.Value;

                if (string.IsNullOrWhiteSpace(email))
                {
                    return BadRequest(new { Message = "SSO token does not contain a valid email claim" });
                }

                var name = principal.FindFirst(ClaimTypes.Name)?.Value 
                    ?? principal.FindFirst("name")?.Value 
                    ?? email;

                var role = principal.FindFirst(ClaimTypes.Role)?.Value 
                    ?? principal.FindFirst("role")?.Value 
                    ?? "Company";

                // Ensure user exists in AspNetUsers
                var user = await _userManager.FindByEmailAsync(email);
                if (user == null)
                {
                    user = new AppUser
                    {
                        UserName = email,
                        Email = email,
                        FullName = name,
                        EmailConfirmed = true
                    };
                    var createResult = await _userManager.CreateAsync(user, "SsoPass@" + Guid.NewGuid().ToString("N").Substring(0, 8) + "1!");
                    if (!createResult.Succeeded)
                    {
                        return BadRequest(createResult.Errors);
                    }

                    if (!await _roleManager.RoleExistsAsync(role))
                    {
                        await _roleManager.CreateAsync(new IdentityRole(role));
                    }
                    await _userManager.AddToRoleAsync(user, role);
                }
                else
                {
                    if (!string.IsNullOrWhiteSpace(name) && user.FullName != name)
                    {
                        user.FullName = name;
                        await _userManager.UpdateAsync(user);
                    }
                }

                var userRoles = await _userManager.GetRolesAsync(user);
                if (userRoles.Count == 0)
                {
                    if (!await _roleManager.RoleExistsAsync(role))
                    {
                        await _roleManager.CreateAsync(new IdentityRole(role));
                    }
                    await _userManager.AddToRoleAsync(user, role);
                    userRoles = await _userManager.GetRolesAsync(user);
                }
                var assignedRole = userRoles.FirstOrDefault() ?? role;

                var companyName = principal.FindFirst("companyName")?.Value;
                var companyRegNo = principal.FindFirst("companyRegNo")?.Value;

                // Auto-link or provision Company record in MEA database if applicable
                if (assignedRole == "Company" && !string.IsNullOrWhiteSpace(companyName) && !user.CompanyId.HasValue)
                {
                    var existingCompany = await _dbContext.Companies.FirstOrDefaultAsync(c =>
                        c.CompanyName == companyName ||
                        (!string.IsNullOrEmpty(companyRegNo) && c.RegistrationNo == companyRegNo));

                    if (existingCompany != null)
                    {
                        user.CompanyId = existingCompany.Id;
                        if (string.IsNullOrEmpty(existingCompany.UserId))
                        {
                            existingCompany.UserId = user.Id;
                        }
                        await _userManager.UpdateAsync(user);
                        await _dbContext.SaveChangesAsync();
                    }
                    else
                    {
                        var defaultStatus = await _dbContext.CompanyStatuses.FirstOrDefaultAsync();
                        var defaultCountry = await _dbContext.ListedCountries.FirstOrDefaultAsync();
                        var newCompany = new Company
                        {
                            CompanyName = companyName,
                            RegistrationNo = !string.IsNullOrEmpty(companyRegNo) ? companyRegNo : "REG-" + user.Id.Substring(0, Math.Min(8, user.Id.Length)),
                            CompanyEmail = email,
                            CompanyPhone = "000-0000000",
                            CompanyAddress = "Sri Lanka",
                            CompanyStatusId = defaultStatus?.Id ?? 1,
                            ListedCountryId = defaultCountry?.Id ?? 1,
                            UserId = user.Id
                        };
                        _dbContext.Companies.Add(newCompany);
                        await _dbContext.SaveChangesAsync();
                        user.CompanyId = newCompany.Id;
                        await _userManager.UpdateAsync(user);
                    }
                }

                // Issue application session JWT
                var signInKey = new SymmetricSecurityKey(key);
                var claims = new ClaimsIdentity(new Claim[]
                {
                    new Claim("UserId", user.Id.ToString()),
                    new Claim(ClaimTypes.Email, user.Email ?? string.Empty),
                    new Claim(ClaimTypes.Name, user.FullName ?? string.Empty),
                    new Claim(ClaimTypes.Role, assignedRole),
                    new Claim("role", assignedRole),
                    new Claim("CompanyId", user.CompanyId?.ToString() ?? string.Empty)
                });

                var tokenDescriptor = new SecurityTokenDescriptor
                {
                    Subject = claims,
                    Expires = DateTime.UtcNow.AddHours(24),
                    SigningCredentials = new SigningCredentials(
                        signInKey,
                        SecurityAlgorithms.HmacSha256Signature
                    )
                };

                var securityToken = tokenHandler.CreateToken(tokenDescriptor);
                var appToken = tokenHandler.WriteToken(securityToken);

                return Ok(new
                {
                    token = appToken,
                    email = user.Email ?? string.Empty,
                    userId = user.Id,
                    role = assignedRole,
                    name = user.FullName ?? string.Empty,
                    companyId = user.CompanyId,
                    companyName = companyName
                });
            }
            catch (Exception ex)
            {
                return StatusCode(500, new { Message = "SSO authentication failed: " + ex.Message });
            }
        }
    }

    public class SsoLoginModel
    {
        public required string Token { get; set; }
    }

    public class UserRegistrationModel
    {
        public required string FullName { get; set; }
        public required string Email { get; set; }
        public required string Password { get; set; }
        public required string Role { get; set; }
    }

    public class LoginModel
    {
        public required string Email { get; set; }
        public required string Password { get; set; }
    }
}
