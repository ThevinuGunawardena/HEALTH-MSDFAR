using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateUsaCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "SignatureName",
                table: "UsaCertificates",
                newName: "SignatoryName");

            migrationBuilder.RenameColumn(
                name: "SignatureDesignation",
                table: "UsaCertificates",
                newName: "Qualification");

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "UsaCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId1",
                table: "UsaCertificates",
                type: "nvarchar(450)",
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_UsaCertificates_SignatoryUserId",
                table: "UsaCertificates",
                column: "SignatoryUserId");

            migrationBuilder.CreateIndex(
                name: "IX_UsaCertificates_SignatoryUserId1",
                table: "UsaCertificates",
                column: "SignatoryUserId1");

            migrationBuilder.AddForeignKey(
                name: "FK_UsaCertificates_AspNetUsers_SignatoryUserId",
                table: "UsaCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_UsaCertificates_AspNetUsers_SignatoryUserId1",
                table: "UsaCertificates",
                column: "SignatoryUserId1",
                principalTable: "AspNetUsers",
                principalColumn: "Id");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_UsaCertificates_AspNetUsers_SignatoryUserId",
                table: "UsaCertificates");

            migrationBuilder.DropForeignKey(
                name: "FK_UsaCertificates_AspNetUsers_SignatoryUserId1",
                table: "UsaCertificates");

            migrationBuilder.DropIndex(
                name: "IX_UsaCertificates_SignatoryUserId",
                table: "UsaCertificates");

            migrationBuilder.DropIndex(
                name: "IX_UsaCertificates_SignatoryUserId1",
                table: "UsaCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "UsaCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId1",
                table: "UsaCertificates");

            migrationBuilder.RenameColumn(
                name: "SignatoryName",
                table: "UsaCertificates",
                newName: "SignatureName");

            migrationBuilder.RenameColumn(
                name: "Qualification",
                table: "UsaCertificates",
                newName: "SignatureDesignation");
        }
    }
}
