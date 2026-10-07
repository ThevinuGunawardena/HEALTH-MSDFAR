using MEA.Server.DTO.MyCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings
{
    public static class MyCertificateMappings
    {
        public static MyCertificate ToEntity(this CreateMyCertificateDto dto, string companyUserId)
        {
            var entity = new MyCertificate
            {
                CertificateRequestId = dto.CertificateRequestId,
                CompanyUserId = companyUserId,
                CreatedAt = DateTime.UtcNow,

                ExporterName = dto.ExporterName,
                CertificateReferenceNo = dto.CertificateReferenceNo,
                QualityCertificateNo = dto.QualityCertificateNo,
                
                CompetentAuthority = dto.CompetentAuthority,
                LocalAuthority = dto.LocalAuthority,
                ImporterDetails = dto.ImporterDetails,
                
                CountryOfOrigin = dto.CountryOfOrigin,
                CountryOfOriginIso = dto.CountryOfOriginIso,
                CountryOfDestination = dto.CountryOfDestination,
                CountryOfDestinationIso = dto.CountryOfDestinationIso,
                
                ProcessingEstablishment = dto.ProcessingEstablishment,
                AuthorizationNo = dto.AuthorizationNo,
                PlaceOfLoading = dto.PlaceOfLoading,
                
                TransportAir = dto.TransportAir ?? false,
                TransportShip = dto.TransportShip ?? false,
                TransportRail = dto.TransportRail ?? false,
                TransportRoad = dto.TransportRoad ?? false,
                TransportOther = dto.TransportOther ?? false,
                
                PortOfEntry = dto.PortOfEntry,
                TransportCompany = dto.TransportCompany,
                
                ConditionAmbient = dto.ConditionAmbient ?? false,
                ConditionChilled = dto.ConditionChilled ?? false,
                ConditionFrozen = dto.ConditionFrozen ?? false,
                
                ContainerSealIdentification = dto.ContainerSealIdentification,
                InvoiceNo = dto.InvoiceNo,
                TransitCountry = dto.TransitCountry,
                DepartureDate = dto.DepartureDate,
                CertifyingOfficialDate = dto.CertifyingOfficialDate,

                CertificateReferenceNoPage2 = dto.CertificateReferenceNoPage2,
                ProductBrand = dto.ProductBrand,
                OriginFisheries = dto.OriginFisheries ?? false,
                OriginAquaculture = dto.OriginAquaculture ?? false,
                CertifiedProductFor = dto.CertifiedProductFor,
                TreatmentType = dto.TreatmentType,

                CertificateReferenceNoPage3 = dto.CertificateReferenceNoPage3,
                AdditionalInformation = dto.AdditionalInformation,

                OfficialStamp = dto.OfficialStamp,
                OfficialSignature = dto.OfficialSignature,
                SignatoryUserId = dto.SignatoryUserId,
                SignatoryName = dto.SignatoryName,
                Qualification = dto.Qualification,
                CertificateType = dto.CertificateType ?? "single"
            };

            if (dto.Products != null && dto.Products.Any())
            {
                foreach (var p in dto.Products)
                {
                    entity.Products.Add(new MyCertificateProduct
                    {
                        HsCode = p.HsCode,
                        Description = p.Description,
                        ScientificName = p.ScientificName,
                        BatchCode = p.BatchCode,
                        NumberOfPackages = p.NumberOfPackages ?? 0,
                        NetWeight = p.NetWeight ?? 0
                    });
                }
            }

            return entity;
        }
    }
}

