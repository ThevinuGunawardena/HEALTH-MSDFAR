using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;
using MEA.Server.Entities;

namespace MEA.Server.Data
{
    public class AppDbContext : IdentityDbContext<AppUser>
    {
        public AppDbContext(DbContextOptions<AppDbContext> options)
            : base(options)
        { }

        public DbSet<AppUser> AppUsers { get; set; }
        public DbSet<Company> Companies { get; set; }
        public DbSet<Country> Countries { get; set; }
        public DbSet<ProductCertificate> ProductCertificates { get; set; }
        public DbSet<CompanyStatus> CompanyStatuses { get; set; }
        public DbSet<ListedCountry> ListedCountries { get; set; }
        public DbSet<CertificateRequest> CertificateRequests { get; set; }
        public DbSet<VetCertificateForm> VetCertificateForms { get; set; }
        public DbSet<VetCertificateAttachment> VetCertificateAttachments { get; set; }
        public DbSet<VetCertificateProduct> VetCertificateProducts { get; set; }
        public DbSet<AuCertificate> AuCertificates { get; set; }
        public DbSet<AuCertificateProduct> AuCertificateProducts { get; set; }
        public DbSet<AmCertificate> AmCertificates { get; set; }
        public DbSet<AmAttachment> AmAttachments { get; set; }
        public DbSet<AmPreExportCertificate> AmPreExportCertificates { get; set; }
        public DbSet<BrCertificate> BrCertificates { get; set; }
        public DbSet<BrCertificateProduct> BrCertificateProducts { get; set; }
        public DbSet<ChCertificate> ChCertificates { get; set; }
        public DbSet<ChAttachment> ChAttachments { get; set; }
        public DbSet<HkCertificate> HkCertificates { get; set; }
        public DbSet<HkCertificateProduct> HkCertificateProducts { get; set; }
        public DbSet<IdCertificate> IdCertificates { get; set; }
        public DbSet<IdCertificateProduct> IdCertificateProducts { get; set; }
        public DbSet<IndCertificate> IndCertificates { get; set; }
        public DbSet<IndCertificateProduct> IndCertificateProducts { get; set; }
        public DbSet<JpCertificate> JpCertificates { get; set; }
        public DbSet<KwCertificate> KwCertificates { get; set; }
        public DbSet<KwCertificateProduct> KwCertificateProducts { get; set; }
        public DbSet<MyCertificate> MyCertificates { get; set; }
        public DbSet<MyCertificateProduct> MyCertificateProducts { get; set; }
        public DbSet<NzCertificate> NzCertificates { get; set; }
        public DbSet<NzCertificateProduct> NzCertificateProducts { get; set; }
        public DbSet<RuCertificate> RuCertificates { get; set; }
        public DbSet<RuPreExportCertificate> RuPreExportCertificates { get; set; }
        public DbSet<RuAttachment> RuAttachments { get; set; }
        public DbSet<KzCertificate> KzCertificates { get; set; }
        public DbSet<KzPreExportCertificate> KzPreExportCertificates { get; set; }
        public DbSet<KzAttachment> KzAttachments { get; set; }
        public DbSet<TwCertificate> TwCertificates { get; set; }
        public DbSet<TwCertificateProduct> TwCertificateProducts { get; set; }
        public DbSet<UaCertificate> UaCertificates { get; set; }
        public DbSet<UaCertificateProduct> UaCertificateProducts { get; set; }
        public DbSet<UkCertificate> UkCertificates { get; set; }
        public DbSet<UkCertificateProduct> UkCertificateProducts { get; set; }
        public DbSet<UsaCertificate> UsaCertificates { get; set; }
        public DbSet<UsaCertificateProductAttachment> UsaCertificateProductAttachments { get; set; }
        public DbSet<CaCertificate> CaCertificates { get; set; }
        public DbSet<CaCertificateProductAttachment> CaCertificateProductAttachments { get; set; }
        public DbSet<SaCertificate> SaCertificates { get; set; }
        public DbSet<SaCertificateProductAttachment> SaCertificateProductAttachments { get; set; }
        public DbSet<ZaCertificate> ZaCertificates { get; set; }
        public DbSet<ZaCertificateProductAttachment> ZaCertificateProductAttachments { get; set; }
        public DbSet<IlCertificate> IlCertificates { get; set; }
        public DbSet<IlCertificateProduct> IlCertificateProducts { get; set; }
        public DbSet<MvCertificate> MvCertificates { get; set; }
        public DbSet<MvCertificateProduct> MvCertificateProducts { get; set; }
        public DbSet<MvCertificateProductSecond> MvCertificateProductSecond { get; set; }
        public DbSet<MvCertificateProductAttachment> MvCertificateProductAttachments { get; set; }
        public DbSet<ReferenceSequence> ReferenceSequences { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            modelBuilder.Entity<Country>(entity =>
            {
                entity.ToTable("Countries");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.Name)
                    .IsRequired()
                    .HasMaxLength(120);
                entity.HasIndex(x => x.Name)
                    .IsUnique();
            });

            modelBuilder.Entity<ProductCertificate>(entity =>
            {
                entity.ToTable("ProductCertificates");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.Name)
                    .IsRequired()
                    .HasMaxLength(120);
                entity.HasIndex(x => x.Name)
                    .IsUnique();
            });

            modelBuilder.Entity<CompanyStatus>(entity =>
            {
                entity.ToTable("CompanyStatuses");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.Name)
                    .IsRequired()
                    .HasMaxLength(100);
                entity.HasIndex(x => x.Name)
                    .IsUnique();
            });

            modelBuilder.Entity<ListedCountry>(entity =>
            {
                entity.ToTable("ListedCountries");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.Name)
                    .IsRequired()
                    .HasMaxLength(120);
                entity.HasIndex(x => x.Name)
                    .IsUnique();
            });

            modelBuilder.Entity<Company>(entity =>
            {
                entity.ToTable("Companies");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyName)
                    .IsRequired()
                    .HasMaxLength(200);
                entity.Property(x => x.CompanyEmail)
                    .IsRequired()
                    .HasMaxLength(200);
                entity.Property(x => x.CompanyPhone)
                    .IsRequired()
                    .HasMaxLength(20);
                entity.Property(x => x.CompanyAddress)
                    .IsRequired()
                    .HasMaxLength(500);
                entity.Property(x => x.RegistrationNo)
                    .IsRequired()
                    .HasMaxLength(100);
                entity.Property(x => x.CreatedAt)
                    .HasDefaultValueSql("GETUTCDATE()")
                    .IsRequired();

                entity.HasIndex(x => x.UserId);
                entity.HasIndex(x => x.CompanyStatusId);
                entity.HasIndex(x => x.ListedCountryId);
                entity.HasIndex(x => x.ProductCertificateId);

                entity.HasOne(x => x.User)
                    .WithMany()
                    .HasForeignKey(x => x.UserId)
                    .OnDelete(DeleteBehavior.SetNull)
                    .IsRequired(false);

                entity.HasOne(x => x.CompanyStatus)
                    .WithMany(x => x.Companies)
                    .HasForeignKey(x => x.CompanyStatusId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.ListedCountry)
                    .WithMany(x => x.Companies)
                    .HasForeignKey(x => x.ListedCountryId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.ProductCertificate)
                    .WithMany(x => x.Companies)
                    .HasForeignKey(x => x.ProductCertificateId)
                    .OnDelete(DeleteBehavior.SetNull);
            });

            modelBuilder.Entity<BrCertificate>(entity =>
            {
                entity.ToTable("BrCertificates");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.RefNumber).HasMaxLength(50);
                entity.Property(x => x.CountryOfExport).HasMaxLength(120);
                entity.Property(x => x.CertificateNo).HasMaxLength(50);
                entity.Property(x => x.CompetentAuthority).HasMaxLength(120);
                entity.Property(x => x.LocalCompetentAuthority).HasMaxLength(120);
                entity.Property(x => x.ExporterName).HasMaxLength(120);
                entity.Property(x => x.ExporterAddress).HasMaxLength(250);
                entity.Property(x => x.ImporterName).HasMaxLength(120);
                entity.Property(x => x.ImporterAddress).HasMaxLength(250);
                entity.Property(x => x.CountryOrigin).HasMaxLength(120);
                entity.Property(x => x.CountryOriginISO).HasMaxLength(10);
                entity.Property(x => x.CountryOfDestination).HasMaxLength(120);
                entity.Property(x => x.CountryDestinationISO).HasMaxLength(10);
                entity.Property(x => x.PlaceOfLoading).HasMaxLength(120);
                entity.Property(x => x.DeclaredPointOfEntry).HasMaxLength(120);
                entity.Property(x => x.ConditionsForTransportStorage).HasMaxLength(250);
                entity.Property(x => x.IdentificationOfContainers).HasMaxLength(120);
                entity.Property(x => x.IdentificationOfFoodProducts).HasMaxLength(250);
                entity.Property(x => x.ProducerDetails).HasMaxLength(250);
                entity.Property(x => x.HsCode).HasMaxLength(50);
                entity.Property(x => x.IntendedPurpose).HasMaxLength(120);
                entity.Property(x => x.PlaceAndDate).HasMaxLength(120);
                entity.Property(x => x.OfficialStamp).HasColumnType("nvarchar(max)");
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);
                entity.Property(x => x.ModeloConformeCircularNo).HasMaxLength(120);
                entity.Property(x => x.SanitaryCertification).HasMaxLength(250);
                entity.Property(x => x.TotalNetWeight).HasColumnType("decimal(18,2)");
                entity.Property(x => x.DateOfIssue).HasColumnType("datetime2");
                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.BrCertificate)
                    .HasForeignKey(x => x.BrCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<BrCertificateProduct>(entity =>
            {
                entity.ToTable("BrCertificateProducts");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.NameOfTheProduct).HasMaxLength(120);
                entity.Property(x => x.ScientificName).HasMaxLength(120);
                entity.Property(x => x.TypeOfPackaging).HasMaxLength(120);
                entity.Property(x => x.NumberOfPackages).HasColumnType("int");
                entity.Property(x => x.NetWeight).HasColumnType("decimal(18,2)");
            });

            modelBuilder.Entity<CertificateRequest>(entity =>
            {
                entity.ToTable("CertificateRequests", tableBuilder =>
                {
                    tableBuilder.HasCheckConstraint(
                        "CK_CertificateRequests_TypeCountry",
                        "([CertificateType] = 0 AND [CountryId] IS NULL) OR ([CertificateType] = 1 AND [CountryId] IS NOT NULL)");

                });

                entity.HasKey(x => x.Id);

                entity.Property(x => x.ReferenceNumber)
                    .IsRequired()
                    .HasMaxLength(32);

                entity.HasIndex(x => x.ReferenceNumber)
                    .IsUnique();

                entity.Property(x => x.CertificateType)
                    .HasConversion<int>()
                    .IsRequired();

                entity.Property(x => x.Status)
                    .HasConversion<int>()
                    .HasDefaultValue(CertificateStatus.Pending)
                    .IsRequired();

                entity.Property(x => x.CreatedAt)
                    .HasDefaultValueSql("GETUTCDATE()")
                    .IsRequired();

                entity.Property(x => x.CompanyUserId)
                    .IsRequired();

                entity.HasIndex(x => new { x.CompanyUserId, x.CreatedAt });
                entity.HasIndex(x => x.Status);

                entity.HasOne<AppUser>()
                    .WithMany(x => x.CertificateRequests)
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<Country>()
                    .WithMany(x => x.CertificateRequests)
                    .HasForeignKey(x => x.CountryId)
                    .OnDelete(DeleteBehavior.Restrict);
            });

            modelBuilder.Entity<VetCertificateForm>(entity =>
            {
                entity.ToTable("VetCertificateForms");

                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId)
                    .IsRequired();

                entity.Property(x => x.CreatedAt)
                    .HasDefaultValueSql("GETUTCDATE()")
                    .IsRequired();

                entity.Property(x => x.OldHC).HasMaxLength(100);
                entity.Property(x => x.NewHC).HasMaxLength(100);
                entity.Property(x => x.LandingSite).HasMaxLength(250);
                entity.Property(x => x.BoatRegistration).HasMaxLength(120);
                entity.Property(x => x.BoatNumber).HasMaxLength(120);
                entity.Property(x => x.AquaSupplier).HasMaxLength(250);
                entity.Property(x => x.CountryOrigin).HasMaxLength(120);
                entity.Property(x => x.HealthCertNo).HasMaxLength(150);
                entity.Property(x => x.UploadedCertificateFile).HasColumnType("varbinary(max)");
                entity.Property(x => x.ConsignorName).HasMaxLength(200);
                entity.Property(x => x.ConsignorPostal).HasMaxLength(30);
                entity.Property(x => x.ConsignorTel).HasMaxLength(40);
                entity.Property(x => x.ConsigneeName).HasMaxLength(200);
                entity.Property(x => x.ConsigneePostal).HasMaxLength(30);
                entity.Property(x => x.ConsigneeTel).HasMaxLength(40);
                entity.Property(x => x.CountryOriginISO).HasMaxLength(10);
                entity.Property(x => x.RegionOriginISO).HasMaxLength(20);
                entity.Property(x => x.CountryDestinationISO).HasMaxLength(10);
                entity.Property(x => x.ProcessingEstName).HasMaxLength(250);
                entity.Property(x => x.ApprovalNo).HasMaxLength(120);
                entity.Property(x => x.PlaceOfLoading).HasMaxLength(200);
                entity.Property(x => x.TransportId).HasMaxLength(120);
                entity.Property(x => x.DocReferences).HasMaxLength(150);
                entity.Property(x => x.EntryBIP).HasMaxLength(200);
                entity.Property(x => x.DescCommon).HasMaxLength(250);
                entity.Property(x => x.DescScientific).HasMaxLength(250);
                entity.Property(x => x.ProcessingType).HasMaxLength(150);
                entity.Property(x => x.HsCode).HasMaxLength(40);
                entity.Property(x => x.Quantity).HasMaxLength(60);
                entity.Property(x => x.NumPackages).HasMaxLength(60);
                entity.Property(x => x.PackagingType).HasMaxLength(120);
                entity.Property(x => x.ContainerId).HasMaxLength(120);
                entity.Property(x => x.ForImportEU).HasMaxLength(120);
                entity.Property(x => x.NetWeight).HasMaxLength(60);
                entity.Property(x => x.PaymentSlipFile).HasColumnType("varbinary(max)");
                entity.Property(x => x.Signature).HasColumnType("nvarchar(max)");
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Designation).HasMaxLength(120);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.VetCertificateForm)
                    .HasForeignKey(x => x.VetCertificateFormId)
                    .OnDelete(DeleteBehavior.Cascade);

                entity.HasMany(x => x.Attachments)
                    .WithOne(x => x.VetCertificateForm)
                    .HasForeignKey(x => x.VetCertificateFormId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<VetCertificateAttachment>(entity =>
            {
                entity.ToTable("VetCertificateAttachments");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.FileOrder).HasColumnType("int");
                entity.Property(x => x.OriginalFileName).HasMaxLength(260);
                entity.Property(x => x.SecondaryFileName).HasMaxLength(260);
                entity.Property(x => x.ContentType).HasMaxLength(120);
                entity.Property(x => x.FileContent).HasColumnType("varbinary(max)").IsRequired();

                entity.HasIndex(x => x.VetCertificateFormId);
                entity.HasIndex(x => new { x.VetCertificateFormId, x.FileOrder });
            });

            modelBuilder.Entity<VetCertificateProduct>(entity =>
            {
                entity.ToTable("VetCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.ProductOrder).HasColumnType("int");
                entity.Property(x => x.DescCommon).HasMaxLength(250);
                entity.Property(x => x.DescScientific).HasMaxLength(250);
                entity.Property(x => x.ProcessingType).HasMaxLength(150);
                entity.Property(x => x.HsCode).HasMaxLength(40);
                entity.Property(x => x.Quantity).HasMaxLength(60);
                entity.Property(x => x.NumPackages).HasMaxLength(60);
                entity.Property(x => x.PackagingType).HasMaxLength(120);
                entity.Property(x => x.ContainerId).HasMaxLength(120);
                entity.Property(x => x.ForImportEU).HasMaxLength(120);
                entity.Property(x => x.NetWeight).HasMaxLength(60);

                entity.HasIndex(x => x.VetCertificateFormId);
                entity.HasIndex(x => new { x.VetCertificateFormId, x.ProductOrder });
            });

            modelBuilder.Entity<AuCertificate>(entity =>
            {
                entity.ToTable("AuCertificates");

                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId)
                    .IsRequired();

                entity.Property(x => x.CreatedAt)
                    .HasDefaultValueSql("GETUTCDATE()")
                    .IsRequired();

                entity.Property(x => x.ConsignorName).HasMaxLength(200);
                entity.Property(x => x.ConsignorPostal).HasMaxLength(30);
                entity.Property(x => x.ConsignorTel).HasMaxLength(40);
                entity.Property(x => x.CertRefNumber).HasMaxLength(150);
                entity.Property(x => x.CentralCompetentAuthority).HasMaxLength(200);
                entity.Property(x => x.LocalCompetentAuthority).HasMaxLength(200);
                entity.Property(x => x.ConsigneeName).HasMaxLength(200);
                entity.Property(x => x.ConsigneePostal).HasMaxLength(30);
                entity.Property(x => x.ConsigneeTel).HasMaxLength(40);
                entity.Property(x => x.CountryOriginISO).HasMaxLength(20);
                entity.Property(x => x.RegionOriginISO).HasMaxLength(20);
                entity.Property(x => x.CountryDestinationISO).HasMaxLength(20);
                entity.Property(x => x.PlaceOfOriginName).HasMaxLength(250);
                entity.Property(x => x.PlaceOfOriginApprovalNo).HasMaxLength(120);
                entity.Property(x => x.PlaceOfLoading).HasMaxLength(200);
                entity.Property(x => x.HsCode).HasMaxLength(40);
                entity.Property(x => x.NumPackages).HasMaxLength(60);
                entity.Property(x => x.PackagingType).HasMaxLength(120);
                entity.Property(x => x.ExportApprovalNumber).HasMaxLength(120);
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);
                entity.Property(x => x.Stamp).HasColumnType("nvarchar(max)");
                entity.Property(x => x.Signature).HasColumnType("nvarchar(max)");

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.AuCertificate)
                    .HasForeignKey(x => x.AuCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<AuCertificateProduct>(entity =>
            {
                entity.ToTable("AuCertificateProducts");

                entity.HasKey(x => x.Id);

                entity.Property(x => x.SpeciesScientificName).HasMaxLength(250);
                entity.Property(x => x.NatureOfCommodity).HasMaxLength(250);
                entity.Property(x => x.TreatmentType).HasMaxLength(150);
                entity.Property(x => x.ApprovalNumberOfEstablishments).HasMaxLength(150);
                entity.Property(x => x.ManufacturingPlant).HasMaxLength(250);
                entity.Property(x => x.NetWeight).HasColumnType("decimal(18,2)");

                entity.HasIndex(x => x.AuCertificateId);
            });

                modelBuilder.Entity<AmCertificate>(entity =>
                {
                    entity.ToTable("AmCertificates");

                    entity.HasKey(x => x.Id);

                    entity.Property(x => x.CompanyUserId)
                        .IsRequired();

                    entity.Property(x => x.CreatedAt)
                        .HasDefaultValueSql("GETUTCDATE()")
                        .IsRequired();

                    entity.Property(x => x.ConsignorName).HasMaxLength(200);
                    entity.Property(x => x.ConsignorPostal).HasMaxLength(30);
                    entity.Property(x => x.ConsignorTel).HasMaxLength(40);
                    entity.Property(x => x.CertRefNumber).HasMaxLength(150);
                    entity.Property(x => x.CentralCompetentAuthority).HasMaxLength(200);
                    entity.Property(x => x.LocalCompetentAuthority).HasMaxLength(200);
                    entity.Property(x => x.ConsigneeName).HasMaxLength(200);
                    entity.Property(x => x.ConsigneePostal).HasMaxLength(30);
                    entity.Property(x => x.ConsigneeTel).HasMaxLength(40);
                    entity.Property(x => x.CountryOriginISO).HasMaxLength(20);
                    entity.Property(x => x.RegionOriginISO).HasMaxLength(20);
                    entity.Property(x => x.CountryDestinationISO).HasMaxLength(20);
                    entity.Property(x => x.PlaceOfOriginName).HasMaxLength(250);
                    entity.Property(x => x.PlaceOfOriginApprovalNo).HasMaxLength(120);
                    entity.Property(x => x.PlaceOfLoading).HasMaxLength(200);
                    entity.Property(x => x.HsCode).HasMaxLength(40);
                    entity.Property(x => x.NumPackages).HasMaxLength(60);
                    entity.Property(x => x.PackagingType).HasMaxLength(120);
                    entity.Property(x => x.CertificateNo).HasMaxLength(100);
                    entity.Property(x => x.CountryIssuing).HasMaxLength(200);
                    entity.Property(x => x.CompetentAuthorityExporting).HasMaxLength(250);
                    entity.Property(x => x.OrganizationIssuing).HasMaxLength(250);
                    entity.Property(x => x.CountryOfTransit).HasMaxLength(200);
                    entity.Property(x => x.PointOfCrossingBorder).HasMaxLength(200);
                    entity.Property(x => x.ProductName).HasMaxLength(500);
                    entity.Property(x => x.NetWeight).HasMaxLength(100);
                    entity.Property(x => x.NumberOfSeal).HasMaxLength(100);
                    entity.Property(x => x.IdentificationMarks).HasMaxLength(200);
                    entity.Property(x => x.StorageConditions).HasMaxLength(200);
                    entity.Property(x => x.FactoryVessel).HasMaxLength(250);
                    entity.Property(x => x.ColdStore).HasMaxLength(250);
                    entity.Property(x => x.AdministrativeUnit).HasMaxLength(200);
                    entity.Property(x => x.PlaceOfIssue).HasMaxLength(200);
                    entity.Property(x => x.IdentificationMarksAttachment).HasMaxLength(200);
                    entity.Property(x => x.ExportApprovalNumber).HasMaxLength(120);
                    entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                    entity.Property(x => x.SignatoryName).HasMaxLength(200);
                    entity.Property(x => x.Qualification).HasMaxLength(200);
                    entity.Property(x => x.Stamp).HasColumnType("nvarchar(max)");
                    entity.Property(x => x.Signature).HasColumnType("nvarchar(max)");

                    entity.HasIndex(x => x.CompanyUserId);
                    entity.HasIndex(x => x.SignatoryUserId);
                    entity.HasIndex(x => x.CreatedAt);
                    entity.HasIndex(x => x.CertificateRequestId);

                    entity.HasOne(x => x.CertificateRequest)
                        .WithMany()
                        .HasForeignKey(x => x.CertificateRequestId)
                        .OnDelete(DeleteBehavior.Restrict);

                    entity.HasOne<AppUser>()
                        .WithMany()
                        .HasForeignKey(x => x.CompanyUserId)
                        .OnDelete(DeleteBehavior.Restrict);

                    entity.HasOne<AppUser>()
                        .WithMany()
                        .HasForeignKey(x => x.SignatoryUserId)
                        .OnDelete(DeleteBehavior.Restrict);

                        entity.HasMany(x => x.PreExportCertificates)
                            .WithOne(x => x.AmCertificate)
                            .HasForeignKey(x => x.AmCertificateId)
                            .OnDelete(DeleteBehavior.Cascade);

                        entity.HasMany(x => x.Attachments)
                            .WithOne(x => x.AmCertificate)
                            .HasForeignKey(x => x.AmCertificateId)
                            .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity<ChCertificate>(entity =>
            {
                entity.ToTable("ChCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.CountryOfExport).HasMaxLength(120);
                entity.Property(x => x.CountryOfProduction).HasMaxLength(120);
                entity.Property(x => x.CompetentAuthority).HasMaxLength(200);
                entity.Property(x => x.DepartmentOfIssuance).HasMaxLength(200);

                entity.Property(x => x.CommodityName).HasMaxLength(250);
                entity.Property(x => x.ScientificName).HasMaxLength(250);
                entity.Property(x => x.LatinName).HasMaxLength(250);
                entity.Property(x => x.Number).HasMaxLength(100);

                entity.Property(x => x.ArtificialCulture).HasMaxLength(10);
                entity.Property(x => x.WildCaught).HasMaxLength(10);
                entity.Property(x => x.CatchArea).HasMaxLength(200);
                entity.Property(x => x.PackagingEnterpriseName).HasMaxLength(250);
                entity.Property(x => x.PackagingEnterpriseAddress).HasMaxLength(500);
                entity.Property(x => x.PackagingEnterpriseRegNumber).HasMaxLength(100);

                entity.Property(x => x.PortOfDeparture).HasMaxLength(200);
                entity.Property(x => x.IdentificationDocumentReferences).HasMaxLength(500);

                entity.Property(x => x.ExporterName).HasMaxLength(250);
                entity.Property(x => x.ExporterAddress).HasMaxLength(500);
                entity.Property(x => x.ImporterName).HasMaxLength(250);
                entity.Property(x => x.ImporterAddress).HasMaxLength(500);

                entity.Property(x => x.PlaceOfIssue).HasMaxLength(200);
                entity.Property(x => x.OfficialStamp).HasColumnType("nvarchar(max)");
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(1000);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Attachments)
                    .WithOne(x => x.ChCertificate)
                    .HasForeignKey(x => x.ChCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<ChAttachment>(entity =>
            {
                entity.ToTable("ChAttachments");
                entity.HasKey(x => x.Id);
            });

            modelBuilder.Entity<HkCertificate>(entity =>
            {
                entity.ToTable("HkCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.IdentificationNumber).HasMaxLength(50);
                entity.Property(x => x.CountryOfDispatch).HasMaxLength(120);
                entity.Property(x => x.CompetentAuthority).HasMaxLength(200);
                entity.Property(x => x.CertifyingBody).HasMaxLength(200);

                entity.Property(x => x.SealIdentificationNumber).HasMaxLength(100);
                entity.Property(x => x.StorageTemperature).HasMaxLength(50);

                entity.Property(x => x.ProvenanceDetails).HasMaxLength(1000);
                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);

                entity.Property(x => x.PlaceOfDispatch).HasMaxLength(200);
                entity.Property(x => x.DestinationCountryPlace).HasMaxLength(200);
                entity.Property(x => x.MeansOfTransport).HasMaxLength(200);
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);

                entity.Property(x => x.PlaceOfIssue).HasMaxLength(200);
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);
                entity.Property(x => x.OfficerTel).HasMaxLength(50);
                entity.Property(x => x.OfficerFax).HasMaxLength(50);
                entity.Property(x => x.OfficerEmail).HasMaxLength(120);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.HkCertificate)
                    .HasForeignKey(x => x.HkCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<HkCertificateProduct>(entity =>
            {
                entity.ToTable("HkCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.Description).HasMaxLength(500);
                entity.Property(x => x.Species).HasMaxLength(250);
                entity.Property(x => x.ProcessingType).HasMaxLength(200);
                entity.Property(x => x.PackagingType).HasMaxLength(200);
                entity.Property(x => x.LotCode).HasMaxLength(100);
                entity.Property(x => x.NetWeight).HasColumnType("decimal(18,2)");

                entity.HasIndex(x => x.HkCertificateId);
            });

            modelBuilder.Entity<IdCertificate>(entity =>
            {
                entity.ToTable("IdCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.NumberNomor).HasMaxLength(50);
                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);
                entity.Property(x => x.CompetentAuthority).HasMaxLength(200);
                entity.Property(x => x.EstablishmentName).HasMaxLength(250);
                entity.Property(x => x.EstablishmentRegNo).HasMaxLength(100);
                entity.Property(x => x.EstablishmentAddress).HasMaxLength(1000);
                entity.Property(x => x.CountryRegionOrigin).HasMaxLength(200);
                entity.Property(x => x.PortOfShipment).HasMaxLength(200);
                entity.Property(x => x.CommodityDescription).HasMaxLength(1000);
                entity.Property(x => x.TotalPackages).HasMaxLength(100);
                entity.Property(x => x.PackagingType).HasMaxLength(200);
                entity.Property(x => x.TotalQuantityKg).HasMaxLength(100);
                entity.Property(x => x.ContainerSealNumber).HasMaxLength(200);
                entity.Property(x => x.PortOfDestination).HasMaxLength(200);
                entity.Property(x => x.TransportVesselName).HasMaxLength(250);
                entity.Property(x => x.TransportVoyageNumber).HasMaxLength(100);
                entity.Property(x => x.TestingLaboratory).HasMaxLength(250);
                entity.Property(x => x.LaboratoryAddress).HasMaxLength(1000);
                entity.Property(x => x.ApprovingOfficerName).HasMaxLength(250);
                entity.Property(x => x.TestResultNumber).HasMaxLength(100);
                entity.Property(x => x.AttestationRefNumber).HasMaxLength(100);
                entity.Property(x => x.AdditionalInformation).HasMaxLength(2000);
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);
                entity.Property(x => x.CertifiedIssuedAt).HasMaxLength(200);
                entity.Property(x => x.CertifiedPhone).HasMaxLength(50);
                entity.Property(x => x.CertifiedFax).HasMaxLength(50);
                entity.Property(x => x.CertifiedEmail).HasMaxLength(120);
                entity.Property(x => x.CertifiedPosition).HasMaxLength(200);
                entity.Property(x => x.CertifiedAddress).HasMaxLength(1000);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.IdCertificate)
                    .HasForeignKey(x => x.IdCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<IdCertificateProduct>(entity =>
            {
                entity.ToTable("IdCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.No).HasMaxLength(50);
                entity.Property(x => x.CommonName).HasMaxLength(250);
                entity.Property(x => x.ScientificName).HasMaxLength(250);
                entity.Property(x => x.HsCode).HasMaxLength(50);
                entity.Property(x => x.Quantity).HasColumnType("decimal(18,2)");
                entity.Property(x => x.Unit).HasMaxLength(50);

                entity.HasIndex(x => x.IdCertificateId);
            });

            modelBuilder.Entity<IndCertificate>(entity =>
            {
                entity.ToTable("IndCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.CountryOfDispatch).HasMaxLength(120);
                entity.Property(x => x.CertificateNumber).HasMaxLength(50);

                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsignorTel).HasMaxLength(50);

                entity.Property(x => x.CompetentAuthorityDetails).HasMaxLength(1000);

                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsigneeTel).HasMaxLength(50);

                entity.Property(x => x.CountryOfOrigin).HasMaxLength(120);
                entity.Property(x => x.CountryOfOriginIso).HasMaxLength(10);
                entity.Property(x => x.CountryOfDestination).HasMaxLength(120);
                entity.Property(x => x.CountryOfDestinationIso).HasMaxLength(10);
                entity.Property(x => x.PlaceOfLoading).HasMaxLength(200);

                entity.Property(x => x.MeansOfTransport).HasMaxLength(200);
                entity.Property(x => x.DeclaredPointOfEntry).HasMaxLength(200);
                entity.Property(x => x.ConditionsForTransportStorage).HasMaxLength(500);
                entity.Property(x => x.TotalQuantity).HasMaxLength(100);
                entity.Property(x => x.InvoiceNoDate).HasMaxLength(100);

                entity.Property(x => x.FoodDescription).HasMaxLength(1000);
                entity.Property(x => x.IntendedPurpose).HasMaxLength(200);
                entity.Property(x => x.ProducerNameAddress).HasMaxLength(1000);
                entity.Property(x => x.ApprovalNumberDetails).HasMaxLength(500);

                entity.Property(x => x.AttestationPlace).HasMaxLength(200);
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.IndCertificate)
                    .HasForeignKey(x => x.IndCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<IndCertificateProduct>(entity =>
            {
                entity.ToTable("IndCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.NameOfProduct).HasMaxLength(250);
                entity.Property(x => x.LotNo).HasMaxLength(100);
                entity.Property(x => x.TypeOfPackaging).HasMaxLength(200);
                entity.Property(x => x.NetWeight).HasColumnType("decimal(18,2)");

                entity.HasIndex(x => x.IndCertificateId);
            });

            modelBuilder.Entity<JpCertificate>(entity =>
            {
                entity.ToTable("JpCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.MyRef).HasMaxLength(50);
                entity.Property(x => x.YourRef).HasMaxLength(50);
                entity.Property(x => x.ItemName).HasMaxLength(250);
                entity.Property(x => x.NumberOfPackages).HasMaxLength(50);
                entity.Property(x => x.NetWeight).HasMaxLength(50);
                
                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);
                
                entity.Property(x => x.DespatchFrom).HasMaxLength(200);
                entity.Property(x => x.DespatchTo).HasMaxLength(200);
                entity.Property(x => x.DespatchByShip).HasMaxLength(200);
                
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);
                entity.Property(x => x.CertificateType).HasMaxLength(100);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);
            });

            modelBuilder.Entity<KwCertificate>(entity =>
            {
                entity.ToTable("KwCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                
                entity.Property(x => x.CertificateReferenceNo).HasMaxLength(100);
                entity.Property(x => x.PlaceOfIssue).HasMaxLength(250);
                
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);
                
                entity.Property(x => x.CompetentAuthority).HasMaxLength(250);
                entity.Property(x => x.CompetentAuthorityAddress).HasMaxLength(1000);
                
                entity.Property(x => x.CountryOfOrigin).HasMaxLength(100);
                entity.Property(x => x.CountryOfOriginIso).HasMaxLength(10);
                entity.Property(x => x.CountryOfDestination).HasMaxLength(100);
                entity.Property(x => x.CountryOfDestinationIso).HasMaxLength(10);
                
                entity.Property(x => x.ProducerName).HasMaxLength(250);
                entity.Property(x => x.ProducerAddress).HasMaxLength(1000);
                entity.Property(x => x.PackingEstName).HasMaxLength(250);
                entity.Property(x => x.PackingEstAddress).HasMaxLength(1000);
                
                entity.Property(x => x.BorderOfEntry).HasMaxLength(250);
                entity.Property(x => x.BorderLoadingCountry).HasMaxLength(100);
                entity.Property(x => x.BorderLoadingPlace).HasMaxLength(250);
                
                entity.Property(x => x.VehicleIdentificationNo).HasMaxLength(100);
                
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.CompanyUser)
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(p => p.KwCertificate)
                    .HasForeignKey(p => p.KwCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<KwCertificateProduct>(entity =>
            {
                entity.ToTable("KwCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.NameDescription).HasMaxLength(500);
                entity.Property(x => x.HsCodes).HasMaxLength(100);
                entity.Property(x => x.TreatmentDerivedFrom).HasMaxLength(250);
                entity.Property(x => x.BrandName).HasMaxLength(250);
                
                entity.Property(x => x.BatchLotNo).HasMaxLength(100);
                entity.Property(x => x.TotalWeight).HasColumnType("decimal(18,2)");

                entity.HasIndex(x => x.KwCertificateId);
            });

            modelBuilder.Entity<MyCertificate>(entity =>
            {
                entity.ToTable("MyCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.ExporterName).HasMaxLength(250);
                entity.Property(x => x.CertificateReferenceNo).HasMaxLength(100);
                entity.Property(x => x.QualityCertificateNo).HasMaxLength(100);
                
                entity.Property(x => x.CompetentAuthority).HasMaxLength(250);
                entity.Property(x => x.LocalAuthority).HasMaxLength(250);
                
                entity.Property(x => x.ImporterDetails).HasMaxLength(1000);
                
                entity.Property(x => x.CountryOfOrigin).HasMaxLength(100);
                entity.Property(x => x.CountryOfOriginIso).HasMaxLength(10);
                entity.Property(x => x.CountryOfDestination).HasMaxLength(100);
                entity.Property(x => x.CountryOfDestinationIso).HasMaxLength(10);
                
                entity.Property(x => x.ProcessingEstablishment).HasMaxLength(250);
                entity.Property(x => x.AuthorizationNo).HasMaxLength(100);
                
                entity.Property(x => x.PlaceOfLoading).HasMaxLength(250);
                entity.Property(x => x.PortOfEntry).HasMaxLength(250);
                entity.Property(x => x.TransportCompany).HasMaxLength(250);
                
                entity.Property(x => x.ContainerSealIdentification).HasMaxLength(250);
                entity.Property(x => x.InvoiceNo).HasMaxLength(100);
                entity.Property(x => x.TransitCountry).HasMaxLength(100);
                
                entity.Property(x => x.CertificateReferenceNoPage2).HasMaxLength(100);
                entity.Property(x => x.ProductBrand).HasMaxLength(250);
                entity.Property(x => x.CertifiedProductFor).HasMaxLength(250);
                entity.Property(x => x.TreatmentType).HasMaxLength(250);

                entity.Property(x => x.CertificateReferenceNoPage3).HasMaxLength(100);
                entity.Property(x => x.AdditionalInformation).HasMaxLength(2000);

                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.CompanyUser)
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(p => p.MyCertificate)
                    .HasForeignKey(p => p.MyCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<MyCertificateProduct>(entity =>
            {
                entity.ToTable("MyCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.HsCode).HasMaxLength(100);
                entity.Property(x => x.Description).HasMaxLength(500);
                entity.Property(x => x.ScientificName).HasMaxLength(250);
                entity.Property(x => x.BatchCode).HasMaxLength(100);
                
                entity.Property(x => x.NetWeight).HasColumnType("decimal(18,2)");

                entity.HasIndex(x => x.MyCertificateId);
            });

            modelBuilder.Entity<NzCertificate>(entity =>
            {
                entity.ToTable("NzCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.CertificateRefNumber).HasMaxLength(100);
                
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.CountryOfOrigin).HasMaxLength(100);
                entity.Property(x => x.CountryOfDestination).HasMaxLength(100);
                
                entity.Property(x => x.ProcessorName).HasMaxLength(250);
                entity.Property(x => x.ProcessorEstablishmentNumber).HasMaxLength(100);
                
                entity.Property(x => x.PortDispatchedFrom).HasMaxLength(250);
                entity.Property(x => x.CompetentAuthority).HasMaxLength(250);
                entity.Property(x => x.MeansOfTransport).HasMaxLength(250);
                entity.Property(x => x.TemperatureOfCommodities).HasMaxLength(100);
                
                entity.Property(x => x.ContainerNumber).HasMaxLength(100);
                entity.Property(x => x.OfficialSealNumber).HasMaxLength(100);
                
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(200);
                entity.Property(x => x.Qualification).HasMaxLength(200);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.CompanyUser)
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(p => p.NzCertificate)
                    .HasForeignKey(p => p.NzCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<NzCertificateProduct>(entity =>
            {
                entity.ToTable("NzCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.ProductName).HasMaxLength(500);
                entity.Property(x => x.AquaticAnimalSpecies).HasMaxLength(250);
                entity.Property(x => x.HsCode).HasMaxLength(50);
                entity.Property(x => x.NetWeightKg).HasColumnType("decimal(18,2)");

                entity.HasIndex(x => x.NzCertificateId);
            });

            modelBuilder.Entity<RuCertificate>(entity =>
            {
                entity.ToTable("RuCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.ConsignorNameAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsigneeNameAddress).HasMaxLength(1000);
                entity.Property(x => x.MeansOfTransport).HasMaxLength(500);
                entity.Property(x => x.CountryOfTransit).HasMaxLength(200);
                entity.Property(x => x.CertificateNo).HasMaxLength(100);
                entity.Property(x => x.CountryOfOrigin).HasMaxLength(200);
                entity.Property(x => x.CountryIssuing).HasMaxLength(200);
                entity.Property(x => x.CompetentAuthorityExporting).HasMaxLength(250);
                entity.Property(x => x.OrganizationIssuing).HasMaxLength(250);
                entity.Property(x => x.PointOfCrossingBorder).HasMaxLength(250);

                entity.Property(x => x.ProductName).HasMaxLength(250);
                entity.Property(x => x.TypeOfPackage).HasMaxLength(200);
                entity.Property(x => x.NumberOfPackages).HasMaxLength(100);
                entity.Property(x => x.NetWeight).HasMaxLength(100);
                entity.Property(x => x.NumberOfSeal).HasMaxLength(100);
                entity.Property(x => x.IdentificationMarks).HasMaxLength(250);
                entity.Property(x => x.StorageConditions).HasMaxLength(250);

                entity.Property(x => x.EstablishmentNameAddressRegNo).HasMaxLength(1000);
                entity.Property(x => x.FactoryVessel).HasMaxLength(250);
                entity.Property(x => x.ColdStore).HasMaxLength(250);
                entity.Property(x => x.AdministrativeUnit).HasMaxLength(250);

                entity.Property(x => x.PlaceOfIssue).HasMaxLength(250);
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(250);
                entity.Property(x => x.Qualification).HasMaxLength(250);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasIndex(x => x.SignatoryUserId);

                entity.Property(x => x.IdentificationMarksAttachment).HasMaxLength(500);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);
            });

            modelBuilder.Entity<RuPreExportCertificate>(entity =>
            {
                entity.ToTable("RuPreExportCertificates");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.Date).HasMaxLength(100);
                entity.Property(x => x.Number).HasMaxLength(100);
                entity.Property(x => x.CountryOfOrigin).HasMaxLength(200);
                entity.Property(x => x.AdministrativeTerritory).HasMaxLength(200);
                entity.Property(x => x.ApprovalNumber).HasMaxLength(100);
                entity.Property(x => x.ProductNameAndQuantity).HasMaxLength(500);
            });

            modelBuilder.Entity<RuAttachment>(entity =>
            {
                entity.ToTable("RuAttachments");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.Product).HasMaxLength(500);
                entity.Property(x => x.NumberOfKgs).HasColumnType("decimal(18,2)");
            });

            modelBuilder.Entity<TwCertificate>(entity =>
            {
                entity.ToTable("TwCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.ReferenceNo).HasMaxLength(100);
                entity.Property(x => x.CountryOfExport).HasMaxLength(200);
                entity.Property(x => x.CountryOfProduction).HasMaxLength(200);
                entity.Property(x => x.CompetentAuthority).HasMaxLength(250);
                entity.Property(x => x.DepartmentIssuance).HasMaxLength(250);

                entity.Property(x => x.ProductionPlace).HasMaxLength(500);
                entity.Property(x => x.ProcessingType).HasMaxLength(250);
                entity.Property(x => x.ProductionMode).HasMaxLength(250);
                entity.Property(x => x.AquacultureArea).HasMaxLength(250);
                entity.Property(x => x.CatchArea).HasMaxLength(250);
                entity.Property(x => x.HarvestingArea).HasMaxLength(250);
                entity.Property(x => x.VesselName).HasMaxLength(250);
                entity.Property(x => x.EnterpriseName).HasMaxLength(250);
                entity.Property(x => x.EnterpriseRegistrationNo).HasMaxLength(250);

                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);
                entity.Property(x => x.PlaceOfDispatch).HasMaxLength(250);
                entity.Property(x => x.PlaceOfDestination).HasMaxLength(250);
                entity.Property(x => x.MeansOfTransport).HasMaxLength(250);
                entity.Property(x => x.VesselNameTransport).HasMaxLength(250);
                entity.Property(x => x.FlightNumber).HasMaxLength(100);
                entity.Property(x => x.OtherTransportMeans).HasMaxLength(250);
                entity.Property(x => x.ContainerNumber).HasMaxLength(100);
                entity.Property(x => x.SealNumber).HasMaxLength(100);

                entity.Property(x => x.PlaceOfIssue).HasMaxLength(250);
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(250);
                entity.Property(x => x.Qualification).HasMaxLength(250);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasIndex(x => x.SignatoryUserId);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(p => p.TwCertificate)
                    .HasForeignKey(p => p.TwCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<TwCertificateProduct>(entity =>
            {
                entity.ToTable("TwCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CommodityName).HasMaxLength(500);
                entity.Property(x => x.HsCode).HasMaxLength(100);
                entity.Property(x => x.ScientificName).HasMaxLength(250);
                entity.Property(x => x.NetWeight).HasColumnType("decimal(18,2)");

                entity.HasIndex(x => x.TwCertificateId);
            });

            modelBuilder.Entity<UaCertificate>(entity =>
            {
                entity.ToTable("UaCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);
                entity.Property(x => x.PersonResponsibleName).HasMaxLength(250);
                entity.Property(x => x.PersonResponsibleAddress).HasMaxLength(1000);

                entity.Property(x => x.CertificateReferenceNumber).HasMaxLength(100);
                entity.Property(x => x.CentralCompetentAuthority).HasMaxLength(250);
                entity.Property(x => x.LocalCompetentAuthority).HasMaxLength(250);

                entity.Property(x => x.DescriptionOfCommodity).HasMaxLength(1000);
                entity.Property(x => x.CommodityCodeHS).HasMaxLength(100);
                entity.Property(x => x.Quantity).HasMaxLength(100);
                entity.Property(x => x.NumberOfPackages).HasMaxLength(100);
                entity.Property(x => x.SealContainerNo).HasMaxLength(100);
                entity.Property(x => x.TypeOfPackaging).HasMaxLength(250);

                entity.Property(x => x.HealthCertificateReferenceNumber).HasMaxLength(100);
                entity.Property(x => x.AdditionalInformation).HasMaxLength(2000);
                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(250);
                entity.Property(x => x.Qualification).HasMaxLength(250);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasIndex(x => x.SignatoryUserId);

                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(p => p.UaCertificate)
                    .HasForeignKey(p => p.UaCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<UaCertificateProduct>(entity =>
            {
                entity.ToTable("UaCertificateProducts");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.Species).HasMaxLength(250);
                entity.Property(x => x.NatureOfCommodity).HasMaxLength(250);
                entity.Property(x => x.TreatmentApprovalNumber).HasMaxLength(250);
                entity.Property(x => x.ManufacturingPlant).HasMaxLength(500);
                entity.Property(x => x.NumberOfPackaging).HasMaxLength(100);
                entity.Property(x => x.TypeOfPackaging).HasMaxLength(250);
                entity.Property(x => x.NetWeight).HasMaxLength(100);

                entity.HasIndex(x => x.UaCertificateId);
            });

            modelBuilder.Entity<UkCertificate>(entity =>
            {
                entity.ToTable("UkCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.CertificateReferenceNo).HasMaxLength(100);

                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsignorTel).HasMaxLength(100);

                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsigneeTel).HasMaxLength(100);

                entity.Property(x => x.OperatorName).HasMaxLength(250);
                entity.Property(x => x.OperatorAddress).HasMaxLength(1000);
                entity.Property(x => x.OperatorTel).HasMaxLength(100);

                entity.Property(x => x.CountryOfOrigin).HasMaxLength(200);
                entity.Property(x => x.CountryOfOriginISO).HasMaxLength(20);
                entity.Property(x => x.RegionOfOrigin).HasMaxLength(200);
                entity.Property(x => x.RegionOfOriginCode).HasMaxLength(50);

                entity.Property(x => x.CountryOfDestination).HasMaxLength(200);
                entity.Property(x => x.CountryOfDestinationISO).HasMaxLength(20);
                entity.Property(x => x.RegionOfDestination).HasMaxLength(200);
                entity.Property(x => x.RegionOfDestinationCode).HasMaxLength(50);

                entity.Property(x => x.PlaceOfDispatchName).HasMaxLength(250);
                entity.Property(x => x.PlaceOfDispatchApprovalNo).HasMaxLength(100);
                entity.Property(x => x.PlaceOfDispatchAddress).HasMaxLength(1000);

                entity.Property(x => x.PlaceOfDestinationName).HasMaxLength(250);
                entity.Property(x => x.PlaceOfDestinationAddress).HasMaxLength(1000);

                entity.Property(x => x.PlaceOfLoading).HasMaxLength(250);
                entity.Property(x => x.TimeOfDeparture).HasMaxLength(50);

                entity.Property(x => x.TransportIdentification).HasMaxLength(250);
                entity.Property(x => x.EntryBCP).HasMaxLength(250);

                entity.Property(x => x.AccompDocType).HasMaxLength(100);
                entity.Property(x => x.AccompDocNo).HasMaxLength(100);

                entity.Property(x => x.ContainerSealNo).HasMaxLength(100);

                entity.Property(x => x.Field21).HasMaxLength(1000);
                entity.Property(x => x.Field22).HasMaxLength(1000);

                entity.Property(x => x.TotalNumberOfPackages).HasMaxLength(100);
                entity.Property(x => x.TotalNetWeight).HasMaxLength(100);
                entity.Property(x => x.TotalGrossWeight).HasMaxLength(100);

                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(250);
                entity.Property(x => x.Qualification).HasMaxLength(250);

                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.CompanyUser)
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne<AppUser>()
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(p => p.UkCertificate)
                    .HasForeignKey(p => p.UkCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<UkCertificateProduct>(entity =>
            {
                entity.ToTable("UkCertificateProducts");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.Species).HasMaxLength(250);
                entity.Property(x => x.NatureOfCommodity).HasMaxLength(500);
                entity.Property(x => x.TreatmentType).HasMaxLength(250);
                entity.Property(x => x.VesselPlant).HasMaxLength(500);
                entity.Property(x => x.NumberOfPackages).HasMaxLength(100);
                entity.Property(x => x.NetWeight).HasMaxLength(100);
                entity.Property(x => x.BatchNo).HasMaxLength(100);
                entity.Property(x => x.TypeOfPackaging).HasMaxLength(250);

                entity.HasIndex(x => x.UkCertificateId);
            });

            modelBuilder.Entity<UsaCertificate>(entity =>
            {
                entity.ToTable("UsaCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.Property(x => x.MyRef).HasMaxLength(100);
                entity.Property(x => x.YourRef).HasMaxLength(100);
                entity.Property(x => x.ItemName).HasMaxLength(500);
                entity.Property(x => x.NumberOfPackages).HasMaxLength(100);
                entity.Property(x => x.NetWeight).HasMaxLength(100);

                entity.Property(x => x.ConsignorName).HasMaxLength(250);
                entity.Property(x => x.ConsignorAddress).HasMaxLength(1000);
                entity.Property(x => x.ConsigneeName).HasMaxLength(250);
                entity.Property(x => x.ConsigneeAddress).HasMaxLength(1000);

                entity.Property(x => x.DespatchFrom).HasMaxLength(250);
                entity.Property(x => x.DespatchTo).HasMaxLength(250);
                entity.Property(x => x.DespatchByShip).HasMaxLength(250);

                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(250);
                entity.Property(x => x.Qualification).HasMaxLength(250);

                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.SignatoryUser)
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.CompanyUser)
                    .WithMany()
                    .HasForeignKey(x => x.CompanyUserId)
                    .OnDelete(DeleteBehavior.Restrict);
            });

            modelBuilder.Entity<IlCertificate>(entity =>
            {
                entity.ToTable("IlCertificates");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.CertificationNo).HasMaxLength(50);
                entity.Property(x => x.CentralCompetentAuthority).HasMaxLength(200);
                entity.Property(x => x.LocalCompetentAuthority).HasMaxLength(200);
                entity.Property(x => x.ConsignorName).HasMaxLength(200);
                entity.Property(x => x.ConsigneeName).HasMaxLength(200);
                entity.Property(x => x.PlaceOfOriginName).HasMaxLength(250);
                entity.Property(x => x.PlaceOfArrival).HasMaxLength(200);
                entity.Property(x => x.PlaceOfDestinationName).HasMaxLength(250);
                entity.Property(x => x.MeansOfTransportIdentification).HasMaxLength(150);
                entity.Property(x => x.ContainerNo).HasMaxLength(100);
                entity.Property(x => x.SealNo).HasMaxLength(100);
                entity.Property(x => x.MeansOfTransportReference).HasMaxLength(150);
                entity.Property(x => x.EntryBIP).HasMaxLength(200);
                entity.Property(x => x.Remarks).HasMaxLength(1000);

                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.SignatoryName).HasMaxLength(250);
                entity.Property(x => x.Qualification).HasMaxLength(250);
                
                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.IlCertificate)
                    .HasForeignKey(x => x.IlCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);

                entity.HasOne(x => x.SignatoryUser)
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);
            });

            modelBuilder.Entity<IlCertificateProduct>(entity =>
            {
                entity.ToTable("IlCertificateProducts");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.DescriptionOfCommodity).HasMaxLength(500);
                entity.Property(x => x.SpeciesScientificName).HasMaxLength(250);
                entity.Property(x => x.NatureOfCommodity).HasMaxLength(250);
                entity.Property(x => x.TreatmentType).HasMaxLength(250);
                entity.Property(x => x.ApprovalNo).HasMaxLength(150);
                entity.Property(x => x.NetWeight).HasColumnType("decimal(18,2)");
                entity.Property(x => x.LotNo).HasMaxLength(100);
                
                entity.HasIndex(x => x.IlCertificateId);
            });

            modelBuilder.Entity<MvCertificate>(entity =>
            {
                entity.ToTable("MvCertificates");
                entity.HasKey(x => x.Id);

                entity.Property(x => x.ConsignorExporter).HasMaxLength(500);
                entity.Property(x => x.CertificateNumber).HasMaxLength(100);
                entity.Property(x => x.CompetentAuthority).HasMaxLength(250);
                entity.Property(x => x.CertifyingBody).HasMaxLength(250);
                entity.Property(x => x.ConsigneeImporter).HasMaxLength(500);
                entity.Property(x => x.CountryOfOrigin).HasMaxLength(150);
                entity.Property(x => x.CountryOfOriginISO).HasMaxLength(10);
                entity.Property(x => x.CountryOfDestination).HasMaxLength(150);
                entity.Property(x => x.CountryOfDestinationISO).HasMaxLength(10);
                entity.Property(x => x.PlaceOfLoading).HasMaxLength(500);
                
                entity.Property(x => x.PointsOfEntry).HasMaxLength(250);
                entity.Property(x => x.ConditionsOfStorage).HasMaxLength(250);
                entity.Property(x => x.TotalQuantity).HasMaxLength(100);
                entity.Property(x => x.SealNumber).HasMaxLength(250);
                entity.Property(x => x.TotalNumberOfPackages).HasMaxLength(100);
                entity.Property(x => x.ApprovalNumberOfEstablishments).HasMaxLength(250);
                entity.Property(x => x.DescriptionOfCommodity).HasMaxLength(1000);
                entity.Property(x => x.CertifyingOfficerName).HasMaxLength(250);
                entity.Property(x => x.SignatoryName).HasMaxLength(250);
                entity.Property(x => x.Qualification).HasMaxLength(250);
                entity.Property(x => x.CompanyRegistrationNo).HasMaxLength(100);

                entity.Property(x => x.SignatoryUserId).HasMaxLength(450);
                entity.Property(x => x.CompanyUserId).IsRequired();
                entity.Property(x => x.CreatedAt).HasDefaultValueSql("GETUTCDATE()").IsRequired();

                entity.HasIndex(x => x.SignatoryUserId);
                entity.HasIndex(x => x.CompanyUserId);
                entity.HasIndex(x => x.CreatedAt);
                entity.HasIndex(x => x.CertificateRequestId);

                entity.HasOne(x => x.CertificateRequest)
                    .WithMany()
                    .HasForeignKey(x => x.CertificateRequestId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasOne(x => x.SignatoryUser)
                    .WithMany()
                    .HasForeignKey(x => x.SignatoryUserId)
                    .OnDelete(DeleteBehavior.Restrict);

                entity.HasMany(x => x.Products)
                    .WithOne(x => x.MvCertificate)
                    .HasForeignKey(x => x.MvCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);

                entity.HasMany(x => x.ProductsSecond)
                    .WithOne(x => x.MvCertificate)
                    .HasForeignKey(x => x.MvCertificateId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<MvCertificateProduct>(entity =>
            {
                entity.ToTable("MvCertificateProducts");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.No).HasMaxLength(50);
                entity.Property(x => x.NatureOfCommodity).HasMaxLength(500);
                entity.Property(x => x.Species).HasMaxLength(500);
                entity.Property(x => x.PurposeOfUse).HasMaxLength(500);
            });

            modelBuilder.Entity<MvCertificateProductSecond>(entity =>
            {
                entity.ToTable("MvCertificateProductSecond");
                entity.HasKey(x => x.Id);
                entity.Property(x => x.No).HasMaxLength(50);
                entity.Property(x => x.NameOfTheProduct).HasMaxLength(500);
                entity.Property(x => x.LotIdentifier).HasMaxLength(500);
                entity.Property(x => x.TypeOfPackaging).HasMaxLength(500);
            });
            modelBuilder.Entity<ReferenceSequence>(entity =>
            {
                entity.ToTable("ReferenceSequences");
            });
        }
    }
}

