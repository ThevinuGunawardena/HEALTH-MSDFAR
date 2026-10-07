using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateRuCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "SignatureNamePosition",
                table: "RuCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "RuCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "RuCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "RuCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_RuCertificates_SignatoryUserId",
                table: "RuCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_RuCertificates_AspNetUsers_SignatoryUserId",
                table: "RuCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_RuCertificates_AspNetUsers_SignatoryUserId",
                table: "RuCertificates");

            migrationBuilder.DropIndex(
                name: "IX_RuCertificates_SignatoryUserId",
                table: "RuCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "RuCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "RuCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "RuCertificates");

            migrationBuilder.AddColumn<string>(
                name: "SignatureNamePosition",
                table: "RuCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);
        }
    }
}
