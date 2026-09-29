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
    TwCertificateView,
    CreateTwCertificatePayload,
    CreateTwCertificateProductPayload,
    VetFormFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { toLocalISOString } from '@/shared/utils/date-utils';

export const DEFAULT_TC4361_PRODUCTS = [
    { scientificName: 'GLASSEYE SNAPPER( Heteropriacanthus cruentatus)', netWeight: '', numberOfPackages: '' },
    { scientificName: 'GROUPER FISH (Epinephelus malabaricus )', netWeight: '', numberOfPackages: '' },
    { scientificName: 'GROUPER FISH ( Epinephelus fuscoguttatus )', netWeight: '', numberOfPackages: '' },
    { scientificName: 'PLAIN GROUPER (Epinephelus diacanthus)', netWeight: '', numberOfPackages: '' },
    { scientificName: 'SPANISH MACKERAL (Scomberomorus commerson)', netWeight: '', numberOfPackages: '' },
    { scientificName: 'Barramundi (Lates calcarifer)', netWeight: '', numberOfPackages: '' },
    { scientificName: 'Pomfret( Pampus argenteus)', netWeight: '', numberOfPackages: '' },
    { scientificName: 'EMPEOR FISH ( Lethrinus nebulosus )', netWeight: '', numberOfPackages: '' },
    { scientificName: 'SAND WHITING (Silago sihama)', netWeight: '', numberOfPackages: '' },
    { scientificName: 'TRAVELLY (Caranx senegallus)', netWeight: '', numberOfPackages: '' },
    { scientificName: 'PEARL SPOT( Etroplus suratensis )', netWeight: '', numberOfPackages: '' },
    { scientificName: 'WHITE FISH ( Lactarius lactarius )', netWeight: '', numberOfPackages: '' },
    { scientificName: 'SOLDIER FISH ( Myripristis berndti)', netWeight: '', numberOfPackages: '' }
];

@Component({
    selector: 'app-tw-certificate',
    standalone: true,
    imports: [
        CommonModule,
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
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './tw-certificate.component.html',
    styleUrls: ['./tw-certificate.component.css', '../certificate-print.css']
})
export class TwCertificateComponent implements OnInit {
    form: FormGroup;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;
    isApproved = false;

    // View mode: 'fish' | 'shellfish' | 'quality'
    viewMode: 'fish' | 'shellfish' | 'quality' = 'fish';

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
            // Certificate Type: 'fish' or 'shellfish'
            certificateType: ['fish'],
            includeAttachment: [true],
            includeQualityCertificate: [false],

            // Certificate Details
            referenceNo: ['TC 4361'],

            // Section I: Information of competent authority
            countryOfExport: ['SRI LANKA'],
            countryOfProduction: ['SRI LANKA'],
            competentAuthority: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],
            departmentIssuance: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],

            // Section II: Identification of fishery products (Single / Summary fields)
            commodityName: ['WILD CAUGHT FRESH CHILLED FISH – SEE THE ATTACHMENT'],
            hsCode: ['SEE THE ATTACHMENT'],
            scientificName: ['SEE THE ATTACHMENT'],
            numberOfPackages: [''],
            netWeight: [''],

            // Products array (for Attachment page - initialized with default 13 items from TC 4361)
            products: this.fb.array(DEFAULT_TC4361_PRODUCTS.map((p) => this.createProductRow(p))),

            // Section III: Origin of the fishery products
            productionPlace: ['INDIAN OCEAN'],
            processingType: ['FRESH'],
            productionMode: [''],
            aquaculturedYes: [false],
            aquaculturedNo: [false],
            wildCaughtYes: [true],
            wildCaughtNo: [false],
            aquacultureArea: [''],
            catchArea: ['FAO 57'],
            harvestingArea: ['FAO 57'],
            vesselName: ['***'],
            enterpriseName: ['MAISHA ANISHA LANKA (PVT) LTD'],
            enterpriseRegistrationNo: ['DFAR/FPE/98/74'],
            enterpriseAddress: ['NO : 847, KETAGEWATTA , RAGAMA, SRI LANKA.\nLicense No: DFAR/FPE/98/74'],
            productionDate: [new Date('2026-08-21')],

            // Section IV: Information of Transport
            consignorName: ['ZAM MARINE (PVT) LTD'],
            consignorAddress: ['NO. 49, JETTY STREET , KALPITIYA,\nSRI LANKA.'],
            consigneeName: ['ASSEMBLE WEALTHY CO'],
            consigneeAddress: ['14 LN 220 SEC 1 MINYI RD\nWUGU DIST 24855 NEW TAIPEI CITY\nTAIPEI, TAIPEI TIWAN PROVINCE OF CHINA'],
            placeOfDispatch: ['COLOMBO – SRI LANKA'],
            placeOfDestination: ['TAIWAN'],
            meansOfTransport: ['BY AIR FREIGHT'],
            vesselNameTransport: ['***'],
            flightNumber: ['***'],
            otherTransportMeans: ['***'],
            containerNumber: ['***'],
            sealNumber: ['***'],

            // Section V: Health Attestation
            placeOfIssue: ['COLOMBO – SRI LANKA'],
            dateOfIssue: [new Date('2026-08-21')],
            officialStamp: [''],
            officialSignature: [''],
            signatoryUserId: [null, Validators.required],
            signatoryName: [''],
            qualification: [''],

            // Quality Certificate Attachment Fields
            qualityDocumentReferenceNo: ['TC 4361'],
            qualityHumanConsumptionYes: [true],
            qualityHumanConsumptionNo: [false],
            qualityEstablishmentName: ['MAISHA ANISHA LANKA (PVT) LTD'],
            qualityEstablishmentAuthNumber: ['DFAR/FPE/98/74'],
            qualityProducts: this.fb.array([this.createQualityProductRow()]),
            qualityTestedBy: ['SGS LANKA (PVT) LTD., 141/6, VAUXHALL ST, COLOMBO 02, SRI LANKA.'],
            qualityCountryOfDestination: ['TAIWAN'],
            qualityCommercialInvoice: [''],
            qualityUniqueCode: [''],
            qualityObservations: ['']
        });

        this.form.get('certificateType')?.valueChanges.subscribe((type: 'fish' | 'shellfish') => {
            if (type && this.viewMode !== 'quality') {
                this.viewMode = type;
            }
        });

        this.form.get('includeAttachment')?.valueChanges.subscribe((incl: boolean) => {
            if (incl) {
                const currentComm = this.form.get('commodityName')?.value;
                if (!currentComm || !currentComm.includes('SEE THE ATTACHMENT')) {
                    const prefix = currentComm ? `${currentComm} – ` : 'WILD CAUGHT FRESH CHILLED FISH – ';
                    this.form.patchValue({
                        commodityName: `${prefix}SEE THE ATTACHMENT`,
                        hsCode: 'SEE THE ATTACHMENT',
                        scientificName: 'SEE THE ATTACHMENT'
                    });
                }
                if (this.products.length === 0) {
                    this.loadDefaultAttachmentProducts();
                }
            }
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    get qualityProducts(): FormArray {
        return this.form.get('qualityProducts') as FormArray;
    }

    createProductRow(data?: any): FormGroup {
        return this.fb.group({
            commodityName: [data?.commodityName || ''],
            hsCode: [data?.hsCode || ''],
            scientificName: [data?.scientificName || data?.product || data?.productName || ''],
            numberOfPackages: [data?.numberOfPackages !== undefined ? data.numberOfPackages : ''],
            netWeight: [data?.netWeight !== undefined ? data.netWeight : '']
        });
    }

    createQualityProductRow(data?: any): FormGroup {
        return this.fb.group({
            productName: [data?.productName || ''],
            productType: [data?.productType || ''],
            testResults: [
                data?.testResults ||
                'AEROBIC PLATE COUNT (CFU/g) - 30°C – 5.5×102\n' +
                'Enterobacteriaceae (MPN/g) - NOT DETECTED\n' +
                'COLIFORMS (MPN/g) - NOT DETECTED\n' +
                'E.coli (MPN/g) -NOT DETECTED\n' +
                'Staphylococcus aureus (CFU/g) - < 10\n' +
                'Salmonella spp. in 25g - ABSENT\n' +
                'Listeria monocytogenes in 25g - ABSENT\n' +
                '~ Vibrio spp. in 25g - ABSENT\n' +
                '~ Shigella in 25g - ABSENT'
            ],
            lotCode: [data?.lotCode || ''],
            samplingDate: [data?.samplingDate || ''],
            numberOfBoxes: [data?.numberOfBoxes || ''],
            lotWeight: [data?.lotWeight || '']
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

    loadDefaultAttachmentProducts(): void {
        this.products.clear();
        DEFAULT_TC4361_PRODUCTS.forEach((p) => {
            this.products.push(this.createProductRow(p));
        });
    }

    clearAttachmentProducts(): void {
        this.products.clear();
        this.products.push(this.createProductRow());
    }

    addQualityProduct(): void {
        this.qualityProducts.push(this.createQualityProductRow());
    }

    removeQualityProduct(index: number): void {
        if (this.qualityProducts.length > 1) {
            this.qualityProducts.removeAt(index);
        }
    }

    calculateTotalPackages(): string {
        const total = this.products.controls.reduce((sum, c) => {
            const val = c.get('numberOfPackages')?.value;
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

    calculateQualityTotalBoxes(): string {
        const total = this.qualityProducts.controls.reduce((sum, c) => {
            const val = c.get('numberOfBoxes')?.value;
            if (typeof val === 'number') return sum + val;
            const parsed = parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total}CTNS` : '';
    }

    calculateQualityTotalWeight(): string {
        const total = this.qualityProducts.controls.reduce((sum, c) => {
            const val = c.get('lotWeight')?.value;
            if (typeof val === 'number') return sum + val;
            const parsed = parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total.toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} KGS` : '';
    }

    get totalPages(): number {
        if (this.viewMode === 'quality') return 1;
        let base = 2;
        if (this.form.get('includeAttachment')?.value) base += 1;
        if (this.form.get('includeQualityCertificate')?.value) base += 1;
        return base;
    }

    get attachmentPageNumber(): number {
        return 3;
    }

    get qualityPageNumber(): number {
        if (this.viewMode === 'quality') return 1;
        return this.totalPages;
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

            if (params['mode'] === 'quality' || this.router.url.includes('tw-quality-certificate')) {
                this.viewMode = 'quality';
            }

            if (params['ref']) {
                this.form.patchValue({
                    referenceNo: params['ref'],
                    qualityDocumentReferenceNo: params['ref']
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
                    const st = typeof req.status === 'string' ? req.status.toLowerCase() : (req.status === 1 ? 'confirmed' : 'pending');
                    this.isApproved = (st === 'confirmed' || st === 'approved' || req.status === 1);
                }
            },
            error: () => {}
        });
    }

    onCertificateTypeChange(type: 'fish' | 'shellfish') {
        this.viewMode = type;
        this.form.get('certificateType')?.setValue(type);
        if (type === 'shellfish') {
            this.form.patchValue({
                includeAttachment: false,
                commodityName: 'BLUE SWIMMING CRAB',
                scientificName: 'Portunus pelagicus',
                hsCode: '',
                processingType: 'Chilled'
            });
        } else {
            this.form.patchValue({
                commodityName: 'WILD CAUGHT FRESH CHILLED FISH – SEE THE ATTACHMENT',
                scientificName: 'SEE THE ATTACHMENT',
                hsCode: 'SEE THE ATTACHMENT',
                processingType: 'FRESH'
            });
            if (this.products.length === 0) {
                this.loadDefaultAttachmentProducts();
            }
        }
    }

    private loadSavedCertificateData(requestId: number): void {
        this.certificateService.getTwCertificateByRequestId(requestId).subscribe({
            next: (data: TwCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                const certType = (data.certificateType as 'fish' | 'shellfish') || 'fish';
                this.viewMode = certType;

                const firstProd = data.products && data.products.length > 0 ? data.products[0] : null;
                const hasMultipleProducts = (data.products && data.products.length > 1);

                this.form.patchValue({
                    certificateType: certType,
                    includeAttachment: hasMultipleProducts,
                    referenceNo: data.referenceNo || 'TC 4361',
                    qualityDocumentReferenceNo: data.referenceNo || 'TC 4361',
                    countryOfExport: data.countryOfExport || 'SRI LANKA',
                    countryOfProduction: data.countryOfProduction || 'SRI LANKA',
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    departmentIssuance: data.departmentIssuance || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',

                    commodityName: firstProd?.commodityName || (hasMultipleProducts ? 'WILD CAUGHT FRESH CHILLED FISH – SEE THE ATTACHMENT' : ''),
                    hsCode: firstProd?.hsCode || (hasMultipleProducts ? 'SEE THE ATTACHMENT' : ''),
                    scientificName: firstProd?.scientificName || (hasMultipleProducts ? 'SEE THE ATTACHMENT' : ''),
                    numberOfPackages: firstProd?.numberOfPackages ? `${firstProd.numberOfPackages}` : '',
                    netWeight: firstProd?.netWeight ? `${firstProd.netWeight}` : '',

                    productionPlace: data.productionPlace || 'INDIAN OCEAN',
                    processingType: data.processingType || 'FRESH',
                    productionMode: data.productionMode || '',
                    aquaculturedYes: data.aquaculturedYes ?? false,
                    aquaculturedNo: data.aquaculturedNo ?? false,
                    wildCaughtYes: data.wildCaughtYes ?? true,
                    wildCaughtNo: data.wildCaughtNo ?? false,
                    aquacultureArea: data.aquacultureArea || '',
                    catchArea: data.catchArea || 'FAO 57',
                    harvestingArea: data.harvestingArea || 'FAO 57',
                    vesselName: data.vesselName || '***',
                    enterpriseName: data.enterpriseName || 'MAISHA ANISHA LANKA (PVT) LTD',
                    enterpriseRegistrationNo: data.enterpriseRegistrationNo || 'DFAR/FPE/98/74',
                    productionDate: data.productionDate ? new Date(data.productionDate) : new Date(),

                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    placeOfDispatch: data.placeOfDispatch || 'COLOMBO – SRI LANKA',
                    placeOfDestination: data.placeOfDestination || 'TAIPEI,- TAIWAN',
                    meansOfTransport: data.meansOfTransport || 'BY AIR FREIGHT',
                    vesselNameTransport: data.vesselNameTransport || '***',
                    flightNumber: data.flightNumber || '***',
                    otherTransportMeans: data.otherTransportMeans || '***',
                    containerNumber: data.containerNumber || '***',
                    sealNumber: data.sealNumber || '***',

                    placeOfIssue: data.placeOfIssue || 'COLOMBO – SRI LANKA',
                    dateOfIssue: data.dateOfIssue ? new Date(data.dateOfIssue) : new Date(),
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || '',
                    signatoryUserId: data.signatoryUserId,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || '',

                    qualityEstablishmentName: data.enterpriseName || 'MAISHA ANISHA LANKA (PVT) LTD',
                    qualityEstablishmentAuthNumber: data.enterpriseRegistrationNo || 'DFAR/FPE/98/74',
                    qualityCountryOfDestination: 'TAIWAN'
                });

                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();

                this.selectedUserQualification = data.qualification ?? null;
                if (data.signatoryUserId) {
                    this.previousSignatoryUserId = data.signatoryUserId;
                }

                if (data.products && data.products.length > 0) {
                    this.products.clear();
                    data.products.forEach((p) => {
                        this.products.push(this.createProductRow(p));
                    });
                } else {
                    this.loadDefaultAttachmentProducts();
                }

                if (!this.viewOnly) {
                    this.form.enable();
                }
            },
            error: () => {
                this.loadVetFormData(requestId);
            }
        });
    }

    private loadVetFormData(requestId: number) {
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
            next: (vetForm: VetFormFieldResponse) => {
                if (!vetForm) return;

                const firstProd = vetForm.products && vetForm.products.length > 0 ? vetForm.products[0] : null;
                const hasMultipleProducts = (vetForm.products && vetForm.products.length > 1);

                this.form.patchValue({
                    includeAttachment: hasMultipleProducts,
                    referenceNo: vetForm.healthCertNo || 'TC 4361',
                    qualityDocumentReferenceNo: vetForm.healthCertNo || 'TC 4361',
                    countryOfExport: vetForm.countryOrigin || 'SRI LANKA',
                    countryOfProduction: vetForm.countryOrigin || 'SRI LANKA',
                    commodityName: hasMultipleProducts ? 'WILD CAUGHT FRESH CHILLED FISH – SEE THE ATTACHMENT' : (firstProd?.descCommon || vetForm.descCommon || ''),
                    hsCode: hasMultipleProducts ? 'SEE THE ATTACHMENT' : (firstProd?.hsCode || vetForm.hsCode || ''),
                    scientificName: hasMultipleProducts ? 'SEE THE ATTACHMENT' : (firstProd?.descScientific || ''),
                    numberOfPackages: firstProd?.numPackages || vetForm.numPackages || '',
                    netWeight: firstProd?.netWeight || vetForm.netWeight || '',

                    consignorName: vetForm.consignorName || '',
                    consignorAddress: vetForm.consignorAddress || '',
                    consigneeName: vetForm.consigneeName || '',
                    consigneeAddress: vetForm.consigneeAddress || '',
                    placeOfDispatch: vetForm.placeOfLoading || 'COLOMBO – SRI LANKA',
                    placeOfDestination: vetForm.entryBIP || 'TAIPEI,- TAIWAN',
                    dateOfIssue: vetForm.dateOfDeparture ? new Date(vetForm.dateOfDeparture) : new Date(),
                    enterpriseName: vetForm.processingEstName || 'MAISHA ANISHA LANKA (PVT) LTD',
                    enterpriseRegistrationNo: vetForm.approvalNo || 'DFAR/FPE/98/74',
                    wildCaughtYes: vetForm.productTypeWildCaught ?? true,
                    wildCaughtNo: !(vetForm.productTypeWildCaught ?? true),
                    aquaculturedYes: vetForm.productTypeAquaculture ?? false,
                    aquaculturedNo: !(vetForm.productTypeAquaculture ?? false),

                    qualityEstablishmentName: vetForm.processingEstName || 'MAISHA ANISHA LANKA (PVT) LTD',
                    qualityEstablishmentAuthNumber: vetForm.approvalNo || 'DFAR/FPE/98/74',
                    qualityCommercialInvoice: vetForm.docReferences || '',
                    qualityCountryOfDestination: 'TAIWAN'
                });

                if (vetForm.products && vetForm.products.length > 0) {
                    this.products.clear();
                    vetForm.products.forEach((p) => {
                        this.products.push(
                            this.createProductRow({
                                commodityName: p.descCommon || vetForm.descCommon,
                                hsCode: p.hsCode || vetForm.hsCode,
                                scientificName: p.descScientific || p.descCommon,
                                numberOfPackages: p.numPackages ? parseInt(p.numPackages, 10) : '',
                                netWeight: p.netWeight ? parseFloat(p.netWeight) : ''
                            })
                        );
                    });

                    // Also populate Quality Products array
                    this.qualityProducts.clear();
                    vetForm.products.forEach((p) => {
                        this.qualityProducts.push(
                            this.createQualityProductRow({
                                productName: p.descCommon || '',
                                productType: p.descCommon ? `FROZEN WHOLE ROUND ${p.descCommon}` : '',
                                lotCode: '',
                                samplingDate: '',
                                numberOfBoxes: p.numPackages ? `${p.numPackages} CTNS` : '',
                                lotWeight: p.netWeight ? `${p.netWeight} KGS` : ''
                            })
                        );
                    });
                } else {
                    this.loadDefaultAttachmentProducts();
                }
            }
        });
    }

    // ─────────────────────────────────────────────────────────────
    // Sample Loaders (Matching Official Provided DFAR Certificates)
    // ─────────────────────────────────────────────────────────────

    /**
     * 1. Taiwan Shellfish Health Certificate (TA 6894 - 2 Pages)
     */
    loadSampleShellfish(): void {
        this.viewMode = 'shellfish';
        this.form.patchValue({
            certificateType: 'shellfish',
            includeAttachment: false,
            includeQualityCertificate: false,
            referenceNo: 'TA 6894',
            countryOfExport: 'SRI LANKA',
            countryOfProduction: 'SRI LANKA',
            competentAuthority: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            departmentIssuance: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            commodityName: 'BLUE SWIMMING CRAB',
            hsCode: '',
            scientificName: 'Portunus pelagicus',
            numberOfPackages: '',
            netWeight: '',
            productionPlace: '',
            processingType: 'Chilled',
            productionMode: 'Wild Caught',
            aquaculturedYes: false,
            aquaculturedNo: false,
            wildCaughtYes: true,
            wildCaughtNo: false,
            aquacultureArea: '',
            catchArea: 'FAO 57',
            harvestingArea: 'FAO 57',
            vesselName: '',
            enterpriseName: 'WESTERN LANKA FISHERIES (PVT) LTD,',
            enterpriseRegistrationNo: 'DFAR/FPE/98/26',
            enterpriseAddress: 'NO:126, NEGOMBO ROAD, WAHATIYAGODA, \nPAMUNUGAMA, JA ELA. SRI LANKA.\nLicense No: DFAR/FPE/98/26',
            productionDate: new Date('2026-01-12'),
            consignorName: 'WESTERN LANKA FISHERIES (PVT) LTD,',
            consignorAddress: 'NO:126, NEGOMBO ROAD, WAHATIYAGODA, \nPAMUNUGAMA, JA ELA. SRI LANKA.',
            consigneeName: 'FEASTOGETHER GROUP,',
            consigneeAddress: 'No.12, LN.150, WUQING RD,DAYUAN DIST., \nTAOYUAN CTY 337,\nTAIWAN (R.O.C) 33755',
            placeOfDispatch: 'COLOMBO – SRI LANKA',
            placeOfDestination: 'TAIPEI,- TAIWAN',
            meansOfTransport: 'BY AIR FREIGHT',
            vesselNameTransport: '***',
            flightNumber: 'MHI78/MH366',
            otherTransportMeans: '***',
            containerNumber: '***',
            sealNumber: '***',
            placeOfIssue: 'COLOMBO – SRI LANKA',
            dateOfIssue: new Date('2026-01-12')
        });

        this.products.clear();
        this.products.push(this.createProductRow({
            commodityName: 'BLUE SWIMMING CRAB',
            scientificName: 'Portunus pelagicus',
            numberOfPackages: '',
            netWeight: ''
        }));

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Shell Fish Sample',
            detail: 'Loaded Taiwan Shell Fish Health Certificate sample (TA 6894 - 2 Pages).'
        });
    }

    /**
     * 2. Taiwan Fish and Fishery Products Certificate with Attachment (TC 4361 - 3 Pages)
     */
    loadSampleFish(): void {
        this.viewMode = 'fish';
        this.form.patchValue({
            certificateType: 'fish',
            includeAttachment: true,
            includeQualityCertificate: false,
            referenceNo: 'TC 4361',
            countryOfExport: 'SRI LANKA',
            countryOfProduction: 'SRI LANKA',
            competentAuthority: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            departmentIssuance: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            commodityName: 'WILD CAUGHT FRESH CHILLED FISH – SEE THE ATTACHMENT',
            hsCode: 'SEE THE ATTACHMENT',
            scientificName: 'SEE THE ATTACHMENT',
            numberOfPackages: '',
            netWeight: '',
            productionPlace: 'INDIAN OCEAN',
            processingType: 'FRESH',
            productionMode: 'Wild Caught',
            aquaculturedYes: false,
            aquaculturedNo: false,
            wildCaughtYes: true,
            wildCaughtNo: false,
            aquacultureArea: '',
            catchArea: 'FAO 57',
            harvestingArea: 'FAO 57',
            vesselName: '',
            enterpriseName: 'MAISHA ANISHA LANKA (PVT) LTD',
            enterpriseRegistrationNo: 'DFAR/FPE/98/74',
            enterpriseAddress: 'NO : 847, KETAGEWATTA , RAGAMA, SRI LANKA.\nLicense No: DFAR/FPE/98/74',
            productionDate: new Date('2026-08-21'),
            consignorName: 'ZAM MARINE (PVT) LTD',
            consignorAddress: 'NO. 49, JETTY STREET , KALPITIYA,\nSRI LANKA.',
            consigneeName: 'ASSEMBLE WEALTHY CO',
            consigneeAddress: '14 LN 220 SEC 1 MINYI RD\nWUGU DIST 24855 NEW TAIPEI CITY\nTAIPEI, TAIPEI TIWAN PROVINCE OF CHINA',
            placeOfDispatch: 'COLOMBO – SRI LANKA',
            placeOfDestination: 'TAIWAN',
            meansOfTransport: 'BY AIR FREIGHT',
            vesselNameTransport: '***',
            flightNumber: '',
            otherTransportMeans: '***',
            containerNumber: '***',
            sealNumber: '***',
            placeOfIssue: 'COLOMBO – SRI LANKA',
            dateOfIssue: new Date('2026-08-21')
        });

        this.loadDefaultAttachmentProducts();

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Fish Sample with Attachment',
            detail: 'Loaded Taiwan Fish Health Certificate with 13 products in Attachment table (TC 4361 - 3 Pages).'
        });
    }

    /**
     * 3. Quality Certificate Sample (TA 9637)
     */
    loadSampleQuality(): void {
        this.form.patchValue({
            referenceNo: 'TA 9637',
            qualityDocumentReferenceNo: 'TA 9637',
            qualityHumanConsumptionYes: true,
            qualityHumanConsumptionNo: false,
            qualityEstablishmentName: 'ANNAI AND SONS (PRIVATE) LIMITED',
            qualityEstablishmentAuthNumber: 'DFAR/FPE/98/91',
            qualityTestedBy: 'SGS LANKA (PVT) LTD., 141/6, VAUXHALL ST, COLOMBO 02, SRI LANKA.',
            qualityCountryOfDestination: 'TAIWAN',
            qualityCommercialInvoice: 'ANNAI/2026/12FR',
            qualityUniqueCode: '',
            qualityObservations: '',
            includeQualityCertificate: true
        });

        this.qualityProducts.clear();
        this.qualityProducts.push(this.createQualityProductRow({
            productName: 'CUTTLE\nFISH',
            productType: 'FROZEN\nWHOLE\nROUND\nCUTTLE\nFISH',
            testResults:
                'AEROBIC PLATE\nCOUNT (CFU/g) -\n30°C – 5.5×102\n\n' +
                'Enterobacteriaceae\n(MPN/g) - NOT\nDETECTED\n\n' +
                'COLIFORMS (MPN/g)\n- NOT DETECTED\n\n' +
                'E.coli (MPN/g) -NOT\nDETECTED\n\n' +
                'Staphylococcus aureus\n(CFU/g) - < 10\n' +
                'Salmonella spp. in 25g\n- ABSENT\n\n' +
                'Listeria\nmonocytogenes in\n25g - ABSENT\n' +
                '~ Vibrio spp. in 25g -\nABSENT\n' +
                '~ Shigella in 25g -\nABSENT',
            lotCode: 'K 25323\n–\nB 26042',
            samplingDate: '20.11.2025–\n02.12.2025',
            numberOfBoxes: '1150CTNS',
            lotWeight: '23,000.000 KGS'
        }));

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Quality Certificate',
            detail: 'Loaded Quality Certificate attachment for Taiwan (TA 9637).'
        });
    }

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.previousSignatoryUserId = null;
            this.setSelectedUserQualification(userId);
            return;
        }

        const user = this.users.find((u) => u.id === userId);
        if (!user) return;

        this.pendingSignatoryUserId = userId;
        this.pendingSignatoryUserEmail = user.email;
        this.showPasswordDialog = true;
    }

    onSignatoryConfirmed(userId: string) {
        this.previousSignatoryUserId = userId;
        this.showPasswordDialog = false;
        this.setSelectedUserQualification(userId);
    }

    onSignatoryCanceled() {
        this.showPasswordDialog = false;
        this.form.get('signatoryUserId')?.setValue(this.previousSignatoryUserId, { emitEvent: false });
        if (this.previousSignatoryUserId) {
            this.setSelectedUserQualification(this.previousSignatoryUserId);
        } else {
            this.selectedUserQualification = null;
        }
        this.pendingSignatoryUserId = null;
    }

    setSelectedUserQualification(userId: string | null) {
        const user = this.users.find((u) => u.id === userId);
        this.selectedUserQualification = user?.qualification || null;
        if (user) {
            this.form.patchValue({
                signatoryName: user.name,
                qualification: user.qualification
            });
        }
    }

    onSubmit() {
        if (!this.isCompany && !this.form.get('signatoryUserId')?.value && this.viewMode !== 'quality') {
            this.messageService.add({
                severity: 'error',
                summary: 'Signatory Required',
                detail: 'Please select and verify an authorized signatory.'
            });
            return;
        }

        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        if (this.form.invalid && this.viewMode !== 'quality') {
            this.form.markAllAsTouched();
            this.messageService.add({
                severity: 'error',
                summary: 'Validation Error',
                detail: 'Please fill all required fields.'
            });
            return;
        }

        this.isSaving = true;
        const raw = this.form.getRawValue();

        const payload: CreateTwCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            certificateType: raw.certificateType || this.viewMode,
            referenceNo: raw.referenceNo,
            countryOfExport: raw.countryOfExport,
            countryOfProduction: raw.countryOfProduction,
            competentAuthority: raw.competentAuthority,
            departmentIssuance: raw.departmentIssuance,
            productionPlace: raw.productionPlace,
            processingType: raw.processingType,
            productionMode: raw.productionMode,
            aquaculturedYes: raw.aquaculturedYes,
            aquaculturedNo: raw.aquaculturedNo,
            wildCaughtYes: raw.wildCaughtYes,
            wildCaughtNo: raw.wildCaughtNo,
            aquacultureArea: raw.aquacultureArea,
            catchArea: raw.catchArea,
            harvestingArea: raw.harvestingArea,
            vesselName: raw.vesselName,
            enterpriseName: raw.enterpriseName,
            enterpriseRegistrationNo: raw.enterpriseRegistrationNo,
            productionDate: raw.productionDate ? toLocalISOString(raw.productionDate) : null,
            consignorName: raw.consignorName,
            consignorAddress: raw.consignorAddress,
            consigneeName: raw.consigneeName,
            consigneeAddress: raw.consigneeAddress,
            placeOfDispatch: raw.placeOfDispatch,
            placeOfDestination: raw.placeOfDestination,
            meansOfTransport: raw.meansOfTransport,
            vesselNameTransport: raw.vesselNameTransport,
            flightNumber: raw.flightNumber,
            otherTransportMeans: raw.otherTransportMeans,
            containerNumber: raw.containerNumber,
            sealNumber: raw.sealNumber,
            placeOfIssue: raw.placeOfIssue,
            dateOfIssue: raw.dateOfIssue ? toLocalISOString(raw.dateOfIssue) : null,
            officialStamp: raw.officialStamp,
            officialSignature: raw.officialSignature,
            signatoryUserId: raw.signatoryUserId,
            signatoryName: raw.signatoryName,
            qualification: raw.qualification,
            products: (raw.products || []).map((p: any) => ({
                commodityName: p.commodityName || '',
                hsCode: p.hsCode || '',
                scientificName: p.scientificName || '',
                numberOfPackages: Number(p.numberOfPackages) || 0,
                netWeight: Number(p.netWeight) || 0
            }))
        };

        this.certificateService.submitTwCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Taiwan Certificate saved successfully!'
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
                    detail: 'Failed to save Taiwan Certificate.'
                });
            }
        });
    }

    printCertificate(): void {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }
        const ref = this.form.get('referenceNo')?.value || 'Taiwan_Certificate';
        let suffix = '';
        if (this.viewMode === 'quality') suffix = '_Quality';
        else if (this.form.get('certificateType')?.value === 'shellfish') suffix = '_Shellfish';
        else suffix = '_Fish';

        const originalTitle = document.title;
        document.title = `${ref}_Taiwan_Certificate${suffix}`;
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
