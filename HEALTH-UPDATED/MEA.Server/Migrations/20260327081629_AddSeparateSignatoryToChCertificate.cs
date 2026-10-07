using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddSeparateSignatoryToChCertificate : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "VeterinarySignature",
                table: "ChCertificates",
                newName: "SignatoryName");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "ChCertificates",
                type: "nvarchar(1000)",
                maxLength: 1000,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "ChCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "ChCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "ChCertificates");

            migrationBuilder.RenameColumn(
                name: "SignatoryName",
                table: "ChCertificates",
                newName: "VeterinarySignature");
        }
    }
}
