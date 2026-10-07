using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddIlCertificate : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "IlCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    CertificationNo = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CentralCompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CentralCompetentAuthorityEmail = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    LocalCompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfOriginName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfOriginAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfOriginApprovalNo = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    DateOfArrival = table.Column<DateTime>(type: "datetime2", nullable: true),
                    PlaceOfArrival = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    PlaceOfArrivalAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfDestinationName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfDestinationAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfDestinationApprovalNo = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    DateOfContainerization = table.Column<DateTime>(type: "datetime2", nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TransportSea = table.Column<bool>(type: "bit", nullable: true),
                    TransportAir = table.Column<bool>(type: "bit", nullable: true),
                    TransportRail = table.Column<bool>(type: "bit", nullable: true),
                    TransportRoad = table.Column<bool>(type: "bit", nullable: true),
                    MeansOfTransportIdentification = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    ContainerNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    SealNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    MeansOfTransportReference = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    EntryBIP = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ReadyToEat = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    NonReadyToEat = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    Remarks = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    SignatoryName = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    Designation = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    SignatureDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    Stamp = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    Signature = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    SignatoryUserId = table.Column<string>(type: "nvarchar(max)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IlCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_IlCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "IlCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    IlCertificateId = table.Column<int>(type: "int", nullable: false),
                    DescriptionOfCommodity = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    SpeciesScientificName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NatureOfCommodity = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TreatmentType = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ApprovalNo = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: true),
                    NetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: true),
                    HarvestingDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ProductionDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    BestBefore = table.Column<DateTime>(type: "datetime2", nullable: true),
                    LotNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IlCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_IlCertificateProducts_IlCertificates_IlCertificateId",
                        column: x => x.IlCertificateId,
                        principalTable: "IlCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_IlCertificateProducts_IlCertificateId",
                table: "IlCertificateProducts",
                column: "IlCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_IlCertificates_CertificateRequestId",
                table: "IlCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_IlCertificates_CompanyUserId",
                table: "IlCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_IlCertificates_CreatedAt",
                table: "IlCertificates",
                column: "CreatedAt");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "IlCertificateProducts");

            migrationBuilder.DropTable(
                name: "IlCertificates");
        }
    }
}
