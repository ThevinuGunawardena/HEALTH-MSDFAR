using MEA.Server.DTO.JpCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings
{
    public static class JpCertificateMappings
    {
        public static JpCertificate ToEntity(this CreateJpCertificateDto dto, string companyUserId)
        {
            var entity = new JpCertificate
            {
                CertificateRequestId = dto.CertificateRequestId,
                CompanyUserId = companyUserId,
                CreatedAt = DateTime.UtcNow,

                MyRef = dto.MyRef,
                YourRef = dto.YourRef,
                Date = dto.Date,
                
                ItemName = dto.ItemName,
                NumberOfPackages = dto.NumberOfPackages,
                NetWeight = dto.NetWeight,
                
                ProcessingPlantName = dto.ProcessingPlantName,
                ProcessingPlantAddress = dto.ProcessingPlantAddress,
                CompetentAuthorityRegNo = dto.CompetentAuthorityRegNo,

                ConsignorName = dto.ConsignorName,
                ConsignorAddress = dto.ConsignorAddress,
                
                ConsigneeName = dto.ConsigneeName,
                ConsigneeAddress = dto.ConsigneeAddress,
                
                DespatchFrom = dto.DespatchFrom,
                DespatchTo = dto.DespatchTo,
                DespatchByShip = dto.DespatchByShip,
                
                OfficialStamp = dto.OfficialStamp,
                OfficialSignature = dto.OfficialSignature,
                SignatoryUserId = dto.SignatoryUserId,
                SignatoryName = dto.SignatoryName,
                Qualification = dto.Qualification,
                CertificateType = dto.CertificateType ?? "single"
            };

            return entity;
        }
    }
}

