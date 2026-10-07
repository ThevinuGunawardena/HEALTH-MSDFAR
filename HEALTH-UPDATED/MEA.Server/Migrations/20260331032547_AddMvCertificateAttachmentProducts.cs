using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddMvCertificateAttachmentProducts : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "MvCertificateProductAttachments",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    MvCertificateId = table.Column<int>(type: "int", nullable: false),
                    Product = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    LotIdentifier = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    TypeOfPackaging = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    NumberOfKgs = table.Column<string>(type: "nvarchar(max)", nullable: true),
                    NumberOfBoxes = table.Column<int>(type: "int", nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_MvCertificateProductAttachments", x => x.Id);
                    table.ForeignKey(
                        name: "FK_MvCertificateProductAttachments_MvCertificates_MvCertificateId",
                        column: x => x.MvCertificateId,
                        principalTable: "MvCertificates",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_MvCertificateProductAttachments_MvCertificateId",
                table: "MvCertificateProductAttachments",
                column: "MvCertificateId");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "MvCertificateProductAttachments");
        }
    }
}
