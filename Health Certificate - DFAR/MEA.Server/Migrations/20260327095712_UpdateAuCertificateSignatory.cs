using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateAuCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "Designation",
                table: "AuCertificates",
                newName: "Qualification");

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "AuCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_AuCertificates_SignatoryUserId",
                table: "AuCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_AuCertificates_AspNetUsers_SignatoryUserId",
                table: "AuCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_AuCertificates_AspNetUsers_SignatoryUserId",
                table: "AuCertificates");

            migrationBuilder.DropIndex(
                name: "IX_AuCertificates_SignatoryUserId",
                table: "AuCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "AuCertificates");

            migrationBuilder.RenameColumn(
                name: "Qualification",
                table: "AuCertificates",
                newName: "Designation");
        }
    }
}
