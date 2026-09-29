namespace MEA.Server.DTO.MvCertificate;

public class MvCertificateDto
{
    public int Id { get; set; }
    public int? CertificateRequestId { get; set; }

    public string? ConsignorExporter { get; set; }
    public string? CertificateNumber { get; set; }
    public string? CompetentAuthority { get; set; }
    public string? CertifyingBody { get; set; }
    public string? ConsigneeImporter { get; set; }
    
    public string? CountryOfOrigin { get; set; }
    public string? CountryOfOriginISO { get; set; }
    public string? CountryOfDestination { get; set; }
    public string? CountryOfDestinationISO { get; set; }
    
    public string? PlaceOfLoading { get; set; }
    
    public bool TransportAeroPlane { get; set; }
    public bool TransportShip { get; set; }
    public bool TransportRailway { get; set; }
    public bool TransportRoad { get; set; }
    public bool TransportOther { get; set; }
    
    public string? PointsOfEntry { get; set; }
    public string? ConditionsOfStorage { get; set; }
    public string? TotalQuantity { get; set; }
    public string? SealNumber { get; set; }
    public string? TotalNumberOfPackages { get; set; }
    public string? ApprovalNumberOfEstablishments { get; set; }
    public string? DescriptionOfCommodity { get; set; }
    
    public string? CertifyingOfficerName { get; set; }
    public DateTime? CertifyingOfficerDate { get; set; }

    public string? SignatoryUserId { get; set; }
    public string? SignatoryName { get; set; }
    public string? Qualification { get; set; }
    public string? CompanyRegistrationNo { get; set; }
    public string? OfficialStamp { get; set; }
    public string? OfficialSignature { get; set; }
    public string? CertificateType { get; set; }

    public List<MvCertificateProductDto> Products { get; set; } = new();
    public List<MvCertificateProductSecondDto> ProductsSecond { get; set; } = new();
    public List<MvCertificateProductAttachmentDto> ProductsAttachment { get; set; } = new();
}

public class MvCertificateProductDto
{
    public string? No { get; set; }
    public string? NatureOfCommodity { get; set; }
    public string? Species { get; set; }
    public string? PurposeOfUse { get; set; }

    public MvCertificateProductDto ToDto(Entities.MvCertificateProduct entity)
    {
        return new MvCertificateProductDto
        {
            No = entity.No,
            NatureOfCommodity = entity.NatureOfCommodity,
            Species = entity.Species,
            PurposeOfUse = entity.PurposeOfUse
        };
    }
}

public class MvCertificateProductSecondDto
{
    public string? No { get; set; }
    public string? NameOfTheProduct { get; set; }
    public string? LotIdentifier { get; set; }
    public string? TypeOfPackaging { get; set; }
    public int? NumberOfPackages { get; set; }
    public string? NetWeight { get; set; }

    public MvCertificateProductSecondDto ToDto(Entities.MvCertificateProductSecond entity)
    {
        return new MvCertificateProductSecondDto
        {
            No = entity.No,
            NameOfTheProduct = entity.NameOfTheProduct,
            LotIdentifier = entity.LotIdentifier,
            TypeOfPackaging = entity.TypeOfPackaging,
            NumberOfPackages = entity.NumberOfPackages,
            NetWeight = entity.NetWeight
        };
    }
}

public class MvCertificateProductAttachmentDto
{
    public string? Product { get; set; }
    public string? LotIdentifier { get; set; }
    public string? TypeOfPackaging { get; set; }
    public string? NumberOfKgs { get; set; }
    public int? NumberOfBoxes { get; set; }

    public MvCertificateProductAttachmentDto ToDto(Entities.MvCertificateProductAttachment entity)
    {
        return new MvCertificateProductAttachmentDto
        {
            Product = entity.Product,
            LotIdentifier = entity.LotIdentifier,
            TypeOfPackaging = entity.TypeOfPackaging,
            NumberOfKgs = entity.NumberOfKgs,
            NumberOfBoxes = entity.NumberOfBoxes
        };
    }
}
