using System;
using System.Linq;
using MEA.Server.DTO.RuCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings
{
    public static class RuCertificateMappings
    {
        public static RuCertificate ToEntity(this CreateRuCertificateDto dto, string companyUserId)
        {
            var entity = new RuCertificate
            {
                CertificateRequestId = dto.CertificateRequestId,
                CompanyUserId = companyUserId,
                CreatedAt = DateTime.UtcNow,

                ConsignorNameAddress = dto.ConsignorNameAddress,
                ConsigneeNameAddress = dto.ConsigneeNameAddress,
                MeansOfTransport = dto.MeansOfTransport,
                CountryOfTransit = dto.CountryOfTransit,
                CertificateNo = dto.CertificateNo,
                CountryOfOrigin = dto.CountryOfOrigin,
                CountryIssuing = dto.CountryIssuing,
                CompetentAuthorityExporting = dto.CompetentAuthorityExporting,
                OrganizationIssuing = dto.OrganizationIssuing,
                PointOfCrossingBorder = dto.PointOfCrossingBorder,

                ProductName = dto.ProductName,
                ProductionDate = dto.ProductionDate,
                TypeOfPackage = dto.TypeOfPackage,
                NumberOfPackages = dto.NumberOfPackages,
                NetWeight = dto.NetWeight,
                NumberOfSeal = dto.NumberOfSeal,
                IdentificationMarks = dto.IdentificationMarks,
                StorageConditions = dto.StorageConditions,

                EstablishmentNameAddressRegNo = dto.EstablishmentNameAddressRegNo,
                FactoryVessel = dto.FactoryVessel,
                ColdStore = dto.ColdStore,
                AdministrativeUnit = dto.AdministrativeUnit,

                PlaceOfIssue = dto.PlaceOfIssue,
                DateOfIssue = dto.DateOfIssue,
                OfficialStamp = dto.OfficialStamp,
                OfficialSignature = dto.OfficialSignature,
                SignatoryUserId = dto.SignatoryUserId,
                SignatoryName = dto.SignatoryName,
                Qualification = dto.Qualification,
                CertificateType = dto.CertificateType ?? "attachment",

                DateOfAttachment = dto.DateOfAttachment,
                IdentificationMarksAttachment = dto.IdentificationMarksAttachment
            };

            if (dto.PreExportCertificates != null)
            {
                foreach (var pDto in dto.PreExportCertificates)
                {
                    entity.PreExportCertificates.Add(new RuPreExportCertificate
                    {
                        Date = pDto.Date,
                        Number = pDto.Number,
                        CountryOfOrigin = pDto.CountryOfOrigin,
                        AdministrativeTerritory = pDto.AdministrativeTerritory,
                        ApprovalNumber = pDto.ApprovalNumber,
                        ProductNameAndQuantity = pDto.ProductNameAndQuantity
                    });
                }
            }

            if (dto.Attachments != null)
            {
                foreach (var aDto in dto.Attachments)
                {
                    entity.Attachments.Add(new RuAttachment
                    {
                        Product = aDto.Product,
                        NumberOfKgs = aDto.NumberOfKgs,
                        NumberOfBoxes = aDto.NumberOfBoxes
                    });
                }
            }

            return entity;
        }
    }
}

