using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class RefineMvCertificate : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CommoditiesFor",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "CommoditiesForOther",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "CompetentAuthorityOrigin",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "ConditionsOfStorageOther",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "EstimatedDateOfDeparture",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "GrossWeight",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "NetWeight",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "NumberOfPackages",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "PlaceOfDispatch",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "PlaceOfOrigin",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "RegionOfOriginCode",
                table: "MvCertificates");

            migrationBuilder.RenameColumn(
                name: "RegionOfOrigin",
                table: "MvCertificates",
                newName: "CountryOfDestination");

            migrationBuilder.RenameColumn(
                name: "MeansOfTransportNo",
                table: "MvCertificates",
                newName: "TotalQuantity");

            migrationBuilder.RenameColumn(
                name: "MeansOfTransport",
                table: "MvCertificates",
                newName: "ApprovalNumberOfEstablishments");

            migrationBuilder.RenameColumn(
                name: "ContainerNumber",
                table: "MvCertificates",
                newName: "TotalNumberOfPackages");

            migrationBuilder.AlterColumn<string>(
                name: "SealNumber",
                table: "MvCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(100)",
                oldMaxLength: 100,
                oldNullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CountryOfDestinationISO",
                table: "MvCertificates",
                type: "nvarchar(10)",
                maxLength: 10,
                nullable: true);

            migrationBuilder.AddColumn<bool>(
                name: "TransportAeroPlane",
                table: "MvCertificates",
                type: "bit",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "TransportOther",
                table: "MvCertificates",
                type: "bit",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "TransportRailway",
                table: "MvCertificates",
                type: "bit",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "TransportRoad",
                table: "MvCertificates",
                type: "bit",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<bool>(
                name: "TransportShip",
                table: "MvCertificates",
                type: "bit",
                nullable: false,
                defaultValue: false);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CountryOfDestinationISO",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "TransportAeroPlane",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "TransportOther",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "TransportRailway",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "TransportRoad",
                table: "MvCertificates");

            migrationBuilder.DropColumn(
                name: "TransportShip",
                table: "MvCertificates");

            migrationBuilder.RenameColumn(
                name: "TotalQuantity",
                table: "MvCertificates",
                newName: "MeansOfTransportNo");

            migrationBuilder.RenameColumn(
                name: "TotalNumberOfPackages",
                table: "MvCertificates",
                newName: "ContainerNumber");

            migrationBuilder.RenameColumn(
                name: "CountryOfDestination",
                table: "MvCertificates",
                newName: "RegionOfOrigin");

            migrationBuilder.RenameColumn(
                name: "ApprovalNumberOfEstablishments",
                table: "MvCertificates",
                newName: "MeansOfTransport");

            migrationBuilder.AlterColumn<string>(
                name: "SealNumber",
                table: "MvCertificates",
                type: "nvarchar(100)",
                maxLength: 100,
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(250)",
                oldMaxLength: 250,
                oldNullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CommoditiesFor",
                table: "MvCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CommoditiesForOther",
                table: "MvCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "CompetentAuthorityOrigin",
                table: "MvCertificates",
                type: "nvarchar(250)",
                maxLength: 250,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ConditionsOfStorageOther",
                table: "MvCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);

            migrationBuilder.AddColumn<DateTime>(
                name: "EstimatedDateOfDeparture",
                table: "MvCertificates",
                type: "datetime2",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "GrossWeight",
                table: "MvCertificates",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NetWeight",
                table: "MvCertificates",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "NumberOfPackages",
                table: "MvCertificates",
                type: "int",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "PlaceOfDispatch",
                table: "MvCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "PlaceOfOrigin",
                table: "MvCertificates",
                type: "nvarchar(500)",
                maxLength: 500,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "RegionOfOriginCode",
                table: "MvCertificates",
                type: "nvarchar(50)",
                maxLength: 50,
                nullable: true);
        }
    }
}
