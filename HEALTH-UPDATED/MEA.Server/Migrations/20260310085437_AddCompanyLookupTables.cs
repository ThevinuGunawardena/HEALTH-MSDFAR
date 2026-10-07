using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

#pragma warning disable CA1814 // Prefer jagged arrays over multidimensional

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddCompanyLookupTables : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "CompanyStatuses",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    Name = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CompanyStatuses", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "ListedCountries",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    Name = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ListedCountries", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "ProductCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    Name = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ProductCertificates", x => x.Id);
                });

            migrationBuilder.CreateIndex(
                name: "IX_CompanyStatuses_Name",
                table: "CompanyStatuses",
                column: "Name",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_ListedCountries_Name",
                table: "ListedCountries",
                column: "Name",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_ProductCertificates_Name",
                table: "ProductCertificates",
                column: "Name",
                unique: true);

            migrationBuilder.InsertData(
                table: "CompanyStatuses",
                columns: new[] { "Id", "Name" },
                values: new object[,]
                {
                    { 1, "Non EU" },
                    { 2, "EU" },
                    { 3, "Packing Center" }
                });

            migrationBuilder.InsertData(
                table: "ProductCertificates",
                columns: new[] { "Id", "Name" },
                values: new object[,]
                {
                    { 1, "ISO 9001" },
                    { 2, "ISO 14001" },
                    { 3, "CE Mark" },
                    { 4, "FDA Approved" },
                    { 5, "RoHS" }
                });

            migrationBuilder.InsertData(
                table: "ListedCountries",
                columns: new[] { "Id", "Name" },
                values: new object[,]
                {
                    { 1, "Armenia" },
                    { 2, "Australia" },
                    { 3, "Brazil" },
                    { 4, "China" },
                    { 5, "Hong Kong" },
                    { 6, "India" },
                    { 7, "Indonesia" },
                    { 8, "Japan" },
                    { 9, "Kuwait" },
                    { 10, "Malaysia" },
                    { 11, "New Zealand" },
                    { 12, "Russia" },
                    { 13, "Taiwan" },
                    { 14, "Ukraine" },
                    { 15, "United States of America" }
                });

            migrationBuilder.AddColumn<int>(
                name: "CompanyStatusId",
                table: "Companies",
                type: "int",
                nullable: false,
                defaultValue: 1);

            migrationBuilder.AddColumn<int>(
                name: "ListedCountryId",
                table: "Companies",
                type: "int",
                nullable: false,
                defaultValue: 1);

            migrationBuilder.AddColumn<int>(
                name: "ProductCertificateId",
                table: "Companies",
                type: "int",
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_Companies_CompanyStatusId",
                table: "Companies",
                column: "CompanyStatusId");

            migrationBuilder.CreateIndex(
                name: "IX_Companies_ListedCountryId",
                table: "Companies",
                column: "ListedCountryId");

            migrationBuilder.CreateIndex(
                name: "IX_Companies_ProductCertificateId",
                table: "Companies",
                column: "ProductCertificateId");

            migrationBuilder.AddForeignKey(
                name: "FK_Companies_CompanyStatuses_CompanyStatusId",
                table: "Companies",
                column: "CompanyStatusId",
                principalTable: "CompanyStatuses",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_Companies_ListedCountries_ListedCountryId",
                table: "Companies",
                column: "ListedCountryId",
                principalTable: "ListedCountries",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_Companies_ProductCertificates_ProductCertificateId",
                table: "Companies",
                column: "ProductCertificateId",
                principalTable: "ProductCertificates",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Companies_CompanyStatuses_CompanyStatusId",
                table: "Companies");

            migrationBuilder.DropForeignKey(
                name: "FK_Companies_ListedCountries_ListedCountryId",
                table: "Companies");

            migrationBuilder.DropForeignKey(
                name: "FK_Companies_ProductCertificates_ProductCertificateId",
                table: "Companies");

            migrationBuilder.DropIndex(
                name: "IX_Companies_CompanyStatusId",
                table: "Companies");

            migrationBuilder.DropIndex(
                name: "IX_Companies_ListedCountryId",
                table: "Companies");

            migrationBuilder.DropIndex(
                name: "IX_Companies_ProductCertificateId",
                table: "Companies");

            migrationBuilder.DropColumn(
                name: "CompanyStatusId",
                table: "Companies");

            migrationBuilder.DropColumn(
                name: "ListedCountryId",
                table: "Companies");

            migrationBuilder.DropColumn(
                name: "ProductCertificateId",
                table: "Companies");

            migrationBuilder.DropTable(
                name: "CompanyStatuses");

            migrationBuilder.DropTable(
                name: "ListedCountries");

            migrationBuilder.DropTable(
                name: "ProductCertificates");
        }
    }
}
