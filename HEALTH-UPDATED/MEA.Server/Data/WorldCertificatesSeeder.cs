using MEA.Server.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace MEA.Server.Data
{
    public static class WorldCertificatesSeeder
    {
        public const string OfficialStampSvg = "data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\" viewBox=\"0 0 200 200\"><circle cx=\"100\" cy=\"100\" r=\"90\" fill=\"none\" stroke=\"%23047857\" stroke-width=\"4\" stroke-dasharray=\"6,3\"/><circle cx=\"100\" cy=\"100\" r=\"78\" fill=\"none\" stroke=\"%23047857\" stroke-width=\"2\"/><path id=\"stamp-curve\" d=\"M 30 100 A 70 70 0 0 1 170 100\" fill=\"none\"/><path id=\"stamp-curve-bottom\" d=\"M 170 100 A 70 70 0 0 1 30 100\" fill=\"none\"/><text fill=\"%23047857\" font-size=\"11\" font-weight=\"bold\" font-family=\"Arial, sans-serif\" letter-spacing=\"2\"><textPath href=\"%23stamp-curve\" startOffset=\"50%\" text-anchor=\"middle\">DEPT. OF FISHERIES %26 AQUATIC RES.</textPath></text><text fill=\"%23047857\" font-size=\"10\" font-weight=\"bold\" font-family=\"Arial, sans-serif\" letter-spacing=\"1.5\"><textPath href=\"%23stamp-curve-bottom\" startOffset=\"50%\" text-anchor=\"middle\">★ SRI LANKA ★ OFFICIAL</textPath></text><circle cx=\"100\" cy=\"100\" r=\"45\" fill=\"%23ecfdf5\" stroke=\"%23047857\" stroke-width=\"1.5\"/><text x=\"100\" y=\"93\" text-anchor=\"middle\" fill=\"%23047857\" font-size=\"9\" font-weight=\"bold\" font-family=\"Arial, sans-serif\">CERTIFIED</text><text x=\"100\" y=\"105\" text-anchor=\"middle\" fill=\"%23047857\" font-size=\"11\" font-weight=\"bold\" font-family=\"Arial, sans-serif\">APPROVED</text><text x=\"100\" y=\"116\" text-anchor=\"middle\" fill=\"%23047857\" font-size=\"8\" font-family=\"Arial, sans-serif\">DFAR-LK-VET</text></svg>";

        public const string OfficialSignatureSvg = "data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"220\" height=\"80\" viewBox=\"0 0 220 80\"><path d=\"M 15 55 Q 35 15, 55 45 T 90 35 T 120 50 Q 145 10, 160 45 T 195 40 M 45 45 Q 90 70, 180 50\" fill=\"none\" stroke=\"%231e3a8a\" stroke-width=\"2.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>";

        public static async Task SeedAllAsync(AppDbContext context, UserManager<AppUser> userManager)
        {
            var companyUser = await userManager.FindByEmailAsync("company@gmail.com");
            var adminUser = await userManager.FindByEmailAsync("admin@gmail.com");

            if (companyUser == null || adminUser == null)
            {
                return;
            }

            // Ensure company entity exists
            var existingCompany = await context.Companies.FirstOrDefaultAsync(c => c.UserId == companyUser.Id || c.CompanyEmail == companyUser.Email);
            if (existingCompany == null)
            {
                var defaultStatus = await context.CompanyStatuses.FirstOrDefaultAsync() ?? new CompanyStatus { Name = "Non EU" };
                if (defaultStatus.Id == 0)
                {
                    context.CompanyStatuses.Add(defaultStatus);
                    await context.SaveChangesAsync();
                }

                var defaultListedCountry = await context.ListedCountries.FirstOrDefaultAsync() ?? new ListedCountry { Name = "China" };
                if (defaultListedCountry.Id == 0)
                {
                    context.ListedCountries.Add(defaultListedCountry);
                    await context.SaveChangesAsync();
                }

                existingCompany = new Company
                {
                    CompanyName = "Oceanic Exports (Pvt) Ltd",
                    CompanyEmail = companyUser.Email ?? "company@gmail.com",
                    CompanyPhone = "+94 11 243 5678",
                    CompanyAddress = "No. 124, Harbour Road, Mutwal, Colombo 15, Sri Lanka",
                    RegistrationNo = "DFAR/EXP/2026/042",
                    CompanyStatusId = defaultStatus.Id,
                    ListedCountryId = defaultListedCountry.Id,
                    UserId = companyUser.Id,
                    CreatedAt = DateTime.UtcNow
                };

                context.Companies.Add(existingCompany);
                await context.SaveChangesAsync();
            }

            if (companyUser.CompanyId == null || companyUser.CompanyId != existingCompany.Id)
            {
                companyUser.CompanyId = existingCompany.Id;
                companyUser.Qualification = "Quality Assurance Manager";
                await userManager.UpdateAsync(companyUser);
            }

            if (string.IsNullOrEmpty(adminUser.Qualification))
            {
                adminUser.Qualification = "Authorized Fish Inspection Veterinarian (B.V.Sc., M.Sc.)";
                await userManager.UpdateAsync(adminUser);
            }

            var countries = await context.Countries.ToListAsync();

            // 22 Supported Countries Configuration
            var countryConfigs = new[]
            {
                new { Name = "China", Code = "CH", Iso = "CN", CertNumber = "TC 4643" },
                new { Name = "United States of America", Code = "USA", Iso = "US", CertNumber = "USA-DFAR-2026-001" },
                new { Name = "United Kingdom", Code = "UK", Iso = "GB", CertNumber = "GB-DFAR-2026-002" },
                new { Name = "Australia", Code = "AU", Iso = "AU", CertNumber = "AU-DFAR-2026-003" },
                new { Name = "Canada", Code = "CA", Iso = "CA", CertNumber = "CA-DFAR-2026-004" },
                new { Name = "Russia", Code = "RU", Iso = "RU", CertNumber = "RU-DFAR-2026-005" },
                new { Name = "Japan", Code = "JP", Iso = "JP", CertNumber = "JP-DFAR-2026-006" },
                new { Name = "Maldives", Code = "MV", Iso = "MV", CertNumber = "MV-DFAR-2026-007" },
                new { Name = "Malaysia", Code = "MY", Iso = "MY", CertNumber = "MY-DFAR-2026-008" },
                new { Name = "Armenia", Code = "AM", Iso = "AM", CertNumber = "AM-DFAR-2026-009" },
                new { Name = "Brazil", Code = "BR", Iso = "BR", CertNumber = "BR-DFAR-2026-010" },
                new { Name = "India", Code = "IND", Iso = "IN", CertNumber = "IND-DFAR-2026-011" },
                new { Name = "Indonesia", Code = "ID", Iso = "ID", CertNumber = "ID-DFAR-2026-012" },
                new { Name = "Israel", Code = "IL", Iso = "IL", CertNumber = "IL-DFAR-2026-013" },
                new { Name = "Kazakhstan", Code = "KZ", Iso = "KZ", CertNumber = "KZ-DFAR-2026-014" },
                new { Name = "Kuwait", Code = "KW", Iso = "KW", CertNumber = "KW-DFAR-2026-015" },
                new { Name = "New Zealand", Code = "NZ", Iso = "NZ", CertNumber = "NZ-DFAR-2026-016" },
                new { Name = "Saudi Arabia", Code = "SA", Iso = "SA", CertNumber = "SA-DFAR-2026-017" },
                new { Name = "South Africa", Code = "ZA", Iso = "ZA", CertNumber = "ZA-DFAR-2026-018" },
                new { Name = "Taiwan", Code = "TW", Iso = "TW", CertNumber = "TW-DFAR-2026-019" },
                new { Name = "Ukraine", Code = "UA", Iso = "UA", CertNumber = "UA-DFAR-2026-020" },
                new { Name = "Hong Kong", Code = "HK", Iso = "HK", CertNumber = "HK-DFAR-2026-021" },
                new { Name = "European Union", Code = "EU", Iso = "EU", CertNumber = "EU-DFAR-2026-000" }
            };

            for (int i = 0; i < countryConfigs.Length; i++)
            {
                var cfg = countryConfigs[i];
                Country? country = null;
                if (cfg.Code != "EU")
                {
                    country = countries.FirstOrDefault(c => c.Name.Equals(cfg.Name, StringComparison.OrdinalIgnoreCase));
                    if (country == null)
                    {
                        country = new Country { Name = cfg.Name };
                        context.Countries.Add(country);
                        await context.SaveChangesAsync();
                    }
                }

                var refNumber = $"HC-2026-{cfg.Code}-001";
                var req = await context.CertificateRequests.FirstOrDefaultAsync(r => r.ReferenceNumber == refNumber);
                if (req == null)
                {
                    req = new CertificateRequest
                    {
                        ReferenceNumber = refNumber,
                        CertificateType = cfg.Code == "EU" ? CertificateType.EU : CertificateType.NonEU,
                        CompanyUserId = companyUser.Id,
                        CountryId = country?.Id,
                        Status = CertificateStatus.Confirmed,
                        CreatedAt = DateTime.UtcNow.AddHours(-1 * (i + 1))
                    };

                    context.CertificateRequests.Add(req);
                    await context.SaveChangesAsync();
                }

                // Seed Vet Form (Application)
                var vetForm = await context.VetCertificateForms.FirstOrDefaultAsync(v => v.CertificateRequestId == req.Id);
                if (vetForm == null)
                {
                    vetForm = new VetCertificateForm
                    {
                        CertificateRequestId = req.Id,
                        CompanyUserId = companyUser.Id,
                        CreatedAt = req.CreatedAt,
                        ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                        ConsignorAddress = "NO. 124, HARBOUR ROAD, MUTWAL, COLOMBO 15, SRI LANKA",
                        ConsignorPostal = "01500",
                        ConsignorTel = "+94 11 243 5678",
                        ConsigneeName = $"GLOBAL SEAFOOD IMPORTS ({cfg.Name.ToUpperInvariant()}) CORP",
                        ConsigneeAddress = $"PORT LOGISTICS AVENUE, {cfg.Name.ToUpperInvariant()}",
                        ConsigneePostal = "90210",
                        ConsigneeTel = "+1 800 555 0199",
                        CountryOrigin = "SRI LANKA",
                        CountryOriginISO = "LK",
                        CountryDestinationISO = cfg.Iso,
                        ProcessingEstName = "OCEANIC EXPORTS FISH PROCESSING FACILITY",
                        ProcessingEstAddress = "MUTWAL FISHERY HARBOUR COMPLEX, COLOMBO 15",
                        ApprovalNo = "DFAR/FQC/PP/042",
                        PlaceOfLoading = "BANDARANAIKE INTERNATIONAL AIRPORT (CMB)",
                        DateOfDeparture = DateTime.UtcNow.AddDays(2),
                        TransportAeroPlane = true,
                        TransportId = $"AIR FLIGHT LK-{800 + i}",
                        DescCommon = "CHILLED GROUPER & YELLOWFIN TUNA FILLETS",
                        DescScientific = "Epinephelus malabaricus / Thunnus albacares",
                        ProcessingType = "Chilled Whole Round / Fresh Fillets",
                        HsCode = "0302.89.00",
                        TemperatureChilled = true,
                        Quantity = "2,500 KG",
                        NetWeight = "2500",
                        NumPackages = "125 BOXES",
                        PackagingType = "INSULATED STYROFOAM CARTONS WITH DRY ICE",
                        ContainerId = $"DFAR-LK-{1000 + i}",
                        ForHumanConsumption = true,
                        ForImportEU = cfg.Code == "EU" ? "Yes" : "No",
                        NatureWildOrigin = true,
                        TreatmentChilled = true,
                        SignatoryName = "Dr. N. Fernando",
                        Designation = "Authorized Fish Inspection Veterinarian",
                        SignatureDate = DateTime.UtcNow,
                        SignatureTime = DateTime.UtcNow,
                        Signature = OfficialSignatureSvg,
                        Attestation61_1 = true,
                        Attestation61_2 = true,
                        Attestation61_3 = true,
                        Attestation61_4 = true,
                        Attestation61_5 = true,
                        Attestation62_1 = true,
                        Attestation62_2 = true
                    };

                    context.VetCertificateForms.Add(vetForm);
                    await context.SaveChangesAsync();
                }

                // Seed Specific Country Certificate
                await SeedCountryCertificateAsync(context, cfg.Code, req.Id, refNumber, companyUser.Id, adminUser.Id, cfg.Name, cfg.Iso);
            }
        }

        private static async Task SeedCountryCertificateAsync(
            AppDbContext context,
            string countryCode,
            int requestId,
            string refNumber,
            string companyUserId,
            string adminUserId,
            string countryName,
            string countryIso)
        {
            switch (countryCode)
            {
                case "EU":
                    // European Union uses the standard Vet Health Certificate
                    break;
                case "CH":
                    if (!await context.ChCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var ch = new ChCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            RefNumber = refNumber,
                            CertificateType = "attachment",
                            CountryOfExport = "SRI LANKA",
                            CountryOfProduction = "SRI LANKA",
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            DepartmentOfIssuance = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            CommodityName = "CHILLED GROUPER & POMFRET FISH",
                            ScientificName = "Epinephelus malabaricus",
                            LatinName = "Epinephelus malabaricus",
                            Number = "125",
                            NumberOfPackages = "125 BOXES",
                            NetWeight = "2500.00 KG",
                            ProductionDate = DateTime.UtcNow.AddDays(-2),
                            LotNumber = "LOT-2026-CH-01",
                            OriginRawMaterialsCountry = "SRI LANKA",
                            ProcessingType = "Chilled Whole Round",
                            ProductionMode = "Wild Caught",
                            WildCaughtBool = true,
                            CatchArea = "FAO 51 - Indian Ocean",
                            ProcessingPlantNameAddress = "OCEANIC EXPORTS FISH PROCESSING FACILITY, MUTWAL, COLOMBO 15",
                            ProcessingPlantRegNo = "DFAR/FQC/PP/042",
                            PackagingEnterpriseName = "OCEANIC PACKAGING DIVISION",
                            PackagingEnterpriseAddress = "MUTWAL FISHERY HARBOUR, COLOMBO",
                            PackagingEnterpriseRegNumber = "DFAR/FQC/PK/042",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "SHANGHAI SEAFOOD IMPORT & EXPORT CO., LTD",
                            ConsigneeAddress = "WAIGAOQIAO FREE TRADE ZONE, PUDONG, SHANGHAI, CHINA",
                            PlaceOfDispatch = "COLOMBO",
                            PlaceOfDestination = "SHANGHAI PORT",
                            TransportAeroPlane = true,
                            FlightNumber = "UL 866",
                            ContainerNumber = "CONT-CH-9988",
                            SealNumber = "SEAL-CH-4433",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            PortOfDeparture = "BANDARANAIKE INT'L AIRPORT (CMB)",
                            PlaceOfIssue = "COLOMBO",
                            DateOfIssue = DateTime.UtcNow,
                            OfficialStamp = OfficialStampSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian"
                        };

                        ch.Attachments.Add(new ChAttachment { Product = "CHILLED GROUPER FISH (Epinephelus malabaricus)", NetWeight = 1250, NumberOfBoxes = 65 });
                        ch.Attachments.Add(new ChAttachment { Product = "CHILLED CHINESE POMFRET FISH (Pampus chinensis)", NetWeight = 1250, NumberOfBoxes = 60 });
                        context.ChCertificates.Add(ch);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "USA":
                    if (!await context.UsaCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var usa = new UsaCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            MyRef = refNumber,
                            YourRef = "PO-USA-9981",
                            Date = DateTime.UtcNow,
                            CertificateNumber = refNumber,
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CertifyingBody = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginISO = "LK",
                            CountryOfDestination = "UNITED STATES OF AMERICA",
                            CountryOfDestinationISO = "US",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, MUTWAL, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "AMERICAN PACIFIC SEAFOOD DISTRIBUTORS LLC",
                            ConsigneeAddress = "100 FISHERMAN WHARF, LOS ANGELES, CA 90021, USA",
                            PlaceOfLoading = "BANDARANAIKE INTERNATIONAL AIRPORT",
                            TransportAeroPlane = true,
                            DespatchFrom = "COLOMBO, SRI LANKA",
                            DespatchTo = "LOS ANGELES (LAX), USA",
                            ItemName = "CHILLED YELLOWFIN TUNA & SWORDFISH LOINS",
                            NumberOfPackages = "120 BOXES",
                            NetWeight = "2400 KG",
                            TotalQuantity = "2,400 KG",
                            TotalNumberOfPackages = "120 BOXES",
                            ProcessingPlantName = "OCEANIC EXPORTS PLANT",
                            ProcessingPlantAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            CompetentAuthorityRegNo = "DFAR/FQC/PP/042",
                            ApprovalNumberOfEstablishments = "DFAR/FQC/PP/042",
                            PointsOfEntry = "LOS ANGELES (LAX)",
                            ConditionsOfStorage = "CHILLED (0°C TO 4°C)",
                            SealNumber = "SEAL-US-7788",
                            DescriptionOfCommodity = "FRESH CHILLED YELLOWFIN TUNA LOINS (Thunnus albacares)",
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Designation = "Authorized Fish Inspection Veterinarian",
                            Qualification = "B.V.Sc., M.Sc.",
                            CompanyRegistrationNo = "DFAR/EXP/2026/042",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            CertificateType = "attachment"
                        };

                        usa.ProductsAttachment.Add(new UsaCertificateProductAttachment { Product = "CHILLED YELLOWFIN TUNA LOINS", LotIdentifier = "LOT-US-01", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "1400", NumberOfBoxes = 70 });
                        usa.ProductsAttachment.Add(new UsaCertificateProductAttachment { Product = "CHILLED SWORDFISH LOINS", LotIdentifier = "LOT-US-02", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "1000", NumberOfBoxes = 50 });
                        context.UsaCertificates.Add(usa);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "UK":
                    if (!await context.UkCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var uk = new UkCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateReferenceNo = refNumber,
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsignorTel = "+94 11 243 5678",
                            ConsigneeName = "BRITISH SEAFOOD IMPORTS LTD",
                            ConsigneeAddress = "ROYAL VICTORIA DOCKS, LONDON E16 1AA, UNITED KINGDOM",
                            ConsigneeTel = "+44 20 7946 0912",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginISO = "LK",
                            CountryOfDestination = "UNITED KINGDOM",
                            CountryOfDestinationISO = "GB",
                            PlaceOfDispatchName = "OCEANIC EXPORTS PLANT",
                            PlaceOfDispatchAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            PlaceOfDispatchApprovalNo = "DFAR/FQC/PP/042",
                            PlaceOfLoading = "BANDARANAIKE INTERNATIONAL AIRPORT",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            TimeOfDeparture = "14:30 UTC",
                            TransportAeroplane = true,
                            TransportIdentification = "BRITISH AIRWAYS BA 2068",
                            EntryBCP = "LONDON HEATHROW (LHR)",
                            TempChilled = true,
                            GoodsHumanConsumption = true,
                            TotalNumberOfPackages = "110 BOXES",
                            TotalNetWeight = "2200 KG",
                            TotalGrossWeight = "2420 KG",
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Official Veterinarian (DFAR / Sri Lanka)",
                            CertifiedDate = DateTime.UtcNow,
                            StrikeAnimalHealthAll = true
                        };

                        uk.Products.Add(new UkCertificateProduct { Species = "Thunnus albacares", NatureOfCommodity = "Wild Origin", TreatmentType = "Chilled", VesselPlant = "DFAR/FQC/PP/042", NumberOfPackages = "70", NetWeight = "1400", BatchNo = "BATCH-UK-01", TypeOfPackaging = "Styrofoam Cartons" });
                        uk.Products.Add(new UkCertificateProduct { Species = "Epinephelus malabaricus", NatureOfCommodity = "Wild Origin", TreatmentType = "Chilled", VesselPlant = "DFAR/FQC/PP/042", NumberOfPackages = "40", NetWeight = "800", BatchNo = "BATCH-UK-02", TypeOfPackaging = "Styrofoam Cartons" });
                        context.UkCertificates.Add(uk);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "AU":
                    if (!await context.AuCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var au = new AuCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertRefNumber = refNumber,
                            CertRefNumberA = refNumber,
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "AUSTRALIAN SEAFOOD WHOLESALERS PTY LTD",
                            ConsigneeAddress = "SYDNEY FISH MARKET, PYRMONT NSW 2009, AUSTRALIA",
                            CentralCompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            LocalCompetentAuthority = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            CountryOrigin = "SRI LANKA",
                            CountryOriginISO = "LK",
                            CountryDestination = "AUSTRALIA",
                            CountryDestinationISO = "AU",
                            PlaceOfLoading = "COLOMBO AIRPORT",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            TransportAeroPlane = true,
                            DocReferences = "AWB-607-12345678",
                            EntryBIP = "SYDNEY (SYD)",
                            DescCommon = "CHILLED FRESH YELLOWFIN TUNA & SNAPPER",
                            HsCode = "0302.89",
                            Quantity = "2,000 KG",
                            TemperatureChilled = true,
                            NumPackages = "100 BOXES",
                            PackagingType = "STYROFOAM CARTONS",
                            ContainerId = "AU-CONT-5522",
                            ForHumanConsumption = true,
                            HealthCertNo = refNumber,
                            ExportApprovalNumber = "DFAR/FQC/PP/042",
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Official Inspector",
                            SignatureDate = DateTime.UtcNow,
                            Stamp = OfficialStampSvg,
                            Signature = OfficialSignatureSvg,
                            CertificateType = "single"
                        };

                        au.Products.Add(new AuCertificateProduct { SpeciesScientificName = "Thunnus albacares", NatureOfCommodity = "Wild Caught", TreatmentType = "Chilled Fresh", ApprovalNumberOfEstablishments = "DFAR/FQC/PP/042", ManufacturingPlant = "OCEANIC EXPORTS PLANT", NumberOfPackages = 60, NetWeight = 1200 });
                        au.Products.Add(new AuCertificateProduct { SpeciesScientificName = "Lutjanus campechanus", NatureOfCommodity = "Wild Caught", TreatmentType = "Chilled Fresh", ApprovalNumberOfEstablishments = "DFAR/FQC/PP/042", ManufacturingPlant = "OCEANIC EXPORTS PLANT", NumberOfPackages = 40, NetWeight = 800 });
                        context.AuCertificates.Add(au);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "CA":
                    if (!await context.CaCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var ca = new CaCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            MyRef = refNumber,
                            YourRef = "PO-CA-1029",
                            Date = DateTime.UtcNow,
                            CertificateNumber = refNumber,
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CertifyingBody = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginISO = "LK",
                            CountryOfDestination = "CANADA",
                            CountryOfDestinationISO = "CA",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "CANADIAN NORTH SEAFOOD INC",
                            ConsigneeAddress = "75 FRONT STREET EAST, TORONTO, ON M5E 1V9, CANADA",
                            PlaceOfLoading = "BANDARANAIKE INTERNATIONAL AIRPORT",
                            TransportAeroPlane = true,
                            DespatchFrom = "COLOMBO, SRI LANKA",
                            DespatchTo = "TORONTO (YYZ), CANADA",
                            ItemName = "CHILLED YELLOWFIN TUNA & REEF FISH",
                            NumberOfPackages = "100 BOXES",
                            NetWeight = "2000 KG",
                            TotalQuantity = "2,000 KG",
                            TotalNumberOfPackages = "100 BOXES",
                            ProcessingPlantName = "OCEANIC EXPORTS PLANT",
                            ProcessingPlantAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            CompetentAuthorityRegNo = "DFAR/FQC/PP/042",
                            ApprovalNumberOfEstablishments = "DFAR/FQC/PP/042",
                            PointsOfEntry = "TORONTO (YYZ)",
                            ConditionsOfStorage = "CHILLED (0°C TO 4°C)",
                            SealNumber = "SEAL-CA-8899",
                            DescriptionOfCommodity = "FRESH CHILLED YELLOWFIN TUNA LOINS",
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Designation = "Authorized Fish Inspection Veterinarian",
                            Qualification = "B.V.Sc., M.Sc.",
                            CompanyRegistrationNo = "DFAR/EXP/2026/042",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            CertificateType = "attachment"
                        };

                        ca.ProductsAttachment.Add(new CaCertificateProductAttachment { Product = "CHILLED YELLOWFIN TUNA LOINS", LotIdentifier = "LOT-CA-01", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "1200", NumberOfBoxes = 60 });
                        ca.ProductsAttachment.Add(new CaCertificateProductAttachment { Product = "CHILLED GROUPER FILLETS", LotIdentifier = "LOT-CA-02", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "800", NumberOfBoxes = 40 });
                        context.CaCertificates.Add(ca);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "RU":
                    if (!await context.RuCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var ru = new RuCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateNo = refNumber,
                            ConsignorNameAddress = "OCEANIC EXPORTS (PVT) LTD, NO. 124, HARBOUR ROAD, MUTWAL, COLOMBO 15, SRI LANKA",
                            ConsigneeNameAddress = "RUSSIAN SEAFOOD IMPORTS LLC, LENINGRADSKY PROSPEKT 39, MOSCOW, RUSSIA",
                            MeansOfTransport = "AEROFLOT FLIGHT SU 332",
                            CountryOfOrigin = "SRI LANKA",
                            CountryIssuing = "SRI LANKA",
                            CompetentAuthorityExporting = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            OrganizationIssuing = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            PointOfCrossingBorder = "MOSCOW SHEREMETYEVO (SVO)",
                            ProductName = "CHILLED WHOLE ROUND TUNA & SWORDFISH",
                            ProductionDate = DateTime.UtcNow.AddDays(-2),
                            TypeOfPackage = "STYROFOAM CARTONS",
                            NumberOfPackages = "120 BOXES",
                            NetWeight = "2400.00 KG",
                            NumberOfSeal = "SEAL-RU-5544",
                            IdentificationMarks = "DFAR-LK-EXP",
                            StorageConditions = "CHILLED (0°C TO 4°C)",
                            EstablishmentNameAddressRegNo = "OCEANIC EXPORTS FISH PROCESSING FACILITY, MUTWAL, COLOMBO 15 (REG: DFAR/FQC/PP/042)",
                            PlaceOfIssue = "COLOMBO",
                            DateOfIssue = DateTime.UtcNow,
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian",
                            CertificateType = "full"
                        };

                        ru.Attachments.Add(new RuAttachment { Product = "CHILLED YELLOWFIN TUNA (Thunnus albacares)", NumberOfKgs = 1400, NumberOfBoxes = 70 });
                        ru.Attachments.Add(new RuAttachment { Product = "CHILLED SWORDFISH (Xiphias gladius)", NumberOfKgs = 1000, NumberOfBoxes = 50 });
                        context.RuCertificates.Add(ru);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "JP":
                    if (!await context.JpCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var jp = new JpCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            MyRef = refNumber,
                            YourRef = "PO-JP-8821",
                            Date = DateTime.UtcNow,
                            ItemName = "SASHIMI GRADE FRESH BIGEYE & YELLOWFIN TUNA",
                            NumberOfPackages = "90 BOXES",
                            NetWeight = "1800 KG",
                            ProcessingPlantName = "OCEANIC EXPORTS SASHIMI PROCESSING FACILITY",
                            ProcessingPlantAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            CompetentAuthorityRegNo = "DFAR/FQC/PP/042",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "TOKYO TSUKIJI TUNA TRADERS CO., LTD",
                            ConsigneeAddress = "6-20-5 TSUKIJI, CHUO-KU, TOKYO 104-0045, JAPAN",
                            DespatchFrom = "COLOMBO (CMB)",
                            DespatchTo = "TOKYO NARITA (NRT)",
                            DespatchByShip = "AIR FREIGHT JL 750",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer",
                            CertificateType = "vibrio"
                        };
                        context.JpCertificates.Add(jp);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "MV":
                    if (!await context.MvCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var mv = new MvCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            ConsignorExporter = "OCEANIC EXPORTS (PVT) LTD, COLOMBO, SRI LANKA",
                            CertificateNumber = refNumber,
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CertifyingBody = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ConsigneeImporter = "MIFCO SEAFOOD DISTRIBUTION, BODUTHAKURUFAANU MAGU, MALE, MALDIVES",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginISO = "LK",
                            CountryOfDestination = "MALDIVES",
                            CountryOfDestinationISO = "MV",
                            PlaceOfLoading = "COLOMBO AIRPORT",
                            TransportAeroPlane = true,
                            PointsOfEntry = "VELANA INTERNATIONAL AIRPORT (MLE)",
                            ConditionsOfStorage = "CHILLED (0°C TO 4°C)",
                            TotalQuantity = "1,500 KG",
                            SealNumber = "SEAL-MV-3322",
                            TotalNumberOfPackages = "75 BOXES",
                            ApprovalNumberOfEstablishments = "DFAR/FQC/PP/042",
                            DescriptionOfCommodity = "FRESH CHILLED REEF FISH & CRAB",
                            CertifyingOfficerName = "Dr. N. Fernando",
                            CertifyingOfficerDate = DateTime.UtcNow,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian",
                            CompanyRegistrationNo = "DFAR/EXP/2026/042",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            CertificateType = "generic"
                        };

                        mv.Products.Add(new MvCertificateProduct { NatureOfCommodity = "Chilled Reef Fish", Species = "Lutjanus malabaricus", PurposeOfUse = "Human Consumption", No = "1" });
                        mv.ProductsSecond.Add(new MvCertificateProductSecond { No = "1", NameOfTheProduct = "Malabar Red Snapper", LotIdentifier = "LOT-MV-01", TypeOfPackaging = "Styrofoam Box", NumberOfPackages = 75, NetWeight = "1500 KG" });
                        mv.ProductsAttachment.Add(new MvCertificateProductAttachment { Product = "Malabar Red Snapper", LotIdentifier = "LOT-MV-01", TypeOfPackaging = "Styrofoam Carton", NumberOfKgs = "1500", NumberOfBoxes = 75 });
                        context.MvCertificates.Add(mv);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "MY":
                    if (!await context.MyCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var my = new MyCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            ExporterName = "OCEANIC EXPORTS (PVT) LTD, NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            CertificateReferenceNo = refNumber,
                            QualityCertificateNo = $"QC-{refNumber}",
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            LocalAuthority = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ImporterDetails = "MALAYSIA AQUATIC COMMODITIES SDN BHD, PORT KLANG, SELANGOR, MALAYSIA",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginIso = "LK",
                            CountryOfDestination = "MALAYSIA",
                            CountryOfDestinationIso = "MY",
                            ProcessingEstablishment = "OCEANIC EXPORTS FISH PROCESSING FACILITY, MUTWAL, COLOMBO 15",
                            AuthorizationNo = "DFAR/FQC/PP/042",
                            PlaceOfLoading = "COLOMBO AIRPORT",
                            TransportAir = true,
                            PortOfEntry = "KUALA LUMPUR (KUL)",
                            TransportCompany = "MALAYSIA AIRLINES MH 178",
                            ConditionChilled = true,
                            ContainerSealIdentification = "SEAL-MY-9911",
                            InvoiceNo = "INV-MY-2026-001",
                            TransitCountry = "NONE",
                            DepartureDate = DateTime.UtcNow.AddDays(2),
                            CertifyingOfficialDate = DateTime.UtcNow,
                            CertificateReferenceNoPage2 = refNumber,
                            ProductBrand = "OCEANIC FRESH",
                            OriginFisheries = true,
                            CertifiedProductFor = "Human Consumption",
                            TreatmentType = "Chilled Fresh",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer",
                            CertificateType = "health"
                        };

                        my.Products.Add(new MyCertificateProduct { HsCode = "0302.89", Description = "Chilled Yellowfin Tuna", ScientificName = "Thunnus albacares", BatchCode = "BATCH-MY-01", NumberOfPackages = 60, NetWeight = 1200 });
                        my.Products.Add(new MyCertificateProduct { HsCode = "0302.89", Description = "Chilled Grouper", ScientificName = "Epinephelus malabaricus", BatchCode = "BATCH-MY-02", NumberOfPackages = 40, NetWeight = 800 });
                        context.MyCertificates.Add(my);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "AM":
                    if (!await context.AmCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var am = new AmCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            CertRefNumber = refNumber,
                            CertRefNumberA = refNumber,
                            CentralCompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            LocalCompetentAuthority = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ConsigneeName = "ARMENIAN SEAFOOD IMPORTS CJSC",
                            ConsigneeAddress = "KOMITAS AVENUE 49, YEREVAN 0014, ARMENIA",
                            CountryOrigin = "SRI LANKA",
                            CountryOriginISO = "LK",
                            CountryDestination = "ARMENIA",
                            CountryDestinationISO = "AM",
                            PlaceOfLoading = "COLOMBO AIRPORT",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            TransportAeroPlane = true,
                            DescCommon = "FROZEN TUNA & REEF FISH FILLETS",
                            HsCode = "0303.42",
                            Quantity = "1,800 KG",
                            TemperatureFrozen = true,
                            NumPackages = "90 BOXES",
                            PackagingType = "MASTER CARTONS",
                            ContainerId = "CONT-AM-1122",
                            ForHumanConsumption = true,
                            HealthCertNo = refNumber,
                            ProductName = "FROZEN YELLOWFIN TUNA FILLETS",
                            ProductionDate = DateTime.UtcNow.AddDays(-5),
                            NetWeight = "1800 KG",
                            NumberOfSeal = "SEAL-AM-6677",
                            PlaceOfIssue = "COLOMBO",
                            DateOfIssue = DateTime.UtcNow,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian",
                            SignatureDate = DateTime.UtcNow,
                            Stamp = OfficialStampSvg,
                            Signature = OfficialSignatureSvg
                        };

                        am.Attachments.Add(new AmAttachment { Product = "FROZEN YELLOWFIN TUNA FILLETS (Thunnus albacares)", NumberOfKgs = 1000, NumberOfBoxes = 50 });
                        am.Attachments.Add(new AmAttachment { Product = "FROZEN SNAPPER FILLETS (Lutjanus argentimaculatus)", NumberOfKgs = 800, NumberOfBoxes = 40 });
                        context.AmCertificates.Add(am);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "BR":
                    if (!await context.BrCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var br = new BrCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            RefNumber = refNumber,
                            CertificateNo = refNumber,
                            CountryOfExport = "SRI LANKA",
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            LocalCompetentAuthority = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ExporterName = "OCEANIC EXPORTS (PVT) LTD",
                            ExporterAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ImporterName = "BRASIL SEAFOOD IMPORTADORA LTDA",
                            ImporterAddress = "AVENIDA PAULISTA 1000, SAO PAULO, SP, BRAZIL",
                            CountryOrigin = "SRI LANKA",
                            CountryOriginISO = "LK",
                            CountryOfDestination = "BRAZIL",
                            CountryDestinationISO = "BR",
                            PlaceOfLoading = "COLOMBO PORT",
                            TransportShip = true,
                            DeclaredPointOfEntry = "SANTOS PORT (BRSSZ)",
                            ConditionsForTransportStorage = "FROZEN (-18°C)",
                            IdentificationOfContainers = "BR-CONT-4411",
                            ProducerDetails = "OCEANIC EXPORTS PLANT, MUTWAL, COLOMBO 15",
                            HsCode = "0303.42",
                            IntendedPurpose = "Human Consumption",
                            TotalNetWeight = 2200,
                            PlaceAndDate = "COLOMBO, SRI LANKA",
                            DateOfIssue = DateTime.UtcNow,
                            OfficialStamp = OfficialStampSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer"
                        };

                        br.Products.Add(new BrCertificateProduct { NameOfTheProduct = "Frozen Yellowfin Tuna Loins", ScientificName = "Thunnus albacares", TypeOfPackaging = "Vacuum Pack Cartons", NumberOfPackages = 60, NetWeight = 1200 });
                        br.Products.Add(new BrCertificateProduct { NameOfTheProduct = "Frozen Swordfish Steaks", ScientificName = "Xiphias gladius", TypeOfPackaging = "Vacuum Pack Cartons", NumberOfPackages = 50, NetWeight = 1000 });
                        context.BrCertificates.Add(br);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "IND":
                    if (!await context.IndCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var ind = new IndCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateType = "import_fish",
                            CertificateNumber = refNumber,
                            MyRef = refNumber,
                            CountryOfDispatch = "SRI LANKA",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginIso = "LK",
                            CountryOfDestination = "INDIA",
                            CountryOfDestinationIso = "IN",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsignorTel = "+94 11 243 5678",
                            ConsigneeName = "INDIA SEAFOOD IMPORTERS PVT LTD",
                            ConsigneeAddress = "WILLINGDON ISLAND, KOCHI, KERALA 682003, INDIA",
                            ConsigneeTel = "+91 484 266 8800",
                            CompetentAuthorityDetails = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES, SRI LANKA",
                            PlaceOfLoading = "COLOMBO HARBOUR / AIRPORT",
                            MeansOfTransport = "AIR CARGO FLIGHT AI 282",
                            DeclaredPointOfEntry = "CHENNAI / KOCHI AIRPORT",
                            ConditionsForTransportStorage = "CHILLED (0°C TO 4°C)",
                            TotalQuantity = "2,000 KG",
                            InvoiceNoDate = "INV-IN-2026-001 DATED TODAY",
                            FoodDescription = "CHILLED REEF FISH & MUD CRABS",
                            IntendedPurpose = "Direct Human Consumption",
                            ProducerNameAddress = "OCEANIC EXPORTS FISH PROCESSING FACILITY, COLOMBO 15",
                            ApprovalNumberDetails = "DFAR/FQC/PP/042",
                            DateOfManufacture = DateTime.UtcNow.AddDays(-2),
                            BestBefore = DateTime.UtcNow.AddDays(14),
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer",
                            AuthorizedOfficialDate = DateTime.UtcNow,
                            AuthorizedOfficialSignature = OfficialSignatureSvg,
                            OfficialStamp = OfficialStampSvg
                        };

                        ind.Products.Add(new IndCertificateProduct { NameOfProduct = "Chilled Mud Crab (Scylla serrata)", LotNo = "LOT-IN-01", TypeOfPackaging = "Ventilated Boxes", NumberOfPackages = 50, NetWeight = 1000 });
                        ind.Products.Add(new IndCertificateProduct { NameOfProduct = "Chilled Grouper (Epinephelus coioides)", LotNo = "LOT-IN-02", TypeOfPackaging = "Styrofoam Boxes", NumberOfPackages = 50, NetWeight = 1000 });
                        context.IndCertificates.Add(ind);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "ID":
                    if (!await context.IdCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var id = new IdCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            NumberNomor = refNumber,
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "PT INDONESIA SEAFOOD NUSANTARA",
                            ConsigneeAddress = "JALAN MUARA BARU NO. 12, JAKARTA UTARA 14440, INDONESIA",
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            EstablishmentProcessing = true,
                            EstablishmentName = "OCEANIC EXPORTS PLANT",
                            EstablishmentRegNo = "DFAR/FQC/PP/042",
                            EstablishmentAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            CountryRegionOrigin = "SRI LANKA",
                            SourceWildCaught = true,
                            PortOfShipment = "COLOMBO (CMB)",
                            TransportAir = true,
                            CommodityDescription = "FRESH CHILLED FISH AND CRUSTACEANS",
                            TempChilled = true,
                            IntendedHumanConsumption = true,
                            TotalPackages = "100 BOXES",
                            PackagingType = "STYROFOAM CARTONS",
                            TotalQuantityKg = "2000",
                            ContainerSealNumber = "SEAL-ID-9933",
                            PortOfDestination = "SOEKARNO-HATTA AIRPORT, JAKARTA",
                            TransportVesselName = "GARUDA INDONESIA GA 892",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            TestingLaboratory = "NATIONAL AQUATIC RESOURCES RESEARCH AGENCY (NARA)",
                            LaboratoryAddress = "CROWH ISLAND, MATTAKKULIYA, COLOMBO 15",
                            ApprovingOfficerName = "Dr. N. Fernando",
                            TestResultNumber = "TR-DFAR-2026-098",
                            AttestFisheryProducts = true,
                            AttestClauseA = true,
                            AttestClauseB = true,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer",
                            CertifiedIssuedAt = "COLOMBO",
                            CertifiedDate = DateTime.UtcNow,
                            CertifiedPosition = "Head of Fish Inspection Division",
                            CertifiedPhone = "+94 11 243 5678",
                            CertifiedEmail = "info@fisheries.gov.lk",
                            CertifiedAddress = "NEW SECRETARIAT, MALIGAWATTA, COLOMBO 10, SRI LANKA"
                        };

                        id.Products.Add(new IdCertificateProduct { No = "1", CommonName = "Yellowfin Tuna", ScientificName = "Thunnus albacares", HsCode = "0302.89", Quantity = 1200, Unit = "KG" });
                        id.Products.Add(new IdCertificateProduct { No = "2", CommonName = "Malabar Grouper", ScientificName = "Epinephelus malabaricus", HsCode = "0302.89", Quantity = 800, Unit = "KG" });
                        context.IdCertificates.Add(id);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "IL":
                    if (!await context.IlCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var il = new IlCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateType = "attachment",
                            CertificationNo = refNumber,
                            CentralCompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CentralCompetentAuthorityEmail = "fishinspection@fisheries.gov.lk",
                            LocalCompetentAuthority = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            CountryOfOrigin = "SRI LANKA",
                            PlaceOfOriginName = "OCEANIC EXPORTS FISH PROCESSING FACILITY",
                            PlaceOfOriginAddress = "MUTWAL FISHERY HARBOUR COMPLEX, COLOMBO 15",
                            PlaceOfOriginApprovalNo = "DFAR/FQC/PP/042",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            PostalCodeConsignor = "01500",
                            TelNoConsignor = "+94 11 243 5678",
                            EmailConsignor = "exports@oceanic.lk",
                            ConsigneeName = "ISRAEL FISH & SEAFOOD IMPORTERS LTD",
                            ConsigneeAddress = "PORT OF ASHDOD LOGISTICS CENTER, ASHDOD 77100, ISRAEL",
                            PostalCodeConsignee = "77100",
                            TelNoConsignee = "+972 8 851 8111",
                            EmailConsignee = "orders@israelseafood.co.il",
                            PlaceOfLoading = "COLOMBO AIRPORT",
                            PortOfEntry = "BEN GURION AIRPORT (TLV)",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            TransportAir = true,
                            ContainerNo = "CONT-IL-5544",
                            SealNo = "SEAL-IL-1122",
                            NonReadyToEat = "YES - REQUIRES COOKING",
                            PlaceOfIssue = "COLOMBO",
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian",
                            SignatureDate = DateTime.UtcNow,
                            Stamp = OfficialStampSvg,
                            Signature = OfficialSignatureSvg,
                            SignatoryUserId = adminUserId
                        };

                        il.Products.Add(new IlCertificateProduct { DescriptionOfCommodity = "Chilled Yellowfin Tuna Loins", SpeciesScientificName = "Thunnus albacares", NatureOfCommodity = "Wild Caught", TreatmentType = "Chilled Fresh", ApprovalNo = "DFAR/FQC/PP/042", NumberOfPackages = 60, NetWeight = 1200, ProductionDate = DateTime.UtcNow.AddDays(-2), BestBefore = DateTime.UtcNow.AddDays(14), LotNo = "LOT-IL-01" });
                        il.Products.Add(new IlCertificateProduct { DescriptionOfCommodity = "Chilled Grouper Fillets", SpeciesScientificName = "Epinephelus malabaricus", NatureOfCommodity = "Wild Caught", TreatmentType = "Chilled Fresh", ApprovalNo = "DFAR/FQC/PP/042", NumberOfPackages = 40, NetWeight = 800, ProductionDate = DateTime.UtcNow.AddDays(-2), BestBefore = DateTime.UtcNow.AddDays(14), LotNo = "LOT-IL-02" });
                        context.IlCertificates.Add(il);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "KZ":
                    if (!await context.KzCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var kz = new KzCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateNo = refNumber,
                            ConsignorNameAddress = "OCEANIC EXPORTS (PVT) LTD, NO. 124, HARBOUR ROAD, MUTWAL, COLOMBO 15, SRI LANKA",
                            ConsigneeNameAddress = "KAZAKHSTAN SEAFOOD TRADE LLP, ALMATY LOGISTICS PARK, ALMATY 050000, KAZAKHSTAN",
                            MeansOfTransport = "AIR ASTANA KC 882",
                            CountryOfOrigin = "SRI LANKA",
                            CountryIssuing = "SRI LANKA",
                            CompetentAuthorityExporting = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            OrganizationIssuing = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            PointOfCrossingBorder = "ALMATY INTERNATIONAL AIRPORT (ALA)",
                            ProductName = "CHILLED WHOLE ROUND TUNA & GROUPER",
                            ProductionDate = DateTime.UtcNow.AddDays(-2),
                            TypeOfPackage = "STYROFOAM CARTONS",
                            NumberOfPackages = "100 BOXES",
                            NetWeight = "2000.00 KG",
                            NumberOfSeal = "SEAL-KZ-7722",
                            IdentificationMarks = "DFAR-LK-EXP",
                            StorageConditions = "CHILLED (0°C TO 4°C)",
                            EstablishmentNameAddressRegNo = "OCEANIC EXPORTS FISH PROCESSING FACILITY, MUTWAL, COLOMBO 15 (REG: DFAR/FQC/PP/042)",
                            PlaceOfIssue = "COLOMBO",
                            DateOfIssue = DateTime.UtcNow,
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian",
                            CertificateType = "full"
                        };

                        kz.Attachments.Add(new KzAttachment { Product = "CHILLED YELLOWFIN TUNA (Thunnus albacares)", NumberOfKgs = 1200, NumberOfBoxes = 60 });
                        kz.Attachments.Add(new KzAttachment { Product = "CHILLED SNAPPER (Lutjanus argentimaculatus)", NumberOfKgs = 800, NumberOfBoxes = 40 });
                        context.KzCertificates.Add(kz);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "KW":
                    if (!await context.KwCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var kw = new KwCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateReferenceNo = refNumber,
                            PlaceOfIssue = "COLOMBO",
                            DateOfIssue = DateTime.UtcNow,
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "KUWAIT SEAFOOD WHOLESALERS WLL",
                            ConsigneeAddress = "SHUWAIKH INDUSTRIAL AREA, BLOCK 1, KUWAIT CITY, KUWAIT",
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CompetentAuthorityAddress = "NEW SECRETARIAT, MALIGAWATTA, COLOMBO 10, SRI LANKA",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginIso = "LK",
                            CountryOfDestination = "KUWAIT",
                            CountryOfDestinationIso = "KW",
                            ProducerName = "OCEANIC EXPORTS PLANT",
                            ProducerAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            PackingEstName = "OCEANIC EXPORTS PACKING FACILITY",
                            PackingEstAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            PackingEstApprovalNo = "DFAR/FQC/PP/042",
                            BorderOfEntry = "KUWAIT INTERNATIONAL AIRPORT (KWI)",
                            BorderLoadingCountry = "SRI LANKA",
                            BorderLoadingPlace = "COLOMBO (CMB)",
                            TransportByAir = true,
                            VehicleIdentificationNo = "KUWAIT AIRWAYS KU 362",
                            TempChilled = true,
                            CommoditiesHumanConsumption = true,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            SignatureDate = DateTime.UtcNow,
                            CertificateType = "single"
                        };

                        kw.Products.Add(new KwCertificateProduct { NameDescription = "Chilled Yellowfin Tuna", HsCodes = "0302.89", TreatmentDerivedFrom = "Chilled Fresh", BrandName = "Oceanic", ProductionDate = DateTime.UtcNow.AddDays(-2), ExpiryDate = DateTime.UtcNow.AddDays(14), NumberPackages = 50, BatchLotNo = "LOT-KW-01", TotalWeight = 1000 });
                        kw.Products.Add(new KwCertificateProduct { NameDescription = "Chilled Pomfret", HsCodes = "0302.89", TreatmentDerivedFrom = "Chilled Fresh", BrandName = "Oceanic", ProductionDate = DateTime.UtcNow.AddDays(-2), ExpiryDate = DateTime.UtcNow.AddDays(14), NumberPackages = 50, BatchLotNo = "LOT-KW-02", TotalWeight = 1000 });
                        context.KwCertificates.Add(kw);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "NZ":
                    if (!await context.NzCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var nz = new NzCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateRefNumber = refNumber,
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "NEW ZEALAND PACIFIC SEAFOOD LTD",
                            ConsigneeAddress = "AUCKLAND HARBOUR DOCKS, AUCKLAND 1010, NEW ZEALAND",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfDestination = "NEW ZEALAND",
                            ProcessorName = "OCEANIC EXPORTS FISH PROCESSING FACILITY",
                            ProcessorAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            ProcessorEstablishmentNumber = "DFAR/FQC/PP/042",
                            PortDispatchedFrom = "COLOMBO AIRPORT (CMB)",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            MeansOfTransport = "AIR NEW ZEALAND NZ 882",
                            TransportAeroplan = true,
                            TemperatureOfCommodities = "CHILLED (0°C TO 4°C)",
                            ContainerNumber = "CONT-NZ-2299",
                            OfficialSealNumber = "SEAL-NZ-8811",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer",
                            SignatureDate = DateTime.UtcNow,
                            CertificateType = "fish"
                        };

                        nz.Products.Add(new NzCertificateProduct { ProductName = "Chilled Tuna Loins", AquaticAnimalSpecies = "Thunnus albacares", ProductionDate = DateTime.UtcNow.AddDays(-2), NumberOfPackages = 60, NetWeightKg = 1200, HsCode = "0302.89" });
                        nz.Products.Add(new NzCertificateProduct { ProductName = "Chilled Snapper", AquaticAnimalSpecies = "Lutjanus malabaricus", ProductionDate = DateTime.UtcNow.AddDays(-2), NumberOfPackages = 40, NetWeightKg = 800, HsCode = "0302.89" });
                        context.NzCertificates.Add(nz);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "SA":
                    if (!await context.SaCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var sa = new SaCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            MyRef = refNumber,
                            YourRef = "PO-SA-7733",
                            Date = DateTime.UtcNow,
                            CertificateNumber = refNumber,
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CertifyingBody = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "SAUDI FISHERIES IMPORT COMPANY (SFICO)",
                            ConsigneeAddress = "KING ABDULAZIZ SEAPORT, DAMMAM 31411, SAUDI ARABIA",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginISO = "LK",
                            CountryOfDestination = "SAUDI ARABIA",
                            CountryOfDestinationISO = "SA",
                            PlaceOfLoading = "COLOMBO AIRPORT",
                            TransportAeroPlane = true,
                            DespatchFrom = "COLOMBO (CMB)",
                            DespatchTo = "RIYADH (RUH), SAUDI ARABIA",
                            ItemName = "FRESH CHILLED YELLOWFIN TUNA & GROUPER",
                            NumberOfPackages = "100 BOXES",
                            NetWeight = "2000 KG",
                            TotalQuantity = "2,000 KG",
                            TotalNumberOfPackages = "100 BOXES",
                            ProcessingPlantName = "OCEANIC EXPORTS PLANT",
                            ProcessingPlantAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            CompetentAuthorityRegNo = "DFAR/FQC/PP/042",
                            ApprovalNumberOfEstablishments = "DFAR/FQC/PP/042",
                            PointsOfEntry = "KING KHALID AIRPORT (RUH)",
                            ConditionsOfStorage = "CHILLED (0°C TO 4°C)",
                            SealNumber = "SEAL-SA-4499",
                            DescriptionOfCommodity = "FRESH CHILLED WHOLE AND FILLETS",
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Designation = "Authorized Fish Inspection Veterinarian",
                            Qualification = "B.V.Sc., M.Sc.",
                            CompanyRegistrationNo = "DFAR/EXP/2026/042",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            CertificateType = "letter"
                        };

                        sa.ProductsAttachment.Add(new SaCertificateProductAttachment { Product = "CHILLED YELLOWFIN TUNA", LotIdentifier = "LOT-SA-01", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "1200", NumberOfBoxes = 60 });
                        sa.ProductsAttachment.Add(new SaCertificateProductAttachment { Product = "CHILLED GROUPER", LotIdentifier = "LOT-SA-02", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "800", NumberOfBoxes = 40 });
                        context.SaCertificates.Add(sa);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "ZA":
                    if (!await context.ZaCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var za = new ZaCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            MyRef = refNumber,
                            YourRef = "PO-ZA-5511",
                            Date = DateTime.UtcNow,
                            CertificateNumber = refNumber,
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CertifyingBody = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "SOUTH AFRICA CAPE SEAFOOD IMPORTS PTY LTD",
                            ConsigneeAddress = "CAPE TOWN HARBOUR PRECINCT, CAPE TOWN 8001, SOUTH AFRICA",
                            CountryOfOrigin = "SRI LANKA",
                            CountryOfOriginISO = "LK",
                            CountryOfDestination = "SOUTH AFRICA",
                            CountryOfDestinationISO = "ZA",
                            PlaceOfLoading = "COLOMBO AIRPORT",
                            TransportAeroPlane = true,
                            DespatchFrom = "COLOMBO (CMB)",
                            DespatchTo = "CAPE TOWN (CPT), SOUTH AFRICA",
                            ItemName = "CHILLED YELLOWFIN TUNA & SWORDFISH",
                            NumberOfPackages = "100 BOXES",
                            NetWeight = "2000 KG",
                            TotalQuantity = "2,000 KG",
                            TotalNumberOfPackages = "100 BOXES",
                            ProcessingPlantName = "OCEANIC EXPORTS PLANT",
                            ProcessingPlantAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            CompetentAuthorityRegNo = "DFAR/FQC/PP/042",
                            ApprovalNumberOfEstablishments = "DFAR/FQC/PP/042",
                            PointsOfEntry = "CAPE TOWN INTERNATIONAL AIRPORT (CPT)",
                            ConditionsOfStorage = "CHILLED (0°C TO 4°C)",
                            SealNumber = "SEAL-ZA-2233",
                            DescriptionOfCommodity = "FRESH CHILLED YELLOWFIN TUNA LOINS",
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Designation = "Authorized Fish Inspection Veterinarian",
                            Qualification = "B.V.Sc., M.Sc.",
                            CompanyRegistrationNo = "DFAR/EXP/2026/042",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            CertificateType = "letter"
                        };

                        za.ProductsAttachment.Add(new ZaCertificateProductAttachment { Product = "CHILLED YELLOWFIN TUNA LOINS", LotIdentifier = "LOT-ZA-01", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "1200", NumberOfBoxes = 60 });
                        za.ProductsAttachment.Add(new ZaCertificateProductAttachment { Product = "CHILLED SWORDFISH LOINS", LotIdentifier = "LOT-ZA-02", TypeOfPackaging = "STYROFOAM BOX", NumberOfKgs = "800", NumberOfBoxes = 40 });
                        context.ZaCertificates.Add(za);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "TW":
                    if (!await context.TwCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var tw = new TwCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            ReferenceNo = refNumber,
                            CountryOfExport = "SRI LANKA",
                            CountryOfProduction = "SRI LANKA",
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            DepartmentIssuance = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ProductionPlace = "COLOMBO, SRI LANKA",
                            ProcessingType = "Chilled Whole Round",
                            ProductionMode = "Wild Caught",
                            WildCaughtYes = true,
                            CatchArea = "FAO 51 - Indian Ocean",
                            EnterpriseName = "OCEANIC EXPORTS FISH PROCESSING FACILITY",
                            EnterpriseRegistrationNo = "DFAR/FQC/PP/042",
                            ProductionDate = DateTime.UtcNow.AddDays(-2),
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsigneeName = "TAIWAN FISHERY & SEAFOOD CORP",
                            ConsigneeAddress = "QIANZHEN FISHERY HARBOUR, KAOHSIUNG 806, TAIWAN",
                            PlaceOfDispatch = "COLOMBO",
                            PlaceOfDestination = "KAOHSIUNG / TAIPEI (TPE)",
                            MeansOfTransport = "CHINA AIRLINES CI 992",
                            ContainerNumber = "CONT-TW-6677",
                            SealNumber = "SEAL-TW-1199",
                            PlaceOfIssue = "COLOMBO",
                            DateOfIssue = DateTime.UtcNow,
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer",
                            CertificateType = "single"
                        };

                        tw.Products.Add(new TwCertificateProduct { CommodityName = "Chilled Yellowfin Tuna", HsCode = "0302.89", ScientificName = "Thunnus albacares", NumberOfPackages = 60, NetWeight = 1200 });
                        tw.Products.Add(new TwCertificateProduct { CommodityName = "Chilled Malabar Grouper", HsCode = "0302.89", ScientificName = "Epinephelus malabaricus", NumberOfPackages = 40, NetWeight = 800 });
                        context.TwCertificates.Add(tw);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "UA":
                    if (!await context.UaCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var ua = new UaCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            CertificateReferenceNumber = refNumber,
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA",
                            ConsignorPostalCode = "01500",
                            ConsignorTelNo = "+94 11 243 5678",
                            CentralCompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            LocalCompetentAuthority = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ConsigneeName = "UKRAINE SEAFOOD TRADING LLC",
                            ConsigneeAddress = "KHRESHCHATYK STREET 22, KYIV 01001, UKRAINE",
                            CountryOfOriginName = "SRI LANKA",
                            CountryOfOriginISO = "LK",
                            CountryDestinationName = "UKRAINE",
                            CountryDestinationISO = "UA",
                            PlaceOriginName = "OCEANIC EXPORTS PLANT",
                            PlaceOriginApprovalNumber = "DFAR/FQC/PP/042",
                            PlaceOriginAddress = "MUTWAL FISHERY HARBOUR, COLOMBO 15",
                            PlaceLoadingAddress = "BANDARANAIKE INTERNATIONAL AIRPORT",
                            DateOfDeparture = DateTime.UtcNow.AddDays(2),
                            TransportAeroplane = true,
                            TransportIdentification = "AIR CARGO FLIGHT LK-992",
                            EntryBIPUkraine = "KYIV BORYSPIL (KBP)",
                            DescriptionOfCommodity = "FRESH CHILLED YELLOWFIN TUNA & SNAPPER",
                            CommodityCodeHS = "0302.89",
                            Quantity = "2,000 KG",
                            TemperatureChilled = true,
                            NumberOfPackages = "100 BOXES",
                            TypeOfPackaging = "STYROFOAM CARTONS",
                            CommoditiesHumanConsumption = true,
                            HealthCertificateReferenceNumber = refNumber,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Veterinarian",
                            OfficialStamp = OfficialStampSvg,
                            OfficialSignature = OfficialSignatureSvg,
                            CertifiedDate = DateTime.UtcNow,
                            CertificateType = "attachment"
                        };

                        ua.Products.Add(new UaCertificateProduct { Species = "Thunnus albacares", NatureOfCommodity = "Wild Caught", TreatmentApprovalNumber = "DFAR/FQC/PP/042", ManufacturingPlant = "OCEANIC EXPORTS PLANT", NumberOfPackaging = "60", TypeOfPackaging = "Styrofoam", NetWeight = "1200" });
                        ua.Products.Add(new UaCertificateProduct { Species = "Lutjanus malabaricus", NatureOfCommodity = "Wild Caught", TreatmentApprovalNumber = "DFAR/FQC/PP/042", ManufacturingPlant = "OCEANIC EXPORTS PLANT", NumberOfPackaging = "40", TypeOfPackaging = "Styrofoam", NetWeight = "800" });
                        context.UaCertificates.Add(ua);
                        await context.SaveChangesAsync();
                    }
                    break;

                case "HK":
                    if (!await context.HkCertificates.AnyAsync(c => c.CertificateRequestId == requestId))
                    {
                        var hk = new HkCertificate
                        {
                            CertificateRequestId = requestId,
                            CompanyUserId = companyUserId,
                            CreatedAt = DateTime.UtcNow,
                            IdentificationNumber = refNumber,
                            CertificateType = "attachment",
                            CountryOfDispatch = "SRI LANKA",
                            CompetentAuthority = "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
                            CertifyingBody = "FISH INSPECTION AND QUALITY CONTROL DIVISION",
                            ContainerNumber = "CONT-HK-8877",
                            SealNumber = "SEAL-HK-3322",
                            StorageTemperature = "CHILLED (0°C TO 4°C)",
                            ApprovalNumber = "DFAR/FQC/PP/042",
                            ProcessingEstablishment = "OCEANIC EXPORTS FISH PROCESSING FACILITY, MUTWAL, COLOMBO 15",
                            ProvenanceDetails = "SRI LANKA (FAO 51 - INDIAN OCEAN)",
                            ConsignorName = "OCEANIC EXPORTS (PVT) LTD",
                            ConsignorAddress = "NO. 124, HARBOUR ROAD, MUTWAL, COLOMBO 15, SRI LANKA",
                            PlaceOfDispatch = "COLOMBO (CMB)",
                            DestinationCountryPlace = "HONG KONG INTERNATIONAL AIRPORT (HKG)",
                            MeansOfTransport = "CATHAY PACIFIC CX 610",
                            ConsigneeName = "HONG KONG SEAFOOD WHOLESALE MARKET CORP",
                            ConsigneeAddress = "ABERDEEN FISHERY HARBOUR, HONG KONG",
                            PlaceOfIssue = "COLOMBO",
                            DateOfIssue = DateTime.UtcNow,
                            SignatoryUserId = adminUserId,
                            SignatoryName = "Dr. N. Fernando",
                            Qualification = "Authorized Fish Inspection Officer",
                            OfficialSignature = OfficialSignatureSvg,
                            OfficerTel = "+94 11 243 5678",
                            OfficerEmail = "fishinspection@fisheries.gov.lk"
                        };

                        hk.Products.Add(new HkCertificateProduct { Description = "Chilled Yellowfin Tuna", Species = "Thunnus albacares", ProcessingType = "Chilled Whole", PackagingType = "Styrofoam Boxes", LotCode = "LOT-HK-01", NumberOfPackages = 60, PackagesUnit = "BOXES", NetWeight = 1200, NetWeightUnit = "KG" });
                        hk.Products.Add(new HkCertificateProduct { Description = "Chilled Chinese Pomfret", Species = "Pampus chinensis", ProcessingType = "Chilled Whole", PackagingType = "Styrofoam Boxes", LotCode = "LOT-HK-02", NumberOfPackages = 40, PackagesUnit = "BOXES", NetWeight = 800, NetWeightUnit = "KG" });
                        context.HkCertificates.Add(hk);
                        await context.SaveChangesAsync();
                    }
                    break;
            }
        }
    }
}
