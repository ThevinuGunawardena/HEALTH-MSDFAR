using MEA.Server.Data;
using MEA.Server.Entities;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [Authorize]
    public class UserController : ControllerBase
    {
        private readonly UserManager<AppUser> _userManager;
        private readonly RoleManager<IdentityRole> _roleManager;
        private readonly AppDbContext _dbContext;

        public UserController(UserManager<AppUser> userManager, RoleManager<IdentityRole> roleManager, AppDbContext dbContext)
        {
            _userManager = userManager;
            _roleManager = roleManager;
            _dbContext = dbContext;
        }

        [Authorize(Roles = "Admin,User")]
        [HttpGet("roles")]
        public IActionResult GetRoles()
        {
            var roles = _roleManager.Roles
                .OrderBy(r => r.Name)
                .Select(r => new
                {
                    id = r.Id,
                    name = r.Name ?? string.Empty
                })
                .ToList();

            return Ok(roles);
        }

        [Authorize(Roles = "Admin,User,Company")]
        [HttpGet("users")]
        public async Task<IActionResult> GetAllUsers()
        {
            var users = await _userManager.Users
                .AsNoTracking()
                .Select(u => new
                {
                    u.Id,
                    u.FullName,
                    u.Email,
                    u.PhoneNumber,
                    u.CompanyId,
                    u.Qualification,
                    u.LockoutEnd
                })
                .ToListAsync();

            var userRoleEntries = await _dbContext.UserRoles.AsNoTracking().ToListAsync();
            var userRoleMap = userRoleEntries
                .GroupBy(ur => ur.UserId)
                .ToDictionary(g => g.Key, g => g.First().RoleId);

            var roleIds = userRoleMap.Values.Distinct().ToList();

            var companyIds = users
                .Where(u => u.CompanyId.HasValue)
                .Select(u => u.CompanyId!.Value)
                .Distinct()
                .ToList();

            var roleMap = await _roleManager.Roles
                .AsNoTracking()
                .Where(r => roleIds.Contains(r.Id))
                .Select(r => new { r.Id, r.Name })
                .ToDictionaryAsync(r => r.Id, r => r.Name ?? string.Empty);

            var companyMap = await _dbContext.Companies
                .AsNoTracking()
                .Where(c => companyIds.Contains(c.Id))
                .Select(c => new { c.Id, c.CompanyName })
                .ToDictionaryAsync(c => c.Id, c => c.CompanyName);

            var userList = users.Select(u =>
            {
                var roleId = userRoleMap.TryGetValue(u.Id, out var rid) ? rid : null;
                var roleName = roleId != null && roleMap.TryGetValue(roleId, out var rname) ? rname : string.Empty;
                return new
                {
                    id = u.Id,
                    name = u.FullName,
                    email = u.Email,
                    phone = u.PhoneNumber ?? string.Empty,
                    roleId,
                    roleName,
                    companyId = u.CompanyId,
                    companyName = u.CompanyId.HasValue && companyMap.TryGetValue(u.CompanyId.Value, out var cname) ? cname : string.Empty,
                    qualification = u.Qualification ?? string.Empty,
                    isActive = u.LockoutEnd == null || u.LockoutEnd <= DateTimeOffset.UtcNow
                };
            }).ToList();

            return Ok(userList);
        }

        [Authorize(Roles = "Admin")]
        [HttpPost("saveuser")]
        public async Task<IActionResult> CreateUser([FromBody] UserCreateModel model)
        {
            var existingUser = await _userManager.FindByEmailAsync(model.Email);
            if (existingUser != null)
            {
                return BadRequest(new { Message = "Email already exists" });
            }

            var selectedRole = await _roleManager.FindByIdAsync(model.RoleId);
            if (selectedRole == null)
            {
                return BadRequest(new { Message = "Invalid role id" });
            }

            string companyName = string.Empty;
            if (model.CompanyId.HasValue)
            {
                var companyExists = await _dbContext.Companies
                    .AsNoTracking()
                    .AnyAsync(c => c.Id == model.CompanyId.Value);

                if (!companyExists)
                {
                    return BadRequest(new { Message = "Invalid company id" });
                }

                companyName = await _dbContext.Companies
                    .AsNoTracking()
                    .Where(c => c.Id == model.CompanyId.Value)
                    .Select(c => c.CompanyName)
                    .FirstAsync();
            }

            var user = new AppUser
            {
                FullName = model.FullName,
                Email = model.Email,
                UserName = model.Email,
                PhoneNumber = model.Phone,
                CompanyId = model.CompanyId,
                Qualification = model.Qualification
            };

            var defaultPassword = model.Password ?? "User@123";
            var result = await _userManager.CreateAsync(user, defaultPassword);
            
            if (!result.Succeeded)
            {
                return BadRequest(new
                {
                    Message = "User creation failed",
                    Errors = result.Errors.Select(e => e.Description)
                });
            }

            var roleAssignmentResult = await _userManager.AddToRoleAsync(user, selectedRole.Name ?? string.Empty);
            if (!roleAssignmentResult.Succeeded)
            {
                return BadRequest(new
                {
                    Message = "Role assignment failed",
                    Errors = roleAssignmentResult.Errors.Select(e => e.Description)
                });
            }

            return Ok(new
            {
                id = user.Id,
                name = user.FullName,
                email = user.Email,
                phone = user.PhoneNumber,
                roleId = selectedRole.Id,
                roleName = selectedRole.Name ?? string.Empty,
                companyId = user.CompanyId,
                companyName,
                qualification = user.Qualification,
                isActive = true
            });
        }

        [Authorize(Roles = "Admin")]
        [HttpPut("updateuser/{id}")]
        public async Task<IActionResult> UpdateUser(string id, [FromBody] UserUpdateModel model)
        {
            var user = await _userManager.FindByIdAsync(id);
            if (user == null)
            {
                return NotFound(new { Message = "User not found" });
            }

            // Check if email is being changed and if it already exists
            if (user.Email != model.Email)
            {
                var existingUser = await _userManager.FindByEmailAsync(model.Email);
                if (existingUser != null && existingUser.Id != id)
                {
                    return BadRequest(new { Message = "Email already exists" });
                }
            }

            var selectedRole = await _roleManager.FindByIdAsync(model.RoleId);
            if (selectedRole == null)
            {
                return BadRequest(new { Message = "Invalid role id" });
            }

            string companyName = string.Empty;
            if (model.CompanyId.HasValue)
            {
                var companyExists = await _dbContext.Companies
                    .AsNoTracking()
                    .AnyAsync(c => c.Id == model.CompanyId.Value);

                if (!companyExists)
                {
                    return BadRequest(new { Message = "Invalid company id" });
                }

                companyName = await _dbContext.Companies
                    .AsNoTracking()
                    .Where(c => c.Id == model.CompanyId.Value)
                    .Select(c => c.CompanyName)
                    .FirstAsync();
            }

            user.FullName = model.FullName;
            user.Email = model.Email;
            user.UserName = model.Email;
            user.PhoneNumber = model.Phone;
            user.CompanyId = model.CompanyId;
            user.Qualification = model.Qualification;

            var currentRoles = await _userManager.GetRolesAsync(user);
            if (currentRoles.Any())
            {
                var removeRolesResult = await _userManager.RemoveFromRolesAsync(user, currentRoles);
                if (!removeRolesResult.Succeeded)
                {
                    return BadRequest(new
                    {
                        Message = "Failed to clear existing roles",
                        Errors = removeRolesResult.Errors.Select(e => e.Description)
                    });
                }
            }

            var addRoleResult = await _userManager.AddToRoleAsync(user, selectedRole.Name ?? string.Empty);
            if (!addRoleResult.Succeeded)
            {
                return BadRequest(new
                {
                    Message = "Role assignment failed",
                    Errors = addRoleResult.Errors.Select(e => e.Description)
                });
            }

            // Handle password update if provided
            if (!string.IsNullOrEmpty(model.Password))
            {
                user.PasswordHash = _userManager.PasswordHasher.HashPassword(user, model.Password);
            }

            var result = await _userManager.UpdateAsync(user);
            
            if (!result.Succeeded)
            {
                return BadRequest(new
                {
                    Message = "User update failed",
                    Errors = result.Errors.Select(e => e.Description)
                });
            }

            return Ok(new
            {
                id = user.Id,
                name = user.FullName,
                email = user.Email,
                phone = user.PhoneNumber,
                roleId = selectedRole.Id,
                roleName = selectedRole.Name ?? string.Empty,
                companyId = user.CompanyId,
                companyName,
                qualification = user.Qualification,
                isActive = user.LockoutEnd == null || user.LockoutEnd <= DateTimeOffset.UtcNow
            });
        }

        [Authorize(Roles = "Admin")]
        [HttpDelete("deleteuser/{id}")]
        public async Task<IActionResult> DeleteUser(string id)
        {
            var user = await _userManager.FindByIdAsync(id);
            if (user == null)
            {
                return NotFound(new { Message = "User not found" });
            }

            var result = await _userManager.DeleteAsync(user);
            
            if (!result.Succeeded)
            {
                return BadRequest(new
                {
                    Message = "User deletion failed",
                    Errors = result.Errors.Select(e => e.Description)
                });
            }

            return Ok(new { Message = "User deleted successfully" });
        }
    }

    public class UserCreateModel
    {
        public required string FullName { get; set; }
        public required string Email { get; set; }
        public required string Phone { get; set; }
        public required string RoleId { get; set; }
        public int? CompanyId { get; set; }
        public string Qualification { get; set; } = string.Empty;
        public string? Password { get; set; }
    }

    public class UserUpdateModel
    {
        public required string FullName { get; set; }
        public required string Email { get; set; }
        public required string Phone { get; set; }
        public required string RoleId { get; set; }
        public int? CompanyId { get; set; }
        public string Qualification { get; set; } = string.Empty;
        public string? Password { get; set; }
    }
}

