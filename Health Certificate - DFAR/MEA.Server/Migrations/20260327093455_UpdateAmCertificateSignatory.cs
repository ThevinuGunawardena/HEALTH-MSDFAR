using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateAmCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "Designation",
                table: "AmCertificates",
                newName: "Qualification");

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "AmCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_AmCertificates_SignatoryUserId",
                table: "AmCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_AmCertificates_AspNetUsers_SignatoryUserId",
                table: "AmCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_AmCertificates_AspNetUsers_SignatoryUserId",
                table: "AmCertificates");

            migrationBuilder.DropIndex(
                name: "IX_AmCertificates_SignatoryUserId",
                table: "AmCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "AmCertificates");

            migrationBuilder.RenameColumn(
                name: "Qualification",
                table: "AmCertificates",
                newName: "Designation");
        }
    }
}
