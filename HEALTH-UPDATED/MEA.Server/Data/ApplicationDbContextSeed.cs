using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using MEA.Server.Entities;
using System;
using System.Linq;

namespace MEA.Server.Data
{
    public static class ApplicationDbContextSeed
    {
        public static async Task SeedAsync(
            AppDbContext context, 
            RoleManager<IdentityRole> roleManager,
            UserManager<AppUser> userManager)
        {
            await context.Database.EnsureCreatedAsync();
            await EnsureSchemaUpdatesAsync(context);

            // Seed roles if they don't exist
            string[] roles = { "Admin", "Company", "User" };

            foreach (var roleName in roles)
            {
                var roleExists = await roleManager.RoleExistsAsync(roleName);
                if (!roleExists)
                {
                    await roleManager.CreateAsync(new IdentityRole(roleName));
                }
            }

            // Seed company lookup data
            await SeedProductCertificatesAsync(context);
            await SeedCompanyStatusesAsync(context);
            await SeedListedCountriesAsync(context);

            // Seed sample users
            await SeedUsersAsync(userManager, context);

            // Seed countries
            await SeedCountriesAsync(context);

            // Seed sample submitted certificates across all 22 countries
            await WorldCertificatesSeeder.SeedAllAsync(context, userManager);

            // Clean up sample dummy replacement requests
            await CleanupSampleReplacementRequestsAsync(context);
        }

        private static async Task EnsureSchemaUpdatesAsync(AppDbContext context)
        {
            try
            {
                var sql = @"
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'ReplacementRequests')
BEGIN
    CREATE TABLE [ReplacementRequests] (
        [Id] int NOT NULL IDENTITY,
        [OriginalCertificateRequestId] int NULL,
        [OriginalReferenceNumber] nvarchar(max) NOT NULL DEFAULT '',
        [ReplacementReferenceNumber] nvarchar(max) NOT NULL DEFAULT '',
        [CompanyUserId] nvarchar(max) NOT NULL DEFAULT '',
        [CompanyName] nvarchar(max) NOT NULL DEFAULT '',
        [Country] nvarchar(max) NOT NULL DEFAULT '',
        [CertificateType] nvarchar(max) NOT NULL DEFAULT '',
        [Reason] nvarchar(max) NOT NULL DEFAULT '',
        [Remarks] nvarchar(max) NULL,
        [RejectionReason] nvarchar(max) NULL,
        [Status] int NOT NULL DEFAULT 0,
        [CreatedAt] datetime2 NOT NULL DEFAULT '0001-01-01T00:00:00.0000000',
        [ProcessedAt] datetime2 NULL,
        [ProcessedByUserId] nvarchar(max) NULL,
        CONSTRAINT [PK_ReplacementRequests] PRIMARY KEY ([Id])
    );
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'CertificateRequests')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CertificateRequests') AND name = 'CancelsAndReplacesRef')
        ALTER TABLE [CertificateRequests] ADD [CancelsAndReplacesRef] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CertificateRequests') AND name = 'CancelsAndReplacesDate')
        ALTER TABLE [CertificateRequests] ADD [CancelsAndReplacesDate] datetime2 NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CertificateRequests') AND name = 'ReplacedCertificateRequestId')
        ALTER TABLE [CertificateRequests] ADD [ReplacedCertificateRequestId] int NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'VetCertificateForms')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Attestation61_1')
        ALTER TABLE [VetCertificateForms] ADD [Attestation61_1] bit NULL DEFAULT 1;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Attestation61_2')
        ALTER TABLE [VetCertificateForms] ADD [Attestation61_2] bit NULL DEFAULT 1;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Attestation61_3')
        ALTER TABLE [VetCertificateForms] ADD [Attestation61_3] bit NULL DEFAULT 1;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Attestation61_4')
        ALTER TABLE [VetCertificateForms] ADD [Attestation61_4] bit NULL DEFAULT 1;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Attestation61_5')
        ALTER TABLE [VetCertificateForms] ADD [Attestation61_5] bit NULL DEFAULT 1;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Attestation62_1')
        ALTER TABLE [VetCertificateForms] ADD [Attestation62_1] bit NULL DEFAULT 1;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Attestation62_2')
        ALTER TABLE [VetCertificateForms] ADD [Attestation62_2] bit NULL DEFAULT 1;

    IF EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('VetCertificateForms') AND name = 'Signature')
        ALTER TABLE [VetCertificateForms] ALTER COLUMN [Signature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'UsaCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CertificateNumber')
        ALTER TABLE [UsaCertificates] ADD [CertificateNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CompetentAuthority')
        ALTER TABLE [UsaCertificates] ADD [CompetentAuthority] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CertifyingBody')
        ALTER TABLE [UsaCertificates] ADD [CertifyingBody] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CountryOfOrigin')
        ALTER TABLE [UsaCertificates] ADD [CountryOfOrigin] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CountryOfOriginISO')
        ALTER TABLE [UsaCertificates] ADD [CountryOfOriginISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CountryOfDestination')
        ALTER TABLE [UsaCertificates] ADD [CountryOfDestination] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CountryOfDestinationISO')
        ALTER TABLE [UsaCertificates] ADD [CountryOfDestinationISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'PlaceOfLoading')
        ALTER TABLE [UsaCertificates] ADD [PlaceOfLoading] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'TransportAeroPlane')
        ALTER TABLE [UsaCertificates] ADD [TransportAeroPlane] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'TransportShip')
        ALTER TABLE [UsaCertificates] ADD [TransportShip] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'TransportRailway')
        ALTER TABLE [UsaCertificates] ADD [TransportRailway] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'TransportRoad')
        ALTER TABLE [UsaCertificates] ADD [TransportRoad] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'TransportOther')
        ALTER TABLE [UsaCertificates] ADD [TransportOther] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'PointsOfEntry')
        ALTER TABLE [UsaCertificates] ADD [PointsOfEntry] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'ConditionsOfStorage')
        ALTER TABLE [UsaCertificates] ADD [ConditionsOfStorage] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'TotalQuantity')
        ALTER TABLE [UsaCertificates] ADD [TotalQuantity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'SealNumber')
        ALTER TABLE [UsaCertificates] ADD [SealNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'TotalNumberOfPackages')
        ALTER TABLE [UsaCertificates] ADD [TotalNumberOfPackages] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'ApprovalNumberOfEstablishments')
        ALTER TABLE [UsaCertificates] ADD [ApprovalNumberOfEstablishments] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'DescriptionOfCommodity')
        ALTER TABLE [UsaCertificates] ADD [DescriptionOfCommodity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'ProcessingPlantName')
        ALTER TABLE [UsaCertificates] ADD [ProcessingPlantName] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'ProcessingPlantAddress')
        ALTER TABLE [UsaCertificates] ADD [ProcessingPlantAddress] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CompetentAuthorityRegNo')
        ALTER TABLE [UsaCertificates] ADD [CompetentAuthorityRegNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'Designation')
        ALTER TABLE [UsaCertificates] ADD [Designation] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CompanyRegistrationNo')
        ALTER TABLE [UsaCertificates] ADD [CompanyRegistrationNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [UsaCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [UsaCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [UsaCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [UsaCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UsaCertificates') AND name = 'CertificateType')
        ALTER TABLE [UsaCertificates] ADD [CertificateType] nvarchar(max) NULL;
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'UsaCertificateProductAttachments')
BEGIN
    CREATE TABLE [UsaCertificateProductAttachments] (
        [Id] int IDENTITY(1,1) NOT NULL,
        [UsaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_UsaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_UsaCertificateProductAttachments_UsaCertificates_UsaCertificateId] FOREIGN KEY ([UsaCertificateId]) REFERENCES [UsaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'CaCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CertificateNumber')
        ALTER TABLE [CaCertificates] ADD [CertificateNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CompetentAuthority')
        ALTER TABLE [CaCertificates] ADD [CompetentAuthority] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CertifyingBody')
        ALTER TABLE [CaCertificates] ADD [CertifyingBody] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CountryOfOrigin')
        ALTER TABLE [CaCertificates] ADD [CountryOfOrigin] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CountryOfOriginISO')
        ALTER TABLE [CaCertificates] ADD [CountryOfOriginISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CountryOfDestination')
        ALTER TABLE [CaCertificates] ADD [CountryOfDestination] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CountryOfDestinationISO')
        ALTER TABLE [CaCertificates] ADD [CountryOfDestinationISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'PlaceOfLoading')
        ALTER TABLE [CaCertificates] ADD [PlaceOfLoading] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'TransportAeroPlane')
        ALTER TABLE [CaCertificates] ADD [TransportAeroPlane] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'TransportShip')
        ALTER TABLE [CaCertificates] ADD [TransportShip] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'TransportRailway')
        ALTER TABLE [CaCertificates] ADD [TransportRailway] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'TransportRoad')
        ALTER TABLE [CaCertificates] ADD [TransportRoad] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'TransportOther')
        ALTER TABLE [CaCertificates] ADD [TransportOther] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'PointsOfEntry')
        ALTER TABLE [CaCertificates] ADD [PointsOfEntry] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'ConditionsOfStorage')
        ALTER TABLE [CaCertificates] ADD [ConditionsOfStorage] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'TotalQuantity')
        ALTER TABLE [CaCertificates] ADD [TotalQuantity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'SealNumber')
        ALTER TABLE [CaCertificates] ADD [SealNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'TotalNumberOfPackages')
        ALTER TABLE [CaCertificates] ADD [TotalNumberOfPackages] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'ApprovalNumberOfEstablishments')
        ALTER TABLE [CaCertificates] ADD [ApprovalNumberOfEstablishments] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'DescriptionOfCommodity')
        ALTER TABLE [CaCertificates] ADD [DescriptionOfCommodity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'ProcessingPlantName')
        ALTER TABLE [CaCertificates] ADD [ProcessingPlantName] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'ProcessingPlantAddress')
        ALTER TABLE [CaCertificates] ADD [ProcessingPlantAddress] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CompetentAuthorityRegNo')
        ALTER TABLE [CaCertificates] ADD [CompetentAuthorityRegNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'Designation')
        ALTER TABLE [CaCertificates] ADD [Designation] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CompanyRegistrationNo')
        ALTER TABLE [CaCertificates] ADD [CompanyRegistrationNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [CaCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [CaCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [CaCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [CaCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('CaCertificates') AND name = 'CertificateType')
        ALTER TABLE [CaCertificates] ADD [CertificateType] nvarchar(max) NULL;
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'CaCertificateProductAttachments')
BEGIN
    CREATE TABLE [CaCertificateProductAttachments] (
        [Id] int IDENTITY(1,1) NOT NULL,
        [CaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_CaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_CaCertificateProductAttachments_CaCertificates_CaCertificateId] FOREIGN KEY ([CaCertificateId]) REFERENCES [CaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'SaCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CertificateNumber')
        ALTER TABLE [SaCertificates] ADD [CertificateNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CompetentAuthority')
        ALTER TABLE [SaCertificates] ADD [CompetentAuthority] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CertifyingBody')
        ALTER TABLE [SaCertificates] ADD [CertifyingBody] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CountryOfOrigin')
        ALTER TABLE [SaCertificates] ADD [CountryOfOrigin] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CountryOfOriginISO')
        ALTER TABLE [SaCertificates] ADD [CountryOfOriginISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CountryOfDestination')
        ALTER TABLE [SaCertificates] ADD [CountryOfDestination] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CountryOfDestinationISO')
        ALTER TABLE [SaCertificates] ADD [CountryOfDestinationISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'PlaceOfLoading')
        ALTER TABLE [SaCertificates] ADD [PlaceOfLoading] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'TransportAeroPlane')
        ALTER TABLE [SaCertificates] ADD [TransportAeroPlane] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'TransportShip')
        ALTER TABLE [SaCertificates] ADD [TransportShip] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'TransportRailway')
        ALTER TABLE [SaCertificates] ADD [TransportRailway] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'TransportRoad')
        ALTER TABLE [SaCertificates] ADD [TransportRoad] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'TransportOther')
        ALTER TABLE [SaCertificates] ADD [TransportOther] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'PointsOfEntry')
        ALTER TABLE [SaCertificates] ADD [PointsOfEntry] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'ConditionsOfStorage')
        ALTER TABLE [SaCertificates] ADD [ConditionsOfStorage] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'TotalQuantity')
        ALTER TABLE [SaCertificates] ADD [TotalQuantity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'SealNumber')
        ALTER TABLE [SaCertificates] ADD [SealNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'TotalNumberOfPackages')
        ALTER TABLE [SaCertificates] ADD [TotalNumberOfPackages] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'ApprovalNumberOfEstablishments')
        ALTER TABLE [SaCertificates] ADD [ApprovalNumberOfEstablishments] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'DescriptionOfCommodity')
        ALTER TABLE [SaCertificates] ADD [DescriptionOfCommodity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'ProcessingPlantName')
        ALTER TABLE [SaCertificates] ADD [ProcessingPlantName] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'ProcessingPlantAddress')
        ALTER TABLE [SaCertificates] ADD [ProcessingPlantAddress] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CompetentAuthorityRegNo')
        ALTER TABLE [SaCertificates] ADD [CompetentAuthorityRegNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'Designation')
        ALTER TABLE [SaCertificates] ADD [Designation] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CompanyRegistrationNo')
        ALTER TABLE [SaCertificates] ADD [CompanyRegistrationNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [SaCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [SaCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [SaCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [SaCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'CertificateType')
        ALTER TABLE [SaCertificates] ADD [CertificateType] nvarchar(max) NULL;
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'SaCertificateProductAttachments')
BEGIN
    CREATE TABLE [SaCertificateProductAttachments] (
        [Id] int IDENTITY(1,1) NOT NULL,
        [SaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_SaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_SaCertificateProductAttachments_SaCertificates_SaCertificateId] FOREIGN KEY ([SaCertificateId]) REFERENCES [SaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'ZaCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CertificateNumber')
        ALTER TABLE [ZaCertificates] ADD [CertificateNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CompetentAuthority')
        ALTER TABLE [ZaCertificates] ADD [CompetentAuthority] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CertifyingBody')
        ALTER TABLE [ZaCertificates] ADD [CertifyingBody] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CountryOfOrigin')
        ALTER TABLE [ZaCertificates] ADD [CountryOfOrigin] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CountryOfOriginISO')
        ALTER TABLE [ZaCertificates] ADD [CountryOfOriginISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CountryOfDestination')
        ALTER TABLE [ZaCertificates] ADD [CountryOfDestination] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CountryOfDestinationISO')
        ALTER TABLE [ZaCertificates] ADD [CountryOfDestinationISO] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'PlaceOfLoading')
        ALTER TABLE [ZaCertificates] ADD [PlaceOfLoading] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'TransportAeroPlane')
        ALTER TABLE [ZaCertificates] ADD [TransportAeroPlane] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'TransportShip')
        ALTER TABLE [ZaCertificates] ADD [TransportShip] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'TransportRailway')
        ALTER TABLE [ZaCertificates] ADD [TransportRailway] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'TransportRoad')
        ALTER TABLE [ZaCertificates] ADD [TransportRoad] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'TransportOther')
        ALTER TABLE [ZaCertificates] ADD [TransportOther] bit NOT NULL DEFAULT 0;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'PointsOfEntry')
        ALTER TABLE [ZaCertificates] ADD [PointsOfEntry] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'ConditionsOfStorage')
        ALTER TABLE [ZaCertificates] ADD [ConditionsOfStorage] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'TotalQuantity')
        ALTER TABLE [ZaCertificates] ADD [TotalQuantity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'SealNumber')
        ALTER TABLE [ZaCertificates] ADD [SealNumber] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'TotalNumberOfPackages')
        ALTER TABLE [ZaCertificates] ADD [TotalNumberOfPackages] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'ApprovalNumberOfEstablishments')
        ALTER TABLE [ZaCertificates] ADD [ApprovalNumberOfEstablishments] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'DescriptionOfCommodity')
        ALTER TABLE [ZaCertificates] ADD [DescriptionOfCommodity] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'ProcessingPlantName')
        ALTER TABLE [ZaCertificates] ADD [ProcessingPlantName] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'ProcessingPlantAddress')
        ALTER TABLE [ZaCertificates] ADD [ProcessingPlantAddress] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CompetentAuthorityRegNo')
        ALTER TABLE [ZaCertificates] ADD [CompetentAuthorityRegNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'Designation')
        ALTER TABLE [ZaCertificates] ADD [Designation] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CompanyRegistrationNo')
        ALTER TABLE [ZaCertificates] ADD [CompanyRegistrationNo] nvarchar(max) NULL;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [ZaCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [ZaCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [ZaCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [ZaCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'CertificateType')
        ALTER TABLE [ZaCertificates] ADD [CertificateType] nvarchar(max) NULL;
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'ZaCertificateProductAttachments')
BEGIN
    CREATE TABLE [ZaCertificateProductAttachments] (
        [Id] int IDENTITY(1,1) NOT NULL,
        [ZaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_ZaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_ZaCertificateProductAttachments_ZaCertificates_ZaCertificateId] FOREIGN KEY ([ZaCertificateId]) REFERENCES [ZaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'HkCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('HkCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [HkCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [HkCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('HkCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [HkCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [HkCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'IndCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('IndCertificates') AND name = 'AuthorizedOfficialSignature')
        ALTER TABLE [IndCertificates] ADD [AuthorizedOfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [IndCertificates] ALTER COLUMN [AuthorizedOfficialSignature] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('IndCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [IndCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [IndCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'IlCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('IlCertificates') AND name = 'Stamp')
        ALTER TABLE [IlCertificates] ADD [Stamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [IlCertificates] ALTER COLUMN [Stamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('IlCertificates') AND name = 'Signature')
        ALTER TABLE [IlCertificates] ADD [Signature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [IlCertificates] ALTER COLUMN [Signature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'JpCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('JpCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [JpCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [JpCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('JpCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [JpCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [JpCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'KzCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('KzCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [KzCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [KzCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('KzCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [KzCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [KzCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'RuCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('RuCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [RuCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [RuCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('RuCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [RuCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [RuCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'KwCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('KwCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [KwCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [KwCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('KwCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [KwCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [KwCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'MyCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('MyCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [MyCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [MyCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('MyCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [MyCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [MyCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'NzCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('NzCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [NzCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [NzCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('NzCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [NzCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [NzCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'SaCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [SaCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [SaCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('SaCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [SaCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [SaCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'ZaCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [ZaCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [ZaCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ZaCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [ZaCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [ZaCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'TwCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('TwCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [TwCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [TwCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('TwCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [TwCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [TwCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'UaCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UaCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [UaCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [UaCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UaCertificates') AND name = 'OfficialSignature')
        ALTER TABLE [UaCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [UaCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'ChCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('ChCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [ChCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [ChCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'AuCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('AuCertificates') AND name = 'Stamp')
        ALTER TABLE [AuCertificates] ADD [Stamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [AuCertificates] ALTER COLUMN [Stamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('AuCertificates') AND name = 'Signature')
        ALTER TABLE [AuCertificates] ADD [Signature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [AuCertificates] ALTER COLUMN [Signature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'AmCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('AmCertificates') AND name = 'Stamp')
        ALTER TABLE [AmCertificates] ADD [Stamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [AmCertificates] ALTER COLUMN [Stamp] nvarchar(max) NULL;

    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('AmCertificates') AND name = 'Signature')
        ALTER TABLE [AmCertificates] ADD [Signature] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [AmCertificates] ALTER COLUMN [Signature] nvarchar(max) NULL;
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'BrCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('BrCertificates') AND name = 'OfficialStamp')
        ALTER TABLE [BrCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
    ELSE
        ALTER TABLE [BrCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;
END



IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'BrCertificates')
BEGIN
    CREATE TABLE [BrCertificates] (
        [Id] int NOT NULL IDENTITY,
        [CertificateRequestId] int NULL,
        [CompanyUserId] nvarchar(450) NOT NULL,
        [CreatedAt] datetime2 NOT NULL DEFAULT (GETUTCDATE()),
        [RefNumber] nvarchar(50) NULL,
        [CountryOfExport] nvarchar(120) NULL,
        [CertificateNo] nvarchar(50) NULL,
        [CompetentAuthority] nvarchar(120) NULL,
        [LocalCompetentAuthority] nvarchar(120) NULL,
        [ExporterName] nvarchar(120) NULL,
        [ExporterAddress] nvarchar(250) NULL,
        [ImporterName] nvarchar(120) NULL,
        [ImporterAddress] nvarchar(250) NULL,
        [CountryOrigin] nvarchar(120) NULL,
        [CountryOriginISO] nvarchar(10) NULL,
        [CountryOfDestination] nvarchar(120) NULL,
        [CountryDestinationISO] nvarchar(10) NULL,
        [PlaceOfLoading] nvarchar(120) NULL,
        [TransportAeroPlane] bit NOT NULL DEFAULT 0,
        [TransportShip] bit NOT NULL DEFAULT 0,
        [TransportRailwayWagon] bit NOT NULL DEFAULT 0,
        [TransportRoadVehicle] bit NOT NULL DEFAULT 0,
        [TransportOther] bit NOT NULL DEFAULT 0,
        [DeclaredPointOfEntry] nvarchar(120) NULL,
        [ConditionsForTransportStorage] nvarchar(250) NULL,
        [IdentificationOfContainers] nvarchar(120) NULL,
        [IdentificationOfFoodProducts] nvarchar(250) NULL,
        [ProducerDetails] nvarchar(250) NULL,
        [HsCode] nvarchar(50) NULL,
        [IntendedPurpose] nvarchar(120) NULL,
        [TotalNetWeight] decimal(18,2) NOT NULL DEFAULT 0,
        [PlaceAndDate] nvarchar(120) NULL,
        [DateOfIssue] datetime2 NOT NULL DEFAULT (GETUTCDATE()),
        [OfficialStamp] nvarchar(max) NULL,
        [SignatoryUserId] nvarchar(450) NULL,
        [SignatoryName] nvarchar(200) NULL,
        [Qualification] nvarchar(200) NULL,
        [ModeloConformeCircularNo] nvarchar(120) NULL,
        [SanitaryCertification] nvarchar(250) NULL,
        CONSTRAINT [PK_BrCertificates] PRIMARY KEY ([Id])
    );
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'BrCertificates')
BEGIN
    ALTER TABLE [BrCertificates] ALTER COLUMN [CertificateNo] nvarchar(50) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [RefNumber] nvarchar(50) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [CountryOfExport] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [CompetentAuthority] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [LocalCompetentAuthority] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [ExporterName] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [ExporterAddress] nvarchar(250) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [ImporterName] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [ImporterAddress] nvarchar(250) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [CountryOrigin] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [CountryOriginISO] nvarchar(10) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [CountryOfDestination] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [CountryDestinationISO] nvarchar(10) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [PlaceOfLoading] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [DeclaredPointOfEntry] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [ConditionsForTransportStorage] nvarchar(250) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [IdentificationOfContainers] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [IdentificationOfFoodProducts] nvarchar(250) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [ProducerDetails] nvarchar(250) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [HsCode] nvarchar(50) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [IntendedPurpose] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [PlaceAndDate] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [ModeloConformeCircularNo] nvarchar(120) NULL;
    ALTER TABLE [BrCertificates] ALTER COLUMN [SanitaryCertification] nvarchar(250) NULL;
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'BrCertificateProducts')
BEGIN
    CREATE TABLE [BrCertificateProducts] (
        [Id] int NOT NULL IDENTITY,
        [BrCertificateId] int NOT NULL,
        [NameOfTheProduct] nvarchar(120) NULL,
        [ScientificName] nvarchar(120) NULL,
        [TypeOfPackaging] nvarchar(120) NULL,
        [NumberOfPackages] int NOT NULL DEFAULT 0,
        [NetWeight] decimal(18,2) NOT NULL DEFAULT 0,
        CONSTRAINT [PK_BrCertificateProducts] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_BrCertificateProducts_BrCertificates_BrCertificateId] FOREIGN KEY ([BrCertificateId]) REFERENCES [BrCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'ChAttachments')
BEGIN
    CREATE TABLE [ChAttachments] (
        [Id] int NOT NULL IDENTITY,
        [ChCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [NetWeight] decimal(18,2) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_ChAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_ChAttachments_ChCertificates_ChCertificateId] FOREIGN KEY ([ChCertificateId]) REFERENCES [ChCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'MvCertificates')
BEGIN
    CREATE TABLE [MvCertificates] (
        [Id] int NOT NULL IDENTITY,
        [CompanyUserId] nvarchar(450) NOT NULL,
        [CertificateRequestId] int NULL,
        [CreatedAt] datetime2 NOT NULL DEFAULT (GETUTCDATE()),
        [ConsignorExporter] nvarchar(500) NULL,
        [CertificateNumber] nvarchar(100) NULL,
        [CompetentAuthority] nvarchar(250) NULL,
        [CertifyingBody] nvarchar(250) NULL,
        [ConsigneeImporter] nvarchar(500) NULL,
        [CountryOfOrigin] nvarchar(150) NULL,
        [CountryOfOriginISO] nvarchar(10) NULL,
        [CompetentAuthorityOrigin] nvarchar(250) NULL,
        [RegionOfOrigin] nvarchar(150) NULL,
        [RegionOfOriginCode] nvarchar(50) NULL,
        [PlaceOfDispatch] nvarchar(500) NULL,
        [PlaceOfOrigin] nvarchar(500) NULL,
        [PlaceOfLoading] nvarchar(500) NULL,
        [MeansOfTransport] nvarchar(250) NULL,
        [MeansOfTransportNo] nvarchar(100) NULL,
        [PointsOfEntry] nvarchar(250) NULL,
        [ConditionsOfStorage] nvarchar(250) NULL,
        [ConditionsOfStorageOther] nvarchar(500) NULL,
        [EstimatedDateOfDeparture] datetime2 NULL,
        [NumberOfPackages] int NULL,
        [NetWeight] nvarchar(max) NULL,
        [GrossWeight] nvarchar(max) NULL,
        [SealNumber] nvarchar(100) NULL,
        [ContainerNumber] nvarchar(100) NULL,
        [DescriptionOfCommodity] nvarchar(1000) NULL,
        [CommoditiesFor] nvarchar(250) NULL,
        [CommoditiesForOther] nvarchar(500) NULL,
        [CertifyingOfficerName] nvarchar(250) NULL,
        [CertifyingOfficerDate] datetime2 NULL,
        [SignatoryUserId] nvarchar(450) NULL,
        [SignatoryName] nvarchar(250) NULL,
        [Qualification] nvarchar(250) NULL,
        [CompanyRegistrationNo] nvarchar(100) NULL,
        [OfficialStamp] nvarchar(max) NULL,
        [OfficialSignature] nvarchar(max) NULL,
        CONSTRAINT [PK_MvCertificates] PRIMARY KEY ([Id])
    );
END

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[MvCertificates]') AND name = 'OfficialStamp')
BEGIN
    ALTER TABLE [MvCertificates] ADD [OfficialStamp] nvarchar(max) NULL;
END
ELSE
BEGIN
    ALTER TABLE [MvCertificates] ALTER COLUMN [OfficialStamp] nvarchar(max) NULL;
END

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(N'[MvCertificates]') AND name = 'OfficialSignature')
BEGIN
    ALTER TABLE [MvCertificates] ADD [OfficialSignature] nvarchar(max) NULL;
END
ELSE
BEGIN
    ALTER TABLE [MvCertificates] ALTER COLUMN [OfficialSignature] nvarchar(max) NULL;
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'MvCertificateProducts')
BEGIN
    CREATE TABLE [MvCertificateProducts] (
        [Id] int NOT NULL IDENTITY,
        [MvCertificateId] int NOT NULL,
        [No] nvarchar(50) NULL,
        [NatureOfCommodity] nvarchar(500) NULL,
        [Species] nvarchar(500) NULL,
        [PurposeOfUse] nvarchar(500) NULL,
        CONSTRAINT [PK_MvCertificateProducts] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_MvCertificateProducts_MvCertificates_MvCertificateId] FOREIGN KEY ([MvCertificateId]) REFERENCES [MvCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'MvCertificateProductSecond')
BEGIN
    CREATE TABLE [MvCertificateProductSecond] (
        [Id] int NOT NULL IDENTITY,
        [MvCertificateId] int NOT NULL,
        [ApprovalNo] nvarchar(100) NULL,
        [IdentificationMark] nvarchar(250) NULL,
        [NumberOfPackages] int NULL,
        [NetWeight] nvarchar(max) NULL,
        CONSTRAINT [PK_MvCertificateProductSecond] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_MvCertificateProductSecond_MvCertificates_MvCertificateId] FOREIGN KEY ([MvCertificateId]) REFERENCES [MvCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'MvCertificateProductAttachments')
BEGIN
    CREATE TABLE [MvCertificateProductAttachments] (
        [Id] int NOT NULL IDENTITY,
        [MvCertificateId] int NOT NULL,
        [SpeciesName] nvarchar(250) NULL,
        [StateOfFish] nvarchar(100) NULL,
        [Grade] nvarchar(100) NULL,
        [PackType] nvarchar(100) NULL,
        [NoOfCartons] int NULL,
        CONSTRAINT [PK_MvCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_MvCertificateProductAttachments_MvCertificates_MvCertificateId] FOREIGN KEY ([MvCertificateId]) REFERENCES [MvCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'CaCertificates')
BEGIN
    CREATE TABLE [CaCertificates] (
        [Id] int NOT NULL IDENTITY,
        [CompanyUserId] nvarchar(450) NULL,
        [CertificateRequestId] int NULL,
        [CreatedAt] datetime2 NOT NULL DEFAULT (GETUTCDATE()),
        [MyRef] nvarchar(max) NULL,
        [YourRef] nvarchar(max) NULL,
        [Date] datetime2 NULL,
        [CertificateNumber] nvarchar(max) NULL,
        [CompetentAuthority] nvarchar(max) NULL,
        [CertifyingBody] nvarchar(max) NULL,
        [ConsignorName] nvarchar(max) NULL,
        [ConsignorAddress] nvarchar(max) NULL,
        [ConsigneeName] nvarchar(max) NULL,
        [ConsigneeAddress] nvarchar(max) NULL,
        [CountryOfOrigin] nvarchar(max) NULL,
        [CountryOfOriginISO] nvarchar(max) NULL,
        [CountryOfDestination] nvarchar(max) NULL,
        [CountryOfDestinationISO] nvarchar(max) NULL,
        [PlaceOfLoading] nvarchar(max) NULL,
        [TransportAeroPlane] bit NOT NULL DEFAULT 0,
        [TransportShip] bit NOT NULL DEFAULT 0,
        [TransportRailway] bit NOT NULL DEFAULT 0,
        [TransportRoad] bit NOT NULL DEFAULT 0,
        [TransportOther] bit NOT NULL DEFAULT 0,
        [DespatchFrom] nvarchar(max) NULL,
        [DespatchTo] nvarchar(max) NULL,
        [DespatchByShip] nvarchar(max) NULL,
        [ItemName] nvarchar(max) NULL,
        [NumberOfPackages] nvarchar(max) NULL,
        [NetWeight] nvarchar(max) NULL,
        [ProcessingPlantName] nvarchar(max) NULL,
        [ProcessingPlantAddress] nvarchar(max) NULL,
        [CompetentAuthorityRegNo] nvarchar(max) NULL,
        [PointsOfEntry] nvarchar(max) NULL,
        [ConditionsOfStorage] nvarchar(max) NULL,
        [TotalQuantity] nvarchar(max) NULL,
        [SealNumber] nvarchar(max) NULL,
        [TotalNumberOfPackages] nvarchar(max) NULL,
        [ApprovalNumberOfEstablishments] nvarchar(max) NULL,
        [DescriptionOfCommodity] nvarchar(max) NULL,
        [SignatoryUserId] nvarchar(450) NULL,
        [SignatoryName] nvarchar(max) NULL,
        [Designation] nvarchar(max) NULL,
        [Qualification] nvarchar(max) NULL,
        [CompanyRegistrationNo] nvarchar(max) NULL,
        [OfficialStamp] nvarchar(max) NULL,
        [OfficialSignature] nvarchar(max) NULL,
        [CertificateType] nvarchar(max) NULL,
        CONSTRAINT [PK_CaCertificates] PRIMARY KEY ([Id])
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'CaCertificateProductAttachments')
BEGIN
    CREATE TABLE [CaCertificateProductAttachments] (
        [Id] int NOT NULL IDENTITY,
        [CaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_CaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_CaCertificateProductAttachments_CaCertificates_CaCertificateId] FOREIGN KEY ([CaCertificateId]) REFERENCES [CaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'SaCertificates')
BEGIN
    CREATE TABLE [SaCertificates] (
        [Id] int NOT NULL IDENTITY,
        [CompanyUserId] nvarchar(450) NULL,
        [CertificateRequestId] int NULL,
        [CreatedAt] datetime2 NOT NULL DEFAULT (GETUTCDATE()),
        [MyRef] nvarchar(max) NULL,
        [YourRef] nvarchar(max) NULL,
        [Date] datetime2 NULL,
        [CertificateNumber] nvarchar(max) NULL,
        [CompetentAuthority] nvarchar(max) NULL,
        [CertifyingBody] nvarchar(max) NULL,
        [ConsignorName] nvarchar(max) NULL,
        [ConsignorAddress] nvarchar(max) NULL,
        [ConsigneeName] nvarchar(max) NULL,
        [ConsigneeAddress] nvarchar(max) NULL,
        [CountryOfOrigin] nvarchar(max) NULL,
        [CountryOfOriginISO] nvarchar(max) NULL,
        [CountryOfDestination] nvarchar(max) NULL,
        [CountryOfDestinationISO] nvarchar(max) NULL,
        [PlaceOfLoading] nvarchar(max) NULL,
        [TransportAeroPlane] bit NOT NULL DEFAULT 0,
        [TransportShip] bit NOT NULL DEFAULT 0,
        [TransportRailway] bit NOT NULL DEFAULT 0,
        [TransportRoad] bit NOT NULL DEFAULT 0,
        [TransportOther] bit NOT NULL DEFAULT 0,
        [DespatchFrom] nvarchar(max) NULL,
        [DespatchTo] nvarchar(max) NULL,
        [DespatchByShip] nvarchar(max) NULL,
        [ItemName] nvarchar(max) NULL,
        [NumberOfPackages] nvarchar(max) NULL,
        [NetWeight] nvarchar(max) NULL,
        [ProcessingPlantName] nvarchar(max) NULL,
        [ProcessingPlantAddress] nvarchar(max) NULL,
        [CompetentAuthorityRegNo] nvarchar(max) NULL,
        [PointsOfEntry] nvarchar(max) NULL,
        [ConditionsOfStorage] nvarchar(max) NULL,
        [TotalQuantity] nvarchar(max) NULL,
        [SealNumber] nvarchar(max) NULL,
        [TotalNumberOfPackages] nvarchar(max) NULL,
        [ApprovalNumberOfEstablishments] nvarchar(max) NULL,
        [DescriptionOfCommodity] nvarchar(max) NULL,
        [SignatoryUserId] nvarchar(450) NULL,
        [SignatoryName] nvarchar(max) NULL,
        [Designation] nvarchar(max) NULL,
        [Qualification] nvarchar(max) NULL,
        [CompanyRegistrationNo] nvarchar(max) NULL,
        [OfficialStamp] nvarchar(max) NULL,
        [OfficialSignature] nvarchar(max) NULL,
        [CertificateType] nvarchar(max) NULL,
        CONSTRAINT [PK_SaCertificates] PRIMARY KEY ([Id])
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'SaCertificateProductAttachments')
BEGIN
    CREATE TABLE [SaCertificateProductAttachments] (
        [Id] int NOT NULL IDENTITY,
        [SaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_SaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_SaCertificateProductAttachments_SaCertificates_SaCertificateId] FOREIGN KEY ([SaCertificateId]) REFERENCES [SaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'ZaCertificates')
BEGIN
    CREATE TABLE [ZaCertificates] (
        [Id] int NOT NULL IDENTITY,
        [CompanyUserId] nvarchar(450) NULL,
        [CertificateRequestId] int NULL,
        [CreatedAt] datetime2 NOT NULL DEFAULT (GETUTCDATE()),
        [MyRef] nvarchar(max) NULL,
        [YourRef] nvarchar(max) NULL,
        [Date] datetime2 NULL,
        [CertificateNumber] nvarchar(max) NULL,
        [CompetentAuthority] nvarchar(max) NULL,
        [CertifyingBody] nvarchar(max) NULL,
        [ConsignorName] nvarchar(max) NULL,
        [ConsignorAddress] nvarchar(max) NULL,
        [ConsigneeName] nvarchar(max) NULL,
        [ConsigneeAddress] nvarchar(max) NULL,
        [CountryOfOrigin] nvarchar(max) NULL,
        [CountryOfOriginISO] nvarchar(max) NULL,
        [CountryOfDestination] nvarchar(max) NULL,
        [CountryOfDestinationISO] nvarchar(max) NULL,
        [PlaceOfLoading] nvarchar(max) NULL,
        [TransportAeroPlane] bit NOT NULL DEFAULT 0,
        [TransportShip] bit NOT NULL DEFAULT 0,
        [TransportRailway] bit NOT NULL DEFAULT 0,
        [TransportRoad] bit NOT NULL DEFAULT 0,
        [TransportOther] bit NOT NULL DEFAULT 0,
        [DespatchFrom] nvarchar(max) NULL,
        [DespatchTo] nvarchar(max) NULL,
        [DespatchByShip] nvarchar(max) NULL,
        [ItemName] nvarchar(max) NULL,
        [NumberOfPackages] nvarchar(max) NULL,
        [NetWeight] nvarchar(max) NULL,
        [ProcessingPlantName] nvarchar(max) NULL,
        [ProcessingPlantAddress] nvarchar(max) NULL,
        [CompetentAuthorityRegNo] nvarchar(max) NULL,
        [PointsOfEntry] nvarchar(max) NULL,
        [ConditionsOfStorage] nvarchar(max) NULL,
        [TotalQuantity] nvarchar(max) NULL,
        [SealNumber] nvarchar(max) NULL,
        [TotalNumberOfPackages] nvarchar(max) NULL,
        [ApprovalNumberOfEstablishments] nvarchar(max) NULL,
        [DescriptionOfCommodity] nvarchar(max) NULL,
        [SignatoryUserId] nvarchar(450) NULL,
        [SignatoryName] nvarchar(max) NULL,
        [Designation] nvarchar(max) NULL,
        [Qualification] nvarchar(max) NULL,
        [CompanyRegistrationNo] nvarchar(max) NULL,
        [OfficialStamp] nvarchar(max) NULL,
        [OfficialSignature] nvarchar(max) NULL,
        [CertificateType] nvarchar(max) NULL,
        CONSTRAINT [PK_ZaCertificates] PRIMARY KEY ([Id])
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'ZaCertificateProductAttachments')
BEGIN
    CREATE TABLE [ZaCertificateProductAttachments] (
        [Id] int NOT NULL IDENTITY,
        [ZaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_ZaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_ZaCertificateProductAttachments_ZaCertificates_ZaCertificateId] FOREIGN KEY ([ZaCertificateId]) REFERENCES [ZaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'UsaCertificateProductAttachments')
BEGIN
    CREATE TABLE [UsaCertificateProductAttachments] (
        [Id] int NOT NULL IDENTITY,
        [UsaCertificateId] int NOT NULL,
        [Product] nvarchar(max) NULL,
        [LotIdentifier] nvarchar(max) NULL,
        [TypeOfPackaging] nvarchar(max) NULL,
        [NumberOfKgs] nvarchar(max) NULL,
        [NumberOfBoxes] int NULL,
        CONSTRAINT [PK_UsaCertificateProductAttachments] PRIMARY KEY ([Id]),
        CONSTRAINT [FK_UsaCertificateProductAttachments_UsaCertificates_UsaCertificateId] FOREIGN KEY ([UsaCertificateId]) REFERENCES [UsaCertificates] ([Id]) ON DELETE CASCADE
    );
END

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'UkCertificates')
BEGIN
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAnimalHealthAll')
        ALTER TABLE [UkCertificates] ADD [StrikeAnimalHealthAll] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT153')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT153] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT154')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT154] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT155')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT155] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT155_Either')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT155_Either] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT155_D_Bkd')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT155_D_Bkd] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT155_D_SvcGs')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT155_D_SvcGs] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT155_D_SvcBkd')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT155_D_SvcBkd] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT155_GsSalinity')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT155_GsSalinity] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhT155_GsEggs')
        ALTER TABLE [UkCertificates] ADD [StrikeAhT155_GsEggs] bit NOT NULL DEFAULT 1;
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('UkCertificates') AND name = 'StrikeAhP502')
        ALTER TABLE [UkCertificates] ADD [StrikeAhP502] bit NOT NULL DEFAULT 1;
END

UPDATE [AspNetUsers] SET [FullName] = 'Company User', [CompanyId] = NULL WHERE [Email] = 'company@gmail.com';
DELETE FROM [Companies] WHERE [CompanyEmail] = 'company@gmail.com' OR [CompanyName] = 'Ceylon Ocean Harvesters (Pvt) Ltd';
UPDATE [AspNetUsers] SET [FullName] = 'NIBM', [CompanyId] = 1, [PasswordHash] = (SELECT TOP 1 [PasswordHash] FROM [AspNetUsers] WHERE [Email] = 'company@gmail.com') WHERE [Email] = 'nibm@gmail.com';
UPDATE [Companies] SET [UserId] = (SELECT TOP 1 [Id] FROM [AspNetUsers] WHERE [Email] = 'nibm@gmail.com') WHERE [Id] = 1 OR [CompanyName] = 'NIBM';

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'RuCertificates')
    UPDATE [RuCertificates] SET [CertificateType] = 'full' WHERE [CertificateType] = 'attachment';

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'KzCertificates')
    UPDATE [KzCertificates] SET [CertificateType] = 'full' WHERE [CertificateType] = 'attachment';

IF EXISTS (SELECT * FROM sys.tables WHERE name = 'JpCertificates')
    UPDATE [JpCertificates] SET [CertificateType] = 'vibrio' WHERE [CertificateType] = 'single';
";
                await context.Database.ExecuteSqlRawAsync(sql);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[EnsureSchemaUpdatesAsync] Error: {ex.Message}");
            }
        }

        private static async Task SeedUsersAsync(UserManager<AppUser> userManager, AppDbContext context)
        {
            // Password that meets requirements: digit, lowercase, uppercase
            const string defaultPassword = "Admin@123";

            // Admin User
            var adminEmail = "admin@gmail.com";
            var adminUser = await userManager.FindByEmailAsync(adminEmail);
            if (adminUser == null)
            {
                adminUser = new AppUser
                {
                    UserName = adminEmail,
                    Email = adminEmail,
                    EmailConfirmed = true,
                    FullName = "Admin User"
                };
                var result = await userManager.CreateAsync(adminUser, defaultPassword);
                if (result.Succeeded)
                {
                    await userManager.AddToRoleAsync(adminUser, "Admin");
                }
                else
                {
                    var errors = string.Join(", ", result.Errors.Select(e => e.Description));
                    throw new Exception($"Failed to create admin user: {errors}");
                }
            }

            // Company User
            var companyEmail = "company@gmail.com";
            var companyUser = await userManager.FindByEmailAsync(companyEmail);
            if (companyUser == null)
            {
                companyUser = new AppUser
                {
                    UserName = companyEmail,
                    Email = companyEmail,
                    EmailConfirmed = true,
                    FullName = "Company User"
                };
                var result = await userManager.CreateAsync(companyUser, defaultPassword);
                if (result.Succeeded)
                {
                    await userManager.AddToRoleAsync(companyUser, "Company");
                }
                else
                {
                    var errors = string.Join(", ", result.Errors.Select(e => e.Description));
                    throw new Exception($"Failed to create company user: {errors}");
                }
            }
            else if (companyUser.FullName != "Company User")
            {
                companyUser.FullName = "Company User";
                await userManager.UpdateAsync(companyUser);
            }

            // Regular User
            var userEmail = "user@gmail.com";
            var regularUser = await userManager.FindByEmailAsync(userEmail);
            if (regularUser == null)
            {
                regularUser = new AppUser
                {
                    UserName = userEmail,
                    Email = userEmail,
                    EmailConfirmed = true,
                    FullName = "Regular User"
                };
                var result = await userManager.CreateAsync(regularUser, defaultPassword);
                if (result.Succeeded)
                {
                    await userManager.AddToRoleAsync(regularUser, "User");
                }
                else
                {
                    var errors = string.Join(", ", result.Errors.Select(e => e.Description));
                    throw new Exception($"Failed to create regular user: {errors}");
                }
            }
        }

        private static async Task SeedCountriesAsync(AppDbContext context)
        {
            var countries = new[]
            {
                "Armenia",
                "Australia",
                "Brazil",
                "Canada",
                "China",
                "Hong Kong",
                "India",
                "Indonesia",
                "Israel",
                "Japan",
                "Kazakhstan",
                "Kuwait",
                "Malaysia",
                "Maldives",
                "New Zealand",
                "Russia",
                "Saudi Arabia",
                "South Africa",
                "Taiwan",
                "Ukraine",
                "United Kingdom",
                "United States of America"
            };

            foreach (var countryName in countries)
            {
                var countryExists = await context.Countries
                    .AnyAsync(c => c.Name == countryName);

                if (!countryExists)
                {
                    context.Countries.Add(new Country
                    {
                        Name = countryName
                    });
                }
            }

            await context.SaveChangesAsync();
        }

        private static async Task SeedProductCertificatesAsync(AppDbContext context)
        {
            var certificates = new[]
            {
                new ProductCertificate { Name = "ISO 9001" },
                new ProductCertificate { Name = "ISO 14001" },
                new ProductCertificate { Name = "CE Mark" },
                new ProductCertificate { Name = "FDA Approved" },
                new ProductCertificate { Name = "RoHS" }
            };

            foreach (var certificate in certificates)
            {
                var exists = await context.ProductCertificates.AnyAsync(x => x.Name == certificate.Name);
                if (!exists)
                {
                    context.ProductCertificates.Add(certificate);
                }
            }

            await context.SaveChangesAsync();
        }

        private static async Task SeedCompanyStatusesAsync(AppDbContext context)
        {
            var statuses = new[]
            {
                new CompanyStatus { Name = "Non EU" },
                new CompanyStatus { Name = "EU" },
                new CompanyStatus { Name = "Packing Center" }
            };

            foreach (var status in statuses)
            {
                var exists = await context.CompanyStatuses.AnyAsync(x => x.Name == status.Name);
                if (!exists)
                {
                    context.CompanyStatuses.Add(status);
                }
            }

            await context.SaveChangesAsync();
        }

        private static async Task SeedListedCountriesAsync(AppDbContext context)
        {
            var listedCountries = new[]
            {
                new ListedCountry { Name = "Armenia" },
                new ListedCountry { Name = "Australia" },
                new ListedCountry { Name = "Brazil" },
                new ListedCountry { Name = "Canada" },
                new ListedCountry { Name = "China" },
                new ListedCountry { Name = "Hong Kong" },
                new ListedCountry { Name = "India" },
                new ListedCountry { Name = "Indonesia" },
                new ListedCountry { Name = "Israel" },
                new ListedCountry { Name = "Japan" },
                new ListedCountry { Name = "Kazakhstan" },
                new ListedCountry { Name = "Kuwait" },
                new ListedCountry { Name = "Malaysia" },
                new ListedCountry { Name = "Maldives" },
                new ListedCountry { Name = "New Zealand" },
                new ListedCountry { Name = "Russia" },
                new ListedCountry { Name = "Saudi Arabia" },
                new ListedCountry { Name = "South Africa" },
                new ListedCountry { Name = "Taiwan" },
                new ListedCountry { Name = "Ukraine" },
                new ListedCountry { Name = "United Kingdom" },
                new ListedCountry { Name = "United States of America" }
            };

            foreach (var listedCountry in listedCountries)
            {
                var exists = await context.ListedCountries.AnyAsync(x => x.Name == listedCountry.Name);
                if (!exists)
                {
                    context.ListedCountries.Add(listedCountry);
                }
            }

            await context.SaveChangesAsync();
        }

        private static async Task CleanupSampleReplacementRequestsAsync(AppDbContext context)
        {
            try
            {
                var sampleRequests = await context.ReplacementRequests
                    .Where(r => r.Reason.Contains("physically damaged") || r.Reason.Contains("Typographical error") || r.Reason.Contains("Amended flight transport") || r.OriginalReferenceNumber == "HC-2026-EU-001" || r.OriginalReferenceNumber == "HC-2026-USA-001" || r.OriginalReferenceNumber == "HC-2026-JP-001")
                    .ToListAsync();

                if (sampleRequests.Count > 0)
                {
                    context.ReplacementRequests.RemoveRange(sampleRequests);
                    await context.SaveChangesAsync();
                }
            }
            catch
            {
                // Ignore if table not yet created
            }
        }
    }
}
