using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateUaCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "OfficialVeterinarianSignature",
                table: "UaCertificates");

            migrationBuilder.RenameColumn(
                name: "OfficialVeterinarianQualification",
                table: "UaCertificates",
                newName: "SignatoryName");

            migrationBuilder.RenameColumn(
                name: "OfficialVeterinarianName",
                table: "UaCertificates",
                newName: "Qualification");

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "UaCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_UaCertificates_SignatoryUserId",
                table: "UaCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_UaCertificates_AspNetUsers_SignatoryUserId",
                table: "UaCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_UaCertificates_AspNetUsers_SignatoryUserId",
                table: "UaCertificates");

            migrationBuilder.DropIndex(
                name: "IX_UaCertificates_SignatoryUserId",
                table: "UaCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "UaCertificates");

            migrationBuilder.RenameColumn(
                name: "SignatoryName",
                table: "UaCertificates",
                newName: "OfficialVeterinarianQualification");

            migrationBuilder.RenameColumn(
                name: "Qualification",
                table: "UaCertificates",
                newName: "OfficialVeterinarianName");

            migrationBuilder.AddColumn<string>(
                name: "OfficialVeterinarianSignature",
                table: "UaCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);
        }
    }
}
