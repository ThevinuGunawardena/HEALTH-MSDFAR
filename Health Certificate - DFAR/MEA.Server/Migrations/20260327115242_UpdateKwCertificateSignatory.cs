using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateKwCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "ResponsibleBodySignature",
                table: "KwCertificates");

            migrationBuilder.DropColumn(
                name: "ResponsibleDesignation",
                table: "KwCertificates");

            migrationBuilder.DropColumn(
                name: "ResponsibleName",
                table: "KwCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "KwCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "KwCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "KwCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_KwCertificates_SignatoryUserId",
                table: "KwCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_KwCertificates_AspNetUsers_SignatoryUserId",
                table: "KwCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_KwCertificates_AspNetUsers_SignatoryUserId",
                table: "KwCertificates");

            migrationBuilder.DropIndex(
                name: "IX_KwCertificates_SignatoryUserId",
                table: "KwCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "KwCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "KwCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "KwCertificates");

            migrationBuilder.AddColumn<string>(
                name: "ResponsibleBodySignature",
                table: "KwCertificates",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ResponsibleDesignation",
                table: "KwCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ResponsibleName",
                table: "KwCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);
        }
    }
}
