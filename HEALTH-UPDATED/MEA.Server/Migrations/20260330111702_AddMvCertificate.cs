using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddMvCertificate : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "MvCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    ConsignorExporter = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    CertificateNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifyingBody = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeImporter = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    CountryOfOriginISO = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    CompetentAuthorityOrigin = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    RegionOfOrigin = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    RegionOfOriginCode = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    PlaceOfDispatch = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    PlaceOfOrigin = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    MeansOfTransport = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    MeansOfTransportNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    PointsOfEntry = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConditionsOfStorage = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConditionsOfStorageOther = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    EstimatedDateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    GrossWeight = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    SealNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ContainerNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    DescriptionOfCommodity = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CommoditiesFor = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CommoditiesForOther = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    CertifyingOfficerName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifyingOfficerDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    SignatoryUserId = table.Column<string>(type: "nvarchar(450)", maxLength: 450, nullable: true),
                    SignatoryName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    Qualification = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CompanyRegistrationNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MvCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MvCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_MvCertificates_AspNetUsers_SignatoryUserId",
                        column: x => x.SignatoryUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_MvCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "MvCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    MvCertificateId = table.Column<int>(type: "int", nullable: false),
                    No = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    NatureOfCommodity = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    Species = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    PurposeOfUse = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MvCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MvCertificateProducts_MvCertificates_MvCertificateId",
                        column: x => x.MvCertificateId,
                        principalTable: "MvCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "MvCertificateProductSecond",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    MvCertificateId = table.Column<int>(type: "int", nullable: false),
                    No = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    NameOfTheProduct = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    LotIdentifier = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    TypeOfPackaging = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(max)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MvCertificateProductSecond", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MvCertificateProductSecond_MvCertificates_MvCertificateId",
                        column: x => x.MvCertificateId,
                        principalTable: "MvCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_MvCertificateProducts_MvCertificateId",
                table: "MvCertificateProducts",
                column: "MvCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_MvCertificateProductSecond_MvCertificateId",
                table: "MvCertificateProductSecond",
                column: "MvCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_MvCertificates_CertificateRequestId",
                table: "MvCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_MvCertificates_CompanyUserId",
                table: "MvCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_MvCertificates_CreatedAt",
                table: "MvCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_MvCertificates_SignatoryUserId",
                table: "MvCertificates",
                column: "SignatoryUserId");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "MvCertificateProducts");

            migrationBuilder.DropTable(
                name: "MvCertificateProductSecond");

            migrationBuilder.DropTable(
                name: "MvCertificates");
        }
    }
}
