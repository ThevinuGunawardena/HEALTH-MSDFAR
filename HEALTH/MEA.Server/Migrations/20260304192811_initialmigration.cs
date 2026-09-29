using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class initialmigration : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "AspNetRoles",
                columns: table => new
                {
                    Id = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    Name = table.Column<string>(type: "nvarchar(256)", maxLength: 256, nullable: true),
                    NormalizedName = table.Column<string>(type: "nvarchar(256)", maxLength: 256, nullable: true),
                    ConcurrencyStamp = table.Column<string>(type: "nvarchar(max)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AspNetRoles", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "AspNetUsers",
                columns: table => new
                {
                    Id = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    FullName = table.Column<string>(type: "nvarchar(110)", nullable: false),
                    UserName = table.Column<string>(type: "nvarchar(256)", maxLength: 256, nullable: true),
                    NormalizedUserName = table.Column<string>(type: "nvarchar(256)", maxLength: 256, nullable: true),
                    Email = table.Column<string>(type: "nvarchar(256)", maxLength: 256, nullable: true),
                    NormalizedEmail = table.Column<string>(type: "nvarchar(256)", maxLength: 256, nullable: true),
                    EmailConfirmed = table.Column<bool>(type: "bit", nullable: false),
                    PasswordHash = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    SecurityStamp = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConcurrencyStamp = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PhoneNumber = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PhoneNumberConfirmed = table.Column<bool>(type: "bit", nullable: false),
                    TwoFactorEnabled = table.Column<bool>(type: "bit", nullable: false),
                    LockoutEnd = table.Column<DateTimeOffset>(type: "datetimeoffset", nullable: true),
                    LockoutEnabled = table.Column<bool>(type: "bit", nullable: false),
                    AccessFailedCount = table.Column<int>(type: "int", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AspNetUsers", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Countries",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    Name = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Countries", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "AspNetRoleClaims",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    RoleId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    ClaimType = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ClaimValue = table.Column<string>(type: "nvarchar(max)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AspNetRoleClaims", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AspNetRoleClaims_AspNetRoles_RoleId",
                        column: x => x.RoleId,
                        principalTable: "AspNetRoles",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AspNetUserClaims",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    UserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    ClaimType = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ClaimValue = table.Column<string>(type: "nvarchar(max)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AspNetUserClaims", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AspNetUserClaims_AspNetUsers_UserId",
                        column: x => x.UserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AspNetUserLogins",
                columns: table => new
                {
                    LoginProvider = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    ProviderKey = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    ProviderDisplayName = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    UserId = table.Column<string>(type: "nvarchar(450)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AspNetUserLogins", x => new { x.LoginProvider, x.ProviderKey });
                    table.ForeignKey(
                        name: "FK_AspNetUserLogins_AspNetUsers_UserId",
                        column: x => x.UserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AspNetUserRoles",
                columns: table => new
                {
                    UserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    RoleId = table.Column<string>(type: "nvarchar(450)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AspNetUserRoles", x => new { x.UserId, x.RoleId });
                    table.ForeignKey(
                        name: "FK_AspNetUserRoles_AspNetRoles_RoleId",
                        column: x => x.RoleId,
                        principalTable: "AspNetRoles",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_AspNetUserRoles_AspNetUsers_UserId",
                        column: x => x.UserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AspNetUserTokens",
                columns: table => new
                {
                    UserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    LoginProvider = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    Name = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    Value = table.Column<string>(type: "nvarchar(max)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AspNetUserTokens", x => new { x.UserId, x.LoginProvider, x.Name });
                    table.ForeignKey(
                        name: "FK_AspNetUserTokens_AspNetUsers_UserId",
                        column: x => x.UserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Companies",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CompanyName = table.Column<string>(type: "nvarchar(max)", nullable: false),
                    CompanyEmail = table.Column<string>(type: "nvarchar(max)", nullable: false),
                    CompanyPhone = table.Column<string>(type: "nvarchar(max)", nullable: false),
                    CompanyAddress = table.Column<string>(type: "nvarchar(max)", nullable: false),
                    RegistrationNo = table.Column<string>(type: "nvarchar(max)", nullable: false),
                    UserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Companies", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Companies_AspNetUsers_UserId",
                        column: x => x.UserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "CertificateRequests",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    ReferenceNumber = table.Column<string>(type: "nvarchar(32)", maxLength: 32, nullable: false),
                    CertificateType = table.Column<int>(type: "int", nullable: false),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CountryId = table.Column<int>(type: "int", nullable: true),
                    Status = table.Column<int>(type: "int", nullable: false, defaultValue: 0),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CertificateRequests", x => x.Id);
                    table.CheckConstraint("CK_CertificateRequests_TypeCountry", "([CertificateType] = 0 AND [CountryId] IS NULL) OR ([CertificateType] = 1 AND [CountryId] IS NOT NULL)");
                    table.ForeignKey(
                        name: "FK_CertificateRequests_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_CertificateRequests_Countries_CountryId",
                        column: x => x.CountryId,
                        principalTable: "Countries",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "AmCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    ConsignorName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsignorPostal = table.Column<string>(type: "nvarchar(30)", maxLength: 30, nullable: true),
                    ConsignorTel = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    CertRefNumber = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    CertRefNumberA = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CentralCompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    LocalCompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsigneePostal = table.Column<string>(type: "nvarchar(30)", maxLength: 30, nullable: true),
                    ConsigneeTel = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    Consignee6 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOrigin = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOriginISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    RegionOrigin = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    RegionOriginISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    CountryDestination = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryDestinationISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    CountryDestination110 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfOriginName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfOriginAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfOriginApprovalNo = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CountryDestination112 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TransportAeroPlane = table.Column<bool>(type: "bit", nullable: true),
                    TransportShip = table.Column<bool>(type: "bit", nullable: true),
                    TransportRailwayWagon = table.Column<bool>(type: "bit", nullable: true),
                    TransportRoadVehicle = table.Column<bool>(type: "bit", nullable: true),
                    TransportOther = table.Column<bool>(type: "bit", nullable: true),
                    TransportId = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ProcessingEstName = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ProcessingEstAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ProcessingEstRegNo = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    EntryBIP = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    Field117 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    DescCommon = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HsCode = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    Quantity = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    TemperatureAmbient = table.Column<bool>(type: "bit", nullable: true),
                    TemperatureChilled = table.Column<bool>(type: "bit", nullable: true),
                    TemperatureFrozen = table.Column<bool>(type: "bit", nullable: true),
                    NumPackages = table.Column<string>(type: "nvarchar(60)", maxLength: 60, nullable: true),
                    ContainerId = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PackagingType = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    ForHumanConsumption = table.Column<bool>(type: "bit", nullable: true),
                    Field126 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ForImportEU = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HealthCertNo = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HealthCertNoB = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ExportApprovalNumber = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    SignatoryName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    Designation = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    SignatureDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    Stamp = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    Signature = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AmCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AmCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_AmCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "AuCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    ConsignorName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsignorPostal = table.Column<string>(type: "nvarchar(30)", maxLength: 30, nullable: true),
                    ConsignorTel = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    CertRefNumber = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    CertRefNumberA = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CentralCompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    LocalCompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsigneePostal = table.Column<string>(type: "nvarchar(30)", maxLength: 30, nullable: true),
                    ConsigneeTel = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    Consignee6 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOrigin = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOriginISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    RegionOrigin = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    RegionOriginISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    CountryDestination = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryDestinationISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    CountryDestination110 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfOriginName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfOriginAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfOriginApprovalNo = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CountryDestination112 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TransportAeroPlane = table.Column<bool>(type: "bit", nullable: true),
                    TransportShip = table.Column<bool>(type: "bit", nullable: true),
                    TransportRailwayWagon = table.Column<bool>(type: "bit", nullable: true),
                    TransportRoadVehicle = table.Column<bool>(type: "bit", nullable: true),
                    TransportOther = table.Column<bool>(type: "bit", nullable: true),
                    DocReferences = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    EntryBIP = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    Field117 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    DescCommon = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HsCode = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    Quantity = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    TemperatureAmbient = table.Column<bool>(type: "bit", nullable: true),
                    TemperatureChilled = table.Column<bool>(type: "bit", nullable: true),
                    TemperatureFrozen = table.Column<bool>(type: "bit", nullable: true),
                    NumPackages = table.Column<string>(type: "nvarchar(60)", maxLength: 60, nullable: true),
                    ContainerId = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PackagingType = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    ForHumanConsumption = table.Column<bool>(type: "bit", nullable: true),
                    Field126 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ForImportEU = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HealthCertNo = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HealthCertNoB = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ExportApprovalNumber = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    SignatoryName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    Designation = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    SignatureDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    Stamp = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    Signature = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AuCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AuCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_AuCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "BrCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    RefNumber = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: false),
                    CountryOfExport = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    CertificateNo = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: false),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    LocalCompetentAuthority = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    ExporterName = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    ExporterAddress = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: false),
                    ImporterName = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    ImporterAddress = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: false),
                    CountryOrigin = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    CountryOriginISO = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: false),
                    CountryOfDestination = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    CountryDestinationISO = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: false),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    TransportAeroPlane = table.Column<bool>(type: "bit", nullable: false),
                    TransportShip = table.Column<bool>(type: "bit", nullable: false),
                    TransportRailwayWagon = table.Column<bool>(type: "bit", nullable: false),
                    TransportRoadVehicle = table.Column<bool>(type: "bit", nullable: false),
                    TransportOther = table.Column<bool>(type: "bit", nullable: false),
                    DeclaredPointOfEntry = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    ConditionsForTransportStorage = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: false),
                    IdentificationOfContainers = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    IdentificationOfFoodProducts = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: false),
                    ProducerDetails = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: false),
                    HsCode = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: false),
                    IntendedPurpose = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    TotalNetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: false),
                    PlaceAndDate = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    DateOfIssue = table.Column<DateTime>(type: "datetime2", nullable: false),
                    OfficialStamp = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    VeterinarySignature = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    ModeloConformeCircularNo = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    SanitaryCertification = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_BrCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_BrCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_BrCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "ChCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    CountryOfExport = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CountryOfProduction = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DepartmentOfIssuance = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CommodityName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ScientificName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    LatinName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    Number = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ArtificialCulture = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    WildCaught = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    CatchArea = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    PackagingEnterpriseName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PackagingEnterpriseAddress = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    PackagingEnterpriseRegNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    PortOfDeparture = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    TransportAeroPlane = table.Column<bool>(type: "bit", nullable: true),
                    TransportShip = table.Column<bool>(type: "bit", nullable: true),
                    TransportRailwayWagon = table.Column<bool>(type: "bit", nullable: true),
                    TransportRoadVehicle = table.Column<bool>(type: "bit", nullable: true),
                    TransportOther = table.Column<bool>(type: "bit", nullable: true),
                    IdentificationDocumentReferences = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    ExporterName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ExporterAddress = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    ImporterName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ImporterAddress = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    PlaceOfIssue = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DateOfIssue = table.Column<DateTime>(type: "datetime2", nullable: true),
                    OfficialStamp = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    VeterinarySignature = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ChCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ChCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_ChCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "HkCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    IdentificationNumber = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CountryOfDispatch = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CertifyingBody = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    SealIdentificationNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    StorageTemperature = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    ProvenanceDetails = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    PlaceOfDispatch = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DestinationCountryPlace = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    MeansOfTransport = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    PlaceOfIssue = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DateOfIssue = table.Column<DateTime>(type: "datetime2", nullable: true),
                    OfficerNamePosition = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    OfficerTel = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    OfficerFax = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    OfficerEmail = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_HkCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_HkCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_HkCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "IdCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    NumberNomor = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    EstablishmentAquaculture = table.Column<bool>(type: "bit", nullable: false),
                    EstablishmentProcessing = table.Column<bool>(type: "bit", nullable: false),
                    EstablishmentOther = table.Column<bool>(type: "bit", nullable: false),
                    EstablishmentName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    EstablishmentRegNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    EstablishmentAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CountryRegionOrigin = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    SourceFarmRaised = table.Column<bool>(type: "bit", nullable: false),
                    SourceWildCaught = table.Column<bool>(type: "bit", nullable: false),
                    PortOfShipment = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    TransportAir = table.Column<bool>(type: "bit", nullable: false),
                    TransportSea = table.Column<bool>(type: "bit", nullable: false),
                    TransportRoad = table.Column<bool>(type: "bit", nullable: false),
                    CommodityDescription = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    TempAmbient = table.Column<bool>(type: "bit", nullable: false),
                    TempFrozen = table.Column<bool>(type: "bit", nullable: false),
                    TempChilled = table.Column<bool>(type: "bit", nullable: false),
                    IntendedHumanConsumption = table.Column<bool>(type: "bit", nullable: false),
                    IntendedCultureBreeding = table.Column<bool>(type: "bit", nullable: false),
                    IntendedTrade = table.Column<bool>(type: "bit", nullable: false),
                    IntendedResearch = table.Column<bool>(type: "bit", nullable: false),
                    IntendedFishFeed = table.Column<bool>(type: "bit", nullable: false),
                    IntendedExhibition = table.Column<bool>(type: "bit", nullable: false),
                    IntendedOther = table.Column<bool>(type: "bit", nullable: false),
                    TotalPackages = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    PackagingType = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    TotalQuantityKg = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ContainerSealNumber = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    PortOfDestination = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    TransportVesselName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TransportVoyageNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TestingLaboratory = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    LaboratoryAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ApprovingOfficerName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TestResultNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    AttestationRefNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    AdditionalInformation = table.Column<string>(type: "nvarchar(2000)", maxLength: 2000, nullable: true),
                    CertifiedName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifiedPosition = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifiedIssuedAt = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CertifiedDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    CertifiedPhone = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CertifiedFax = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CertifiedEmail = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IdCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_IdCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_IdCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "IndCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    CountryOfDispatch = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CertificateNumber = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsignorTel = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CompetentAuthorityDetails = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeTel = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CountryOfOriginIso = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    CountryOfDestination = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    CountryOfDestinationIso = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    MeansOfTransport = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DeclaredPointOfEntry = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConditionsForTransportStorage = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    TotalQuantity = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    InvoiceNoDate = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    FoodDescription = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    IntendedPurpose = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ProducerNameAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ApprovalNumberDetails = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    DateOfManufacture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    BestBefore = table.Column<DateTime>(type: "datetime2", nullable: true),
                    DateOfExpiry = table.Column<DateTime>(type: "datetime2", nullable: true),
                    AttestationPlace = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    AttestationDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    AuthorizedOfficialName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    AuthorizedOfficialDesignation = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    AuthorizedOfficialDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    AuthorizedOfficialSignature = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    OfficialStamp = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IndCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_IndCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_IndCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "JpCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    MyRef = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    YourRef = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    Date = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ItemName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NumberOfPackages = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    DespatchFrom = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DespatchTo = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DespatchByShip = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    SignatureName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    SignatureDesignation = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_JpCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_JpCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_JpCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "KwCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CertificateReferenceNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    PlaceOfIssue = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DateOfIssue = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CompetentAuthorityAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfOriginIso = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    CountryOfDestination = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfDestinationIso = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    ProducerName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProducerAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    PackingEstName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PackingEstAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    BorderOfEntry = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    BorderLoadingCountry = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    BorderLoadingPlace = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TransportByAir = table.Column<bool>(type: "bit", nullable: false),
                    TransportBySea = table.Column<bool>(type: "bit", nullable: false),
                    VehicleIdentificationNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TempChilled = table.Column<bool>(type: "bit", nullable: false),
                    TempFrozen = table.Column<bool>(type: "bit", nullable: false),
                    CommoditiesOther = table.Column<bool>(type: "bit", nullable: false),
                    CommoditiesAfterFurtherProcess = table.Column<bool>(type: "bit", nullable: false),
                    CommoditiesHumanConsumption = table.Column<bool>(type: "bit", nullable: false),
                    ResponsibleBodySignature = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    OfficialStamp = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ResponsibleName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ResponsibleDesignation = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_KwCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_KwCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_KwCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "MyCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    ExporterName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertificateReferenceNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    QualityCertificateNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    LocalAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ImporterDetails = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfOriginIso = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    CountryOfDestination = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfDestinationIso = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    ProcessingEstablishment = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    AuthorizationNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TransportAir = table.Column<bool>(type: "bit", nullable: false),
                    TransportShip = table.Column<bool>(type: "bit", nullable: false),
                    TransportRail = table.Column<bool>(type: "bit", nullable: false),
                    TransportRoad = table.Column<bool>(type: "bit", nullable: false),
                    TransportOther = table.Column<bool>(type: "bit", nullable: false),
                    PortOfEntry = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TransportCompany = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConditionAmbient = table.Column<bool>(type: "bit", nullable: false),
                    ConditionChilled = table.Column<bool>(type: "bit", nullable: false),
                    ConditionFrozen = table.Column<bool>(type: "bit", nullable: false),
                    ContainerSealIdentification = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    InvoiceNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TransitCountry = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    DepartureDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    CertificateReferenceNoPage2 = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ProductBrand = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    OriginFisheries = table.Column<bool>(type: "bit", nullable: false),
                    OriginAquaculture = table.Column<bool>(type: "bit", nullable: false),
                    CertifiedProductFor = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TreatmentType = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertificateReferenceNoPage3 = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    AdditionalInformation = table.Column<string>(type: "nvarchar(2000)", maxLength: 2000, nullable: true),
                    CertifyingOfficialName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifyingOfficialQualification = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CertifyingOfficialDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MyCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MyCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_MyCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "NzCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CertificateRefNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfDestination = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ProcessorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProcessorAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ProcessorEstablishmentNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    PortDispatchedFrom = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    MeansOfTransport = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TransportAeroplan = table.Column<bool>(type: "bit", nullable: false),
                    TransportShip = table.Column<bool>(type: "bit", nullable: false),
                    TemperatureOfCommodities = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ContainerNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    OfficialSealNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CertifyingOfficialName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    Signature = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    SignatureDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()")
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_NzCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_NzCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_NzCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "RuCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    ConsignorNameAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeNameAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    MeansOfTransport = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    CountryOfTransit = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CertificateNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CountryIssuing = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CompetentAuthorityExporting = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    OrganizationIssuing = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PointOfCrossingBorder = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProductName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProductionDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TypeOfPackage = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    NumberOfPackages = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    NumberOfSeal = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    IdentificationMarks = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    StorageConditions = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    EstablishmentNameAddressRegNo = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    FactoryVessel = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ColdStore = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    AdministrativeUnit = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfIssue = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DateOfIssue = table.Column<DateTime>(type: "datetime2", nullable: true),
                    SignatureNamePosition = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    DateOfAttachment = table.Column<DateTime>(type: "datetime2", nullable: true),
                    IdentificationMarksAttachment = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RuCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RuCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "TwCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    ReferenceNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfExport = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CountryOfProduction = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    CompetentAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DepartmentIssuance = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProductionPlace = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    ProcessingType = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProductionMode = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    AquaculturedYes = table.Column<bool>(type: "bit", nullable: false),
                    AquaculturedNo = table.Column<bool>(type: "bit", nullable: false),
                    WildCaughtYes = table.Column<bool>(type: "bit", nullable: false),
                    WildCaughtNo = table.Column<bool>(type: "bit", nullable: false),
                    AquacultureArea = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CatchArea = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    HarvestingArea = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    VesselName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    EnterpriseName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    EnterpriseRegistrationNo = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProductionDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    PlaceOfDispatch = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PlaceOfDestination = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    MeansOfTransport = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    VesselNameTransport = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    FlightNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    OtherTransportMeans = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ContainerNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    SealNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    PlaceOfIssue = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DateOfIssue = table.Column<DateTime>(type: "datetime2", nullable: true),
                    OfficialStamp = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    OfficialSignature = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TwCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TwCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_TwCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "UaCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsignorPostalCode = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsignorTelNo = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CertificateReferenceNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CentralCompetentAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    LocalCompetentAuthority = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneePostalCode = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsigneeTel = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PersonResponsibleName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    PersonResponsibleAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    PersonResponsiblePostalCode = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PersonResponsibleTel = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOfOriginName = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOfOriginISO = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOfOriginISOCode = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOfOriginZone = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ZoneOrigin = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ZoneOriginCode = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryDestinationName = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryDestinationISO = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryDestinationISOCode = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryDestinationZone = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ZoneDestination = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ZoneDestinationCode = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOriginName = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOriginApprovalNumber = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceOriginAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    Field112 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    PlaceLoadingAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TransportAeroplane = table.Column<bool>(type: "bit", nullable: false),
                    TransportShip = table.Column<bool>(type: "bit", nullable: false),
                    TransportRailwayWagon = table.Column<bool>(type: "bit", nullable: false),
                    TransportRoadVehicle = table.Column<bool>(type: "bit", nullable: false),
                    TransportOther = table.Column<bool>(type: "bit", nullable: false),
                    TransportIdentification = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    TransportDocumentReferences = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    EntryBIPUkraine = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    DescriptionOfCommodity = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    CommodityCodeHS = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    Quantity = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TemperatureAmbient = table.Column<bool>(type: "bit", nullable: false),
                    TemperatureChilled = table.Column<bool>(type: "bit", nullable: false),
                    TemperatureFrozen = table.Column<bool>(type: "bit", nullable: false),
                    NumberOfPackages = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    SealContainerNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TypeOfPackaging = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CommoditiesHumanConsumption = table.Column<bool>(type: "bit", nullable: false),
                    Field126 = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ForImportIntoUkraine = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HealthInfoNotes = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HealthCertificateReferenceNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    AdditionalInformation = table.Column<string>(type: "nvarchar(2000)", maxLength: 2000, nullable: true),
                    OfficialVeterinarianName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    OfficialVeterinarianQualification = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    OfficialVeterinarianSignature = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    CertifiedDate = table.Column<DateTime>(type: "datetime2", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_UaCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_UaCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_UaCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "UsaCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    MyRef = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    YourRef = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    Date = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ItemName = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    NumberOfPackages = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(1000)", maxLength: 1000, nullable: true),
                    DespatchFrom = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DespatchTo = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    DespatchByShip = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    SignatureName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    SignatureDesignation = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_UsaCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_UsaCertificates_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_UsaCertificates_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "VetCertificateForms",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    CertificateRequestId = table.Column<int>(type: "int", nullable: true),
                    CompanyUserId = table.Column<string>(type: "nvarchar(450)", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "datetime2", nullable: false, defaultValueSql: "GETUTCDATE()"),
                    OldHC = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    NewHC = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    LandingSite = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    BoatRegistration = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    BoatNumber = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    SupplierNameAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ArrivalAtFactory = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ProcessingDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    FarmLocation = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    FarmOwnerName = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    FarmOwnerAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    HarvestDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ArrivalTimeProduct = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ProcessingDates = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    AquaSupplier = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    CountryOrigin = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    ArrivalConsignment = table.Column<DateTime>(type: "datetime2", nullable: true),
                    HealthCertNo = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    ProductTypeAquaculture = table.Column<bool>(type: "bit", nullable: true),
                    ProductTypeWildCaught = table.Column<bool>(type: "bit", nullable: true),
                    UploadedCertificateFile = table.Column<byte[]>(type: "varbinary(max)", nullable: true),
                    ConsignorName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsignorAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsignorPostal = table.Column<string>(type: "nvarchar(30)", maxLength: 30, nullable: true),
                    ConsignorTel = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    ConsigneeName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ConsigneeAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ConsigneePostal = table.Column<string>(type: "nvarchar(30)", maxLength: 30, nullable: true),
                    ConsigneeTel = table.Column<string>(type: "nvarchar(40)", maxLength: 40, nullable: true),
                    CountryOriginISO = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    RegionOriginISO = table.Column<string>(type: "nvarchar(20)", maxLength: 20, nullable: true),
                    CountryDestinationISO = table.Column<string>(type: "nvarchar(10)", maxLength: 10, nullable: true),
                    ProcessingEstName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProcessingEstAddress = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ApprovalNo = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    PlaceOfLoading = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    DateOfDeparture = table.Column<DateTime>(type: "datetime2", nullable: true),
                    TransportAeroPlane = table.Column<bool>(type: "bit", nullable: true),
                    TransportShip = table.Column<bool>(type: "bit", nullable: true),
                    TransportRailwayWagon = table.Column<bool>(type: "bit", nullable: true),
                    TransportRoadVehicle = table.Column<bool>(type: "bit", nullable: true),
                    TransportOther = table.Column<bool>(type: "bit", nullable: true),
                    TransportId = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    DocReferences = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    EntryBIP = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
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
                    NetWeight = table.Column<string>(type: "nvarchar(60)", maxLength: 60, nullable: true),
                    PaymentSlipFile = table.Column<byte[]>(type: "varbinary(max)", nullable: true),
                    SignatureDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    SignatureTime = table.Column<DateTime>(type: "datetime2", nullable: true),
                    Signature = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    SignatoryName = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    Designation = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VetCertificateForms", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VetCertificateForms_AspNetUsers_CompanyUserId",
                        column: x => x.CompanyUserId,
                        principalTable: "AspNetUsers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                    table.ForeignKey(
                        name: "FK_VetCertificateForms_CertificateRequests_CertificateRequestId",
                        column: x => x.CertificateRequestId,
                        principalTable: "CertificateRequests",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Restrict);
                });

            migrationBuilder.CreateTable(
                name: "AmAttachments",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    AmCertificateId = table.Column<int>(type: "int", nullable: false),
                    Product = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    NumberOfKgs = table.Column<decimal>(type: "decimal(18,2)", nullable: true),
                    NumberOfBoxes = table.Column<int>(type: "int", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AmAttachments", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AmAttachments_AmCertificates_AmCertificateId",
                        column: x => x.AmCertificateId,
                        principalTable: "AmCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AmPreExportCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    AmCertificateId = table.Column<int>(type: "int", nullable: false),
                    Date = table.Column<DateTime>(type: "datetime2", nullable: true),
                    Number = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    AdministrativeTerritory = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ApprovalNumber = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    ProductNameAndQuantity = table.Column<string>(type: "nvarchar(max)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AmPreExportCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AmPreExportCertificates_AmCertificates_AmCertificateId",
                        column: x => x.AmCertificateId,
                        principalTable: "AmCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "AuCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    AuCertificateId = table.Column<int>(type: "int", nullable: false),
                    SpeciesScientificName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NatureOfCommodity = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TreatmentType = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    ApprovalNumberOfEstablishments = table.Column<string>(type: "nvarchar(150)", maxLength: 150, nullable: true),
                    ManufacturingPlant = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: true),
                    NetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_AuCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_AuCertificateProducts_AuCertificates_AuCertificateId",
                        column: x => x.AuCertificateId,
                        principalTable: "AuCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "BrCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    BrCertificateId = table.Column<int>(type: "int", nullable: false),
                    NameOfTheProduct = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    ScientificName = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    TypeOfPackaging = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: false),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: false),
                    NetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_BrCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_BrCertificateProducts_BrCertificates_BrCertificateId",
                        column: x => x.BrCertificateId,
                        principalTable: "BrCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "HkCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    HkCertificateId = table.Column<int>(type: "int", nullable: false),
                    Description = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    Species = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProcessingType = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    PackagingType = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    LotCode = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: true),
                    NetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_HkCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_HkCertificateProducts_HkCertificates_HkCertificateId",
                        column: x => x.HkCertificateId,
                        principalTable: "HkCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "IdCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    IdCertificateId = table.Column<int>(type: "int", nullable: false),
                    No = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    CommonName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ScientificName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    HsCode = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true),
                    Quantity = table.Column<decimal>(type: "decimal(18,2)", nullable: true),
                    Unit = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IdCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_IdCertificateProducts_IdCertificates_IdCertificateId",
                        column: x => x.IdCertificateId,
                        principalTable: "IdCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "IndCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    IndCertificateId = table.Column<int>(type: "int", nullable: false),
                    NameOfProduct = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    LotNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TypeOfPackaging = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: true),
                    NetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_IndCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_IndCertificateProducts_IndCertificates_IndCertificateId",
                        column: x => x.IndCertificateId,
                        principalTable: "IndCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "KwCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    KwCertificateId = table.Column<int>(type: "int", nullable: false),
                    NameDescription = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    HsCodes = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TreatmentDerivedFrom = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    BrandName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ProductionDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    ExpiryDate = table.Column<DateTime>(type: "datetime2", nullable: true),
                    NumberPackages = table.Column<int>(type: "int", nullable: false),
                    BatchLotNo = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TotalWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_KwCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_KwCertificateProducts_KwCertificates_KwCertificateId",
                        column: x => x.KwCertificateId,
                        principalTable: "KwCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "MyCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    MyCertificateId = table.Column<int>(type: "int", nullable: false),
                    HsCode = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    Description = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    ScientificName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    BatchCode = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: false),
                    NetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MyCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MyCertificateProducts_MyCertificates_MyCertificateId",
                        column: x => x.MyCertificateId,
                        principalTable: "MyCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "NzCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    NzCertificateId = table.Column<int>(type: "int", nullable: false),
                    ProductName = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    AquaticAnimalSpecies = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: false),
                    NetWeightKg = table.Column<decimal>(type: "decimal(18,2)", nullable: false),
                    HsCode = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_NzCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_NzCertificateProducts_NzCertificates_NzCertificateId",
                        column: x => x.NzCertificateId,
                        principalTable: "NzCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "RuAttachments",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    RuCertificateId = table.Column<int>(type: "int", nullable: false),
                    Product = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    NumberOfKgs = table.Column<decimal>(type: "decimal(18,2)", nullable: true),
                    NumberOfBoxes = table.Column<int>(type: "int", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RuAttachments", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RuAttachments_RuCertificates_RuCertificateId",
                        column: x => x.RuCertificateId,
                        principalTable: "RuCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "RuPreExportCertificates",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    RuCertificateId = table.Column<int>(type: "int", nullable: false),
                    Date = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    Number = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    CountryOfOrigin = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    AdministrativeTerritory = table.Column<string>(type: "nvarchar(200)", maxLength: 200, nullable: true),
                    ApprovalNumber = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ProductNameAndQuantity = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_RuPreExportCertificates", x => x.Id);
                    table.ForeignKey(
                        name: "FK_RuPreExportCertificates_RuCertificates_RuCertificateId",
                        column: x => x.RuCertificateId,
                        principalTable: "RuCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "TwCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    TwCertificateId = table.Column<int>(type: "int", nullable: false),
                    CommodityName = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    HsCode = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    ScientificName = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NumberOfPackages = table.Column<int>(type: "int", nullable: false),
                    NetWeight = table.Column<decimal>(type: "decimal(18,2)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_TwCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_TwCertificateProducts_TwCertificates_TwCertificateId",
                        column: x => x.TwCertificateId,
                        principalTable: "TwCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "UaCertificateProducts",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    UaCertificateId = table.Column<int>(type: "int", nullable: false),
                    Species = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NatureOfCommodity = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    TreatmentApprovalNumber = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    ManufacturingPlant = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: true),
                    NumberOfPackaging = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true),
                    TypeOfPackaging = table.Column<string>(type: "nvarchar(250)", maxLength: 250, nullable: true),
                    NetWeight = table.Column<string>(type: "nvarchar(100)", maxLength: 100, nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_UaCertificateProducts", x => x.Id);
                    table.ForeignKey(
                        name: "FK_UaCertificateProducts_UaCertificates_UaCertificateId",
                        column: x => x.UaCertificateId,
                        principalTable: "UaCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_AmAttachments_AmCertificateId",
                table: "AmAttachments",
                column: "AmCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_AmCertificates_CertificateRequestId",
                table: "AmCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_AmCertificates_CompanyUserId",
                table: "AmCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_AmCertificates_CreatedAt",
                table: "AmCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_AmPreExportCertificates_AmCertificateId",
                table: "AmPreExportCertificates",
                column: "AmCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_AspNetRoleClaims_RoleId",
                table: "AspNetRoleClaims",
                column: "RoleId");

            migrationBuilder.CreateIndex(
                name: "RoleNameIndex",
                table: "AspNetRoles",
                column: "NormalizedName",
                unique: true,
                filter: "[NormalizedName] IS NOT NULL");

            migrationBuilder.CreateIndex(
                name: "IX_AspNetUserClaims_UserId",
                table: "AspNetUserClaims",
                column: "UserId");

            migrationBuilder.CreateIndex(
                name: "IX_AspNetUserLogins_UserId",
                table: "AspNetUserLogins",
                column: "UserId");

            migrationBuilder.CreateIndex(
                name: "IX_AspNetUserRoles_RoleId",
                table: "AspNetUserRoles",
                column: "RoleId");

            migrationBuilder.CreateIndex(
                name: "EmailIndex",
                table: "AspNetUsers",
                column: "NormalizedEmail");

            migrationBuilder.CreateIndex(
                name: "UserNameIndex",
                table: "AspNetUsers",
                column: "NormalizedUserName",
                unique: true,
                filter: "[NormalizedUserName] IS NOT NULL");

            migrationBuilder.CreateIndex(
                name: "IX_AuCertificateProducts_AuCertificateId",
                table: "AuCertificateProducts",
                column: "AuCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_AuCertificates_CertificateRequestId",
                table: "AuCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_AuCertificates_CompanyUserId",
                table: "AuCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_AuCertificates_CreatedAt",
                table: "AuCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_BrCertificateProducts_BrCertificateId",
                table: "BrCertificateProducts",
                column: "BrCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_BrCertificates_CertificateRequestId",
                table: "BrCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_BrCertificates_CompanyUserId",
                table: "BrCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_BrCertificates_CreatedAt",
                table: "BrCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_CertificateRequests_CompanyUserId_CreatedAt",
                table: "CertificateRequests",
                columns: new[] { "CompanyUserId", "CreatedAt" });

            migrationBuilder.CreateIndex(
                name: "IX_CertificateRequests_CountryId",
                table: "CertificateRequests",
                column: "CountryId");

            migrationBuilder.CreateIndex(
                name: "IX_CertificateRequests_ReferenceNumber",
                table: "CertificateRequests",
                column: "ReferenceNumber",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_CertificateRequests_Status",
                table: "CertificateRequests",
                column: "Status");

            migrationBuilder.CreateIndex(
                name: "IX_ChCertificates_CertificateRequestId",
                table: "ChCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_ChCertificates_CompanyUserId",
                table: "ChCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_ChCertificates_CreatedAt",
                table: "ChCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_Companies_UserId",
                table: "Companies",
                column: "UserId");

            migrationBuilder.CreateIndex(
                name: "IX_Countries_Name",
                table: "Countries",
                column: "Name",
                unique: true);

            migrationBuilder.CreateIndex(
                name: "IX_HkCertificateProducts_HkCertificateId",
                table: "HkCertificateProducts",
                column: "HkCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_HkCertificates_CertificateRequestId",
                table: "HkCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_HkCertificates_CompanyUserId",
                table: "HkCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_HkCertificates_CreatedAt",
                table: "HkCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_IdCertificateProducts_IdCertificateId",
                table: "IdCertificateProducts",
                column: "IdCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_IdCertificates_CertificateRequestId",
                table: "IdCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_IdCertificates_CompanyUserId",
                table: "IdCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_IdCertificates_CreatedAt",
                table: "IdCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_IndCertificateProducts_IndCertificateId",
                table: "IndCertificateProducts",
                column: "IndCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_IndCertificates_CertificateRequestId",
                table: "IndCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_IndCertificates_CompanyUserId",
                table: "IndCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_IndCertificates_CreatedAt",
                table: "IndCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_JpCertificates_CertificateRequestId",
                table: "JpCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_JpCertificates_CompanyUserId",
                table: "JpCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_JpCertificates_CreatedAt",
                table: "JpCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_KwCertificateProducts_KwCertificateId",
                table: "KwCertificateProducts",
                column: "KwCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_KwCertificates_CertificateRequestId",
                table: "KwCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_KwCertificates_CompanyUserId",
                table: "KwCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_KwCertificates_CreatedAt",
                table: "KwCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_MyCertificateProducts_MyCertificateId",
                table: "MyCertificateProducts",
                column: "MyCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_MyCertificates_CertificateRequestId",
                table: "MyCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_MyCertificates_CompanyUserId",
                table: "MyCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_MyCertificates_CreatedAt",
                table: "MyCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_NzCertificateProducts_NzCertificateId",
                table: "NzCertificateProducts",
                column: "NzCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_NzCertificates_CertificateRequestId",
                table: "NzCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_NzCertificates_CompanyUserId",
                table: "NzCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_NzCertificates_CreatedAt",
                table: "NzCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_RuAttachments_RuCertificateId",
                table: "RuAttachments",
                column: "RuCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_RuCertificates_CertificateRequestId",
                table: "RuCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_RuCertificates_CompanyUserId",
                table: "RuCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_RuCertificates_CreatedAt",
                table: "RuCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_RuPreExportCertificates_RuCertificateId",
                table: "RuPreExportCertificates",
                column: "RuCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_TwCertificateProducts_TwCertificateId",
                table: "TwCertificateProducts",
                column: "TwCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_TwCertificates_CertificateRequestId",
                table: "TwCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_TwCertificates_CompanyUserId",
                table: "TwCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_TwCertificates_CreatedAt",
                table: "TwCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_UaCertificateProducts_UaCertificateId",
                table: "UaCertificateProducts",
                column: "UaCertificateId");

            migrationBuilder.CreateIndex(
                name: "IX_UaCertificates_CertificateRequestId",
                table: "UaCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_UaCertificates_CompanyUserId",
                table: "UaCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_UaCertificates_CreatedAt",
                table: "UaCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_UsaCertificates_CertificateRequestId",
                table: "UsaCertificates",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_UsaCertificates_CompanyUserId",
                table: "UsaCertificates",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_UsaCertificates_CreatedAt",
                table: "UsaCertificates",
                column: "CreatedAt");

            migrationBuilder.CreateIndex(
                name: "IX_VetCertificateForms_CertificateRequestId",
                table: "VetCertificateForms",
                column: "CertificateRequestId");

            migrationBuilder.CreateIndex(
                name: "IX_VetCertificateForms_CompanyUserId",
                table: "VetCertificateForms",
                column: "CompanyUserId");

            migrationBuilder.CreateIndex(
                name: "IX_VetCertificateForms_CreatedAt",
                table: "VetCertificateForms",
                column: "CreatedAt");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "AmAttachments");

            migrationBuilder.DropTable(
                name: "AmPreExportCertificates");

            migrationBuilder.DropTable(
                name: "AspNetRoleClaims");

            migrationBuilder.DropTable(
                name: "AspNetUserClaims");

            migrationBuilder.DropTable(
                name: "AspNetUserLogins");

            migrationBuilder.DropTable(
                name: "AspNetUserRoles");

            migrationBuilder.DropTable(
                name: "AspNetUserTokens");

            migrationBuilder.DropTable(
                name: "AuCertificateProducts");

            migrationBuilder.DropTable(
                name: "BrCertificateProducts");

            migrationBuilder.DropTable(
                name: "ChCertificates");

            migrationBuilder.DropTable(
                name: "Companies");

            migrationBuilder.DropTable(
                name: "HkCertificateProducts");

            migrationBuilder.DropTable(
                name: "IdCertificateProducts");

            migrationBuilder.DropTable(
                name: "IndCertificateProducts");

            migrationBuilder.DropTable(
                name: "JpCertificates");

            migrationBuilder.DropTable(
                name: "KwCertificateProducts");

            migrationBuilder.DropTable(
                name: "MyCertificateProducts");

            migrationBuilder.DropTable(
                name: "NzCertificateProducts");

            migrationBuilder.DropTable(
                name: "RuAttachments");

            migrationBuilder.DropTable(
                name: "RuPreExportCertificates");

            migrationBuilder.DropTable(
                name: "TwCertificateProducts");

            migrationBuilder.DropTable(
                name: "UaCertificateProducts");

            migrationBuilder.DropTable(
                name: "UsaCertificates");

            migrationBuilder.DropTable(
                name: "VetCertificateForms");

            migrationBuilder.DropTable(
                name: "AmCertificates");

            migrationBuilder.DropTable(
                name: "AspNetRoles");

            migrationBuilder.DropTable(
                name: "AuCertificates");

            migrationBuilder.DropTable(
                name: "BrCertificates");

            migrationBuilder.DropTable(
                name: "HkCertificates");

            migrationBuilder.DropTable(
                name: "IdCertificates");

            migrationBuilder.DropTable(
                name: "IndCertificates");

            migrationBuilder.DropTable(
                name: "KwCertificates");

            migrationBuilder.DropTable(
                name: "MyCertificates");

            migrationBuilder.DropTable(
                name: "NzCertificates");

            migrationBuilder.DropTable(
                name: "RuCertificates");

            migrationBuilder.DropTable(
                name: "TwCertificates");

            migrationBuilder.DropTable(
                name: "UaCertificates");

            migrationBuilder.DropTable(
                name: "CertificateRequests");

            migrationBuilder.DropTable(
                name: "AspNetUsers");

            migrationBuilder.DropTable(
                name: "Countries");
        }
    }
}
