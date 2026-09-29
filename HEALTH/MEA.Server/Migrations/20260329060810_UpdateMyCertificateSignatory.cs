using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class UpdateMyCertificateSignatory : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CertifyingOfficialDate",
                table: "MyCertificates");

            migrationBuilder.DropColumn(
                name: "CertifyingOfficialName",
                table: "MyCertificates");

            migrationBuilder.DropColumn(
                name: "CertifyingOfficialQualification",
                table: "MyCertificates");

            migrationBuilder.AddColumn<string>(
                name: "Qualification",
                table: "MyCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryName",
                table: "MyCertificates",
                type: "nvarchar(200)",
                maxLength: 200,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SignatoryUserId",
                table: "MyCertificates",
                type: "nvarchar(450)",
                maxLength: 450,
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_MyCertificates_SignatoryUserId",
                table: "MyCertificates",
                column: "SignatoryUserId");

            migrationBuilder.AddForeignKey(
                name: "FK_MyCertificates_AspNetUsers_SignatoryUserId",
                table: "MyCertificates",
                column: "SignatoryUserId",
                principalTable: "AspNetUsers",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_MyCertificates_AspNetUsers_SignatoryUserId",
                table: "MyCertificates");

            migrationBuilder.DropIndex(
                name: "IX_MyCertificates_SignatoryUserId",
                table: "MyCertificates");

            migrationBuilder.DropColumn(
                name: "Qualification",
                table: "MyCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryName",
                table: "MyCertificates");

            migrationBuilder.DropColumn(
                name: "SignatoryUserId",
                table: "MyCertificates");

            migrationBuilder.AddColumn<DateTime>(
                name: "CertifyingOfficialDate",
                table: "MyCertificates",
                type: "datetime2",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CertifyingOfficialName",
                table: "MyCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CertifyingOfficialQualification",
                table: "MyCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);
        }
    }
}
