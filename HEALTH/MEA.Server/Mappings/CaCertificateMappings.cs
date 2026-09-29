using MEA.Server.DTO.CaCertificate;
using MEA.Server.Entities;

namespace MEA.Server.Mappings;

public static class CaCertificateMappings
{
    public static CaCertificate ToEntity(this CaCertificateDto dto, string companyUserId)
    {
        return new CaCertificate
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
            ConsignorName = dto.ConsignorName,
            ConsignorAddress = dto.ConsignorAddress,
            ConsigneeName = dto.ConsigneeName,
            ConsigneeAddress = dto.ConsigneeAddress,
            CountryOfOrigin = dto.CountryOfOrigin ?? "SRI LANKA",
            CountryOfOriginISO = dto.CountryOfOriginISO ?? "LK",
            CountryOfDestination = dto.CountryOfDestination ?? "CANADA",
            CountryOfDestinationISO = dto.CountryOfDestinationISO ?? "CA",
            PlaceOfLoading = dto.PlaceOfLoading,
            
            TransportAeroPlane = dto.TransportAeroPlane,
            TransportShip = dto.TransportShip,
            TransportRailway = dto.TransportRailway,
            TransportRoad = dto.TransportRoad,
            TransportOther = dto.TransportOther,
            
            DespatchFrom = dto.DespatchFrom,
            DespatchTo = dto.DespatchTo,
            DespatchByShip = dto.DespatchByShip,
            
            ItemName = dto.ItemName,
            NumberOfPackages = dto.NumberOfPackages,
            NetWeight = dto.NetWeight,
            ProcessingPlantName = dto.ProcessingPlantName,
            ProcessingPlantAddress = dto.ProcessingPlantAddress,
            CompetentAuthorityRegNo = dto.CompetentAuthorityRegNo,
            
            PointsOfEntry = dto.PointsOfEntry,
            ConditionsOfStorage = dto.ConditionsOfStorage,
            TotalQuantity = dto.TotalQuantity,
            SealNumber = dto.SealNumber,
            TotalNumberOfPackages = dto.TotalNumberOfPackages,
            ApprovalNumberOfEstablishments = dto.ApprovalNumberOfEstablishments,
            DescriptionOfCommodity = dto.DescriptionOfCommodity,

            SignatoryUserId = dto.SignatoryUserId,
            SignatoryName = dto.SignatoryName,
            Designation = dto.Designation,
            Qualification = dto.Qualification,
            CompanyRegistrationNo = dto.CompanyRegistrationNo,
            OfficialStamp = dto.OfficialStamp,
            OfficialSignature = dto.OfficialSignature,
            CertificateType = dto.CertificateType ?? "letter",

            ProductsAttachment = dto.ProductsAttachment.Select(p => p.ToEntity()).ToList()
        };
    }

    public static CaCertificateProductAttachment ToEntity(this CaCertificateProductAttachmentDto dto)
    {
        return new CaCertificateProductAttachment
        {
            Product = dto.Product,
            LotIdentifier = dto.LotIdentifier,
            TypeOfPackaging = dto.TypeOfPackaging,
            NumberOfKgs = dto.NumberOfKgs,
            NumberOfBoxes = dto.NumberOfBoxes
        };
    }
}
