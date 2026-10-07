using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateIndCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "AuthorizedOfficialDesignation",
                table: "IndCertificates");

            migrationBuilder.DropColumn(
                name: "AuthorizedOfficialName",
                table: "IndCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "IndCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "IndCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "IndCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_IndCertificates_SignatoryUserId",
                table: "IndCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_IndCertificates_AspNetUsers_SignatoryUserId",
                table: "IndCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_IndCertificates_AspNetUsers_SignatoryUserId",
                table: "IndCertificates");

            migrationBuilder.DropIndex(
                name: "IX_IndCertificates_SignatoryUserId",
                table: "IndCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "IndCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "IndCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "IndCertificates");

            migrationBuilder.AddColumn<string>(
                name: "AuthorizedOfficialDesignation",
                table: "IndCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "AuthorizedOfficialName",
                table: "IndCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);
        }
    }
}
