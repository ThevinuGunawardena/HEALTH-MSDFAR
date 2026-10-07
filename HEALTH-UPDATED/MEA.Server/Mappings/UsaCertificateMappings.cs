using MEA.Server.DTO.UsaCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class UsaCertificateMappings
{
    public static UsaCertificate ToEntity(this CreateUsaCertificateDto dto, string companyUserId)
    {
        return new UsaCertificate
        {
            CompanyUserId = companyUserId,
            CertificateRequestId = dto.CertificateRequestId,
            CreatedAt = DateTime.UtcNow,

            MyRef = dto.MyRef,
            YourRef = dto.YourRef,
            Date = dto.Date,
            CertificateNumber = dto.CertificateNumber,
            CompetentAuthority = dto.CompetentAuthority ?? "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
            CertifyingBody = dto.CertifyingBody ?? "DEPARTMENT OF FISHERIES & AQUATIC RESOURCES",
            CountryOfOrigin = dto.CountryOfOrigin ?? "SRI LANKA",
            CountryOfOriginISO = dto.CountryOfOriginISO ?? "LK",
            CountryOfDestination = dto.CountryOfDestination ?? "UNITED STATES OF AMERICA",
            CountryOfDestinationISO = dto.CountryOfDestinationISO ?? "US",
            PlaceOfLoading = dto.PlaceOfLoading,

            TransportAeroPlane = dto.TransportAeroPlane,
            TransportShip = dto.TransportShip,
            TransportRailway = dto.TransportRailway,
            TransportRoad = dto.TransportRoad,
            TransportOther = dto.TransportOther,

            PointsOfEntry = dto.PointsOfEntry,
            ConditionsOfStorage = dto.ConditionsOfStorage,
            TotalQuantity = dto.TotalQuantity,
            SealNumber = dto.SealNumber,
            TotalNumberOfPackages = dto.TotalNumberOfPackages,
            ApprovalNumberOfEstablishments = dto.ApprovalNumberOfEstablishments,
            DescriptionOfCommodity = dto.DescriptionOfCommodity,

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
            SignatoryUserId = string.IsNullOrWhiteSpace(dto.SignatoryUserId) ? null : dto.SignatoryUserId,
            SignatoryName = dto.SignatoryName,
            Designation = dto.Designation,
            Qualification = dto.Qualification,
            CompanyRegistrationNo = dto.CompanyRegistrationNo,
            OfficialStamp = dto.OfficialStamp,
            OfficialSignature = dto.OfficialSignature,
            CertificateType = dto.CertificateType ?? "letter",
            ProductsAttachment = dto.ProductsAttachment?.Select(p => new UsaCertificateProductAttachment
            {
                Product = p.Product,
                LotIdentifier = p.LotIdentifier,
                TypeOfPackaging = p.TypeOfPackaging,
                NumberOfKgs = p.NumberOfKgs,
                NumberOfBoxes = p.NumberOfBoxes
            }).ToList() ?? new List<UsaCertificateProductAttachment>()
        };
    }
}
