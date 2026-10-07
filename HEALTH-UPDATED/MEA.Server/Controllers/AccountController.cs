using System.Security.Claims;
using MEA.Server.Data;
using MEA.Server.Entities;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;

namespace MEA.Server.Controllers
{
    [ApiController]
    [Route("api")]
    [Authorize]
    public class AccountController : ControllerBase
    {
        private readonly UserManager<AppUser> _userManager;
        private readonly AppDbContext _dbContext;

        public AccountController(UserManager<AppUser> userManager, AppDbContext dbContext)
        {
            _userManager = userManager;
            _dbContext = dbContext;
        }

        [HttpGet("UserProfile")]
        public async Task<IActionResult> GetUserProfile()
        {
            string? userID = User.FindFirstValue("UserId");
            if (string.IsNullOrEmpty(userID))
            {
                return Unauthorized();
            }

            var userDetails = await _userManager.FindByIdAsync(userID);
            if (userDetails == null)
            {
                return NotFound();
            }

            var roles = await _userManager.GetRolesAsync(userDetails);
            var companyName = string.Empty;

            if (userDetails.CompanyId.HasValue)
            {
                companyName = await _dbContext.Companies
                    .AsNoTracking()
                    .Where(c => c.Id == userDetails.CompanyId.Value)
                    .Select(c => c.CompanyName)
                    .FirstOrDefaultAsync() ?? string.Empty;
            }

            return Ok(
                new
                {
                    Email = userDetails.Email,
                    FullName = userDetails.FullName,
                    Phone = userDetails.PhoneNumber ?? string.Empty,
                    RoleName = roles.FirstOrDefault() ?? string.Empty,
                    Qualification = userDetails.Qualification ?? string.Empty,
                    CompanyId = userDetails.CompanyId,
                    CompanyName = companyName
                });
        }

        [HttpPut("UserProfile")]
        public async Task<IActionResult> UpdateUserProfile([FromBody] UserProfileUpdateDto model)
        {
            string? userID = User.FindFirstValue("UserId");
            if (string.IsNullOrEmpty(userID))
            {
                return Unauthorized();
            }

            var user = await _userManager.FindByIdAsync(userID);
            if (user == null)
            {
                return NotFound();
            }

            user.FullName = model.FullName ?? user.FullName;
            user.PhoneNumber = model.Phone ?? user.PhoneNumber;
            user.Qualification = model.Qualification ?? user.Qualification;

            if (!string.IsNullOrEmpty(model.Email) && model.Email != user.Email)
            {
                user.Email = model.Email;
                user.UserName = model.Email; // assuming username tracks email
            }

            var result = await _userManager.UpdateAsync(user);
            if (result.Succeeded)
            {
                return Ok(new { message = "Profile updated successfully." });
            }

            return BadRequest(result.Errors);
        }
        [HttpPut("UpdatePassword")]
        public async Task<IActionResult> UpdatePassword([FromBody] UserPasswordUpdateDto model)
        {
            string? userID = User.FindFirstValue("UserId");
            if (string.IsNullOrEmpty(userID))
            {
                return Unauthorized();
            }

            var user = await _userManager.FindByIdAsync(userID);
            if (user == null)
            {
                return NotFound();
            }

            var result = await _userManager.ChangePasswordAsync(user, model.CurrentPassword, model.NewPassword);
            if (result.Succeeded)
            {
                return Ok(new { message = "Password updated successfully." });
            }

            return BadRequest(result.Errors);
        }
    }

    public class UserProfileUpdateDto
    {
        public string? FullName { get; set; }
        public string? Email { get; set; }
        public string? Phone { get; set; }
        public string? Qualification { get; set; }
    }

    public class UserPasswordUpdateDto
    {
        public string CurrentPassword { get; set; } = string.Empty;
        public string NewPassword { get; set; } = string.Empty;
    }
}

