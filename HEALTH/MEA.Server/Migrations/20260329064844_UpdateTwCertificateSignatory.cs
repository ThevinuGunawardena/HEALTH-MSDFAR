using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateTwCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "OfficialSignature",
                table: "TwCertificates");

            migrationBuilder.DropColumn(
                name: "OfficialStamp",
                table: "TwCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "TwCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "TwCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "TwCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_TwCertificates_SignatoryUserId",
                table: "TwCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_TwCertificates_AspNetUsers_SignatoryUserId",
                table: "TwCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_TwCertificates_AspNetUsers_SignatoryUserId",
                table: "TwCertificates");

            migrationBuilder.DropIndex(
                name: "IX_TwCertificates_SignatoryUserId",
                table: "TwCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "TwCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "TwCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "TwCertificates");

            migrationBuilder.AddColumn<string>(
                name: "OfficialSignature",
                table: "TwCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "OfficialStamp",
                table: "TwCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);
        }
    }
}
