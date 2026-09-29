using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddUkCertificate : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "UkCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    CertificateReferenceNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsignorTel = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeTel = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    OperatorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    OperatorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    OperatorTel = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CountryOfOriginISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    RegionOfOrigin = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    RegionOfOriginCode = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CountryOfDestination = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CountryOfDestinationISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    RegionOfDestination = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    RegionOfDestinationCode = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    PlaceOfDispatchName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfDispatchApprovalNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    PlaceOfDispatchAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    PlaceOfDestinationName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfDestinationAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TimeOfDeparture = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    TransportAeroplane = table.Column<bool>(type: "bit", nullable: false),
                    TransportVessel = table.Column<bool>(type: "bit", nullable: false),
                    TransportRailway = table.Column<bool>(type: "bit", nullable: false),
                    TransportRoadVehicle = table.Column<bool>(type: "bit", nullable: false),
                    TransportOther = table.Column<bool>(type: "bit", nullable: false),
                    TransportIdentification = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    EntryBCP = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    AccompDocType = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    AccompDocNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TempAmbient = table.Column<bool>(type: "bit", nullable: false),
                    TempChilled = table.Column<bool>(type: "bit", nullable: false),
                    TempFrozen = table.Column<bool>(type: "bit", nullable: false),
                    ContainerSealNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    GoodsCanningIndustry = table.Column<bool>(type: "bit", nullable: false),
                    GoodsHumanConsumption = table.Column<bool>(type: "bit", nullable: false),
                    Field21 = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    Field22 = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    TotalNumberOfPackages = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TotalNetWeight = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TotalGrossWeight = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    FinalConsumer = table.Column<bool>(type: "bit", nullable: false),
                    CertifyingOfficialName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifyingOfficialQualification = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifiedDate = table.Column<DateTime>(type: "datetime2", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_UkCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_UkCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_UkCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "UkCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    UkCertificateId = table.Column<int>(type: "int", nullable: false),
                    No = table.Column<string>(type: "nvarchar(30)", maxLength: 30, nullable: true),
                    CodeCNTitle = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    Species = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NatureOfCommodity = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    TreatmentType = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    VesselPlant = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    NumberOfPackages = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    BatchNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TypeOfPackaging = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_UkCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_UkCertificateProducts_UkCertificates_UkCertificateId",
                        column: x => x.UkCertificateId,
                        principalTable: "UkCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_UkCertificateProducts_UkCertificateId",
                table: "UkCertificateProducts",
                column: "UkCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_UkCertificates_CertificateRequestId",
                table: "UkCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_UkCertificates_CompanyUserId",
                table: "UkCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_UkCertificates_CreatedAt",
                table: "UkCertificates",
                column: "CreatedAt");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "UkCertificateProducts");

            migrationBuilder.DropTable(
                name: "UkCertificates");
        }
    }
}
