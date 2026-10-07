using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddCertificateTypeToAuCertificate : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CodeCNTitle",
                table: "UkCertificateProducts");

            migrationBuilder.DropColumn(
                name: "No",
                table: "UkCertificateProducts");

            migrationBuilder.AddColumn<string>(
                name: "CertificateType",
                table: "AuCertificates",
                type: "nvarchar(max)",
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CertificateType",
                table: "AuCertificates");

            migrationBuilder.AddColumn<string>(
                name: "CodeCNTitle",
                table: "UkCertificateProducts",
                type: "nvarchar(150)",
                maxLength: 150,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "No",
                table: "UkCertificateProducts",
                type: "nvarchar(30)",
                maxLength: 30,
                nullable: true);
        }
    }
}
