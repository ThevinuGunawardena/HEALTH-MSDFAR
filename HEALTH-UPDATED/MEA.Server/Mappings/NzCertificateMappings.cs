using MEA.Server.DTO.NzCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings
{
    public static class NzCertificateMappings
    {
        public static NzCertificate ToEntity(this CreateNzCertificateDto dto, string companyUserId)
        {
            var entity = new NzCertificate
            {
                CertificateRequestId = dto.CertificateRequestId,
                CompanyUserId = companyUserId,
                CreatedAt = DateTime.UtcNow,

                ConsignorName = dto.ConsignorName,
                ConsignorAddress = dto.ConsignorAddress,
                CertificateRefNumber = dto.CertificateRefNumber,
                
                ConsigneeName = dto.ConsigneeName,
                ConsigneeAddress = dto.ConsigneeAddress,
                
                CountryOfOrigin = dto.CountryOfOrigin,
                CountryOfDestination = dto.CountryOfDestination,
                
                ProcessorName = dto.ProcessorName,
                ProcessorAddress = dto.ProcessorAddress,
                ProcessorEstablishmentNumber = dto.ProcessorEstablishmentNumber,
                
                PortDispatchedFrom = dto.PortDispatchedFrom,
                DateOfDeparture = dto.DateOfDeparture,
                
                CompetentAuthority = dto.CompetentAuthority,
                MeansOfTransport = dto.MeansOfTransport,
                
                TransportAeroplan = dto.TransportAeroplan ?? false,
                TransportShip = dto.TransportShip ?? false,
                
                TemperatureOfCommodities = dto.TemperatureOfCommodities,
                
                ContainerNumber = dto.ContainerNumber,
                OfficialSealNumber = dto.OfficialSealNumber,
                OfficialStamp = dto.OfficialStamp,
                OfficialSignature = dto.OfficialSignature,
                
                SignatoryUserId = dto.SignatoryUserId,
                SignatoryName = dto.SignatoryName,
                Qualification = dto.Qualification,
                SignatureDate = dto.SignatureDate,
                CertificateType = dto.CertificateType ?? "fish"
            };

            if (dto.Products != null && dto.Products.Any())
            {
                foreach (var p in dto.Products)
                {
                    entity.Products.Add(new NzCertificateProduct
                    {
                        ProductName = p.ProductName,
                        AquaticAnimalSpecies = p.AquaticAnimalSpecies,
                        ProductionDate = p.ProductionDate,
                        NumberOfPackages = p.NumberOfPackages ?? 0,
                        NetWeightKg = p.NetWeightKg ?? 0,
                        HsCode = p.HsCode
                    });
                }
            }

            return entity;
        }
    }
}

