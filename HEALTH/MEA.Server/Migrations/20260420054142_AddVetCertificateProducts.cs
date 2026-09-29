using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddVetCertificateProducts : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "VetCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    VetCertificateFormId = table.Column<int>(type: "int", nullable: false),
                    ProductOrder = table.Column<int>(type: "int", nullable: false),
                    DescCommon = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DescScientific = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProcessingType = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    HsCode = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    TemperatureAmbient = table.Column<bool>(type: "bit", nullable: true),
                    TemperatureChilled = table.Column<bool>(type: "bit", nullable: true),
                    TemperatureFrozen = table.Column<bool>(type: "bit", nullable: true),
                    Quantity = table.Column<string>(type: "nvarchar(60)", maxLength: 60, nullable: true),
                    NumPackages = table.Column<string>(type: "nvarchar(60)", maxLength: 60, nullable: true),
                    PackagingType = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    ContainerId = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    ForHumanConsumption = table.Column<bool>(type: "bit", nullable: true),
                    ForImportEU = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    NatureAquaculture = table.Column<bool>(type: "bit", nullable: true),
                    NatureWildOrigin = table.Column<bool>(type: "bit", nullable: true),
                    TreatmentChilled = table.Column<bool>(type: "bit", nullable: true),
                    TreatmentFrozen = table.Column<bool>(type: "bit", nullable: true),
                    TreatmentLive = table.Column<bool>(type: "bit", nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(60)", maxLength: 60, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VetCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VetCertificateProducts_VetCertificateForms_VetCertificateFormId",
                        column: x => x.VetCertificateFormId,
                        principalTable: "VetCertificateForms",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_VetCertificateProducts_VetCertificateFormId",
                table: "VetCertificateProducts",
                column: "VetCertificateFormId");

            migrationBuilder.CreateIndex(
                name: "IX_VetCertificateProducts_VetCertificateFormId_ProductOrder",
                table: "VetCertificateProducts",
                columns: new[] { "VetCertificateFormId", "ProductOrder" });
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "VetCertificateProducts");
        }
    }
}
