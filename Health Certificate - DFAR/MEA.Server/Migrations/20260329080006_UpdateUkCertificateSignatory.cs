using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateUkCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "CertifyingOfficialQualification",
                table: "UkCertificates",
                newName: "SignatoryName");

            migrationBuilder.RenameColumn(
                name: "CertifyingOfficialName",
                table: "UkCertificates",
                newName: "Qualification");

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "UkCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId1",
                table: "UkCertificates",
                type: "nvarchar(450)",
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_UkCertificates_SignatoryUserId",
                table: "UkCertificates",
                column: "SignatoryUserId");

            migrationBuilder.CreateIndex(
                name: "IX_UkCertificates_SignatoryUserId1",
                table: "UkCertificates",
                column: "SignatoryUserId1");

            migrationBuilder.AddForeignKey(
                name: "FK_UkCertificates_AspNetUsers_SignatoryUserId",
                table: "UkCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_UkCertificates_AspNetUsers_SignatoryUserId1",
                table: "UkCertificates",
                column: "SignatoryUserId1",
                principalTable: "AspNetUsers",
                principalColumn: "Id");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_UkCertificates_AspNetUsers_SignatoryUserId",
                table: "UkCertificates");

            migrationBuilder.DropForeignKey(
                name: "FK_UkCertificates_AspNetUsers_SignatoryUserId1",
                table: "UkCertificates");

            migrationBuilder.DropIndex(
                name: "IX_UkCertificates_SignatoryUserId",
                table: "UkCertificates");

            migrationBuilder.DropIndex(
                name: "IX_UkCertificates_SignatoryUserId1",
                table: "UkCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "UkCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId1",
                table: "UkCertificates");

            migrationBuilder.RenameColumn(
                name: "SignatoryName",
                table: "UkCertificates",
                newName: "CertifyingOfficialQualification");

            migrationBuilder.RenameColumn(
                name: "Qualification",
                table: "UkCertificates",
                newName: "CertifyingOfficialName");
        }
    }
}
