import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, FormArray, Validators } from '@angular/forms';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { DatePicker } from 'primeng/datepicker';
import { RadioButton } from 'primeng/radiobutton';
import { TextareaModule } from 'primeng/textarea';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { ActivatedRoute, Router } from '@angular/router';
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { UserService, User } from '@/pages/service/user.service';
import {
    ChCertificateView,
    CertificateRequestService,
    CreateChCertificatePayload,
    VetFormFieldResponse,
    VetProductFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { TooltipModule } from 'primeng/tooltip';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';

export const DEFAULT_CH_PRODUCTS = [
    { product: 'CHILLED GROUPER FISH (Epinephelus malabaricus)', netWeight: null, numberOfBoxes: null },
    { product: 'CHILLED LADY FISH (Sillago sihama)', netWeight: null, numberOfBoxes: null },
    { product: 'CHILLED CROCKER FISH (Otolithes ruber)', netWeight: null, numberOfBoxes: null },
    { product: 'CHILLED CHINESE POMFRET FISH (Pampus chinensis)', netWeight: null, numberOfBoxes: null },
    { product: 'CHILLED SILVER POMFRET FISH (Pampus argenteus)', netWeight: null, numberOfBoxes: null },
    { product: 'CHILLED FOURFINGER THREADFIN FISH (Eleutheronema tetradactylum)', netWeight: null, numberOfBoxes: null }
];

@Component({
    selector: 'app-ch-certificate',
    standalone: true,
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        ButtonModule,
        InputTextModule,
        DatePicker,
        RadioButton,
        TextareaModule,
        ToastModule,
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './ch-certificate.component.html',
    styleUrls: ['./ch-certificate.component.css', '../certificate-print.css']
})
export class ChCertificateComponent implements OnInit {
    form: FormGroup;
    refNumber: string = '';
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;
    isApproved = false;
    isSubmitted = false;

    // View mode: 'attachment' (4 pages), 'single' (3 pages), 'live' (2 pages)
    viewMode: 'attachment' | 'single' | 'live' = 'attachment';

    userOptions: { label: string; value: string }[] = [];
    users: User[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    previousSignatoryUserId: string | null = null;
    isDraggingStamp = false;
    safeStampPdfUrl: SafeResourceUrl | null = null;

    onlyDigits(event: KeyboardEvent): boolean {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    onlyDecimals(event: KeyboardEvent): boolean {
        const charCode = event.which ? event.which : event.keyCode;
        const input = (event.target as HTMLInputElement).value || '';
        if (charCode === 46) {
            if (input.indexOf('.') !== -1) {
                event.preventDefault();
                return false;
            }
            return true;
        }
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    constructor(
        private fb: FormBuilder,
        private route: ActivatedRoute,
        private router: Router,
        private certificateRequestService: CertificateRequestService,
        private messageService: MessageService,
        private userService: UserService,
        private location: Location,
        private authService: AuthService,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['attachment'],
            refNumber: [''],

            // Section I
            countryOfExport: [''],
            countryOfProduction: [''],
            competentAuthority: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],
            departmentOfIssuance: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],

            // Section II
            commodityName: [''],
            scientificName: [''],
            latinName: [''],
            number: [''],
            numberOfPackages: [''],
            netWeight: [''],
            productionDate: [new Date()],
            lotNumber: [''],

            // Section III
            originRawMaterialsCountry: [''],
            processingType: [''],
            productionMode: [''],
            aquacultured: [false],
            wildCaughtBool: [false],
            productiveWaterArea: [''],
            aquacultureArea: [''],
            catchArea: [''],
            artificialCulture: [''],
            wildCaught: [''],

            // Section IV (Establishments)
            aquacultureFarmApprovedReg: [''],
            fishingVessel: [''],
            fishingAndFactoryVessel: [''],
            transportFishingVessel: [''],
            processingPlantNameAddress: [''],
            processingPlantRegNo: [''],
            coldStorageRawMaterials: [''],
            coldStorageProducts: [''],

            // Packaging enterprise (Live animals template)
            packagingEnterpriseName: [''],
            packagingEnterpriseAddress: [''],
            packagingEnterpriseRegNumber: [''],

            // Section V (Transport)
            consignorName: [''],
            consignorAddress: [''],
            consigneeName: [''],
            consigneeAddress: [''],
            placeOfDispatch: [''],
            placeOfDestination: [''],
            meansOfTransport: [''],
            nameOfVessel: [''],
            flightNumber: [''],
            otherTransportMeans: [''],
            containerNumber: [''],
            sealNumber: [''],
            dateOfDeparture: [new Date()],
            portOfDeparture: [''],
            transportAeroPlane: [false],
            transportShip: [false],
            transportRailwayWagon: [false],
            transportRoadVehicle: [false],
            transportOther: [false],
            identificationDocumentReferences: [''],

            // Exporter & Importer (Live template)
            exporterName: [''],
            exporterAddress: [''],
            importerName: [''],
            importerAddress: [''],

            // Signatory & Issue
            placeOfIssue: [''],
            dateOfIssue: [new Date()],
            officialStamp: [''],
            signatoryUserId: [null],
            signatoryName: [''],
            qualification: [''],

            // Attachment Page
            dateOfAttachment: [new Date()],
            identificationMarksAttachment: ['DFAR/FPE/98/62'],
            attachments: this.fb.array([])
        });

        this.loadDefaultProducts();

        this.form.get('viewMode')?.valueChanges.subscribe((mode: 'attachment' | 'single' | 'live') => {
            this.viewMode = mode;
            this.adjustFieldsForMode(mode);
        });
    }

    get attachments(): FormArray {
        return this.form.get('attachments') as FormArray;
    }

    createAttachmentRow(product: string = '', netWeight: number | null = null, numberOfBoxes: number | null = null): FormGroup {
        return this.fb.group({
            product: [product],
            netWeight: [netWeight],
            numberOfBoxes: [numberOfBoxes]
        });
    }

    addAttachment(product: string = '', netWeight: number | null = null, numberOfBoxes: number | null = null) {
        this.attachments.push(this.createAttachmentRow(product, netWeight, numberOfBoxes));
    }

    removeAttachment(index: number) {
        if (this.attachments.length > 1) {
            this.attachments.removeAt(index);
        }
    }

    loadDefaultProducts(): void {
        this.attachments.clear();
        DEFAULT_CH_PRODUCTS.forEach((p) => {
            this.addAttachment(p.product, p.netWeight, p.numberOfBoxes);
        });
    }

    clearProducts(): void {
        this.attachments.clear();
        this.addAttachment('');
    }

    formatProductName(product: string): string {
        if (!product) return '';
        return product.replace(/\(([^)]+)\)/g, '<em>($1)</em>');
    }

    calculateTotalWeight(): number {
        return this.attachments.controls.reduce((sum, ctrl) => {
            const val = parseFloat(ctrl.get('netWeight')?.value) || 0;
            return sum + val;
        }, 0);
    }

    calculateTotalBoxes(): number {
        return this.attachments.controls.reduce((sum, ctrl) => {
            const val = parseInt(ctrl.get('numberOfBoxes')?.value, 10) || 0;
            return sum + val;
        }, 0);
    }

    ngOnInit() {
        this.isCompany = (this.authService.getUserRole() || '').toLowerCase() === 'company';
        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        this.userService.getAllUsers().subscribe((users) => {
            this.users = users;
            this.userOptions = users
                .filter((u) => u.roleName && (u.roleName.toLowerCase() === 'user' || u.roleName.toLowerCase() === 'admin'))
                .map((u) => ({ label: u.name, value: u.id }));
        });

        this.route.queryParams.subscribe((params) => {
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
            }
            if (params['ref']) {
                this.refNumber = params['ref'];
                this.form.patchValue({ refNumber: params['ref'] });
            }
            if (params['requestId']) {
                this.certificateRequestId = Number(params['requestId']);
                this.checkRequestApproval(this.certificateRequestId);
                this.loadSavedCertificateData(this.certificateRequestId);
            }
            if (this.viewOnly) {
                this.form.disable();
            } else {
                this.form.enable();
                if (this.isCompany) {
                    this.form.get('signatoryUserId')?.clearValidators();
                    this.form.get('signatoryUserId')?.updateValueAndValidity();
                }
            }
        });
    }

    private adjustFieldsForMode(mode: 'attachment' | 'single' | 'live') {
        if (mode === 'attachment') {
            if (!this.form.get('commodityName')?.value || this.form.get('commodityName')?.value.includes('Frozen')) {
                this.form.patchValue({
                    commodityName: 'WILD CATCH - SEE THE ATTACHMENT',
                    scientificName: 'SEE THE ATTACHMENT'
                });
            }
        }
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateRequestService.getChCertificateByRequestId(requestId).subscribe({
            next: (data: ChCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                const mode = (data.certificateType as 'attachment' | 'single' | 'live') || 'attachment';
                this.viewMode = mode;
                if (data.id || data.refNumber || data.consignorName || data.exporterName) {
                    this.isSubmitted = true;
                }

                this.form.patchValue({
                    viewMode: mode,
                    refNumber: data.refNumber || this.refNumber || '',
                    countryOfExport: data.countryOfExport || 'SRI LANKA',
                    countryOfProduction: data.countryOfProduction || 'SRI LANKA',
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    departmentOfIssuance: data.departmentOfIssuance || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',

                    commodityName: data.commodityName || '',
                    scientificName: data.scientificName || '',
                    latinName: data.latinName || '',
                    number: data.number || '',
                    numberOfPackages: data.numberOfPackages || '',
                    netWeight: data.netWeight || '',
                    productionDate: data.productionDate ? new Date(data.productionDate) : new Date(),
                    lotNumber: data.lotNumber || '',

                    originRawMaterialsCountry: data.originRawMaterialsCountry || 'SRI LANKA',
                    processingType: data.processingType || 'CHILLED',
                    productionMode: data.productionMode || 'WHOLE FISH',
                    aquacultured: data.aquacultured ?? false,
                    wildCaughtBool: data.wildCaughtBool ?? true,
                    productiveWaterArea: data.productiveWaterArea || 'sea_water',
                    aquacultureArea: data.aquacultureArea || '***',
                    catchArea: data.catchArea || 'FAO 57',
                    artificialCulture: data.artificialCulture || 'no',
                    wildCaught: data.wildCaught || 'yes',

                    aquacultureFarmApprovedReg: data.aquacultureFarmApprovedReg || '***',
                    fishingVessel: data.fishingVessel || '***',
                    fishingAndFactoryVessel: data.fishingAndFactoryVessel || '***',
                    transportFishingVessel: data.transportFishingVessel || '***',
                    processingPlantNameAddress: data.processingPlantNameAddress || '',
                    processingPlantRegNo: data.processingPlantRegNo || '',
                    coldStorageRawMaterials: data.coldStorageRawMaterials || '***',
                    coldStorageProducts: data.coldStorageProducts || '***',

                    packagingEnterpriseName: data.packagingEnterpriseName || '',
                    packagingEnterpriseAddress: data.packagingEnterpriseAddress || '',
                    packagingEnterpriseRegNumber: data.packagingEnterpriseRegNumber || '',

                    consignorName: data.consignorName || data.exporterName || '',
                    consignorAddress: data.consignorAddress || data.exporterAddress || '',
                    consigneeName: data.consigneeName || data.importerName || '',
                    consigneeAddress: data.consigneeAddress || data.importerAddress || '',
                    placeOfDispatch: data.placeOfDispatch || 'COLOMBO – SRI LANKA',
                    placeOfDestination: data.placeOfDestination || 'SHENZHEN- CHINA',
                    meansOfTransport: data.meansOfTransport || 'AIR FREIGHT',
                    nameOfVessel: data.nameOfVessel || '***',
                    flightNumber: data.flightNumber || '***',
                    otherTransportMeans: data.otherTransportMeans || '***',
                    containerNumber: data.containerNumber || '***',
                    sealNumber: data.sealNumber || '***',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    portOfDeparture: data.portOfDeparture || 'COLOMBO – SRI LANKA',

                    transportAeroPlane: data.transportAeroPlane ?? true,
                    transportShip: data.transportShip ?? false,
                    transportRailwayWagon: data.transportRailwayWagon ?? false,
                    transportRoadVehicle: data.transportRoadVehicle ?? false,
                    transportOther: data.transportOther ?? false,
                    identificationDocumentReferences: data.identificationDocumentReferences || '***',

                    exporterName: data.exporterName || data.consignorName || '',
                    exporterAddress: data.exporterAddress || data.consignorAddress || '',
                    importerName: data.importerName || data.consigneeName || '',
                    importerAddress: data.importerAddress || data.consigneeAddress || '',

                    placeOfIssue: data.placeOfIssue || 'COLOMBO – SRI LANKA',
                    dateOfIssue: data.dateOfIssue ? new Date(data.dateOfIssue) : new Date(),
                    officialStamp: data.officialStamp || '',
                    signatoryUserId: data.signatoryUserId || null,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || 'QUALITY CONTROL OFFICER (GRADE II)',

                    dateOfAttachment: data.dateOfAttachment ? new Date(data.dateOfAttachment) : new Date(),
                    identificationMarksAttachment: data.identificationMarksAttachment || 'DFAR/FPE/98/62'
                });

                this.updateSafeStampUrl();

                this.attachments.clear();
                if (data.attachments && data.attachments.length > 0) {
                    data.attachments.forEach((att) => {
                        this.addAttachment(att.product || '', att.netWeight, att.numberOfBoxes);
                    });
                } else {
                    this.loadDefaultProducts();
                }

                if (this.viewOnly) {
                    this.form.disable();
                }
            },
            error: () => {
                this.loadVetFormData(requestId);
            }
        });
    }

    private loadVetFormData(requestId: number) {
        this.certificateRequestService.getVetFormByRequestId(requestId).subscribe({
            next: (data: VetFormFieldResponse) => {
                if (!data) return;

                const defaultRegNo = data.approvalNo || 'DFAR/FPE/98/62';
                const processingAddress = `${data.processingEstName || ''}\n${data.processingEstAddress || ''}\n${defaultRegNo}`.trim();
                const packagingAddress = `${data.processingEstAddress || data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim();

                const isShip = data.transportShip ?? false;
                const meansTransport = isShip ? 'SEA FREIGHT' : 'AIR FREIGHT';

                this.form.patchValue({
                    refNumber: data.healthCertNo || data.newHC || this.refNumber || '',
                    countryOfExport: data.countryOrigin || 'SRI LANKA',
                    countryOfProduction: data.countryOrigin || 'SRI LANKA',
                    originRawMaterialsCountry: data.countryOrigin || 'SRI LANKA',
                    competentAuthority: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    departmentOfIssuance: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',

                    processingType: data.temperatureFrozen ? 'FROZEN' : 'CHILLED',
                    productionMode: 'WHOLE FISH',
                    aquacultured: data.productTypeAquaculture ?? false,
                    wildCaughtBool: data.productTypeWildCaught ?? true,
                    productiveWaterArea: 'sea_water',
                    aquacultureArea: '***',
                    catchArea: data.regionOriginISO || 'FAO 57',
                    artificialCulture: data.productTypeAquaculture ? 'yes' : 'no',
                    wildCaught: data.productTypeWildCaught ? 'yes' : 'no',

                    aquacultureFarmApprovedReg: '***',
                    fishingVessel: '***',
                    fishingAndFactoryVessel: '***',
                    transportFishingVessel: '***',
                    processingPlantNameAddress: processingAddress,
                    processingPlantRegNo: defaultRegNo,
                    coldStorageRawMaterials: '***',
                    coldStorageProducts: '***',

                    packagingEnterpriseName: data.processingEstName || data.consignorName || '',
                    packagingEnterpriseAddress: packagingAddress,
                    packagingEnterpriseRegNumber: defaultRegNo,

                    consignorName: data.consignorName || '',
                    consignorAddress: `${data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim(),
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: `${data.consigneeAddress || ''}\n${data.consigneePostal || ''}`.trim(),
                    placeOfDispatch: 'COLOMBO – SRI LANKA',
                    placeOfDestination: data.countryDestinationISO ? `${data.countryDestinationISO} - CHINA` : 'SHENZHEN- CHINA',
                    meansOfTransport: meansTransport,
                    nameOfVessel: isShip ? (data.transportId || '***') : '***',
                    flightNumber: !isShip ? (data.transportId || '***') : '***',
                    otherTransportMeans: '***',
                    containerNumber: data.containerId || '***',
                    sealNumber: '***',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    portOfDeparture: 'COLOMBO – SRI LANKA',

                    transportAeroPlane: data.transportAeroPlane ?? !isShip,
                    transportShip: data.transportShip ?? isShip,
                    transportRailwayWagon: data.transportRailwayWagon ?? false,
                    transportRoadVehicle: data.transportRoadVehicle ?? false,
                    transportOther: data.transportOther ?? false,
                    identificationDocumentReferences: data.transportId || '***',

                    exporterName: data.consignorName || '',
                    exporterAddress: `${data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim(),
                    importerName: data.consigneeName || '',
                    importerAddress: `${data.consigneeAddress || ''}\n${data.consigneePostal || ''}`.trim(),

                    placeOfIssue: 'COLOMBO – SRI LANKA',
                    dateOfIssue: new Date(),
                    dateOfAttachment: new Date(),
                    identificationMarksAttachment: defaultRegNo,

                    // Single product defaults
                    commodityName: data.products?.[0]?.descCommon ? `${data.temperatureFrozen ? 'Frozen ' : 'Chilled '}${data.products[0].descCommon}` : 'WILD CATCH - SEE THE ATTACHMENT',
                    scientificName: data.products?.[0]?.descScientific || 'SEE THE ATTACHMENT',
                    latinName: data.products?.[0]?.descScientific || 'Scylla serrata',
                    numberOfPackages: data.products?.[0]?.numPackages ? `${data.products[0].numPackages} Cartons` : (data.numPackages ? `${data.numPackages} Cartons` : ''),
                    netWeight: data.products?.[0]?.netWeight ? `${data.products[0].netWeight} kg` : (data.netWeight ? `${data.netWeight} kg` : ''),
                    number: `${data.numPackages || ''} Boxes / ${data.netWeight || ''} kg`,
                    lotNumber: data.processingDate || ''
                });

                // Attachments population
                this.attachments.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p: VetProductFieldResponse) => {
                        const typePrefix = data.temperatureFrozen ? 'FROZEN ' : 'CHILLED ';
                        const name = p.descCommon ? `${typePrefix}${p.descCommon.trim().toUpperCase()}` : '';
                        const sci = p.descScientific ? ` (${p.descScientific.trim()})` : '';
                        const productFormatted = `${name}${sci}`.trim();
                        const nw = p.netWeight ? parseFloat(p.netWeight) || null : null;
                        const bx = p.numPackages ? parseInt(p.numPackages, 10) || null : null;
                        this.addAttachment(productFormatted, nw, bx);
                    });
                } else {
                    this.loadDefaultProducts();
                }
            },
            error: () => {
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to load initial data' });
            }
        });
    }

    onSignatoryUserChange(event: any) {
        const userId = event.value;
        if (!userId) {
            this.form.patchValue({ signatoryName: '', qualification: '' });
            return;
        }

        const selected = this.users.find((u) => u.id === userId);
        if (selected) {
            this.pendingSignatoryUserId = selected.id;
            this.pendingSignatoryUserEmail = selected.email;
            this.selectedUserQualification = selected.qualification || 'QUALITY CONTROL OFFICER (GRADE II)';
            this.showPasswordDialog = true;
        }
    }

    onSignatoryConfirmed(officerName: string) {
        if (this.pendingSignatoryUserId) {
            this.form.patchValue({
                signatoryUserId: this.pendingSignatoryUserId,
                signatoryName: officerName,
                qualification: this.selectedUserQualification || 'QUALITY CONTROL OFFICER (GRADE II)'
            });
            this.previousSignatoryUserId = this.pendingSignatoryUserId;
        }
        this.showPasswordDialog = false;
        this.pendingSignatoryUserId = null;
        this.messageService.add({ severity: 'success', summary: 'Verified', detail: 'Officer PIN confirmed successfully.' });
    }

    onSignatoryCanceled() {
        this.form.patchValue({ signatoryUserId: this.previousSignatoryUserId });
        this.showPasswordDialog = false;
        this.pendingSignatoryUserId = null;
    }

    onSubmit() {
        if (this.form.invalid) {
            this.messageService.add({ severity: 'error', summary: 'Validation Error', detail: 'Please fill in all required fields.' });
            return;
        }

        this.isSaving = true;
        const v = this.form.getRawValue();

        const payload: CreateChCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            certificateType: this.viewMode,
            refNumber: v.refNumber || this.refNumber || '',

            countryOfExport: v.countryOfExport,
            countryOfProduction: v.countryOfProduction,
            competentAuthority: v.competentAuthority,
            departmentOfIssuance: v.departmentOfIssuance,

            commodityName: v.commodityName,
            scientificName: v.scientificName,
            latinName: v.latinName,
            number: v.number,
            numberOfPackages: v.numberOfPackages,
            netWeight: v.netWeight,
            productionDate: v.productionDate ? toLocalISOString(v.productionDate) : null,
            lotNumber: v.lotNumber,

            originRawMaterialsCountry: v.originRawMaterialsCountry,
            processingType: v.processingType,
            productionMode: v.productionMode,
            aquacultured: v.aquacultured,
            wildCaughtBool: v.wildCaughtBool,
            productiveWaterArea: v.productiveWaterArea,
            aquacultureArea: v.aquacultureArea,
            catchArea: v.catchArea,
            artificialCulture: v.artificialCulture,
            wildCaught: v.wildCaught,

            aquacultureFarmApprovedReg: v.aquacultureFarmApprovedReg,
            fishingVessel: v.fishingVessel,
            fishingAndFactoryVessel: v.fishingAndFactoryVessel,
            transportFishingVessel: v.transportFishingVessel,
            processingPlantNameAddress: v.processingPlantNameAddress,
            processingPlantRegNo: v.processingPlantRegNo,
            coldStorageRawMaterials: v.coldStorageRawMaterials,
            coldStorageProducts: v.coldStorageProducts,

            packagingEnterpriseName: v.packagingEnterpriseName,
            packagingEnterpriseAddress: v.packagingEnterpriseAddress,
            packagingEnterpriseRegNumber: v.packagingEnterpriseRegNumber,

            consignorName: v.consignorName,
            consignorAddress: v.consignorAddress,
            consigneeName: v.consigneeName,
            consigneeAddress: v.consigneeAddress,
            placeOfDispatch: v.placeOfDispatch,
            placeOfDestination: v.placeOfDestination,
            meansOfTransport: v.meansOfTransport,
            nameOfVessel: v.nameOfVessel,
            flightNumber: v.flightNumber,
            otherTransportMeans: v.otherTransportMeans,
            containerNumber: v.containerNumber,
            sealNumber: v.sealNumber,
            dateOfDeparture: v.dateOfDeparture ? toLocalISOString(v.dateOfDeparture) : null,
            portOfDeparture: v.portOfDeparture,

            transportAeroPlane: v.transportAeroPlane,
            transportShip: v.transportShip,
            transportRailwayWagon: v.transportRailwayWagon,
            transportRoadVehicle: v.transportRoadVehicle,
            transportOther: v.transportOther,
            identificationDocumentReferences: v.identificationDocumentReferences,

            exporterName: v.exporterName,
            exporterAddress: v.exporterAddress,
            importerName: v.importerName,
            importerAddress: v.importerAddress,

            placeOfIssue: v.placeOfIssue,
            dateOfIssue: v.dateOfIssue ? toLocalISOString(v.dateOfIssue) : null,
            officialStamp: v.officialStamp,
            signatoryUserId: v.signatoryUserId,
            signatoryName: v.signatoryName,
            qualification: v.qualification,

            dateOfAttachment: v.dateOfAttachment ? toLocalISOString(v.dateOfAttachment) : null,
            identificationMarksAttachment: v.identificationMarksAttachment,
            attachments: (v.attachments || []).map((a: any) => ({
                product: a.product,
                netWeight: a.netWeight ? parseFloat(a.netWeight) : null,
                numberOfBoxes: a.numberOfBoxes ? parseInt(a.numberOfBoxes, 10) : null
            }))
        };

        this.certificateRequestService.submitChCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({ severity: 'success', summary: 'Saved', detail: 'China Health Certificate saved successfully.' });
            },
            error: () => {
                this.isSaving = false;
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to save certificate.' });
            }
        });
    }

    private checkRequestApproval(requestId: number): void {
        this.certificateRequestService.getRequestById(requestId).subscribe({
            next: (req) => {
                if (req) {
                    const st = typeof req.status === 'string' ? req.status.toLowerCase() : (req.status === 1 ? 'confirmed' : 'pending');
                    this.isApproved = (st === 'confirmed' || st === 'approved' || req.status === 1);
                }
            },
            error: () => {}
        });
    }

    printCertificate() {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }

        const ref = this.form.get('refNumber')?.value || this.refNumber || 'Draft';
        let pdfName = '';
        if (this.viewMode === 'attachment') {
            pdfName = `${ref}_China_Fish_And_Fishery_Products_Attachment_Health_Certificate.pdf`;
        } else if (this.viewMode === 'single') {
            pdfName = `${ref}_China_Fish_And_Fishery_Products_Health_Certificate.pdf`;
        } else {
            pdfName = `${ref}_China_Live_Aquatic_Animals_Health_Certificate.pdf`;
        }

        const originalTitle = document.title;
        document.title = pdfName;
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }

    goBack() {
        this.location.back();
    }

    isPdf(value: string | null | undefined): boolean {
        if (!value) return false;
        return value.startsWith('data:application/pdf') || value.endsWith('.pdf');
    }

    updateSafeStampUrl() {
        const stamp = this.form.get('officialStamp')?.value;
        if (stamp && this.isPdf(stamp)) {
            this.safeStampPdfUrl = this.sanitizer.bypassSecurityTrustResourceUrl(stamp);
        } else {
            this.safeStampPdfUrl = null;
        }
    }

    onStampFileSelected(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            this.processStampFile(input.files[0]);
            input.value = '';
        }
    }

    onStampDrop(event: DragEvent) {
        event.preventDefault();
        this.isDraggingStamp = false;
        if (this.viewOnly) return;
        if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
            this.processStampFile(event.dataTransfer.files[0]);
        }
    }

    onStampDragOver(event: DragEvent) {
        event.preventDefault();
        if (!this.viewOnly) this.isDraggingStamp = true;
    }

    onStampDragLeave(event: DragEvent) {
        event.preventDefault();
        this.isDraggingStamp = false;
    }

    onStampPaste(event: ClipboardEvent) {
        if (this.viewOnly) return;
        const items = event.clipboardData?.items;
        if (items) {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1 || items[i].type.indexOf('pdf') !== -1) {
                    const file = items[i].getAsFile();
                    if (file) {
                        this.processStampFile(file);
                        event.preventDefault();
                        break;
                    }
                }
            }
        }
    }

    processStampFile(file: File) {
        const isValid = file.type.startsWith('image/') || file.type === 'application/pdf';
        if (!isValid) {
            this.messageService.add({
                severity: 'error',
                summary: 'Invalid File',
                detail: 'Please upload an image (PNG, JPG, Screenshot) or PDF file.'
            });
            return;
        }

        if (file.size > 15 * 1024 * 1024) {
            this.messageService.add({
                severity: 'error',
                summary: 'File Too Large',
                detail: 'File size should not exceed 15MB.'
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            const result = reader.result as string;
            this.form.patchValue({ officialStamp: result });
            this.updateSafeStampUrl();
            this.messageService.add({
                severity: 'success',
                summary: 'Uploaded Successfully',
                detail: 'Official Stamp uploaded.'
            });
        };
        reader.onerror = () => {
            this.messageService.add({
                severity: 'error',
                summary: 'Read Error',
                detail: 'Failed to read the file.'
            });
        };
        reader.readAsDataURL(file);
    }

    removeStamp(event?: Event) {
        if (event) event.stopPropagation();
        this.form.patchValue({ officialStamp: '' });
        this.safeStampPdfUrl = null;
    }
}
