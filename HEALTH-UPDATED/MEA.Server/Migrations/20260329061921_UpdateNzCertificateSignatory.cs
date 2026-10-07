using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateNzCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CertifyingOfficialName",
                table: "NzCertificates");

            migrationBuilder.DropColumn(
                name: "Signature",
                table: "NzCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "NzCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "NzCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "NzCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_NzCertificates_SignatoryUserId",
                table: "NzCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_NzCertificates_AspNetUsers_SignatoryUserId",
                table: "NzCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_NzCertificates_AspNetUsers_SignatoryUserId",
                table: "NzCertificates");

            migrationBuilder.DropIndex(
                name: "IX_NzCertificates_SignatoryUserId",
                table: "NzCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "NzCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "NzCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "NzCertificates");

            migrationBuilder.AddColumn<string>(
                name: "CertifyingOfficialName",
                table: "NzCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "Signature",
                table: "NzCertificates",
                type: "nvarchar(max)",
                nullable: true);
        }
    }
}
