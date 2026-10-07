using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Text;
using MEA.Server.Entities;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Options;
using Microsoft.IdentityModel.Tokens;

namespace MEA.Server.Endpoints
{
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

    public class SsoLoginModel
    {
        public required string Token { get; set; }
    }

    public static class IdentityUserEndpoints
    {
        public static IEndpointRouteBuilder MapIdentityUserEndpoints(this IEndpointRouteBuilder app)
        {
            app.MapPost("/signup", CreateUser);
            app.MapPost("/signin", SignIn);
            app.MapPost("/sso-login", SsoLogin);
            return app;
        }

        [AllowAnonymous]
        private static async Task<IResult> CreateUser(
            UserManager<AppUser> userManager,
            [FromBody] UserRegistrationModel model)
        {
            AppUser user = new AppUser()
            {
                FullName = model.FullName,
                Email = model.Email,
                UserName = model.Email
            };

            var result = await userManager.CreateAsync(user, model.Password);
            if (result.Succeeded)
            {
                await userManager.AddToRoleAsync(user, model.Role);
                return Results.Ok(new { Message = "User created successfully" });
            }
            return Results.BadRequest(result.Errors);
        }

        [AllowAnonymous]
        private static async Task<IResult> SignIn(
            UserManager<AppUser> userManager,
            [FromBody] LoginModel loginmodel,
            IOptions<AppSettings> appSettings)
        {
            var user = await userManager.FindByEmailAsync(loginmodel.Email);
            if (user != null && await userManager.CheckPasswordAsync(user, loginmodel.Password))
            {
                var roles = await userManager.GetRolesAsync(user);
                var role = roles.FirstOrDefault() ?? "User";
                var signInKey = new SymmetricSecurityKey(
                    Encoding.UTF8.GetBytes(appSettings.Value.JWTSecret)
                );
                ClaimsIdentity claims = new ClaimsIdentity(new Claim[]
                {
                    new Claim("UserId", user.Id.ToString()),
                    new Claim(ClaimTypes.Email, user.Email ?? string.Empty),
                    new Claim(ClaimTypes.Name, user.FullName ?? string.Empty),
                    new Claim(ClaimTypes.Role, role),
                    new Claim("role", role)
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
                return Results.Ok(new 
                { 
                    token,
                    email = user.Email ?? string.Empty,
                    userId = user.Id,
                    role = role,
                    name = user.FullName ?? string.Empty
                });
            }
            return Results.BadRequest(new { Message = "Invalid login attempt" });
        }

        [AllowAnonymous]
        private static async Task<IResult> SsoLogin(
            UserManager<AppUser> userManager,
            [FromBody] SsoLoginModel model,
            IOptions<AppSettings> appSettings)
        {
            if (string.IsNullOrWhiteSpace(model?.Token))
            {
                return Results.BadRequest(new { Message = "SSO token is required" });
            }

            try
            {
                var tokenHandler = new JwtSecurityTokenHandler();
                var key = Encoding.UTF8.GetBytes(appSettings.Value.JWTSecret);

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
                    return Results.Unauthorized();
                }

                var email = principal.FindFirst(ClaimTypes.Email)?.Value 
                    ?? principal.FindFirst("email")?.Value 
                    ?? principal.FindFirst(JwtRegisteredClaimNames.Sub)?.Value;

                if (string.IsNullOrWhiteSpace(email))
                {
                    return Results.BadRequest(new { Message = "SSO token does not contain a valid email claim" });
                }

                var name = principal.FindFirst(ClaimTypes.Name)?.Value 
                    ?? principal.FindFirst("name")?.Value 
                    ?? email;

                var role = principal.FindFirst(ClaimTypes.Role)?.Value 
                    ?? principal.FindFirst("role")?.Value 
                    ?? "Company";

                var user = await userManager.FindByEmailAsync(email);
                if (user == null)
                {
                    user = new AppUser
                    {
                        UserName = email,
                        Email = email,
                        FullName = name,
                        EmailConfirmed = true
                    };
                    var createResult = await userManager.CreateAsync(user, "SsoPass@" + Guid.NewGuid().ToString("N").Substring(0, 8) + "1!");
                    if (!createResult.Succeeded)
                    {
                        return Results.BadRequest(createResult.Errors);
                    }

                    await userManager.AddToRoleAsync(user, role);
                }
                else
                {
                    if (!string.IsNullOrWhiteSpace(name) && user.FullName != name)
                    {
                        user.FullName = name;
                        await userManager.UpdateAsync(user);
                    }
                }

                var userRoles = await userManager.GetRolesAsync(user);
                var assignedRole = userRoles.FirstOrDefault() ?? role;

                var signInKey = new SymmetricSecurityKey(key);
                var claims = new ClaimsIdentity(new Claim[]
                {
                    new Claim("UserId", user.Id.ToString()),
                    new Claim(ClaimTypes.Email, user.Email ?? string.Empty),
                    new Claim(ClaimTypes.Name, user.FullName ?? string.Empty),
                    new Claim(ClaimTypes.Role, assignedRole),
                    new Claim("role", assignedRole)
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

                return Results.Ok(new
                {
                    token = appToken,
                    email = user.Email ?? string.Empty,
                    userId = user.Id,
                    role = assignedRole,
                    name = user.FullName ?? string.Empty
                });
            }
            catch (Exception ex)
            {
                return Results.Problem("SSO authentication failed: " + ex.Message);
            }
        }
    }
}
