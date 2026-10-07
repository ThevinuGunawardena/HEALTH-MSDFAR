using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class AddVetCertificateAttachments : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "VetCertificateAttachments",
                columns: table => new
                {
                    Id = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    VetCertificateFormId = table.Column<int>(type: "int", nullable: false),
                    FileOrder = table.Column<int>(type: "int", nullable: false),
                    OriginalFileName = table.Column<string>(type: "nvarchar(260)", maxLength: 260, nullable: true),
                    SecondaryFileName = table.Column<string>(type: "nvarchar(260)", maxLength: 260, nullable: true),
                    ContentType = table.Column<string>(type: "nvarchar(120)", maxLength: 120, nullable: true),
                    FileContent = table.Column<byte[]>(type: "varbinary(max)", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_VetCertificateAttachments", x => x.Id);
                    table.ForeignKey(
                        name: "FK_VetCertificateAttachments_VetCertificateForms_VetCertificateFormId",
                        column: x => x.VetCertificateFormId,
                        principalTable: "VetCertificateForms",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_VetCertificateAttachments_VetCertificateFormId",
                table: "VetCertificateAttachments",
                column: "VetCertificateFormId");

            migrationBuilder.CreateIndex(
                name: "IX_VetCertificateAttachments_VetCertificateFormId_FileOrder",
                table: "VetCertificateAttachments",
                columns: new[] { "VetCertificateFormId", "FileOrder" });

            migrationBuilder.Sql(@"
                INSERT INTO VetCertificateAttachments (VetCertificateFormId, FileOrder, OriginalFileName, SecondaryFileName, ContentType, FileContent)
                SELECT Id, 1, 'legacy-uploaded-certificate', NULL, NULL, UploadedCertificateFile
                FROM VetCertificateForms
                WHERE UploadedCertificateFile IS NOT NULL AND DATALENGTH(UploadedCertificateFile) > 0;
            ");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "VetCertificateAttachments");
        }
    }
}
