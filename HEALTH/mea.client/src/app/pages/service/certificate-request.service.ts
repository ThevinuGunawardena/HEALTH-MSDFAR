import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';
import { catchError, of } from 'rxjs';

export type CertificateType = 'EU' | 'NonEU';

export interface CountryDto {
    id: number;
    name: string;
}

export interface CreateCertificateRequestPayload {
    certificateType: CertificateType;
    countryId: number | null;
    referenceNumber?: string;
    quantity?: number;
}

export interface PaymentSlipPreview {
    mimeType: string;
    contentBase64: string;
}

export interface CertificateRequestResponse {
    id: number;
    referenceNumber: string;
    certificateType: CertificateType | number;
    countryId: number | null;
    countryName?: string | null;
    companyName?: string | null;
    status: 'Pending' | 'Confirmed' | 'Rejected' | number;
    createdAt: string;
    expiresAt?: string;
    isExpired?: boolean;
    hasFormSubmitted?: boolean;
    paymentSlip?: PaymentSlipPreview | null;
    cancelsAndReplacesRef?: string | null;
    cancelsAndReplacesDate?: string | null;
    replacedCertificateRequestId?: number | null;
    requests?: CertificateRequestResponse[];
}

export interface VetCertificateFormResponse {
    id: number;
    certificateRequestId: number | null;
    createdAt: string;
}

export interface CreateIlCertificateProductPayload {
    descriptionOfCommodity: string;
    speciesScientificName: string;
    natureOfCommodity: string;
    treatmentType: string;
    approvalNo: string;
    numberOfPackages: number;
    netWeight: number;
    harvestingDate: string | Date | null;
    productionDate: string | Date | null;
    bestBefore: string | Date | null;
    lotNo: string;
}

export interface CreateIlCertificatePayload {
    certificateRequestId?: number | null;
    certificateType?: string;
    certificationNo: string;
    centralCompetentAuthority: string;
    centralCompetentAuthorityEmail: string;
    localCompetentAuthority: string;
    countryOfOrigin: string;
    placeOfOriginName: string;
    placeOfOriginAddress: string;
    placeOfOriginApprovalNo: string;
    consignorName: string;
    consignorAddress: string;
    postalCodeConsignor?: string;
    telNoConsignor?: string;
    emailConsignor?: string;
    consigneeName: string;
    consigneeAddress: string;
    postalCodeConsignee?: string;
    telNoConsignee?: string;
    emailConsignee?: string;
    placeOfLoading?: string;
    portOfEntry?: string;
    dateOfArrival: string | Date | null;
    placeOfArrival: string;
    placeOfArrivalAddress: string;
    placeOfDestinationName: string;
    placeOfDestinationAddress: string;
    placeOfDestinationApprovalNo: string;
    dateOfContainerization: string | Date | null;
    dateOfDeparture: string | Date | null;
    transportSea: boolean;
    transportAir: boolean;
    transportRail: boolean;
    transportRoad: boolean;
    transportOther?: boolean;
    billOfLading?: string;
    awb?: string;
    meansOfTransportIdentification: string;
    containerNo: string;
    sealNo: string;
    meansOfTransportReference: string;
    entryBIP: string;
    readyToEat: string;
    nonReadyToEat: string;
    shipmentNumber?: string;
    remarks: string;

    // Signature fields
    placeOfIssue?: string;
    signatoryName?: string;
    qualification?: string;
    signatureDate?: string | Date | null;
    stamp?: string;
    signature?: string;
    signatoryUserId?: string | null;

    commodities: CreateIlCertificateProductPayload[];
}

export interface IlCertificateResponse {
    id: number;
    certificateRequestId: number;
    createdAt: string;
}

export interface AuCertificateProductPayload {
    speciesScientificName: string;
    natureOfCommodity: string;
    treatmentType: string;
    approvalNumberOfEstablishments: string;
    manufacturingPlant: string;
    numberOfPackages: number;
    netWeight: number;
}

export interface CreateAuCertificatePayload {
    certificateRequestId?: number | null;
    consignorName: string;
    consignorAddress: string;
    consignorPostal: string;
    consignorTel: string;
    certRefNumber: string;
    certRefNumberA: string;
    centralCompetentAuthority: string;
    localCompetentAuthority: string;
    consigneeName: string;
    consigneeAddress: string;
    consigneePostal: string;
    consigneeTel: string;
    consignee6: string;
    countryOrigin: string;
    countryOriginISO: string;
    regionOrigin: string;
    regionOriginISO: string;
    countryDestination: string;
    countryDestinationISO: string;
    countryDestination110: string;
    placeOfOriginName: string;
    placeOfOriginAddress: string;
    placeOfOriginApprovalNo: string;
    countryDestination112: string;
    placeOfLoading: string;
    dateOfDeparture: string | null;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    docReferences: string;
    entryBIP: string;
    field117: string;
    descCommon: string;
    hsCode: string;
    quantity: string;
    temperatureAmbient: boolean;
    temperatureChilled: boolean;
    temperatureFrozen: boolean;
    numPackages: string;
    containerId: string;
    packagingType: string;
    forHumanConsumption: boolean;
    field126: string;
    forImportEU: string;
    products: AuCertificateProductPayload[];
    healthCertNo: string;
    healthCertNoB: string;
    exportApprovalNumber: string;
    signatoryUserId?: string | null;
    signatoryName: string;
    qualification: string;
    signatureDate: string | null;
    stamp: string;
    signature: string;
    certificateType: string;
}

export interface BrCertificateProductPayload {
    nameOfTheProduct: string;
    scientificName: string;
    typeOfPackaging: string;
    numberOfPackages: number;
    netWeight: number;
}

export interface CreateBrCertificatePayload {
    certificateRequestId?: number | null;
    refNumber: string;
    countryOfExport: string;
    certificateNo: string;
    competentAuthority: string;
    localCompetentAuthority: string;
    exporterName: string;
    exporterAddress: string;
    importerName: string;
    importerAddress: string;
    countryOrigin: string;
    countryOriginISO: string;
    countryOfDestination: string;
    countryDestinationISO: string;
    placeOfLoading: string;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    declaredPointOfEntry: string;
    conditionsForTransportStorage: string;
    identificationOfContainers: string;
    identificationOfFoodProducts: string;
    producerDetails: string;
    hsCode: string;
    intendedPurpose: string;
    totalNetWeight: number;
    placeAndDate: string;
    dateOfIssue: string | Date;
    officialStamp: string;
    signatoryUserId: string | null;
    signatoryName: string | null;
    qualification: string | null;
    modeloConformeCircularNo: string;
    sanitaryCertification: string;
    products: BrCertificateProductPayload[];
}

export interface BrCertificateResponse {
    id: number;
    refNumber: string;
    dateOfIssue: string;
}

export interface AuCertificateResponse {
    id: number;
    certificateRequestId: number | null;
    createdAt: string;
}

export interface VetProductFieldResponse {
    id?: number;
    productOrder?: number;
    descCommon?: string;
    descScientific?: string;
    processingType?: string;
    hsCode?: string;
    temperatureAmbient?: boolean;
    temperatureChilled?: boolean;
    temperatureFrozen?: boolean;
    quantity?: string;
    numPackages?: string;
    packagingType?: string;
    containerId?: string;
    forHumanConsumption?: boolean;
    forImportEU?: string;
    natureAquaculture?: boolean;
    natureWildOrigin?: boolean;
    treatmentChilled?: boolean;
    treatmentFrozen?: boolean;
    treatmentLive?: boolean;
    netWeight?: string;
}

export interface VetAttachmentFieldResponse {
    id: number;
    fileOrder?: number;
    originalFileName?: string;
    secondaryFileName?: string;
    contentType?: string;
}

export interface VetFormFieldResponse {
    id: number;
    certificateRequestId: number;
    oldHC?: string;
    newHC?: string;
    landingSite?: string;
    boatRegistration?: string;
    boatNumber?: string;
    supplierNameAddress?: string;
    arrivalAtFactory?: string;
    processingDate?: string;
    farmLocation?: string;
    farmOwnerName?: string;
    farmOwnerAddress?: string;
    harvestDate?: string;
    arrivalTimeProduct?: string;
    processingDates?: string;
    aquaSupplier?: string;
    countryOrigin?: string;
    arrivalConsignment?: string;
    healthCertNo?: string;
    referenceNumber?: string;
    productTypeAquaculture?: boolean;
    productTypeWildCaught?: boolean;
    consignorName: string;
    consignorAddress: string;
    consignorPostal: string;
    consignorTel: string;
    consigneeName: string;
    consigneeAddress: string;
    consigneePostal: string;
    consigneeTel: string;
    countryOriginISO: string;
    regionOriginISO: string;
    countryDestination?: string;
    countryDestinationISO: string;
    placeOfLoading: string;
    dateOfDeparture: string;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    docReferences: string;
    entryBIP: string;
    descCommon: string;
    hsCode: string;
    quantity: string;
    numPackages: string;
    packagingType: string;
    temperatureAmbient: boolean;
    temperatureChilled: boolean;
    temperatureFrozen: boolean;
    forHumanConsumption: boolean;
    temperature: string;
    nature: string;
    treatment: string;
    netWeight: string;
    processingType: string;
    forImportEU: string;
    transportId?: string;
    containerId?: string;
    sealNumber?: string;
    processingEstName?: string;
    processingEstAddress?: string;
    approvalNo?: string;
    factoryVessel?: string;
    coldStore?: string;
    administrativeUnit?: string;
    descScientific?: string;
    natureAquaculture?: boolean;
    natureWildOrigin?: boolean;
    treatmentChilled?: boolean;
    treatmentFrozen?: boolean;
    treatmentLive?: boolean;
    signatureDate?: string;
    signatureTime?: string;
    signature?: string;
    signatoryName?: string;
    designation?: string;
    attestation61_1?: boolean;
    attestation61_2?: boolean;
    attestation61_3?: boolean;
    attestation61_4?: boolean;
    attestation61_5?: boolean;
    attestation62_1?: boolean;
    attestation62_2?: boolean;
    products?: VetProductFieldResponse[];
    uploadedCertificateFiles?: VetAttachmentFieldResponse[];
    uploadedCertificateFile?: boolean;
}

export interface AmPreExportCertificatePayload {
    date: string | null;
    number: string;
    countryOfOrigin: string;
    administrativeTerritory: string;
    approvalNumber: string;
    productNameAndQuantity: string;
}

export interface AmAttachmentPayload {
    product: string;
    numberOfKgs: number;
    numberOfBoxes: number;
}

export interface CreateAmCertificatePayload {
    certificateRequestId?: number | null;
    consignorName: string;
    consignorAddress: string;
    consignorPostal: string;
    consignorTel: string;
    certRefNumber: string;
    certRefNumberA: string;
    centralCompetentAuthority: string;
    localCompetentAuthority: string;
    consigneeName: string;
    consigneeAddress: string;
    consigneePostal: string;
    consigneeTel: string;
    consignee6: string;
    countryOrigin: string;
    countryOriginISO: string;
    regionOrigin: string;
    regionOriginISO: string;
    countryDestination: string;
    countryDestinationISO: string;
    countryDestination110: string;
    placeOfOriginName: string;
    placeOfOriginAddress: string;
    placeOfOriginApprovalNo: string;
    countryDestination112: string;
    placeOfLoading: string;
    dateOfDeparture: string | null;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    transportId: string;
    entryBIP: string;
    field117: string;
    descCommon: string;
    hsCode: string;
    quantity: string;
    temperatureAmbient: boolean;
    temperatureChilled: boolean;
    temperatureFrozen: boolean;
    numPackages: string;
    containerId: string;
    packagingType: string;
    forHumanConsumption: boolean;
    field126: string;
    forImportEU: string;
    processingEstName: string;
    processingEstRegNo: string;
    healthCertNo?: string;
    healthCertNoB?: string;
    certificateNo?: string;
    countryIssuing?: string;
    competentAuthorityExporting?: string;
    organizationIssuing?: string;
    countryOfTransit?: string;
    pointOfCrossingBorder?: string;
    productName?: string;
    productionDate?: string | Date | null;
    netWeight?: string;
    numberOfSeal?: string;
    identificationMarks?: string;
    storageConditions?: string;
    factoryVessel?: string;
    coldStore?: string;
    administrativeUnit?: string;
    placeOfIssue?: string;
    dateOfIssue?: string | Date | null;
    dateOfAttachment?: string | Date | null;
    identificationMarksAttachment?: string;
    exportApprovalNumber?: string;
    signatoryUserId?: string | null;
    signatoryName?: string;
    qualification?: string;
    signatureDate?: string | null;
    stamp?: string;
    signature?: string;
    preExportCertificates: AmPreExportCertificatePayload[];
    attachments: AmAttachmentPayload[];
}

export interface ChAttachmentPayload {
    product: string;
    netWeight?: number | null;
    numberOfBoxes?: number | null;
}

export interface CreateChCertificatePayload {
    certificateRequestId?: number | null;
    certificateType?: string;
    refNumber?: string;
    countryOfExport: string;
    countryOfProduction: string;
    competentAuthority: string;
    departmentOfIssuance: string;
    commodityName: string;
    scientificName: string;
    latinName: string;
    number: string;
    numberOfPackages?: string;
    netWeight?: string;
    productionDate?: string | Date | null;
    lotNumber?: string;
    originRawMaterialsCountry?: string;
    processingType?: string;
    productionMode?: string;
    aquacultured?: boolean;
    wildCaughtBool?: boolean;
    productiveWaterArea?: string;
    aquacultureArea?: string;
    catchArea: string;
    artificialCulture: string;
    wildCaught: string;
    aquacultureFarmApprovedReg?: string;
    fishingVessel?: string;
    fishingAndFactoryVessel?: string;
    transportFishingVessel?: string;
    processingPlantNameAddress?: string;
    processingPlantRegNo?: string;
    coldStorageRawMaterials?: string;
    coldStorageProducts?: string;
    packagingEnterpriseName: string;
    packagingEnterpriseAddress: string;
    packagingEnterpriseRegNumber: string;
    consignorName?: string;
    consignorAddress?: string;
    consigneeName?: string;
    consigneeAddress?: string;
    placeOfDispatch?: string;
    placeOfDestination?: string;
    meansOfTransport?: string;
    nameOfVessel?: string;
    flightNumber?: string;
    otherTransportMeans?: string;
    containerNumber?: string;
    sealNumber?: string;
    dateOfDeparture: string | Date | null;
    portOfDeparture: string;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    identificationDocumentReferences: string;
    exporterName: string;
    exporterAddress: string;
    importerName: string;
    importerAddress: string;
    placeOfIssue: string;
    dateOfIssue: string | Date | null;
    officialStamp: string;
    signatoryUserId?: string | null;
    signatoryName?: string;
    qualification?: string;
    dateOfAttachment?: string | Date | null;
    identificationMarksAttachment?: string;
    attachments?: ChAttachmentPayload[];
}

export interface ChCertificateResponse {
    id: number;
    dateOfIssue: string;
}

export interface HkCertificateProductPayload {
    description: string;
    species: string;
    processingType: string;
    packagingType: string;
    lotCode: string;
    numberOfPackages: number | null;
    packagesUnit?: string;
    netWeight: number | null;
    netWeightUnit?: string;
}

export interface CreateHkCertificatePayload {
    certificateRequestId?: number | null;
    certificateType?: string;
    identificationNumber: string;
    countryOfDispatch: string;
    competentAuthority: string;
    certifyingBody: string;
    containerNumber?: string;
    sealNumber?: string;
    sealIdentificationNumber: string;
    storageTemperature: string;
    approvalNumber?: string;
    processingEstablishment?: string;
    provenanceDetails: string;
    consignorName: string;
    consignorAddress: string;
    placeOfDispatch: string;
    destinationCountryPlace: string;
    meansOfTransport: string;
    consigneeName: string;
    consigneeAddress: string;
    dateOfAttachment?: string | Date | null;
    attachmentRegNo?: string;
    placeOfIssue: string;
    dateOfIssue: string | Date | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    officialSignature?: string | null;
    officerTel: string;
    officerFax: string;
    officerEmail: string;
    products: HkCertificateProductPayload[];
}

export interface HkCertificateResponse {
    id: number;
    dateOfIssue: string;
}

export interface IdCertificateProductPayload {
    no: string;
    commonName: string;
    scientificName: string;
    hsCode: string;
    quantity: number;
    unit: string;
}

export interface CreateIdCertificatePayload {
    certificateRequestId?: number | null;
    numberNomor: string;
    consignorName: string;
    consignorAddress: string;
    consigneeName: string;
    consigneeAddress: string;
    competentAuthority: string;
    establishmentAquaculture: boolean;
    establishmentProcessing: boolean;
    establishmentOther: boolean;
    establishmentName: string;
    establishmentRegNo: string;
    establishmentAddress: string;
    countryRegionOrigin: string;
    sourceFarmRaised: boolean;
    sourceWildCaught: boolean;
    portOfShipment: string;
    transportAir: boolean;
    transportSea: boolean;
    transportRoad: boolean;
    commodityDescription: string;
    tempAmbient: boolean;
    tempFrozen: boolean;
    tempChilled: boolean;
    intendedHumanConsumption: boolean;
    intendedCultureBreeding: boolean;
    intendedTrade: boolean;
    intendedResearch: boolean;
    intendedFishFeed: boolean;
    intendedExhibition: boolean;
    intendedOther: boolean;
    totalPackages: string;
    packagingType: string;
    totalQuantityKg: string;
    containerSealNumber: string;
    portOfDestination: string;
    transportVesselName: string;
    transportVoyageNumber: string;
    dateOfDeparture: string | Date | null;
    testingLaboratory: string;
    laboratoryAddress: string;
    approvingOfficerName: string;
    testResultNumber: string;
    attestationRefNumber: string;
    attestFinfish: boolean;
    attestMollusca: boolean;
    attestCrustacea: boolean;
    attestFisheryProducts: boolean;
    attestOther: boolean;
    attestClauseA: boolean;
    attestClauseB: boolean;
    attestClauseC: boolean;
    attestClauseCCrustacean: boolean;
    attestClauseCCyprinidae: boolean;
    attestClauseCTilapia: boolean;
    attestClauseCCatfish: boolean;
    attestClauseCOtherFish: boolean;
    attestClauseCVisibleSigns: boolean;
    attestClauseCPackagedContainers: boolean;
    attestClauseD: boolean;
    attestClauseE: boolean;
    additionalInformation: string;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certifiedIssuedAt: string;
    certifiedDate: string | Date | null;
    certifiedPosition?: string | null;
    certifiedPhone: string;
    certifiedFax: string;
    certifiedEmail: string;
    certifiedAddress?: string | null;
    products: IdCertificateProductPayload[];
}

export interface IdCertificateResponse {
    id: number;
    certifiedDate: string;
}

export interface AmCertificateResponse {
    id: number;
    certificateRequestId: number | null;
    createdAt: string;
}

export interface IndCertificateProductPayload {
    nameOfProduct: string;
    lotNo: string;
    typeOfPackaging: string;
    numberOfPackages: number;
    netWeight: number;
}

export interface CreateIndCertificatePayload {
    certificateRequestId?: number | null;
    certificateType?: string;
    countryOfDispatch: string;
    certificateNumber: string;
    myRef?: string;
    yourRef?: string;
    consignorName: string;
    consignorAddress: string;
    consignorTel: string;
    competentAuthorityDetails: string;
    consigneeName: string;
    consigneeAddress: string;
    consigneeTel: string;
    countryOfOrigin: string;
    countryOfOriginIso: string;
    countryOfDestination: string;
    countryOfDestinationIso: string;
    placeOfLoading: string;
    meansOfTransport: string;
    declaredPointOfEntry: string;
    conditionsForTransportStorage: string;
    totalQuantity: string;
    invoiceNoDate: string;
    foodDescription: string;
    intendedPurpose: string;
    producerNameAddress: string;
    approvalNumberDetails: string;
    products: IndCertificateProductPayload[];
    dateOfManufacture: string | Date | null;
    bestBefore: string | Date | null;
    dateOfExpiry: string | Date | null;
    itemDescription?: string;
    numberOfPackagesStr?: string;
    netWeightStr?: string;
    processingPlantNameAddress?: string;
    processingPlantRegNo?: string;
    dispatchFrom?: string;
    dispatchTo?: string;
    modeOfTransport?: string;
    hsCode?: string;
    speciesName?: string;
    previousCertRef?: string;
    consignmentIdentificationDetails?: string;
    productDescription?: string;
    attestationPlace: string;
    attestationDate: string | Date | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    authorizedOfficialDate: string | Date | null;
    authorizedOfficialSignature: string;
    officialStamp: string;
}

export interface IndCertificateResponse {
    id: number;
    attestationDate: string;
}

export interface CreateJpCertificatePayload {
    certificateRequestId?: number | null;
    myRef: string;
    yourRef: string;
    date: string | Date | null;
    itemName: string;
    numberOfPackages: string;
    netWeight: string;
    processingPlantName?: string;
    processingPlantAddress?: string;
    competentAuthorityRegNo?: string;
    consignorName: string;
    consignorAddress: string;
    consigneeName: string;
    consigneeAddress: string;
    despatchFrom: string;
    despatchTo: string;
    despatchByShip: string;
    officialStamp?: string;
    officialSignature?: string;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType: string;
}

export interface JpCertificateResponse {
    id: number;
}

export interface CreateKwCertificateProductPayload {
    nameDescription: string;
    hsCodes: string;
    treatmentDerivedFrom: string;
    brandName: string;
    productionDate: string | Date | null;
    expiryDate: string | Date | null;
    numberPackages: number;
    batchLotNo: string;
    totalWeight: number;
}

export interface CreateKwCertificatePayload {
    certificateRequestId?: number | null;
    consignorName: string;
    consignorAddress: string;
    certificateReferenceNo: string;
    placeOfIssue: string;
    dateOfIssue: string | Date | null;
    consigneeName: string;
    consigneeAddress: string;
    competentAuthority: string;
    competentAuthorityAddress: string;
    countryOfOrigin: string;
    countryOfOriginIso: string;
    countryOfDestination: string;
    countryOfDestinationIso: string;
    producerName: string;
    producerAddress: string;
    packingEstName: string;
    packingEstAddress: string;
    packingEstApprovalNo?: string;
    borderOfEntry: string;
    borderLoadingCountry: string;
    borderLoadingPlace: string;
    transportByAir: boolean;
    transportBySea: boolean;
    vehicleIdentificationNo: string;
    tempChilled: boolean;
    tempFrozen: boolean;
    commoditiesOther: boolean;
    commoditiesAfterFurtherProcess: boolean;
    commoditiesHumanConsumption: boolean;
    products: CreateKwCertificateProductPayload[];
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    officialStamp: string;
    officialSignature?: string;
    signatureDate?: string | Date | null;
    certificateType?: string;
}

export interface KwCertificateResponse {
    id: number;
}

export interface CreateMyCertificateProductPayload {
    hsCode: string;
    description: string;
    scientificName: string;
    batchCode: string;
    numberOfPackages: number;
    netWeight: number;
}

export interface CreateMyCertificatePayload {
    certificateRequestId?: number | null;
    exporterName: string;
    certificateReferenceNo: string;
    qualityCertificateNo: string;
    competentAuthority: string;
    localAuthority: string;
    importerDetails: string;
    countryOfOrigin: string;
    countryOfOriginIso: string;
    countryOfDestination: string;
    countryOfDestinationIso: string;
    processingEstablishment: string;
    authorizationNo: string;
    placeOfLoading: string;
    transportAir: boolean;
    transportShip: boolean;
    transportRail: boolean;
    transportRoad: boolean;
    transportOther: boolean;
    portOfEntry: string;
    transportCompany: string;
    conditionAmbient: boolean;
    conditionChilled: boolean;
    conditionFrozen: boolean;
    containerSealIdentification: string;
    invoiceNo: string;
    transitCountry: string;
    departureDate: string | Date | null;
    certifyingOfficialDate?: string | Date | null;
    certificateReferenceNoPage2: string;
    productBrand: string;
    originFisheries: boolean;
    originAquaculture: boolean;
    certifiedProductFor: string;
    treatmentType: string;
    products: CreateMyCertificateProductPayload[];
    certificateReferenceNoPage3: string;
    additionalInformation: string;
    officialStamp?: string | null;
    officialSignature?: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType?: string | null;
}

export interface MyCertificateResponse {
    id: number;
}

export interface CreateNzCertificateProductPayload {
    productName: string;
    aquaticAnimalSpecies: string;
    productionDate?: string | Date | null;
    numberOfPackages: number;
    netWeightKg: number;
    hsCode: string;
}

export interface CreateNzCertificatePayload {
    certificateRequestId?: number | null;
    consignorName: string;
    consignorAddress: string;
    certificateRefNumber: string;
    consigneeName: string;
    consigneeAddress: string;
    countryOfOrigin: string;
    countryOfDestination: string;
    processorName: string;
    processorAddress: string;
    processorEstablishmentNumber: string;
    portDispatchedFrom: string;
    dateOfDeparture: string | Date | null;
    competentAuthority: string;
    meansOfTransport: string;
    transportAeroplan: boolean;
    transportShip: boolean;
    temperatureOfCommodities: string;
    containerNumber: string;
    officialSealNumber: string;
    officialStamp?: string | null;
    officialSignature?: string | null;
    signatureDate: string | Date | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType?: string | null;
    products: CreateNzCertificateProductPayload[];
}

export interface NzCertificateResponse {
    id: number;
}

export interface RuPreExportCertificatePayload {
    date: string;
    number: string;
    countryOfOrigin: string;
    administrativeTerritory: string;
    approvalNumber: string;
    productNameAndQuantity: string;
}

export interface RuAttachmentPayload {
    product: string;
    numberOfKgs: number;
    numberOfBoxes: number;
}

export interface CreateRuCertificatePayload {
    certificateRequestId?: number | null;
    consignorName: string;
    consignorAddress: string;
    consigneeName: string;
    consigneeAddress: string;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    transportId: string;
    countryOfTransit: string;
    certificateNo: string;
    countryOrigin: string;
    countryIssuing: string;
    competentAuthorityExporting: string;
    organizationIssuing: string;
    pointOfCrossingBorder: string;
    productName: string;
    productionDate: string | Date | null;
    packagingType: string;
    numPackages: string;
    netWeight: string;
    numberOfSeal: string;
    identificationMarks: string;
    storageConditions: string;
    processingEstName: string;
    processingEstRegNo: string;
    processingEstAddress: string;
    factoryVessel: string;
    coldStore: string;
    administrativeUnit: string;
    preExportCertificates: RuPreExportCertificatePayload[];
    placeOfIssue: string;
    dateOfIssue: string | Date | null;
    officialStamp?: string;
    officialSignature?: string;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType?: string;
    dateOfAttachment: string | Date | null;
    identificationMarksAttachment: string;
    attachments: RuAttachmentPayload[];
}

export interface RuCertificateResponse {
    id: number;
}

export interface CreateKzCertificatePayload {
    certificateRequestId?: number | null;
    consignorName: string;
    consignorAddress: string;
    consigneeName: string;
    consigneeAddress: string;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    transportId: string;
    countryOfTransit: string;
    certificateNo: string;
    countryOrigin: string;
    countryIssuing: string;
    competentAuthorityExporting: string;
    organizationIssuing: string;
    pointOfCrossingBorder: string;
    productName: string;
    productionDate: string | Date | null;
    packagingType: string;
    numPackages: string;
    netWeight: string;
    numberOfSeal: string;
    identificationMarks: string;
    storageConditions: string;
    processingEstName: string;
    processingEstRegNo: string;
    processingEstAddress: string;
    factoryVessel: string;
    coldStore: string;
    administrativeUnit: string;
    preExportCertificates: RuPreExportCertificatePayload[];
    placeOfIssue: string;
    dateOfIssue: string | Date | null;
    officialStamp?: string;
    officialSignature?: string;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType?: string;
    dateOfAttachment: string | Date | null;
    identificationMarksAttachment: string;
    attachments: RuAttachmentPayload[];
}

export interface KzCertificateResponse {
    id: number;
}

export interface CreateTwCertificateProductPayload {
    commodityName: string;
    hsCode: string;
    scientificName: string;
    numberOfPackages: number;
    netWeight: number;
}

export interface CreateTwCertificatePayload {
    certificateRequestId?: number | null;
    referenceNo: string;
    countryOfExport: string;
    countryOfProduction: string;
    competentAuthority: string;
    departmentIssuance: string;
    products: CreateTwCertificateProductPayload[];
    productionPlace: string;
    processingType: string;
    productionMode: string;
    aquaculturedYes: boolean;
    aquaculturedNo: boolean;
    wildCaughtYes: boolean;
    wildCaughtNo: boolean;
    aquacultureArea: string;
    catchArea: string;
    harvestingArea: string;
    vesselName: string;
    enterpriseName: string;
    enterpriseRegistrationNo: string;
    productionDate: string | Date | null;
    consignorName: string;
    consignorAddress: string;
    consigneeName: string;
    consigneeAddress: string;
    placeOfDispatch: string;
    placeOfDestination: string;
    meansOfTransport: string;
    vesselNameTransport: string;
    flightNumber: string;
    otherTransportMeans: string;
    containerNumber: string;
    sealNumber: string;
    placeOfIssue: string;
    dateOfIssue: string | Date | null;
    officialStamp?: string;
    officialSignature?: string;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType?: string;
}

export interface TwCertificateResponse {
    id: number;
}

export interface CreateUaCertificateProductPayload {
    species: string;
    natureOfCommodity: string;
    treatmentApprovalNumber: string;
    manufacturingPlant: string;
    numberOfPackaging: string;
    typeOfPackaging: string;
    netWeight: string;
}

export interface CreateUaCertificatePayload {
    certificateRequestId?: number | null;
    consignorName: string;
    consignorAddress: string;
    consignorPostalCode: string;
    consignorTelNo: string;
    certificateReferenceNumber: string;
    centralCompetentAuthority: string;
    localCompetentAuthority: string;
    consigneeName: string;
    consigneeAddress: string;
    consigneePostalCode: string;
    consigneeTel: string;
    personResponsibleName: string;
    personResponsibleAddress: string;
    personResponsiblePostalCode: string;
    personResponsibleTel: string;
    countryOfOriginName: string;
    countryOfOriginISO: string;
    countryOfOriginISOCode: string;
    countryOfOriginZone: string;
    zoneOrigin: string;
    zoneOriginCode: string;
    countryDestinationName: string;
    countryDestinationISO: string;
    countryDestinationISOCode: string;
    countryDestinationZone: string;
    zoneDestination: string;
    zoneDestinationCode: string;
    placeOriginName: string;
    placeOriginApprovalNumber: string;
    placeOriginAddress: string;
    field112: string;
    placeLoadingAddress: string;
    dateOfDeparture: string | Date | null;
    transportAeroplane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    transportIdentification: string;
    transportDocumentReferences: string;
    entryBIPUkraine: string;
    descriptionOfCommodity: string;
    commodityCodeHS: string;
    quantity: string;
    temperatureAmbient: boolean;
    temperatureChilled: boolean;
    temperatureFrozen: boolean;
    numberOfPackages: string;
    sealContainerNo: string;
    typeOfPackaging: string;
    commoditiesHumanConsumption: boolean;
    field126: string;
    forImportIntoUkraine: string;
    products: CreateUaCertificateProductPayload[];
    healthInfoNotes: string;
    healthCertificateReferenceNumber: string;
    additionalInformation?: string;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    officialStamp?: string;
    officialSignature?: string;
    certifiedDate: string | Date | null;
    certificateType?: string;
}

export interface UaCertificateResponse {
    id: number;
}

export interface CreateUkCertificateProductPayload {
    no: string;
    codeCNTitle: string;
    species: string;
    natureOfCommodity: string;
    treatmentType: string;
    vesselPlant: string;
    numberOfPackages: string;
    netWeight: string;
    batchNo: string;
    typeOfPackaging: string;
}

export interface CreateUkCertificatePayload {
    certificateRequestId?: number | null;
    certificateReferenceNo: string;
    consignorName: string;
    consignorAddress: string;
    consignorTel: string;
    consigneeName: string;
    consigneeAddress: string;
    consigneeTel: string;
    operatorName: string;
    operatorAddress: string;
    operatorTel: string;
    countryOfOrigin: string;
    countryOfOriginISO: string;
    regionOfOrigin: string;
    regionOfOriginCode: string;
    countryOfDestination: string;
    countryOfDestinationISO: string;
    regionOfDestination: string;
    regionOfDestinationCode: string;
    placeOfDispatchName: string;
    placeOfDispatchApprovalNo: string;
    placeOfDispatchAddress: string;
    placeOfDestinationName: string;
    placeOfDestinationAddress: string;
    placeOfLoading: string;
    dateOfDeparture: string | Date | null;
    timeOfDeparture: string;
    transportAeroplane: boolean;
    transportVessel: boolean;
    transportRailway: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    transportIdentification: string;
    entryBCP: string;
    accompDocType: string;
    accompDocNo: string;
    tempAmbient: boolean;
    tempChilled: boolean;
    tempFrozen: boolean;
    containerSealNo: string;
    goodsCanningIndustry: boolean;
    goodsHumanConsumption: boolean;
    field21: string;
    field22: string;
    totalNumberOfPackages: string;
    totalNetWeight: string;
    totalGrossWeight: string;
    finalConsumer: boolean;
    // Animal Health Strikethroughs
    strikeAnimalHealthAll?: boolean;
    strikeAhT153?: boolean;
    strikeAhT154?: boolean;
    strikeAhT155?: boolean;
    strikeAhT155_Either?: boolean;
    strikeAhT155_D_Bkd?: boolean;
    strikeAhT155_D_SvcGs?: boolean;
    strikeAhT155_D_SvcBkd?: boolean;
    strikeAhT155_GsSalinity?: boolean;
    strikeAhT155_GsEggs?: boolean;
    strikeAhP502?: boolean;

    signatoryName: string;
    qualification: string;
    certifiedDate: string | Date | null;
    products: CreateUkCertificateProductPayload[];
    signatoryUserId?: string | null;
}

export interface UkCertificateResponse {
    id: number;
}

export interface CreateUsaCertificatePayload {
    certificateRequestId?: number | null;
    myRef: string;
    yourRef: string;
    date: string | Date | null;
    itemName: string;
    numberOfPackages: string;
    netWeight: string;
    processingPlantName?: string | null;
    processingPlantAddress?: string | null;
    competentAuthorityRegNo?: string | null;
    consignorName: string;
    consignorAddress: string;
    consigneeName: string;
    consigneeAddress: string;
    despatchFrom: string;
    despatchTo: string;
    despatchByShip: string;
    signatoryName: string;
    designation?: string | null;
    qualification: string;
    signatoryUserId?: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    certificateType?: string | null;
    productsAttachment?: any[];
}

export interface UsaCertificateResponse {
    id: number;
}

// ---- GET (view) response interfaces ----

export interface IlCertificateProductView {
    id: number;
    descriptionOfCommodity: string | null;
    speciesScientificName: string | null;
    natureOfCommodity: string | null;
    treatmentType: string | null;
    approvalNo: string | null;
    numberOfPackages: number | null;
    netWeight: number | null;
    harvestingDate: string | null;
    productionDate: string | null;
    bestBefore: string | null;
    lotNo: string | null;
}

export interface IlCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    certificateType?: string | null;
    certificationNo: string | null;
    centralCompetentAuthority: string | null;
    centralCompetentAuthorityEmail: string | null;
    localCompetentAuthority: string | null;
    countryOfOrigin: string | null;
    placeOfOriginName: string | null;
    placeOfOriginAddress: string | null;
    placeOfOriginApprovalNo: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    postalCodeConsignor?: string | null;
    telNoConsignor?: string | null;
    emailConsignor?: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    postalCodeConsignee?: string | null;
    telNoConsignee?: string | null;
    emailConsignee?: string | null;
    placeOfLoading?: string | null;
    portOfEntry?: string | null;
    dateOfArrival: string | null;
    placeOfArrival: string | null;
    placeOfArrivalAddress: string | null;
    placeOfDestinationName: string | null;
    placeOfDestinationAddress: string | null;
    placeOfDestinationApprovalNo: string | null;
    dateOfContainerization: string | null;
    dateOfDeparture: string | null;
    transportSea: boolean | null;
    transportAir: boolean | null;
    transportRail: boolean | null;
    transportRoad: boolean | null;
    transportOther?: boolean | null;
    billOfLading?: string | null;
    awb?: string | null;
    meansOfTransportIdentification: string | null;
    containerNo: string | null;
    sealNo: string | null;
    meansOfTransportReference: string | null;
    entryBIP: string | null;
    readyToEat: string | null;
    nonReadyToEat: string | null;
    shipmentNumber?: string | null;
    remarks: string | null;

    // Signature fields
    placeOfIssue?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    signatureDate?: string | null;
    stamp?: string | null;
    signature?: string | null;
    signatoryUserId?: string | null;

    products: IlCertificateProductView[];
}

export interface AuCertificateProductView {
    id: number;
    speciesScientificName: string | null;
    natureOfCommodity: string | null;
    treatmentType: string | null;
    approvalNumberOfEstablishments: string | null;
    manufacturingPlant: string | null;
    numberOfPackages: number | null;
    netWeight: number | null;
}

export interface AuCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consignorPostal: string | null;
    consignorTel: string | null;
    certRefNumber: string | null;
    certRefNumberA: string | null;
    centralCompetentAuthority: string | null;
    localCompetentAuthority: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    consigneePostal: string | null;
    consigneeTel: string | null;
    consignee6: string | null;
    countryOrigin: string | null;
    countryOriginISO: string | null;
    regionOrigin: string | null;
    regionOriginISO: string | null;
    countryDestination: string | null;
    countryDestinationISO: string | null;
    countryDestination110: string | null;
    placeOfOriginName: string | null;
    placeOfOriginAddress: string | null;
    placeOfOriginApprovalNo: string | null;
    countryDestination112: string | null;
    placeOfLoading: string | null;
    dateOfDeparture: string | null;
    transportAeroPlane: boolean | null;
    transportShip: boolean | null;
    transportRailwayWagon: boolean | null;
    transportRoadVehicle: boolean | null;
    transportOther: boolean | null;
    docReferences: string | null;
    entryBIP: string | null;
    field117: string | null;
    descCommon: string | null;
    hsCode: string | null;
    quantity: string | null;
    temperatureAmbient: boolean | null;
    temperatureChilled: boolean | null;
    temperatureFrozen: boolean | null;
    numPackages: string | null;
    containerId: string | null;
    packagingType: string | null;
    forHumanConsumption: boolean | null;
    field126: string | null;
    forImportEU: string | null;
    healthCertNo: string | null;
    healthCertNoB: string | null;
    exportApprovalNumber: string | null;
    signatoryUserId?: string | null;
    signatoryName: string | null;
    qualification: string | null;
    signatureDate: string | null;
    stamp: string | null;
    signature: string | null;
    certificateType: string | null;
    products: AuCertificateProductView[];
}

export interface AmPreExportCertificateView {
    id: number;
    date: string | null;
    number: string | null;
    countryOfOrigin: string | null;
    administrativeTerritory: string | null;
    approvalNumber: string | null;
    productNameAndQuantity: string | null;
}

export interface AmAttachmentView {
    id: number;
    product: string | null;
    numberOfKgs: number | null;
    numberOfBoxes: number | null;
}

export interface AmCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consignorPostal: string | null;
    consignorTel: string | null;
    certRefNumber: string | null;
    certRefNumberA: string | null;
    centralCompetentAuthority: string | null;
    localCompetentAuthority: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    consigneePostal: string | null;
    consigneeTel: string | null;
    consignee6: string | null;
    countryOrigin: string | null;
    countryOriginISO: string | null;
    regionOrigin: string | null;
    regionOriginISO: string | null;
    countryDestination: string | null;
    countryDestinationISO: string | null;
    countryDestination110: string | null;
    placeOfOriginName: string | null;
    placeOfOriginAddress: string | null;
    placeOfOriginApprovalNo: string | null;
    countryDestination112: string | null;
    placeOfLoading: string | null;
    dateOfDeparture: string | null;
    transportAeroPlane: boolean | null;
    transportShip: boolean | null;
    transportRailwayWagon: boolean | null;
    transportRoadVehicle: boolean | null;
    transportOther: boolean | null;
    transportId: string | null;
    entryBIP: string | null;
    field117: string | null;
    descCommon: string | null;
    hsCode: string | null;
    quantity: string | null;
    temperatureAmbient: boolean | null;
    temperatureChilled: boolean | null;
    temperatureFrozen: boolean | null;
    numPackages: string | null;
    containerId: string | null;
    packagingType: string | null;
    forHumanConsumption: boolean | null;
    field126: string | null;
    forImportEU: string | null;
    processingEstName: string | null;
    processingEstAddress: string | null;
    processingEstRegNo: string | null;
    healthCertNo: string | null;
    healthCertNoB: string | null;
    certificateNo: string | null;
    countryIssuing: string | null;
    competentAuthorityExporting: string | null;
    organizationIssuing: string | null;
    countryOfTransit: string | null;
    pointOfCrossingBorder: string | null;
    productName: string | null;
    productionDate: string | null;
    netWeight: string | null;
    numberOfSeal: string | null;
    identificationMarks: string | null;
    storageConditions: string | null;
    factoryVessel: string | null;
    coldStore: string | null;
    administrativeUnit: string | null;
    placeOfIssue: string | null;
    dateOfIssue: string | null;
    dateOfAttachment: string | null;
    identificationMarksAttachment: string | null;
    exportApprovalNumber: string | null;
    signatoryUserId?: string | null;
    signatoryName: string | null;
    qualification: string | null;
    signatureDate: string | null;
    stamp: string | null;
    signature: string | null;
    preExportCertificates: AmPreExportCertificateView[];
    attachments: AmAttachmentView[];
}

export interface BrCertificateProductView {
    id: number;
    nameOfTheProduct: string | null;
    scientificName: string | null;
    typeOfPackaging: string | null;
    numberOfPackages: number | null;
    netWeight: number | null;
}

export interface BrCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    refNumber: string | null;
    countryOfExport: string | null;
    certificateNo: string | null;
    competentAuthority: string | null;
    localCompetentAuthority: string | null;
    exporterName: string | null;
    exporterAddress: string | null;
    importerName: string | null;
    importerAddress: string | null;
    countryOrigin: string | null;
    countryOriginISO: string | null;
    countryOfDestination: string | null;
    countryDestinationISO: string | null;
    placeOfLoading: string | null;
    transportAeroPlane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    declaredPointOfEntry: string | null;
    conditionsForTransportStorage: string | null;
    identificationOfContainers: string | null;
    identificationOfFoodProducts: string | null;
    producerDetails: string | null;
    hsCode: string | null;
    intendedPurpose: string | null;
    totalNetWeight: number;
    placeAndDate: string | null;
    dateOfIssue: string | null;
    officialStamp: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    modeloConformeCircularNo: string | null;
    sanitaryCertification: string | null;
    products: BrCertificateProductView[];
}

export interface ChAttachmentView {
    id: number;
    chCertificateId: number;
    product: string | null;
    netWeight: number | null;
    numberOfBoxes: number | null;
}

export interface ChCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    certificateType: string | null;
    refNumber: string | null;
    countryOfExport: string | null;
    countryOfProduction: string | null;
    competentAuthority: string | null;
    departmentOfIssuance: string | null;
    commodityName: string | null;
    scientificName: string | null;
    latinName: string | null;
    number: string | null;
    numberOfPackages: string | null;
    netWeight: string | null;
    productionDate: string | null;
    lotNumber: string | null;
    originRawMaterialsCountry: string | null;
    processingType: string | null;
    productionMode: string | null;
    aquacultured: boolean | null;
    wildCaughtBool: boolean | null;
    productiveWaterArea: string | null;
    aquacultureArea: string | null;
    catchArea: string | null;
    artificialCulture: string | null;
    wildCaught: string | null;
    aquacultureFarmApprovedReg: string | null;
    fishingVessel: string | null;
    fishingAndFactoryVessel: string | null;
    transportFishingVessel: string | null;
    processingPlantNameAddress: string | null;
    processingPlantRegNo: string | null;
    coldStorageRawMaterials: string | null;
    coldStorageProducts: string | null;
    packagingEnterpriseName: string | null;
    packagingEnterpriseAddress: string | null;
    packagingEnterpriseRegNumber: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    placeOfDispatch: string | null;
    placeOfDestination: string | null;
    meansOfTransport: string | null;
    nameOfVessel: string | null;
    flightNumber: string | null;
    otherTransportMeans: string | null;
    containerNumber: string | null;
    sealNumber: string | null;
    dateOfDeparture: string | null;
    portOfDeparture: string | null;
    transportAeroPlane: boolean | null;
    transportShip: boolean | null;
    transportRailwayWagon: boolean | null;
    transportRoadVehicle: boolean | null;
    transportOther: boolean | null;
    identificationDocumentReferences: string | null;
    exporterName: string | null;
    exporterAddress: string | null;
    importerName: string | null;
    importerAddress: string | null;
    placeOfIssue: string | null;
    dateOfIssue: string | null;
    officialStamp: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    dateOfAttachment?: string | null;
    identificationMarksAttachment?: string | null;
    attachments: ChAttachmentView[];
}

export interface HkCertificateProductView {
    id: number;
    description: string | null;
    species: string | null;
    processingType: string | null;
    packagingType: string | null;
    lotCode: string | null;
    numberOfPackages: number | null;
    packagesUnit?: string | null;
    netWeight: number | null;
    netWeightUnit?: string | null;
}

export interface HkCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    certificateType: string | null;
    identificationNumber: string | null;
    countryOfDispatch: string | null;
    competentAuthority: string | null;
    certifyingBody: string | null;
    containerNumber?: string | null;
    sealNumber?: string | null;
    sealIdentificationNumber: string | null;
    storageTemperature: string | null;
    approvalNumber?: string | null;
    processingEstablishment?: string | null;
    provenanceDetails: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    placeOfDispatch: string | null;
    destinationCountryPlace: string | null;
    meansOfTransport: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    dateOfAttachment?: string | null;
    attachmentRegNo?: string | null;
    placeOfIssue: string | null;
    dateOfIssue: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    officialSignature?: string | null;
    officerTel: string | null;
    officerFax: string | null;
    officerEmail: string | null;
    products: HkCertificateProductView[];
}

export interface IdCertificateProductView {
    id: number;
    no: string | null;
    commonName: string | null;
    scientificName: string | null;
    hsCode: string | null;
    quantity: number | null;
    unit: string | null;
}

export interface IdCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    numberNomor: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    competentAuthority: string | null;
    establishmentAquaculture: boolean;
    establishmentProcessing: boolean;
    establishmentOther: boolean;
    establishmentName: string | null;
    establishmentRegNo: string | null;
    establishmentAddress: string | null;
    countryRegionOrigin: string | null;
    sourceFarmRaised: boolean;
    sourceWildCaught: boolean;
    portOfShipment: string | null;
    transportAir: boolean;
    transportSea: boolean;
    transportRoad: boolean;
    commodityDescription: string | null;
    tempAmbient: boolean;
    tempFrozen: boolean;
    tempChilled: boolean;
    intendedHumanConsumption: boolean;
    intendedCultureBreeding: boolean;
    intendedTrade: boolean;
    intendedResearch: boolean;
    intendedFishFeed: boolean;
    intendedExhibition: boolean;
    intendedOther: boolean;
    totalPackages: string | null;
    packagingType: string | null;
    totalQuantityKg: string | null;
    containerSealNumber: string | null;
    portOfDestination: string | null;
    transportVesselName: string | null;
    transportVoyageNumber: string | null;
    dateOfDeparture: string | null;
    testingLaboratory: string | null;
    laboratoryAddress: string | null;
    approvingOfficerName: string | null;
    testResultNumber: string | null;
    attestationRefNumber: string | null;
    attestFinfish: boolean;
    attestMollusca: boolean;
    attestCrustacea: boolean;
    attestFisheryProducts: boolean;
    attestOther: boolean;
    attestClauseA: boolean;
    attestClauseB: boolean;
    attestClauseC: boolean;
    attestClauseCCrustacean: boolean;
    attestClauseCCyprinidae: boolean;
    attestClauseCTilapia: boolean;
    attestClauseCCatfish: boolean;
    attestClauseCOtherFish: boolean;
    attestClauseCVisibleSigns: boolean;
    attestClauseCPackagedContainers: boolean;
    attestClauseD: boolean;
    attestClauseE: boolean;
    additionalInformation: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certifiedIssuedAt: string | null;
    certifiedDate: string | null;
    certifiedPosition?: string | null;
    certifiedPhone: string | null;
    certifiedFax: string | null;
    certifiedEmail: string | null;
    certifiedAddress?: string | null;
    products: IdCertificateProductView[];
}

export interface IndCertificateProductView {
    id: number;
    nameOfProduct: string | null;
    lotNo: string | null;
    typeOfPackaging: string | null;
    numberOfPackages: number | null;
    netWeight: number | null;
}

export interface IndCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    certificateType: string | null;
    countryOfDispatch: string | null;
    certificateNumber: string | null;
    myRef: string | null;
    yourRef: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consignorTel: string | null;
    competentAuthorityDetails: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    consigneeTel: string | null;
    countryOfOrigin: string | null;
    countryOfOriginIso: string | null;
    countryOfDestination: string | null;
    countryOfDestinationIso: string | null;
    placeOfLoading: string | null;
    meansOfTransport: string | null;
    declaredPointOfEntry: string | null;
    conditionsForTransportStorage: string | null;
    totalQuantity: string | null;
    invoiceNoDate: string | null;
    foodDescription: string | null;
    intendedPurpose: string | null;
    producerNameAddress: string | null;
    approvalNumberDetails: string | null;
    dateOfManufacture: string | null;
    bestBefore: string | null;
    dateOfExpiry: string | null;
    itemDescription?: string | null;
    numberOfPackagesStr?: string | null;
    netWeightStr?: string | null;
    processingPlantNameAddress?: string | null;
    processingPlantRegNo?: string | null;
    dispatchFrom?: string | null;
    dispatchTo?: string | null;
    modeOfTransport?: string | null;
    hsCode?: string | null;
    speciesName?: string | null;
    previousCertRef?: string | null;
    consignmentIdentificationDetails?: string | null;
    productDescription?: string | null;
    attestationPlace: string | null;
    attestationDate: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    authorizedOfficialDate: string | null;
    authorizedOfficialSignature: string | null;
    officialStamp: string | null;
    products: IndCertificateProductView[];
}

export interface JpCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    myRef: string | null;
    yourRef: string | null;
    date: string | null;
    itemName: string | null;
    numberOfPackages: string | null;
    netWeight: string | null;
    processingPlantName?: string | null;
    processingPlantAddress?: string | null;
    competentAuthorityRegNo?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    despatchFrom: string | null;
    despatchTo: string | null;
    despatchByShip: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType: string | null;
}

export interface KwCertificateProductView {
    id: number;
    nameDescription: string | null;
    hsCodes: string | null;
    treatmentDerivedFrom: string | null;
    brandName: string | null;
    productionDate: string | null;
    expiryDate: string | null;
    numberPackages: number;
    batchLotNo: string | null;
    totalWeight: number;
}

export interface KwCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    certificateReferenceNo: string | null;
    placeOfIssue: string | null;
    dateOfIssue: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    competentAuthority: string | null;
    competentAuthorityAddress: string | null;
    countryOfOrigin: string | null;
    countryOfOriginIso: string | null;
    countryOfDestination: string | null;
    countryOfDestinationIso: string | null;
    producerName: string | null;
    producerAddress: string | null;
    packingEstName: string | null;
    packingEstAddress: string | null;
    packingEstApprovalNo?: string | null;
    borderOfEntry: string | null;
    borderLoadingCountry: string | null;
    borderLoadingPlace: string | null;
    transportByAir: boolean;
    transportBySea: boolean;
    vehicleIdentificationNo: string | null;
    tempChilled: boolean;
    tempFrozen: boolean;
    commoditiesOther: boolean;
    commoditiesAfterFurtherProcess: boolean;
    commoditiesHumanConsumption: boolean;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    officialStamp: string | null;
    officialSignature?: string | null;
    signatureDate?: string | null;
    certificateType?: string | null;
    products: KwCertificateProductView[];
}

export interface MyCertificateProductView {
    id: number;
    hsCode: string | null;
    description: string | null;
    scientificName: string | null;
    batchCode: string | null;
    numberOfPackages: number;
    netWeight: number;
}

export interface MyCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    exporterName: string | null;
    certificateReferenceNo: string | null;
    qualityCertificateNo: string | null;
    competentAuthority: string | null;
    localAuthority: string | null;
    importerDetails: string | null;
    countryOfOrigin: string | null;
    countryOfOriginIso: string | null;
    countryOfDestination: string | null;
    countryOfDestinationIso: string | null;
    processingEstablishment: string | null;
    authorizationNo: string | null;
    placeOfLoading: string | null;
    transportAir: boolean;
    transportShip: boolean;
    transportRail: boolean;
    transportRoad: boolean;
    transportOther: boolean;
    portOfEntry: string | null;
    transportCompany: string | null;
    conditionAmbient: boolean;
    conditionChilled: boolean;
    conditionFrozen: boolean;
    containerSealIdentification: string | null;
    invoiceNo: string | null;
    transitCountry: string | null;
    departureDate: string | null;
    certifyingOfficialDate?: string | null;
    certificateReferenceNoPage2: string | null;
    productBrand: string | null;
    originFisheries: boolean;
    originAquaculture: boolean;
    certifiedProductFor: string | null;
    treatmentType: string | null;
    certificateReferenceNoPage3: string | null;
    additionalInformation: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    signatoryUserId?: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType?: string | null;
    products: MyCertificateProductView[];
}

export interface NzCertificateProductView {
    id: number;
    productName: string | null;
    aquaticAnimalSpecies: string | null;
    productionDate?: string | null;
    numberOfPackages: number;
    netWeightKg: number;
    hsCode: string | null;
}

export interface NzCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    certificateRefNumber: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    countryOfOrigin: string | null;
    countryOfDestination: string | null;
    processorName: string | null;
    processorAddress: string | null;
    processorEstablishmentNumber: string | null;
    portDispatchedFrom: string | null;
    dateOfDeparture: string | null;
    competentAuthority: string | null;
    meansOfTransport: string | null;
    transportAeroplan: boolean;
    transportShip: boolean;
    temperatureOfCommodities: string | null;
    containerNumber: string | null;
    officialSealNumber: string | null;
    officialStamp: string | null;
    officialSignature?: string | null;
    signatureDate: string | null;
    signatoryUserId: string | null;
    signatoryName?: string | null;
    qualification?: string | null;
    certificateType?: string | null;
    products: NzCertificateProductView[];
}

export interface RuPreExportCertificateView {
    id: number;
    date: string | null;
    number: string | null;
    countryOfOrigin: string | null;
    administrativeTerritory: string | null;
    approvalNumber: string | null;
    productNameAndQuantity: string | null;
}

export interface RuAttachmentView {
    id: number;
    product: string | null;
    numberOfKgs: number | null;
    numberOfBoxes: number | null;
}

export interface RuCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    transportAeroPlane: boolean | null;
    transportShip: boolean | null;
    transportRailwayWagon: boolean | null;
    transportRoadVehicle: boolean | null;
    transportOther: boolean | null;
    transportId: string | null;
    countryOfTransit: string | null;
    certificateNo: string | null;
    countryOrigin: string | null;
    countryIssuing: string | null;
    competentAuthorityExporting: string | null;
    organizationIssuing: string | null;
    pointOfCrossingBorder: string | null;
    productName: string | null;
    productionDate: string | null;
    packagingType: string | null;
    numPackages: string | null;
    netWeight: string | null;
    numberOfSeal: string | null;
    identificationMarks: string | null;
    storageConditions: string | null;
    processingEstName: string | null;
    processingEstRegNo: string | null;
    processingEstAddress: string | null;
    factoryVessel: string | null;
    coldStore: string | null;
    administrativeUnit: string | null;
    placeOfIssue: string | null;
    dateOfIssue: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    signatoryName: string | null;
    qualification: string | null;
    certificateType?: string | null;
    dateOfAttachment: string | null;
    identificationMarksAttachment: string | null;
    preExportCertificates: RuPreExportCertificateView[];
    attachments: RuAttachmentView[];
    signatoryUserId?: string | null;
}

export interface KzCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    transportAeroPlane: boolean | null;
    transportShip: boolean | null;
    transportRailwayWagon: boolean | null;
    transportRoadVehicle: boolean | null;
    transportOther: boolean | null;
    transportId: string | null;
    countryOfTransit: string | null;
    certificateNo: string | null;
    countryOrigin: string | null;
    countryIssuing: string | null;
    competentAuthorityExporting: string | null;
    organizationIssuing: string | null;
    pointOfCrossingBorder: string | null;
    productName: string | null;
    productionDate: string | null;
    packagingType: string | null;
    numPackages: string | null;
    netWeight: string | null;
    numberOfSeal: string | null;
    identificationMarks: string | null;
    storageConditions: string | null;
    processingEstName: string | null;
    processingEstRegNo: string | null;
    processingEstAddress: string | null;
    factoryVessel: string | null;
    coldStore: string | null;
    administrativeUnit: string | null;
    placeOfIssue: string | null;
    dateOfIssue: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    signatoryName: string | null;
    qualification: string | null;
    certificateType?: string | null;
    dateOfAttachment: string | null;
    identificationMarksAttachment: string | null;
    preExportCertificates: RuPreExportCertificateView[];
    attachments: RuAttachmentView[];
    signatoryUserId?: string | null;
}

export interface TwCertificateProductView {
    id: number;
    commodityName: string | null;
    hsCode: string | null;
    scientificName: string | null;
    numberOfPackages: number;
    netWeight: number;
}

export interface TwCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    referenceNo: string | null;
    countryOfExport: string | null;
    countryOfProduction: string | null;
    competentAuthority: string | null;
    departmentIssuance: string | null;
    productionPlace: string | null;
    processingType: string | null;
    productionMode: string | null;
    aquaculturedYes: boolean;
    aquaculturedNo: boolean;
    wildCaughtYes: boolean;
    wildCaughtNo: boolean;
    aquacultureArea: string | null;
    catchArea: string | null;
    harvestingArea: string | null;
    vesselName: string | null;
    enterpriseName: string | null;
    enterpriseRegistrationNo: string | null;
    productionDate: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    placeOfDispatch: string | null;
    placeOfDestination: string | null;
    meansOfTransport: string | null;
    vesselNameTransport: string | null;
    flightNumber: string | null;
    otherTransportMeans: string | null;
    containerNumber: string | null;
    sealNumber: string | null;
    placeOfIssue: string | null;
    dateOfIssue: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    signatoryName: string | null;
    qualification: string | null;
    certificateType?: string | null;
    products: TwCertificateProductView[];
    signatoryUserId?: string | null;
}

export interface UaCertificateProductView {
    id: number;
    species: string | null;
    natureOfCommodity: string | null;
    treatmentApprovalNumber: string | null;
    approvalNumberEstablishments?: string | null;
    manufacturingPlant: string | null;
    numberOfPackaging: string | null;
    typeOfPackaging: string | null;
    netWeight: string | null;
}

export interface UaCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consignorPostalCode: string | null;
    consignorTelNo: string | null;
    certificateReferenceNumber: string | null;
    centralCompetentAuthority: string | null;
    localCompetentAuthority: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    consigneePostalCode: string | null;
    consigneeTel: string | null;
    personResponsibleName: string | null;
    personResponsibleAddress: string | null;
    personResponsiblePostalCode: string | null;
    personResponsibleTel: string | null;
    countryOfOriginName: string | null;
    countryOfOriginISO: string | null;
    countryOfOriginISOCode: string | null;
    countryOfOriginZone: string | null;
    zoneOrigin: string | null;
    zoneOriginCode: string | null;
    countryDestinationName: string | null;
    countryDestinationISO: string | null;
    countryDestinationISOCode: string | null;
    countryDestinationZone: string | null;
    zoneDestination: string | null;
    zoneDestinationCode: string | null;
    placeOriginName: string | null;
    placeOriginApprovalNumber: string | null;
    placeOriginAddress: string | null;
    field112: string | null;
    placeLoadingAddress: string | null;
    dateOfDeparture: string | null;
    transportAeroplane: boolean;
    transportShip: boolean;
    transportRailwayWagon: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    transportIdentification: string | null;
    transportDocumentReferences: string | null;
    entryBIPUkraine: string | null;
    descriptionOfCommodity: string | null;
    commodityCodeHS: string | null;
    quantity: string | null;
    temperatureAmbient: boolean;
    temperatureChilled: boolean;
    temperatureFrozen: boolean;
    numberOfPackages: string | null;
    sealContainerNo: string | null;
    typeOfPackaging: string | null;
    commoditiesHumanConsumption: boolean;
    field126: string | null;
    forImportIntoUkraine: string | null;
    healthInfoNotes: string | null;
    healthCertificateReferenceNumber: string | null;
    additionalInformation: string | null;
    signatoryName: string | null;
    qualification: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    certifiedDate: string | null;
    certificateType?: string | null;
    products: UaCertificateProductView[];
    signatoryUserId?: string | null;
}

export interface UkCertificateProductView {
    id: number;
    no: string | null;
    codeCNTitle: string | null;
    species: string | null;
    natureOfCommodity: string | null;
    treatmentType: string | null;
    vesselPlant: string | null;
    coldStore?: string | null;
    numberOfPackages: string | null;
    netWeight: string | null;
    batchNo: string | null;
    typeOfPackaging: string | null;
}

export interface UkCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    certificateReferenceNo: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consignorTel: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    consigneeTel: string | null;
    operatorName: string | null;
    operatorAddress: string | null;
    operatorTel: string | null;
    countryOfOrigin: string | null;
    countryOfOriginISO: string | null;
    regionOfOrigin: string | null;
    regionOfOriginCode: string | null;
    countryOfDestination: string | null;
    countryOfDestinationISO: string | null;
    regionOfDestination: string | null;
    regionOfDestinationCode: string | null;
    placeOfDispatchName: string | null;
    placeOfDispatchApprovalNo: string | null;
    placeOfDispatchAddress: string | null;
    placeOfDestinationName: string | null;
    placeOfDestinationAddress: string | null;
    placeOfLoading: string | null;
    dateOfDeparture: string | null;
    timeOfDeparture: string | null;
    transportAeroplane: boolean;
    transportVessel: boolean;
    transportRailway: boolean;
    transportRoadVehicle: boolean;
    transportOther: boolean;
    transportIdentification: string | null;
    entryBCP: string | null;
    accompDocType: string | null;
    accompDocNo: string | null;
    tempAmbient: boolean;
    tempChilled: boolean;
    tempFrozen: boolean;
    containerSealNo: string | null;
    goodsCanningIndustry: boolean;
    goodsHumanConsumption: boolean;
    field21: string | null;
    field22: string | null;
    totalNumberOfPackages: string | null;
    totalNetWeight: string | null;
    totalGrossWeight: string | null;
    finalConsumer: boolean;
    // Animal Health Strikethroughs
    strikeAnimalHealthAll?: boolean;
    strikeAhT153?: boolean;
    strikeAhT154?: boolean;
    strikeAhT155?: boolean;
    strikeAhT155_Either?: boolean;
    strikeAhT155_D_Bkd?: boolean;
    strikeAhT155_D_SvcGs?: boolean;
    strikeAhT155_D_SvcBkd?: boolean;
    strikeAhT155_GsSalinity?: boolean;
    strikeAhT155_GsEggs?: boolean;
    strikeAhP502?: boolean;

    signatoryName: string | null;
    qualification: string | null;
    certifiedDate: string | null;
    products: UkCertificateProductView[];
    signatoryUserId?: string | null;
}

export interface MvCertificateProductPayload {
    no: string;
    natureOfCommodity: string;
    species: string;
    purposeOfUse: string;
}

export interface CreateMvCertificatePayload {
    certificateRequestId?: number | null;
    consignorExporter: string;
    certificateNumber: string;
    competentAuthority: string;
    certifyingBody: string;
    consigneeImporter: string;
    countryOfOrigin: string;
    countryOfOriginISO: string;
    competentAuthorityOrigin?: string;
    regionOfOrigin?: string;
    regionOfOriginCode?: string;
    placeOfDispatch?: string;
    placeOfOrigin?: string;
    placeOfLoading: string;
    meansOfTransport?: string;
    meansOfTransportNo?: string;
    pointsOfEntry: string;
    conditionsOfStorage: string;
    conditionsOfStorageOther?: string;
    estimatedDateOfDeparture?: string | Date | null;
    numberOfPackages?: number;
    totalNumberOfPackages?: string;
    netWeight?: number;
    totalQuantity?: string;
    grossWeight?: number;
    sealNumber: string;
    containerNumber?: string;
    descriptionOfCommodity: string;
    commoditiesFor?: string;
    commoditiesForOther?: string;
    certifyingOfficerName: string;
    certifyingOfficerDate: string | Date | null;
    signatoryUserId?: string | null;
    signatoryName?: string;
    qualification?: string;
    companyRegistrationNo?: string;
    officialStamp?: string | null;
    officialSignature?: string | null;
    certificateType?: string | null;
    products: MvCertificateProductPayload[];
    productsSecond: MvCertificateSecondProductPayload[];
    productsAttachment?: MvCertificateProductAttachmentPayload[];
}

export interface MvCertificateProductAttachmentPayload {
    product: string;
    lotIdentifier: string;
    typeOfPackaging: string;
    numberOfKgs: string;
    numberOfBoxes: number;
}

export interface MvCertificateSecondProductPayload {
    no: string;
    nameOfTheProduct: string;
    lotIdentifier: string;
    typeOfPackaging: string;
    numberOfPackages: number;
    netWeight: number;
}

export interface MvCertificateProductView {
    id: number;
    no: string;
    natureOfCommodity: string;
    species: string;
    purposeOfUse: string;
}

export interface MvCertificateProductSecondView {
    id: number;
    no: string;
    nameOfTheProduct: string;
    lotIdentifier: string;
    typeOfPackaging: string;
    numberOfPackages: number;
    netWeight: number;
}

export interface MvCertificateProductAttachmentView {
    id: number;
    product: string;
    lotIdentifier: string;
    typeOfPackaging: string;
    numberOfKgs: string;
    numberOfBoxes: number;
}

export interface MvCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    consignorExporter: string;
    certificateNumber: string;
    competentAuthority: string;
    certifyingBody: string;
    consigneeImporter: string;
    countryOfOrigin: string;
    countryOfOriginISO: string;
    competentAuthorityOrigin?: string;
    regionOfOrigin?: string;
    regionOfOriginCode?: string;
    placeOfDispatch?: string;
    placeOfOrigin?: string;
    placeOfLoading: string;
    meansOfTransport?: string;
    meansOfTransportNo?: string;
    pointsOfEntry: string;
    conditionsOfStorage: string;
    conditionsOfStorageOther?: string;
    estimatedDateOfDeparture?: string | null;
    numberOfPackages?: number;
    totalNumberOfPackages?: string;
    netWeight?: number;
    totalQuantity?: string;
    grossWeight?: number;
    sealNumber: string;
    containerNumber?: string;
    descriptionOfCommodity: string;
    commoditiesFor?: string;
    commoditiesForOther?: string;
    certifyingOfficerName: string;
    certifyingOfficerDate: string | null;
    signatoryUserId?: string | null;
    signatoryName: string;
    qualification: string;
    companyRegistrationNo?: string;
    officialStamp?: string | null;
    officialSignature?: string | null;
    certificateType?: string | null;
    products: MvCertificateProductView[];
    productsSecond: MvCertificateProductSecondView[];
    productsAttachment?: MvCertificateProductAttachmentView[];
}

export interface MvCertificateResponse {
    id: number;
    certificateRequestId: number | null;
    createdAt: string;
}

export interface UsaCertificateView {
    id: number;
    certificateRequestId: number | null;
    referenceNumber?: string | null;
    myRef: string | null;
    yourRef: string | null;
    date: string | null;
    itemName: string | null;
    numberOfPackages: string | null;
    netWeight: string | null;
    processingPlantName: string | null;
    processingPlantAddress: string | null;
    competentAuthorityRegNo: string | null;
    consignorName: string | null;
    consignorAddress: string | null;
    consigneeName: string | null;
    consigneeAddress: string | null;
    despatchFrom: string | null;
    despatchTo: string | null;
    despatchByShip: string | null;
    signatoryName: string | null;
    designation: string | null;
    qualification: string | null;
    signatoryUserId?: string | null;
    officialStamp?: string | null;
    officialSignature?: string | null;
    certificateType?: string | null;
    productsAttachment?: any[];
}

export interface CaProductAttachmentPayload {
    product: string;
    lotIdentifier: string;
    typeOfPackaging: string;
    numberOfKgs: string;
    numberOfBoxes: number;
}

export interface CreateCaCertificatePayload {
    certificateRequestId?: number | null;
    referenceNumber?: string | null;
    myRef?: string;
    yourRef?: string;
    date?: string | Date | null;
    certificateNumber?: string;
    competentAuthority?: string;
    certifyingBody?: string;
    consignorName?: string;
    consignorAddress?: string;
    consigneeName?: string;
    consigneeAddress?: string;
    countryOfOrigin?: string;
    countryOfOriginISO?: string;
    countryOfDestination?: string;
    countryOfDestinationISO?: string;
    placeOfLoading?: string;
    transportAeroPlane?: boolean;
    transportShip?: boolean;
    transportRailway?: boolean;
    transportRoad?: boolean;
    transportOther?: boolean;
    despatchFrom?: string;
    despatchTo?: string;
    despatchByShip?: string;
    itemName?: string;
    numberOfPackages?: string;
    netWeight?: string;
    processingPlantName?: string;
    processingPlantAddress?: string;
    competentAuthorityRegNo?: string;
    pointsOfEntry?: string;
    conditionsOfStorage?: string;
    totalQuantity?: string;
    sealNumber?: string;
    totalNumberOfPackages?: string;
    approvalNumberOfEstablishments?: string;
    descriptionOfCommodity?: string;
    signatoryUserId?: string | null;
    signatoryName?: string;
    designation?: string;
    qualification?: string;
    companyRegistrationNo?: string;
    officialStamp?: string | null;
    officialSignature?: string | null;
    certificateType?: string | null;
    productsAttachment?: CaProductAttachmentPayload[];
}

export type CaCertificateView = CreateCaCertificatePayload & { id: number; createdAt: string };

export type CreateSaCertificatePayload = CreateCaCertificatePayload;
export type SaCertificateView = CaCertificateView;

export type CreateZaCertificatePayload = CreateCaCertificatePayload;
export type ZaCertificateView = CaCertificateView;

@Injectable({
    providedIn: 'root'
})
export class CertificateRequestService {
    constructor(private http: HttpClient) {}

    getCountries() {
        return this.http.get<CountryDto[]>(`${environment.apiBaseUrl}/api/certificaterequest/countries`);
    }

    createRequest(payload: CreateCertificateRequestPayload) {
        return this.http.post<CertificateRequestResponse>(`${environment.apiBaseUrl}/api/certificaterequest/createcertificate-requests`, payload);
    }

    getRequests() {
        return this.http.get<CertificateRequestResponse[]>(`${environment.apiBaseUrl}/api/certificaterequest/certificate-requests`);
    }

    getRequestById(requestId: number) {
        return this.http.get<CertificateRequestResponse>(`${environment.apiBaseUrl}/api/certificaterequest/certificate-requests/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getMyRequests() {
        return this.http.get<CertificateRequestResponse[]>(`${environment.apiBaseUrl}/api/certificaterequest/certificate-requests/my`);
    }

    confirmRequest(requestId: number) {
        return this.http.put(`${environment.apiBaseUrl}/api/certificaterequest/updatecertificate/${requestId}/status`, { status: 'Confirmed' });
    }

    rejectRequest(requestId: number) {
        return this.http.put(`${environment.apiBaseUrl}/api/certificaterequest/updatecertificate/${requestId}/status`, { status: 'Rejected' });
    }

    getVetFormByRequestId(requestId: number) {
        return this.http.get<VetFormFieldResponse>(`${environment.apiBaseUrl}/api/certificaterequest/vetcertificate/${requestId}/vet-form`).pipe(
            catchError(() => of(null as any))
        );
    }

    getVetCertificateAttachmentContent(attachmentId: number) {
        return this.http.get(`${environment.apiBaseUrl}/api/certificaterequest/vetcertificate/attachments/${attachmentId}/content`, {
            observe: 'response',
            responseType: 'blob'
        });
    }

    submitVetCertificateForm(formData: FormData) {
        return this.http.post<VetCertificateFormResponse>(`${environment.apiBaseUrl}/api/certificaterequest/vet-certificate-forms`, formData);
    }

    submitAuCertificate(payload: CreateAuCertificatePayload) {
        return this.http.post<AuCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/au-certificates`, payload);
    }

    submitAmCertificate(payload: CreateAmCertificatePayload) {
        return this.http.post<AmCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/am-certificates`, payload);
    }

    submitBrCertificate(payload: CreateBrCertificatePayload) {
        return this.http.post<BrCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/br-certificates`, payload);
    }

    submitChCertificate(payload: CreateChCertificatePayload) {
        return this.http.post<ChCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/ch-certificates`, payload);
    }

    submitHkCertificate(payload: CreateHkCertificatePayload) {
        return this.http.post<HkCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/hk-certificates`, payload);
    }

    submitIdCertificate(payload: CreateIdCertificatePayload) {
        return this.http.post<IdCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/id-certificates`, payload);
    }

    submitIndCertificate(payload: CreateIndCertificatePayload) {
        return this.http.post<IndCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/ind-certificates`, payload);
    }

    submitJpCertificate(payload: CreateJpCertificatePayload) {
        return this.http.post<JpCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/jp-certificates`, payload);
    }

    submitKwCertificate(payload: CreateKwCertificatePayload) {
        return this.http.post<KwCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/kw-certificates`, payload);
    }

    submitMyCertificate(payload: CreateMyCertificatePayload) {
        return this.http.post<MyCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/my-certificates`, payload);
    }

    submitNzCertificate(payload: CreateNzCertificatePayload) {
        return this.http.post<NzCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/nz-certificates`, payload);
    }

    submitMvCertificate(payload: CreateMvCertificatePayload) {
        return this.http.post<MvCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/mv-certificates`, payload);
    }

    submitRuCertificate(payload: CreateRuCertificatePayload) {
        return this.http.post<RuCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/ru-certificates`, payload);
    }

    submitKzCertificate(payload: CreateKzCertificatePayload) {
        return this.http.post<KzCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/kz-certificates`, payload);
    }

    submitTwCertificate(payload: CreateTwCertificatePayload) {
        return this.http.post<TwCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/tw-certificates`, payload);
    }

    submitUaCertificate(payload: CreateUaCertificatePayload) {
        return this.http.post<UaCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/ua-certificates`, payload);
    }

    submitUkCertificate(payload: CreateUkCertificatePayload) {
        return this.http.post<UkCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/uk-certificates`, payload);
    }

    submitUsaCertificate(payload: CreateUsaCertificatePayload) {
        return this.http.post<UsaCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/usa-certificates`, payload);
    }

    submitIlCertificate(payload: CreateIlCertificatePayload) {
        return this.http.post<IlCertificateResponse>(`${environment.apiBaseUrl}/api/certificaterequest/il-certificates`, payload);
    }

    getAuCertificateByRequestId(requestId: number) {
        return this.http.get<AuCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/au-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getAmCertificateByRequestId(requestId: number) {
        return this.http.get<AmCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/am-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getBrCertificateByRequestId(requestId: number) {
        return this.http.get<BrCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/br-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getChCertificateByRequestId(requestId: number) {
        return this.http.get<ChCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/ch-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getHkCertificateByRequestId(requestId: number) {
        return this.http.get<HkCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/hk-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getIdCertificateByRequestId(requestId: number) {
        return this.http.get<IdCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/id-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getIndCertificateByRequestId(requestId: number) {
        return this.http.get<IndCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/ind-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getJpCertificateByRequestId(requestId: number) {
        return this.http.get<JpCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/jp-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getKwCertificateByRequestId(requestId: number) {
        return this.http.get<KwCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/kw-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getMyCertificateByRequestId(requestId: number) {
        return this.http.get<MyCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/my-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getNzCertificateByRequestId(requestId: number) {
        return this.http.get<NzCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/nz-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getRuCertificateByRequestId(requestId: number) {
        return this.http.get<RuCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/ru-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getKzCertificateByRequestId(requestId: number) {
        return this.http.get<KzCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/kz-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getTwCertificateByRequestId(requestId: number) {
        return this.http.get<TwCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/tw-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getUaCertificateByRequestId(requestId: number) {
        return this.http.get<UaCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/ua-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getUkCertificateByRequestId(requestId: number) {
        return this.http.get<UkCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/uk-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getUsaCertificateByRequestId(requestId: number) {
        return this.http.get<UsaCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/usa-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getIlCertificateByRequestId(requestId: number) {
        return this.http.get<IlCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/il-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getMvCertificateByRequestId(requestId: number) {
        return this.http.get<MvCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/mv-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    getCaCertificateByRequestId(requestId: number) {
        return this.http.get<CaCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/ca-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    submitCaCertificate(payload: CreateCaCertificatePayload) {
        return this.http.post<{ id: number; certificateRequestId?: number; createdAt: string }>(
            `${environment.apiBaseUrl}/api/certificaterequest/ca-certificates`,
            payload
        );
    }

    getSaCertificateByRequestId(requestId: number) {
        return this.http.get<SaCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/sa-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    submitSaCertificate(payload: CreateSaCertificatePayload) {
        return this.http.post<{ id: number; certificateRequestId?: number; createdAt: string }>(
            `${environment.apiBaseUrl}/api/certificaterequest/sa-certificates`,
            payload
        );
    }

    getZaCertificateByRequestId(requestId: number) {
        return this.http.get<ZaCertificateView>(`${environment.apiBaseUrl}/api/certificaterequest/za-certificates/by-request/${requestId}`).pipe(
            catchError(() => of(null as any))
        );
    }

    submitZaCertificate(payload: CreateZaCertificatePayload) {
        return this.http.post<{ id: number; certificateRequestId?: number; createdAt: string }>(
            `${environment.apiBaseUrl}/api/certificaterequest/za-certificates`,
            payload
        );
    }

    // --- Replacement Requests ---
    getReplacementRequests() {
        return this.http.get<ReplacementRequestItem[]>(`${environment.apiBaseUrl}/api/replacement-requests`).pipe(
            catchError(() => of([] as ReplacementRequestItem[]))
        );
    }

    getEligibleCertificatesForReplacement() {
        return this.http.get<EligibleCertificateItem[]>(`${environment.apiBaseUrl}/api/replacement-requests/eligible-certificates`).pipe(
            catchError(() => of([] as EligibleCertificateItem[]))
        );
    }

    createReplacementRequest(payload: { originalCertificateRequestId?: number; originalReferenceNumber?: string; reason: string; remarks?: string }) {
        return this.http.post<ReplacementRequestItem>(`${environment.apiBaseUrl}/api/replacement-requests`, payload);
    }

    approveReplacementRequest(id: number) {
        return this.http.post<{ message: string; item: ReplacementRequestItem }>(`${environment.apiBaseUrl}/api/replacement-requests/${id}/approve`, {});
    }

    rejectReplacementRequest(id: number, reason: string) {
        return this.http.post<{ message: string; item: ReplacementRequestItem }>(`${environment.apiBaseUrl}/api/replacement-requests/${id}/reject`, { reason });
    }

    createReplacement(requestId: number) {
        return this.http.post<{
            newRequestId: number;
            newReferenceNumber: string;
            originalReferenceNumber: string;
            originalDate: string;
            countryName?: string;
            certificateType: string;
        }>(`${environment.apiBaseUrl}/api/certificaterequest/certificate-requests/${requestId}/create-replacement`, {});
    }
}

export interface ReplacementRequestItem {
    id: number;
    originalCertificateRequestId?: number | null;
    originalReferenceNumber: string;
    replacementReferenceNumber: string;
    companyUserId: string;
    companyName: string;
    country: string;
    certificateType: string;
    reason: string;
    remarks?: string | null;
    rejectionReason?: string | null;
    status: number | string; // 0: Pending, 1: Approved, 2: Rejected
    createdAt: string;
    processedAt?: string | null;
    processedByUserId?: string | null;
}

export interface EligibleCertificateItem {
    id: number;
    referenceNumber: string;
    certificateType: string;
    country: string;
    companyName: string;
    createdAt: string;
}
