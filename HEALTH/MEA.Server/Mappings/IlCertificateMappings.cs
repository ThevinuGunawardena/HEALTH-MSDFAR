using MEA.Server.DTO.IlCertificate;
using MEA.Server.Entities;
using System;
using System.Collections.Generic;
using System.Linq;

namespace MEA.Server.Mappings;

public static class IlCertificateMappings
{
    public static IlCertificate ToEntity(this CreateIlCertificateDto dto, string companyUserId) => new()
    {
        CertificateRequestId = dto.CertificateRequestId,
        CompanyUserId = companyUserId,
        CreatedAt = DateTime.UtcNow,
        CertificateType = dto.CertificateType ?? "attachment",
        CertificationNo = dto.CertificationNo,
        CentralCompetentAuthority = dto.CentralCompetentAuthority,
        CentralCompetentAuthorityEmail = dto.CentralCompetentAuthorityEmail,
        LocalCompetentAuthority = dto.LocalCompetentAuthority,
        CountryOfOrigin = dto.CountryOfOrigin,
        PlaceOfOriginName = dto.PlaceOfOriginName,
        PlaceOfOriginAddress = dto.PlaceOfOriginAddress,
        PlaceOfOriginApprovalNo = dto.PlaceOfOriginApprovalNo,
        ConsignorName = dto.ConsignorName,
        ConsignorAddress = dto.ConsignorAddress,
        PostalCodeConsignor = dto.PostalCodeConsignor,
        TelNoConsignor = dto.TelNoConsignor,
        EmailConsignor = dto.EmailConsignor,
        ConsigneeName = dto.ConsigneeName,
        ConsigneeAddress = dto.ConsigneeAddress,
        PostalCodeConsignee = dto.PostalCodeConsignee,
        TelNoConsignee = dto.TelNoConsignee,
        EmailConsignee = dto.EmailConsignee,
        PlaceOfLoading = dto.PlaceOfLoading,
        PortOfEntry = dto.PortOfEntry,
        DateOfArrival = dto.DateOfArrival,
        PlaceOfArrival = dto.PlaceOfArrival,
        PlaceOfArrivalAddress = dto.PlaceOfArrivalAddress,
        PlaceOfDestinationName = dto.PlaceOfDestinationName,
        PlaceOfDestinationAddress = dto.PlaceOfDestinationAddress,
        PlaceOfDestinationApprovalNo = dto.PlaceOfDestinationApprovalNo,
        DateOfContainerization = dto.DateOfContainerization,
        DateOfDeparture = dto.DateOfDeparture,
        TransportSea = dto.TransportSea,
        TransportAir = dto.TransportAir,
        TransportRail = dto.TransportRail,
        TransportRoad = dto.TransportRoad,
        TransportOther = dto.TransportOther,
        BillOfLading = dto.BillOfLading,
        Awb = dto.Awb,
        MeansOfTransportIdentification = dto.MeansOfTransportIdentification,
        ContainerNo = dto.ContainerNo,
        SealNo = dto.SealNo,
        MeansOfTransportReference = dto.MeansOfTransportReference,
        EntryBIP = dto.EntryBIP,
        ReadyToEat = dto.ReadyToEat,
        NonReadyToEat = dto.NonReadyToEat,
        ShipmentNumber = dto.ShipmentNumber,
        Remarks = dto.Remarks,

        // Signature fields
        PlaceOfIssue = dto.PlaceOfIssue,
        SignatoryName = dto.SignatoryName,
        Qualification = dto.Qualification,
        SignatureDate = dto.SignatureDate,
        Stamp = dto.Stamp,
        Signature = dto.Signature,
        SignatoryUserId = dto.SignatoryUserId,

        Products = dto.Commodities?.Select(p => p.ToEntity()).ToList() ?? new List<IlCertificateProduct>()
    };

    public static IlCertificateProduct ToEntity(this CreateIlCertificateProductDto dto) => new()
    {
        DescriptionOfCommodity = dto.DescriptionOfCommodity,
        SpeciesScientificName = dto.SpeciesScientificName,
        NatureOfCommodity = dto.NatureOfCommodity,
        TreatmentType = dto.TreatmentType,
        ApprovalNo = dto.ApprovalNo,
        NumberOfPackages = dto.NumberOfPackages,
        NetWeight = dto.NetWeight,
        HarvestingDate = dto.HarvestingDate,
        ProductionDate = dto.ProductionDate,
        BestBefore = dto.BestBefore,
        LotNo = dto.LotNo
    };
}
