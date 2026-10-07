using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateJpCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "SignatureDesignation",
                table: "JpCertificates");

            migrationBuilder.DropColumn(
                name: "SignatureName",
                table: "JpCertificates");

            migrationBuilder.AlterColumn<string>(
                name: "CertificateType",
                table: "JpCertificates",
                type: "nvarchar(100)",
                maxLength: 100,
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(max)",
                oldNullable: true);

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "JpCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "JpCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "JpCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_JpCertificates_SignatoryUserId",
                table: "JpCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_JpCertificates_AspNetUsers_SignatoryUserId",
                table: "JpCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_JpCertificates_AspNetUsers_SignatoryUserId",
                table: "JpCertificates");

            migrationBuilder.DropIndex(
                name: "IX_JpCertificates_SignatoryUserId",
                table: "JpCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "JpCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "JpCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "JpCertificates");

            migrationBuilder.AlterColumn<string>(
                name: "CertificateType",
                table: "JpCertificates",
                type: "nvarchar(max)",
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(100)",
                oldMaxLength: 100,
                oldNullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatureDesignation",
                table: "JpCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatureName",
                table: "JpCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);
        }
    }
}
