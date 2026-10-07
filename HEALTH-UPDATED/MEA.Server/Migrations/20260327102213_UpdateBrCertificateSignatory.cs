using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateBrCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "VeterinarySignature",
                table: "BrCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "BrCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "BrCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "BrCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_BrCertificates_SignatoryUserId",
                table: "BrCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_BrCertificates_AspNetUsers_SignatoryUserId",
                table: "BrCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_BrCertificates_AspNetUsers_SignatoryUserId",
                table: "BrCertificates");

            migrationBuilder.DropIndex(
                name: "IX_BrCertificates_SignatoryUserId",
                table: "BrCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "BrCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "BrCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "BrCertificates");

            migrationBuilder.AddColumn<string>(
                name: "VeterinarySignature",
                table: "BrCertificates",
                type: "nvarchar(120)",
                maxLength: 120,
                nullable: false,
                defaultValue: "");
        }
    }
}
