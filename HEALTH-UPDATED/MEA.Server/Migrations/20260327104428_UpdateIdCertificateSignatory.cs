using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateIdCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CertifiedName",
                table: "IdCertificates");

            migrationBuilder.DropColumn(
                name: "CertifiedPosition",
                table: "IdCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "IdCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "IdCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "IdCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_IdCertificates_SignatoryUserId",
                table: "IdCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_IdCertificates_AspNetUsers_SignatoryUserId",
                table: "IdCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_IdCertificates_AspNetUsers_SignatoryUserId",
                table: "IdCertificates");

            migrationBuilder.DropIndex(
                name: "IX_IdCertificates_SignatoryUserId",
                table: "IdCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "IdCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "IdCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "IdCertificates");

            migrationBuilder.AddColumn<string>(
                name: "CertifiedName",
                table: "IdCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CertifiedPosition",
                table: "IdCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);
        }
    }
}
