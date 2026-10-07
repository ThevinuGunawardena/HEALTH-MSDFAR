using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace MEA.Server.Migrations
{
    /// <inheritdoc />
    public partial class SeparateEuNonEuReferenceSequence : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "Prefix",
                table: "ReferenceSequences",
                newName: "NonEuPrefix");

            migrationBuilder.RenameColumn(
                name: "CurrentNumber",
                table: "ReferenceSequences",
                newName: "NonEuCurrentNumber");

            migrationBuilder.AddColumn<int>(
                name: "EuCurrentNumber",
                table: "ReferenceSequences",
                type: "int",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.AddColumn<string>(
                name: "EuPrefix",
                table: "ReferenceSequences",
                type: "nvarchar(10)",
                maxLength: 10,
                nullable: false,
                defaultValue: "");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "EuCurrentNumber",
                table: "ReferenceSequences");

            migrationBuilder.DropColumn(
                name: "EuPrefix",
                table: "ReferenceSequences");

            migrationBuilder.RenameColumn(
                name: "NonEuPrefix",
                table: "ReferenceSequences",
                newName: "Prefix");

            migrationBuilder.RenameColumn(
                name: "NonEuCurrentNumber",
                table: "ReferenceSequences",
                newName: "CurrentNumber");
        }
    }
}
