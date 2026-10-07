import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { CheckboxModule } from 'primeng/checkbox';
import { RadioButton } from 'primeng/radiobutton';
import { TooltipModule } from 'primeng/tooltip';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { Select } from 'primeng/select';
import { UserService, User } from '@/pages/service/user.service';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import {
    CertificateRequestService,
    UaCertificateView,
    CreateUaCertificatePayload,
    CreateUaCertificateProductPayload,
    VetFormFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { toLocalISOString } from '@/shared/utils/date-utils';

export const DEFAULT_UA_PRODUCTS = [
    { species: 'Y/F TUNA( (Thunnus albacares)', netWeight: '', numberOfPackaging: '' },
    { species: 'RED MULLET(Parupeneus indicus)', netWeight: '', numberOfPackaging: '' },
    { species: 'MAHI MAHI(Coryphaena hippurus)', netWeight: '', numberOfPackaging: '' },
    { species: 'RED SNAPPER( Lutjanus sp)', netWeight: '', numberOfPackaging: '' },
    { species: 'PARROT FISH( Scarus sp)', netWeight: '', numberOfPackaging: '' },
    { species: 'COBIA (Rachycentron canadum)', netWeight: '', numberOfPackaging: '' },
    { species: 'BLUBBER LIP SNAPPER (Lutjanus rivulatus)', netWeight: '', numberOfPackaging: '' },
    { species: 'SPOTTED GROUPER (Epinephelus malabaricus)', netWeight: '', numberOfPackaging: '' },
    { species: 'SCAMPI (Macrobrachium rosenbergii)', netWeight: '', numberOfPackaging: '' },
    { species: 'BLUE DRAB (Portunus pelagicus)', netWeight: '', numberOfPackaging: '' }
];

@Component({
    selector: 'app-ua-certificate',
    standalone: true,
    imports: [CommonModule,
        FormsModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        ToastModule,
        CheckboxModule,
        RadioButton,
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './ua-certificate.component.html',
    styleUrls: ['./ua-certificate.component.css', '../certificate-print.css']
})
export class UaCertificateComponent implements OnInit {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    form: FormGroup;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }
    isApproved = false;
    refNumber: string = '';

    // View mode: 'full' (4/5 pages) | 'attachment_only' (1 page)
    viewMode: 'full' | 'attachment_only' = 'full';

    users: User[] = [];
    userOptions: { label: string; value: string }[] = [];
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    selectedUserQualification: string | null = null;
    previousSignatoryUserId: string | null = null;

    safeStampPdfUrl: SafeResourceUrl | null = null;
    safeSignaturePdfUrl: SafeResourceUrl | null = null;
    isDraggingStamp = false;
    isDraggingSignature = false;

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
        private messageService: MessageService,
        private certificateService: CertificateRequestService,
        private userService: UserService,
        private authService: AuthService,
        private location: Location,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['full'],
            includeAttachment: [true],

            // Consignor
            consignorName: ['LIHINI SEA FOODS (PVT)LTD', Validators.required],
            consignorAddress: ['ST.JUDE MAWATHA,KATUNERIYA,\nSRI LANKA', Validators.required],
            consignorPostalCode: [''],
            consignorTelNo: [''],

            // Certificate Details
            certificateReferenceNumber: ['', Validators.required],
            centralCompetentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            localCompetentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],

            // Consignee
            consigneeName: ['ATLANTIC-UMMA LLC,', Validators.required],
            consigneeAddress: ['4-A NOVOKONSTANTYNIVSKA\nSTR,KYIV,UKRAINE,04655.', Validators.required],
            consigneePostalCode: ['04655'],
            consigneeTel: [''],

            // Person Responsible in Ukraine
            personResponsibleName: ['ATLANTIC-UMMA LLC', Validators.required],
            personResponsibleAddress: ['4-A NOVOKONSTANTYNIVSKA\nSTR,KYIV,UKRAINE,04655.', Validators.required],
            personResponsiblePostalCode: ['04655'],
            personResponsibleTel: [''],

            // Country & Zone
            countryOfOriginName: ['SRI LANKA', Validators.required],
            countryOfOriginISO: ['LK'],
            countryOfOriginISOCode: ['LK'],
            countryOfOriginZone: ['INDIAN OCEAN'],
            zoneOrigin: ['INDIAN OCEAN'],
            zoneOriginCode: ['57'],
            countryDestinationName: ['UKRAINE', Validators.required],
            countryDestinationISO: ['UA'],
            countryDestinationISOCode: ['UA'],
            countryDestinationZone: [''],
            zoneDestination: [''],
            zoneDestinationCode: [''],

            // Place of Origin
            placeOriginName: ['LIHINI SEA FOODS (PVT)LTD', Validators.required],
            placeOriginApprovalNumber: ['DFAR/FPE/98/21'],
            placeOriginAddress: ['ST.JUDE MAWATHA,KATUNERIYA,SRI LANKA'],
            field112: [''],

            // Loading & Departure
            placeLoadingAddress: ['COLOMBO / SRI LANKA\nST.JUDE MAWATHA,KATUNERIYA,SRI LANKA', Validators.required],
            dateOfDeparture: [new Date('2025-03-19')],

            // Means of Transport
            transportAeroplane: [true],
            transportShip: [false],
            transportRailwayWagon: [false],
            transportRoadVehicle: [false],
            transportOther: [false],
            transportIdentification: [''],
            transportDocumentReferences: [''],

            // Entry BIP & Commodity Code
            entryBIPUkraine: ['JAGODIN'],
            descriptionOfCommodity: [''],
            commodityCodeHS: ['0302, 0304,0306,0307'],
            quantity: [''],

            // Temperature
            temperatureAmbient: [false],
            temperatureChilled: [true],
            temperatureFrozen: [false],

            // Packages & Packaging
            numberOfPackages: [''],
            sealContainerNo: [''],
            typeOfPackaging: ['VACUUM PACKED IN POLYSTYRENE BOXES WITH GEL ICE\nAND CORRUGATED BOXES WITH GEL ICE'],
            commoditiesHumanConsumption: [true],
            field126: [''],
            forImportIntoUkraine: [''],

            // Products list (Attachment Table)
            products: this.fb.array(DEFAULT_UA_PRODUCTS.map((p) => this.createProductRow(p))),

            // Health Notes & Signatures
            healthInfoNotes: [''],
            healthCertificateReferenceNumber: ['SY 7511'],
            additionalInformation: [''],
            officialStamp: [''],
            officialSignature: [''],
            signatoryUserId: [null, Validators.required],
            signatoryName: ['K.H.D GUNARATHNA'],
            qualification: ['QUALITY CONTROL OFFICER (GRADE II)\nB.Sc. (FOOD SCI. & TECH.) SP. (SRI LANKA).'],
            certifiedDate: [new Date('2025-03-19')]
        });

        this.form.get('certificateReferenceNumber')?.valueChanges.subscribe((val) => {
            if (val) {
                this.form.patchValue({ healthCertificateReferenceNumber: val }, { emitEvent: false });
            }
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    createProductRow(data?: any): FormGroup {
        return this.fb.group({
            species: [data?.species || ''],
            natureOfCommodity: [data?.natureOfCommodity || 'WILD ORIGIN'],
            treatmentApprovalNumber: [data?.treatmentApprovalNumber || 'FRESH CHILLED LOINS/ WHOLE'],
            manufacturingPlant: [data?.manufacturingPlant || 'LIHINI SEA FOODS (PVT)LTD'],
            numberOfPackaging: [data?.numberOfPackaging !== undefined ? data.numberOfPackaging : ''],
            typeOfPackaging: [data?.typeOfPackaging || 'VACUUM PACKED IN POLYSTYRENE BOXES WITH GEL ICE AND CORRUGATED BOXES WITH GEL ICE'],
            netWeight: [data?.netWeight !== undefined ? data.netWeight : '']
        });
    }

    addProduct(): void {
        this.products.push(this.createProductRow());
    }

    removeProduct(index: number): void {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
    }

    loadDefaultProducts(): void {
        this.products.clear();
        DEFAULT_UA_PRODUCTS.forEach((p) => {
            this.products.push(this.createProductRow(p));
        });
    }

    clearProducts(): void {
        this.products.clear();
        this.products.push(this.createProductRow());
    }

    calculateTotalPackages(): string {
        const total = this.products.controls.reduce((sum, c) => {
            const val = c.get('numberOfPackaging')?.value;
            const parsed = typeof val === 'number' ? val : parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total}` : '';
    }

    calculateTotalNetWeight(): string {
        const total = this.products.controls.reduce((sum, c) => {
            const val = c.get('netWeight')?.value;
            const parsed = typeof val === 'number' ? val : parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
    }

    get totalPages(): number {
        if (this.viewMode === 'attachment_only') return 1;
        return this.form.get('includeAttachment')?.value ? 5 : 4;
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
            if (params['cancelsAndReplacesRef']) this.cancelsAndReplacesRef = params['cancelsAndReplacesRef'];
            if (params['cancelsAndReplacesDate']) this.cancelsAndReplacesDate = params['cancelsAndReplacesDate'];
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
            }

            if (params['mode'] === 'attachment_only' || this.router.url.includes('ua-attachment-certificate')) {
                this.viewMode = 'attachment_only';
            }

            if (params['ref']) {
                this.refNumber = params['ref'];
                this.form.patchValue({
                    certificateReferenceNumber: params['ref'],
                    healthCertificateReferenceNumber: params['ref']
                });
            }

            if (params['requestId']) {
                this.certificateRequestId = +params['requestId'];
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

    private checkRequestApproval(requestId: number): void {
        this.certificateService.getRequestById(requestId).subscribe({
            next: (req) => {
                    if (req) {
                        if (req.cancelsAndReplacesRef) this.cancelsAndReplacesRef = req.cancelsAndReplacesRef;
                        if (req.cancelsAndReplacesDate) this.cancelsAndReplacesDate = req.cancelsAndReplacesDate;
                    }
                if (req) {
                    const st = typeof req.status === 'string' ? req.status.toLowerCase() : (req.status === 1 ? 'confirmed' : 'pending');
                    this.isApproved = (st === 'confirmed' || st === 'approved' || req.status === 1);
                    if (req.referenceNumber) {
                        this.refNumber = req.referenceNumber;
                        this.form.patchValue({
                            certificateReferenceNumber: req.referenceNumber,
                            healthCertificateReferenceNumber: req.referenceNumber
                        });
                    }
                }
            },
            error: () => {}
        });
    }

    onViewModeChange(mode: 'full' | 'attachment_only') {
        this.viewMode = mode;
        this.form.get('viewMode')?.setValue(mode);
    }

    loadSampleUkraine(): void {
        this.viewMode = 'full';
        this.form.patchValue({
            viewMode: 'full',
            includeAttachment: true,
            certificateReferenceNumber: this.refNumber || 'SY 7511',
            healthCertificateReferenceNumber: this.refNumber || 'SY 7511',
            consignorName: 'LIHINI SEA FOODS (PVT)LTD',
            consignorAddress: 'ST.JUDE MAWATHA,KATUNERIYA,\nSRI LANKA',
            consignorPostalCode: '',
            consignorTelNo: '',
            consigneeName: 'ATLANTIC-UMMA LLC,',
            consigneeAddress: '4-A NOVOKONSTANTYNIVSKA\nSTR,KYIV,UKRAINE,04655.',
            consigneePostalCode: '04655',
            personResponsibleName: 'ATLANTIC-UMMA LLC',
            personResponsibleAddress: '4-A NOVOKONSTANTYNIVSKA\nSTR,KYIV,UKRAINE,04655.',
            personResponsiblePostalCode: '04655',
            countryOfOriginName: 'SRI LANKA',
            countryOfOriginISO: 'LK',
            countryOfOriginZone: 'INDIAN OCEAN',
            zoneOrigin: 'INDIAN OCEAN',
            zoneOriginCode: '57',
            countryDestinationName: 'UKRAINE',
            countryDestinationISO: 'UA',
            placeOriginName: 'LIHINI SEA FOODS (PVT)LTD',
            placeOriginApprovalNumber: 'DFAR/FPE/98/21',
            placeOriginAddress: 'ST.JUDE MAWATHA,KATUNERIYA,SRI LANKA',
            placeLoadingAddress: 'COLOMBO / SRI LANKA\nST.JUDE MAWATHA,KATUNERIYA,SRI LANKA',
            dateOfDeparture: new Date('2025-03-19'),
            transportAeroplane: true,
            transportShip: false,
            transportRailwayWagon: false,
            transportRoadVehicle: false,
            transportOther: false,
            transportIdentification: '',
            transportDocumentReferences: '',
            entryBIPUkraine: 'JAGODIN',
            descriptionOfCommodity: '',
            commodityCodeHS: '0302, 0304,0306,0307',
            temperatureAmbient: false,
            temperatureChilled: true,
            temperatureFrozen: false,
            numberOfPackages: '',
            quantity: '',
            typeOfPackaging: 'VACUUM PACKED IN POLYSTYRENE BOXES WITH GEL ICE\nAND CORRUGATED BOXES WITH GEL ICE',
            commoditiesHumanConsumption: true,
            signatoryName: 'K.H.D GUNARATHNA',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc. (FOOD SCI. & TECH.) SP. (SRI LANKA).',
            certifiedDate: new Date('2025-03-19')
        });

        this.loadDefaultProducts();

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Ukraine Sample',
            detail: 'Loaded Ukraine Certificate sample (SY 7511 - Lihini Sea Foods) with 10 Attachment products.'
        });
    }

    private loadSavedCertificateData(requestId: number): void {
        this.certificateService.getUaCertificateByRequestId(requestId).subscribe({
            next: (data: UaCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                const mode = (data.certificateType as 'full' | 'attachment_only') || 'full';
                this.viewMode = mode;
                const hasMultiple = (data.products && data.products.length > 1);

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813', 'TB 9530', 'TC 4359', 'TC 4035', 'SX 1691', 'TC 4361', 'TA 6894', 'TA 9637', 'SY 7511'];
                const cleanCertNo = (data.certificateReferenceNumber && !dummyValues.includes(data.certificateReferenceNumber.trim())) ? data.certificateReferenceNumber : '';
                const finalCertNo = this.refNumber || (data as any).referenceNumber || cleanCertNo || '';
                if (finalCertNo && !this.refNumber) {
                    this.refNumber = finalCertNo;
                }

                this.form.patchValue({
                    viewMode: mode,
                    includeAttachment: hasMultiple,
                    consignorName: data.consignorName || 'LIHINI SEA FOODS (PVT)LTD',
                    consignorAddress: data.consignorAddress || '',
                    consignorPostalCode: data.consignorPostalCode || '',
                    consignorTelNo: data.consignorTelNo || '',
                    certificateReferenceNumber: finalCertNo,
                    healthCertificateReferenceNumber: finalCertNo,
                    centralCompetentAuthority: data.centralCompetentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    localCompetentAuthority: data.localCompetentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    consigneePostalCode: data.consigneePostalCode || '',
                    consigneeTel: data.consigneeTel || '',
                    personResponsibleName: data.personResponsibleName || '',
                    personResponsibleAddress: data.personResponsibleAddress || '',
                    personResponsiblePostalCode: data.personResponsiblePostalCode || '',
                    personResponsibleTel: data.personResponsibleTel || '',
                    countryOfOriginName: data.countryOfOriginName || 'SRI LANKA',
                    countryOfOriginISO: data.countryOfOriginISO || 'LK',
                    countryOfOriginISOCode: data.countryOfOriginISOCode || 'LK',
                    countryOfOriginZone: data.countryOfOriginZone || 'INDIAN OCEAN',
                    zoneOrigin: data.zoneOrigin || 'INDIAN OCEAN',
                    zoneOriginCode: data.zoneOriginCode || '57',
                    countryDestinationName: data.countryDestinationName || 'UKRAINE',
                    countryDestinationISO: data.countryDestinationISO || 'UA',
                    countryDestinationISOCode: data.countryDestinationISOCode || 'UA',
                    countryDestinationZone: data.countryDestinationZone || '',
                    zoneDestination: data.zoneDestination || '',
                    zoneDestinationCode: data.zoneDestinationCode || '',
                    placeOriginName: data.placeOriginName || 'LIHINI SEA FOODS (PVT)LTD',
                    placeOriginApprovalNumber: data.placeOriginApprovalNumber || 'DFAR/FPE/98/21',
                    placeOriginAddress: data.placeOriginAddress || '',
                    field112: data.field112 || '',
                    placeLoadingAddress: data.placeLoadingAddress || 'COLOMBO / SRI LANKA',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    transportAeroplane: data.transportAeroplane ?? true,
                    transportShip: data.transportShip ?? false,
                    transportRailwayWagon: data.transportRailwayWagon ?? false,
                    transportRoadVehicle: data.transportRoadVehicle ?? false,
                    transportOther: data.transportOther ?? false,
                    transportIdentification: data.transportIdentification || '',
                    transportDocumentReferences: data.transportDocumentReferences || '',
                    entryBIPUkraine: data.entryBIPUkraine || 'JAGODIN',
                    descriptionOfCommodity: data.descriptionOfCommodity || '',
                    commodityCodeHS: data.commodityCodeHS || '0302, 0304,0306,0307',
                    quantity: data.quantity || '',
                    temperatureAmbient: data.temperatureAmbient ?? false,
                    temperatureChilled: data.temperatureChilled ?? true,
                    temperatureFrozen: data.temperatureFrozen ?? false,
                    numberOfPackages: data.numberOfPackages || '',
                    sealContainerNo: data.sealContainerNo || '',
                    typeOfPackaging: data.typeOfPackaging || 'VACUUM PACKED IN POLYSTYRENE BOXES WITH GEL ICE AND CORRUGATED BOXES WITH GEL ICE',
                    commoditiesHumanConsumption: data.commoditiesHumanConsumption ?? true,
                    field126: data.field126 || '',
                    forImportIntoUkraine: data.forImportIntoUkraine || '',
                    healthInfoNotes: data.healthInfoNotes || '',
                    additionalInformation: data.additionalInformation || '',
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || '',
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || '',
                    certifiedDate: data.certifiedDate ? new Date(data.certifiedDate) : new Date(),
                    signatoryUserId: data.signatoryUserId || null
                });

                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();

                this.selectedUserQualification = data.qualification || null;
                this.previousSignatoryUserId = data.signatoryUserId || null;

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p) => {
                        this.products.push(this.createProductRow(p));
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

    private loadVetFormData(requestId: number): void {
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
            next: (data: VetFormFieldResponse) => {
                if (!data) return;

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813', 'TB 9530', 'TC 4359', 'TC 4035', 'SX 1691', 'TC 4361', 'TA 6894', 'TA 9637', 'SY 7511'];
                const rawNo = data.healthCertNo || data.newHC || '';
                const cleanCertNo = (rawNo && !dummyValues.includes(rawNo.trim())) ? rawNo : '';
                const finalCertNo = this.refNumber || data.referenceNumber || cleanCertNo || '';
                if (finalCertNo && !this.refNumber) {
                    this.refNumber = finalCertNo;
                }

                const consignorAddr = `${data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim();
                const consigneeAddr = `${data.consigneeAddress || ''}\n${data.consigneePostal || ''}\nUKRAINE`.trim();
                const plantName = data.processingEstName || data.consignorName || 'LIHINI SEA FOODS (PVT)LTD';
                const plantAddr = `${data.processingEstAddress || data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim();
                const approvalNo = data.approvalNo || 'DFAR/FPE/98/21';
                const isAir = data.transportAeroPlane ?? true;
                const isSea = data.transportShip ?? false;

                const hsCodes = data.products && data.products.length > 0
                    ? Array.from(new Set(data.products.map((p) => p.hsCode).filter(Boolean))).join(', ')
                    : data.hsCode || '0302, 0304,0306,0307';

                const hasMultiple = data.products && data.products.length > 1;

                this.form.patchValue({
                    includeAttachment: hasMultiple,
                    certificateReferenceNumber: finalCertNo,
                    healthCertificateReferenceNumber: finalCertNo,
                    consignorName: data.consignorName || 'LIHINI SEA FOODS (PVT)LTD',
                    consignorAddress: consignorAddr || 'ST.JUDE MAWATHA,KATUNERIYA,\nSRI LANKA',
                    consignorPostalCode: data.consignorPostal || '',
                    consignorTelNo: data.consignorTel || '',
                    consigneeName: data.consigneeName || 'ATLANTIC-UMMA LLC,',
                    consigneeAddress: consigneeAddr || '4-A NOVOKONSTANTYNIVSKA\nSTR,KYIV,UKRAINE,04655.',
                    consigneePostalCode: data.consigneePostal || '04655',
                    personResponsibleName: data.consigneeName || 'ATLANTIC-UMMA LLC',
                    personResponsibleAddress: consigneeAddr || '4-A NOVOKONSTANTYNIVSKA\nSTR,KYIV,UKRAINE,04655.',
                    personResponsiblePostalCode: data.consigneePostal || '04655',
                    countryOfOriginName: data.countryOrigin || 'SRI LANKA',
                    countryOfOriginISO: 'LK',
                    countryOfOriginZone: 'INDIAN OCEAN',
                    zoneOrigin: 'INDIAN OCEAN',
                    zoneOriginCode: '57',
                    countryDestinationName: 'UKRAINE',
                    countryDestinationISO: 'UA',
                    placeOriginName: plantName,
                    placeOriginApprovalNumber: approvalNo,
                    placeOriginAddress: plantAddr || 'ST.JUDE MAWATHA,KATUNERIYA,SRI LANKA',
                    placeLoadingAddress: `COLOMBO / SRI LANKA\n${plantAddr || 'ST.JUDE MAWATHA,KATUNERIYA,SRI LANKA'}`,
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    transportAeroplane: isAir,
                    transportShip: isSea,
                    transportIdentification: data.transportId || '',
                    transportDocumentReferences: data.docReferences || '',
                    entryBIPUkraine: data.entryBIP || 'JAGODIN',
                    commodityCodeHS: hsCodes || '0302, 0304,0306,0307',
                    temperatureChilled: data.temperatureChilled ?? true,
                    temperatureFrozen: data.temperatureFrozen ?? false,
                    numberOfPackages: data.numPackages ? `${data.numPackages}` : '',
                    quantity: data.netWeight ? `${data.netWeight}` : '',
                    commoditiesHumanConsumption: true,
                    certifiedDate: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date()
                });

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p) => {
                        const speciesTitle = p.descScientific
                            ? `${p.descCommon ? p.descCommon.toUpperCase() : ''}(${p.descScientific})`
                            : p.descCommon || '';
                        this.products.push(
                            this.fb.group({
                                species: [speciesTitle],
                                natureOfCommodity: [(p as any).harvestMethod || 'WILD ORIGIN'],
                                treatmentApprovalNumber: [p.processingType || 'FRESH CHILLED LOINS/ WHOLE'],
                                manufacturingPlant: [plantName],
                                numberOfPackaging: [p.numPackages || ''],
                                typeOfPackaging: [data.packagingType || 'VACUUM PACKED IN POLYSTYRENE BOXES WITH GEL ICE AND CORRUGATED BOXES WITH GEL ICE'],
                                netWeight: [p.netWeight || '']
                            })
                        );
                    });
                } else {
                    this.loadDefaultProducts();
                }
            },
            error: () => {
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to load initial vet form data' });
            }
        });
    }

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.form.patchValue({ signatoryName: '', qualification: '' });
            return;
        }

        const selected = this.users.find((u) => u.id === userId);
        if (selected) {
            this.pendingSignatoryUserId = selected.id;
            this.pendingSignatoryUserEmail = selected.email;
            this.selectedUserQualification = selected.qualification || 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc. (FOOD SCI. & TECH.) SP. (SRI LANKA).';
            this.showPasswordDialog = true;
        }
    }

    onSignatoryConfirmed(officerName: string) {
        if (this.pendingSignatoryUserId) {
            this.form.patchValue({
                signatoryUserId: this.pendingSignatoryUserId,
                signatoryName: officerName,
                qualification: this.selectedUserQualification || 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc. (FOOD SCI. & TECH.) SP. (SRI LANKA).'
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

    onSubmit(): void {
        if (!this.isCompany && !this.form.get('signatoryUserId')?.value && this.viewMode !== 'attachment_only') {
            this.messageService.add({
                severity: 'error',
                summary: 'Signatory Required',
                detail: 'Please select and verify authorized signatory.'
            });
            return;
        }

        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        if (this.form.invalid && this.viewMode !== 'attachment_only') {
            this.messageService.add({
                severity: 'error',
                summary: 'Validation Error',
                detail: 'Please fill in all required fields'
            });
            return;
        }

        this.isSaving = true;
        const rawValue = this.form.getRawValue();

        const productPayloads: CreateUaCertificateProductPayload[] = (rawValue.products || []).map((p: any) => ({
            species: p.species || '',
            natureOfCommodity: p.natureOfCommodity || '',
            treatmentApprovalNumber: p.treatmentApprovalNumber || '',
            manufacturingPlant: p.manufacturingPlant || '',
            numberOfPackaging: p.numberOfPackaging ? String(p.numberOfPackaging) : '',
            typeOfPackaging: p.typeOfPackaging || '',
            netWeight: p.netWeight ? String(p.netWeight) : ''
        }));

        const payload: CreateUaCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            consignorName: rawValue.consignorName || '',
            consignorAddress: rawValue.consignorAddress || '',
            consignorPostalCode: rawValue.consignorPostalCode || '',
            consignorTelNo: rawValue.consignorTelNo || '',
            certificateReferenceNumber: rawValue.certificateReferenceNumber || '',
            centralCompetentAuthority: rawValue.centralCompetentAuthority || '',
            localCompetentAuthority: rawValue.localCompetentAuthority || '',
            consigneeName: rawValue.consigneeName || '',
            consigneeAddress: rawValue.consigneeAddress || '',
            consigneePostalCode: rawValue.consigneePostalCode || '',
            consigneeTel: rawValue.consigneeTel || '',
            personResponsibleName: rawValue.personResponsibleName || '',
            personResponsibleAddress: rawValue.personResponsibleAddress || '',
            personResponsiblePostalCode: rawValue.personResponsiblePostalCode || '',
            personResponsibleTel: rawValue.personResponsibleTel || '',
            countryOfOriginName: rawValue.countryOfOriginName || '',
            countryOfOriginISO: rawValue.countryOfOriginISO || '',
            countryOfOriginISOCode: rawValue.countryOfOriginISOCode || '',
            countryOfOriginZone: rawValue.countryOfOriginZone || '',
            zoneOrigin: rawValue.zoneOrigin || '',
            zoneOriginCode: rawValue.zoneOriginCode || '',
            countryDestinationName: rawValue.countryDestinationName || '',
            countryDestinationISO: rawValue.countryDestinationISO || '',
            countryDestinationISOCode: rawValue.countryDestinationISOCode || '',
            countryDestinationZone: rawValue.countryDestinationZone || '',
            zoneDestination: rawValue.zoneDestination || '',
            zoneDestinationCode: rawValue.zoneDestinationCode || '',
            placeOriginName: rawValue.placeOriginName || '',
            placeOriginApprovalNumber: rawValue.placeOriginApprovalNumber || '',
            placeOriginAddress: rawValue.placeOriginAddress || '',
            field112: rawValue.field112 || '',
            placeLoadingAddress: rawValue.placeLoadingAddress || '',
            dateOfDeparture: toLocalISOString(rawValue.dateOfDeparture),
            transportAeroplane: !!rawValue.transportAeroplane,
            transportShip: !!rawValue.transportShip,
            transportRailwayWagon: !!rawValue.transportRailwayWagon,
            transportRoadVehicle: !!rawValue.transportRoadVehicle,
            transportOther: !!rawValue.transportOther,
            transportIdentification: rawValue.transportIdentification || '',
            transportDocumentReferences: rawValue.transportDocumentReferences || '',
            entryBIPUkraine: rawValue.entryBIPUkraine || '',
            descriptionOfCommodity: rawValue.descriptionOfCommodity || '',
            commodityCodeHS: rawValue.commodityCodeHS || '',
            quantity: rawValue.quantity || '',
            temperatureAmbient: !!rawValue.temperatureAmbient,
            temperatureChilled: !!rawValue.temperatureChilled,
            temperatureFrozen: !!rawValue.temperatureFrozen,
            numberOfPackages: rawValue.numberOfPackages || '',
            sealContainerNo: rawValue.sealContainerNo || '',
            typeOfPackaging: rawValue.typeOfPackaging || '',
            commoditiesHumanConsumption: !!rawValue.commoditiesHumanConsumption,
            field126: rawValue.field126 || '',
            forImportIntoUkraine: rawValue.forImportIntoUkraine || '',
            healthInfoNotes: rawValue.healthInfoNotes || '',
            healthCertificateReferenceNumber: rawValue.healthCertificateReferenceNumber || '',
            additionalInformation: rawValue.additionalInformation || '',
            officialStamp: rawValue.officialStamp || '',
            officialSignature: rawValue.officialSignature || '',
            signatoryUserId: rawValue.signatoryUserId || null,
            signatoryName: rawValue.signatoryName || '',
            qualification: rawValue.qualification || '',
            certifiedDate: toLocalISOString(rawValue.certifiedDate),
            certificateType: this.viewMode,
            products: productPayloads
        };

        this.certificateService.submitUaCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Ukraine Health Certificate saved successfully'
                });
                if (this.certificateRequestId) {
                    try {
                        const submitted = JSON.parse(localStorage.getItem('dfar_submitted_requests') || '[]');
                        if (!submitted.includes(this.certificateRequestId)) {
                            submitted.push(this.certificateRequestId);
                            localStorage.setItem('dfar_submitted_requests', JSON.stringify(submitted));
                        }
                    } catch {}
                }
                setTimeout(() => this.goBack(), 1500);
            },
            error: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to save Ukraine Health Certificate'
                });
            }
        });
    }

    printCertificate(): void {
        if (!this.isAdmin) {
            this.messageService.add({
                severity: 'error',
                summary: 'Access Denied',
                detail: 'Only administrators have access to print health certificates.'
            });
            return;
        }
        const ref = this.form.get('certificateReferenceNumber')?.value || 'SY_7511';
        let pdfName = '';
        if (this.viewMode === 'attachment_only') {
            pdfName = `${ref}_Ukraine_Attachment.pdf`;
        } else {
            pdfName = `${ref}_Ukraine_Veterinary_Certificate.pdf`;
        }

        const originalTitle = document.title;
        document.title = pdfName;
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }

    goBack(): void {
        const userRole = (this.authService.getUserRole() || '').toLowerCase();
        const targetUrl = userRole === 'admin' ? '/uikit/admin/certificate-requests' : '/uikit/company-request';
        if (typeof window !== 'undefined' && window.self !== window.top && window.top) {
            window.top.location.href = targetUrl;
        } else {
            this.router.navigate([targetUrl]);
        }
    }

    isPdf(dataUrl?: string | null): boolean {
        if (!dataUrl) return false;
        return dataUrl.startsWith('data:application/pdf') || dataUrl.toLowerCase().endsWith('.pdf');
    }

    updateSafeStampUrl() {
        const stamp = this.form.get('officialStamp')?.value;
        if (stamp && this.isPdf(stamp)) {
            this.safeStampPdfUrl = this.sanitizer.bypassSecurityTrustResourceUrl(stamp);
        } else {
            this.safeStampPdfUrl = null;
        }
    }

    updateSafeSignatureUrl() {
        const sig = this.form.get('officialSignature')?.value;
        if (sig && this.isPdf(sig)) {
            this.safeSignaturePdfUrl = this.sanitizer.bypassSecurityTrustResourceUrl(sig);
        } else {
            this.safeSignaturePdfUrl = null;
        }
    }

    onStampFileSelected(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            this.processFile(input.files[0], 'stamp');
            input.value = '';
        }
    }

    onSignatureFileSelected(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            this.processFile(input.files[0], 'signature');
            input.value = '';
        }
    }

    onStampDrop(event: DragEvent) {
        event.preventDefault();
        this.isDraggingStamp = false;
        if (this.viewOnly) return;
        if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
            this.processFile(event.dataTransfer.files[0], 'stamp');
        }
    }

    onSignatureDrop(event: DragEvent) {
        event.preventDefault();
        this.isDraggingSignature = false;
        if (this.viewOnly) return;
        if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
            this.processFile(event.dataTransfer.files[0], 'signature');
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

    onSignatureDragOver(event: DragEvent) {
        event.preventDefault();
        if (!this.viewOnly) this.isDraggingSignature = true;
    }

    onSignatureDragLeave(event: DragEvent) {
        event.preventDefault();
        this.isDraggingSignature = false;
    }

    onStampPaste(event: ClipboardEvent) {
        if (this.viewOnly) return;
        const items = event.clipboardData?.items;
        if (items) {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1 || items[i].type.indexOf('pdf') !== -1) {
                    const file = items[i].getAsFile();
                    if (file) {
                        this.processFile(file, 'stamp');
                        event.preventDefault();
                        break;
                    }
                }
            }
        }
    }

    onSignaturePaste(event: ClipboardEvent) {
        if (this.viewOnly) return;
        const items = event.clipboardData?.items;
        if (items) {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1 || items[i].type.indexOf('pdf') !== -1) {
                    const file = items[i].getAsFile();
                    if (file) {
                        this.processFile(file, 'signature');
                        event.preventDefault();
                        break;
                    }
                }
            }
        }
    }

    processFile(file: File, target: 'stamp' | 'signature') {
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
            if (target === 'stamp') {
                this.form.patchValue({ officialStamp: result });
                this.updateSafeStampUrl();
            } else {
                this.form.patchValue({ officialSignature: result });
                this.updateSafeSignatureUrl();
            }
            this.messageService.add({
                severity: 'success',
                summary: 'Uploaded Successfully',
                detail: `${target === 'stamp' ? 'Official Stamp' : 'Official Signature'} uploaded.`
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

    removeSignature(event?: Event) {
        if (event) event.stopPropagation();
        this.form.patchValue({ officialSignature: '' });
        this.safeSignaturePdfUrl = null;
    }
}
