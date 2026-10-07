using MEA.Server.DTO.KwCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings
{
    public static class KwCertificateMappings
    {
        public static KwCertificate ToEntity(this CreateKwCertificateDto dto, string companyUserId)
        {
            var entity = new KwCertificate
            {
                CertificateRequestId = dto.CertificateRequestId,
                CompanyUserId = companyUserId,
                CreatedAt = DateTime.UtcNow,

                ConsignorName = dto.ConsignorName,
                ConsignorAddress = dto.ConsignorAddress,
                CertificateReferenceNo = dto.CertificateReferenceNo,
                PlaceOfIssue = dto.PlaceOfIssue,
                DateOfIssue = dto.DateOfIssue,
                
                ConsigneeName = dto.ConsigneeName,
                ConsigneeAddress = dto.ConsigneeAddress,
                
                CompetentAuthority = dto.CompetentAuthority,
                CompetentAuthorityAddress = dto.CompetentAuthorityAddress,
                CountryOfOrigin = dto.CountryOfOrigin,
                CountryOfOriginIso = dto.CountryOfOriginIso,
                CountryOfDestination = dto.CountryOfDestination,
                CountryOfDestinationIso = dto.CountryOfDestinationIso,
                
                ProducerName = dto.ProducerName,
                ProducerAddress = dto.ProducerAddress,
                PackingEstName = dto.PackingEstName,
                PackingEstAddress = dto.PackingEstAddress,
                PackingEstApprovalNo = dto.PackingEstApprovalNo,
                
                BorderOfEntry = dto.BorderOfEntry,
                BorderLoadingCountry = dto.BorderLoadingCountry,
                BorderLoadingPlace = dto.BorderLoadingPlace,
                TransportByAir = dto.TransportByAir ?? false,
                TransportBySea = dto.TransportBySea ?? false,
                VehicleIdentificationNo = dto.VehicleIdentificationNo,
                
                TempChilled = dto.TempChilled ?? false,
                TempFrozen = dto.TempFrozen ?? false,
                CommoditiesOther = dto.CommoditiesOther ?? false,
                CommoditiesAfterFurtherProcess = dto.CommoditiesAfterFurtherProcess ?? false,
                CommoditiesHumanConsumption = dto.CommoditiesHumanConsumption ?? false,

                SignatoryUserId = dto.SignatoryUserId,
                SignatoryName = dto.SignatoryName,
                Qualification = dto.Qualification,
                OfficialStamp = dto.OfficialStamp,
                OfficialSignature = dto.OfficialSignature,
                SignatureDate = dto.SignatureDate,
                CertificateType = dto.CertificateType ?? "single"
            };

            if (dto.Products != null && dto.Products.Any())
            {
                foreach (var p in dto.Products)
                {
                    entity.Products.Add(new KwCertificateProduct
                    {
                        NameDescription = p.NameDescription,
                        HsCodes = p.HsCodes,
                        TreatmentDerivedFrom = p.TreatmentDerivedFrom,
                        BrandName = p.BrandName,
                        ProductionDate = p.ProductionDate,
                        ExpiryDate = p.ExpiryDate,
                        NumberPackages = p.NumberPackages ?? 0,
                        BatchLotNo = p.BatchLotNo,
                        TotalWeight = p.TotalWeight ?? 0
                    });
                }
            }

            return entity;
        }
    }
}

