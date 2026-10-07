using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateHkCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "OfficerNamePosition",
                table: "HkCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "HkCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "HkCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "HkCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_HkCertificates_SignatoryUserId",
                table: "HkCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_HkCertificates_AspNetUsers_SignatoryUserId",
                table: "HkCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_HkCertificates_AspNetUsers_SignatoryUserId",
                table: "HkCertificates");

            migrationBuilder.DropIndex(
                name: "IX_HkCertificates_SignatoryUserId",
                table: "HkCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "HkCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "HkCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "HkCertificates");

            migrationBuilder.AddColumn<string>(
                name: "OfficerNamePosition",
                table: "HkCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);
        }
    }
}
