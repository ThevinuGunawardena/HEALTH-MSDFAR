using MEA.Server.DTO.IndCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings
{
    public static class IndCertificateMappings
    {
        public static IndCertificate ToEntity(this CreateIndCertificateDto dto, string companyUserId)
        {
            var entity = new IndCertificate
            {
                CertificateRequestId = dto.CertificateRequestId,
                CompanyUserId = companyUserId,
                CreatedAt = DateTime.UtcNow,

                CertificateType = dto.CertificateType ?? "import_fish",
                CountryOfDispatch = dto.CountryOfDispatch,
                CertificateNumber = dto.CertificateNumber,
                MyRef = dto.MyRef,
                YourRef = dto.YourRef,
                ConsignorName = dto.ConsignorName,
                ConsignorAddress = dto.ConsignorAddress,
                ConsignorTel = dto.ConsignorTel,
                CompetentAuthorityDetails = dto.CompetentAuthorityDetails,
                ConsigneeName = dto.ConsigneeName,
                ConsigneeAddress = dto.ConsigneeAddress,
                ConsigneeTel = dto.ConsigneeTel,
                CountryOfOrigin = dto.CountryOfOrigin,
                CountryOfOriginIso = dto.CountryOfOriginIso,
                CountryOfDestination = dto.CountryOfDestination,
                CountryOfDestinationIso = dto.CountryOfDestinationIso,
                PlaceOfLoading = dto.PlaceOfLoading,
                MeansOfTransport = dto.MeansOfTransport,
                DeclaredPointOfEntry = dto.DeclaredPointOfEntry,
                ConditionsForTransportStorage = dto.ConditionsForTransportStorage,
                TotalQuantity = dto.TotalQuantity,
                InvoiceNoDate = dto.InvoiceNoDate,
                FoodDescription = dto.FoodDescription,
                IntendedPurpose = dto.IntendedPurpose,
                ProducerNameAddress = dto.ProducerNameAddress,
                ApprovalNumberDetails = dto.ApprovalNumberDetails,
                DateOfManufacture = dto.DateOfManufacture,
                BestBefore = dto.BestBefore,
                DateOfExpiry = dto.DateOfExpiry,

                ItemDescription = dto.ItemDescription,
                NumberOfPackagesStr = dto.NumberOfPackagesStr,
                NetWeightStr = dto.NetWeightStr,
                ProcessingPlantNameAddress = dto.ProcessingPlantNameAddress,
                ProcessingPlantRegNo = dto.ProcessingPlantRegNo,
                DispatchFrom = dto.DispatchFrom,
                DispatchTo = dto.DispatchTo,
                ModeOfTransport = dto.ModeOfTransport,
                HsCode = dto.HsCode,
                SpeciesName = dto.SpeciesName,
                PreviousCertRef = dto.PreviousCertRef,
                ConsignmentIdentificationDetails = dto.ConsignmentIdentificationDetails,
                ProductDescription = dto.ProductDescription,

                AttestationPlace = dto.AttestationPlace,
                AttestationDate = dto.AttestationDate,
                SignatoryUserId = dto.SignatoryUserId,
                SignatoryName = dto.SignatoryName,
                Qualification = dto.Qualification,
                AuthorizedOfficialDate = dto.AuthorizedOfficialDate,
                AuthorizedOfficialSignature = dto.AuthorizedOfficialSignature,
                OfficialStamp = dto.OfficialStamp,

                Products = dto.Products.Select(p => new IndCertificateProduct
                {
                    NameOfProduct = p.NameOfProduct,
                    LotNo = p.LotNo,
                    TypeOfPackaging = p.TypeOfPackaging,
                    NumberOfPackages = p.NumberOfPackages,
                    NetWeight = p.NetWeight
                }).ToList()
            };

            return entity;
        }
    }
}

