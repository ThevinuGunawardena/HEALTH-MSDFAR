import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { CheckboxModule } from 'primeng/checkbox';
import { RadioButtonModule } from 'primeng/radiobutton';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { Select } from 'primeng/select';
import { TooltipModule } from 'primeng/tooltip';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { UserService, User } from '@/pages/service/user.service';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { CertificateRequestService, MyCertificateView, VetFormFieldResponse } from 'src/app/pages/service/certificate-request.service';
import { TableModule } from 'primeng/table';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-my-certificate',
    standalone: true,
    imports: [CommonModule,
        FormsModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        DatePicker,
        ToastModule,
        CheckboxModule,
        RadioButtonModule,
        TableModule,
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './my-certificate.component.html',
    styleUrls: ['./my-certificate.component.css', '../certificate-print.css']
})
export class MyCertificateComponent implements OnInit {
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
    isSubmitted = false;
    refNumber: string = '';
    viewMode: 'fresh' | 'quality' | 'frozen' = 'fresh';

    userOptions: { label: string; value: string }[] = [];
    users: User[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    previousSignatoryUserId: string | null = null;

    isDraggingSignature = false;
    isDraggingStamp = false;
    safeSignaturePdfUrl: SafeResourceUrl | null = null;
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
        private messageService: MessageService,
        private route: ActivatedRoute,
        private router: Router,
        private certificateService: CertificateRequestService,
        private userService: UserService,
        private location: Location,
        private authService: AuthService,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['fresh'],

            // ── Health Certificate Fields ──
            exporterName: [''],
            certificateReferenceNo: [''],
            qualityCertificateNo: ['N/A'],
            competentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            localAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            importerDetails: [''],
            countryOfOrigin: ['SRI LANKA'],
            countryOfOriginIso: ['LK'],
            countryOfDestination: ['MALAYSIA'],
            countryOfDestinationIso: ['MY'],
            processingEstablishment: [''],
            authorizationNo: [''],
            placeOfLoading: ['COLOMBO – SRI LANKA'],
            transportAir: [true],
            transportShip: [false],
            transportShipDetails: [''],
            transportRail: [false],
            transportRoad: [false],
            transportOther: [false],
            portOfEntry: ['MALAYSIA'],
            transportCompany: ['N/A'],
            conditionAmbient: [false],
            conditionChilled: [true],
            conditionFrozen: [false],
            containerSealIdentification: [''],
            invoiceNo: [''],
            transitCountry: ['N/A'],
            departureDate: [new Date()],
            certifyingOfficialDate: [new Date()],

            certificateReferenceNoPage2: [''],
            productBrand: [''],
            originFisheries: [true],
            originAquaculture: [false],
            certifiedProductFor: ['HUMAN CONSUMPTION'],
            treatmentType: ['Fresh'],

            products: this.fb.array([this.createProductRow()]),

            certificateReferenceNoPage3: [''],
            additionalInformation: ['N/A'],
            officialStamp: [''],
            officialSignature: [''],

            signatoryUserId: [null, Validators.required],
            signatoryName: [''],
            qualification: ['QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'],

            // ── Quality Certificate Fields ──
            documentReferenceNo: [''],
            humanConsumptionYes: [true],
            humanConsumptionNo: [false],
            processingEstablishmentName: [''],
            processingEstablishmentAuthNumber: [''],
            testedBy: ['SGS LANKA (PVT) LTD., 141/6, VAUXHALL ST, COLOMBO 02, SRI LANKA.'],
            commercialInvoice: [''],
            uniqueCode: [''],
            observations: [''],
            qualityProducts: this.fb.array([this.createQualityProductRow()])
        });
    }

    ngOnInit(): void {
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

            if (params['mode'] === 'quality') {
                this.viewMode = 'quality';
                this.form.patchValue({ viewMode: 'quality' });
            } else if (params['mode'] === 'frozen' || params['mode'] === 'sea') {
                this.viewMode = 'frozen';
                this.form.patchValue({ viewMode: 'frozen' });
            } else {
                this.viewMode = 'fresh';
                this.form.patchValue({ viewMode: 'fresh' });
            }

            if (params['ref']) {
                this.refNumber = params['ref'];
                this.form.patchValue({
                    certificateReferenceNo: params['ref'],
                    certificateReferenceNoPage2: params['ref'],
                    certificateReferenceNoPage3: params['ref'],
                    documentReferenceNo: params['ref']
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

        this.form.get('viewMode')?.valueChanges.subscribe((mode: 'fresh' | 'quality' | 'frozen') => {
            if (mode) {
                this.viewMode = mode;
            }
        });

        // Sync reference numbers between controls
        this.form.get('certificateReferenceNo')?.valueChanges.subscribe((val) => {
            if (val) {
                this.form.patchValue({
                    certificateReferenceNoPage2: val,
                    certificateReferenceNoPage3: val,
                    documentReferenceNo: this.form.get('documentReferenceNo')?.value || val
                }, { emitEvent: false });
            }
        });

        this.form.get('documentReferenceNo')?.valueChanges.subscribe((val) => {
            if (val && !this.form.get('certificateReferenceNo')?.value) {
                this.form.patchValue({
                    certificateReferenceNo: val,
                    certificateReferenceNoPage2: val,
                    certificateReferenceNoPage3: val
                }, { emitEvent: false });
            }
        });
    }

    onViewModeChange(mode: 'fresh' | 'quality' | 'frozen') {
        this.viewMode = mode;
        this.form.patchValue({ viewMode: mode });

        if (mode === 'fresh') {
            this.form.patchValue({
                transportAir: true,
                transportShip: false,
                transportShipDetails: '',
                transportRail: false,
                transportRoad: false,
                transportOther: false,
                conditionAmbient: false,
                conditionChilled: true,
                conditionFrozen: false,
                treatmentType: 'Fresh',
                portOfEntry: 'MALAYSIA',
                transitCountry: 'N/A',
                transportCompany: 'N/A',
                qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'
            });
        } else if (mode === 'frozen') {
            this.form.patchValue({
                transportAir: false,
                transportShip: true,
                transportShipDetails: ' - (CHARLOTTE\nSCHULTE / 0128E)',
                transportRail: false,
                transportRoad: false,
                transportOther: false,
                conditionAmbient: false,
                conditionChilled: false,
                conditionFrozen: true,
                treatmentType: 'FROZEN',
                portOfEntry: 'PENANG - MALAYSIA',
                transitCountry: 'N/A',
                transportCompany: 'N/A',
                qualification: 'QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).'
            });
        }
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateService.getMyCertificateByRequestId(requestId).subscribe({
            next: (data: MyCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }
                const rawMode = (data.certificateType as string || '').toLowerCase();
                const mode: 'fresh' | 'quality' | 'frozen' =
                    rawMode === 'quality' ? 'quality' :
                    (rawMode === 'frozen' || rawMode === 'sea' || !data.transportAir) ? 'frozen' : 'fresh';
                this.viewMode = mode;
                if (data.id || data.certificateReferenceNo || data.exporterName) {
                    this.isSubmitted = true;
                }

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813'];
                const cleanCertNo = (data.certificateReferenceNo && !dummyValues.includes(data.certificateReferenceNo.trim())) ? data.certificateReferenceNo : '';
                const finalCertNo = this.refNumber || data.referenceNumber || cleanCertNo || '';

                this.form.patchValue({
                    viewMode: mode,
                    exporterName: data.exporterName,
                    certificateReferenceNo: finalCertNo,
                    qualityCertificateNo: data.qualityCertificateNo || 'N/A',
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    localAuthority: data.localAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    importerDetails: data.importerDetails,
                    countryOfOrigin: data.countryOfOrigin || 'SRI LANKA',
                    countryOfOriginIso: data.countryOfOriginIso || 'LK',
                    countryOfDestination: data.countryOfDestination || 'MALAYSIA',
                    countryOfDestinationIso: data.countryOfDestinationIso || 'MY',
                    processingEstablishment: data.processingEstablishment,
                    authorizationNo: data.authorizationNo,
                    placeOfLoading: data.placeOfLoading || 'COLOMBO – SRI LANKA',
                    transportAir: data.transportAir,
                    transportShip: data.transportShip,
                    transportRail: data.transportRail,
                    transportRoad: data.transportRoad,
                    transportOther: data.transportOther,
                    portOfEntry: data.portOfEntry || 'MALAYSIA',
                    transportCompany: data.transportCompany || 'N/A',
                    conditionAmbient: data.conditionAmbient,
                    conditionChilled: data.conditionChilled,
                    conditionFrozen: data.conditionFrozen,
                    containerSealIdentification: data.containerSealIdentification || '',
                    invoiceNo: data.invoiceNo,
                    transitCountry: data.transitCountry || 'N/A',
                    departureDate: data.departureDate ? new Date(data.departureDate) : new Date(),
                    certifyingOfficialDate: data.certifyingOfficialDate ? new Date(data.certifyingOfficialDate) : new Date(),
                    certificateReferenceNoPage2: finalCertNo,
                    productBrand: data.productBrand,
                    originFisheries: data.originFisheries,
                    originAquaculture: data.originAquaculture,
                    certifiedProductFor: data.certifiedProductFor || 'HUMAN CONSUMPTION',
                    treatmentType: data.treatmentType || (mode === 'frozen' ? 'FROZEN' : 'Fresh'),
                    certificateReferenceNoPage3: finalCertNo,
                    additionalInformation: data.additionalInformation || 'N/A',
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || '',
                    signatoryUserId: data.signatoryUserId,
                    signatoryName: data.signatoryName,
                    qualification: data.qualification || (mode === 'frozen'
                        ? 'QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).'
                        : 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'),

                    // Quality Cert fields sync
                    documentReferenceNo: finalCertNo,
                    processingEstablishmentName: data.processingEstablishment || '',
                    processingEstablishmentAuthNumber: data.authorizationNo || '',
                    commercialInvoice: data.invoiceNo || ''
                });

                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();
                this.selectedUserQualification = data.qualification ?? null;
                if (data.signatoryUserId) {
                    this.previousSignatoryUserId = data.signatoryUserId;
                }

                if (data.products && data.products.length > 0) {
                    const arr = this.form.get('products') as FormArray;
                    arr.clear();
                    data.products.forEach((p) => {
                        arr.push(
                            this.fb.group({
                                hsCode: [p.hsCode || ''],
                                description: [p.description || ''],
                                scientificName: [p.scientificName || ''],
                                batchCode: [p.batchCode || ''],
                                numberOfPackages: [p.numberOfPackages || ''],
                                netWeight: [p.netWeight || '']
                            })
                        );
                    });

                    // Also populate qualityProducts
                    const qArr = this.qualityProducts;
                    qArr.clear();
                    data.products.forEach((p) => {
                        qArr.push(
                            this.fb.group({
                                productName: [p.description || ''],
                                productType: [p.description ? `FROZEN WHOLE ROUND ${p.description}` : ''],
                                testResults: [
                                    'AEROBIC PLATE COUNT (CFU/g) -\n30°C – 5.5×102\n\n' +
                                    'Enterobacteriaceae (MPN/g) - NOT DETECTED\n\n' +
                                    'COLIFORMS (MPN/g) - NOT DETECTED\n\n' +
                                    'E.coli (MPN/g) -NOT DETECTED\n\n' +
                                    'Staphylococcus aureus (CFU/g) - < 10\n' +
                                    'Salmonella spp. in 25g - ABSENT\n\n' +
                                    'Listeria monocytogenes in 25g - ABSENT\n' +
                                    '~ Vibrio spp. in 25g - ABSENT\n' +
                                    '~ Shigella in 25g - ABSENT'
                                ],
                                lotCode: [p.batchCode || ''],
                                samplingDate: [''],
                                numberOfBoxes: [p.numberOfPackages ? `${p.numberOfPackages} CTNS` : ''],
                                lotWeight: [p.netWeight ? `${p.netWeight} KGS` : '']
                            })
                        );
                    });
                } else {
                    this.loadVetFormData(requestId);
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

                const isFrozen = !!vetForm.treatmentFrozen || !!vetForm.transportShip;
                this.viewMode = isFrozen ? 'frozen' : 'fresh';

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813'];
                const cleanCertNo = (vetForm.healthCertNo && !dummyValues.includes(vetForm.healthCertNo.trim())) ? vetForm.healthCertNo : 
                                    (vetForm.newHC && !dummyValues.includes(vetForm.newHC.trim())) ? vetForm.newHC : '';
                const certNo = this.refNumber || vetForm.referenceNumber || cleanCertNo || '';

                const exporterFull = vetForm.consignorName && vetForm.consignorAddress
                    ? `${vetForm.consignorName}\n${vetForm.consignorAddress}`
                    : (vetForm.consignorName || '');

                const importerFull = vetForm.consigneeName && vetForm.consigneeAddress
                    ? `${vetForm.consigneeName}\n${vetForm.consigneeAddress}`
                    : (vetForm.consigneeName || '');

                this.form.patchValue({
                    viewMode: this.viewMode,
                    certificateReferenceNo: certNo,
                    certificateReferenceNoPage2: certNo,
                    certificateReferenceNoPage3: certNo,
                    documentReferenceNo: certNo,
                    exporterName: exporterFull,
                    importerDetails: importerFull,
                    countryOfOrigin: vetForm.countryOrigin || 'SRI LANKA',
                    countryOfOriginIso: vetForm.countryOriginISO || 'LK',
                    countryOfDestination: vetForm.countryDestinationISO || 'MALAYSIA',
                    countryOfDestinationIso: 'MY',
                    placeOfLoading: vetForm.placeOfLoading || 'COLOMBO – SRI LANKA',
                    transportAir: vetForm.transportAeroPlane ?? !isFrozen,
                    transportShip: !!vetForm.transportShip || isFrozen,
                    transportShipDetails: isFrozen ? ' - (CHARLOTTE\nSCHULTE / 0128E)' : '',
                    transportRail: !!vetForm.transportRailwayWagon,
                    transportRoad: !!vetForm.transportRoadVehicle,
                    transportOther: !!vetForm.transportOther,
                    portOfEntry: isFrozen ? 'PENANG - MALAYSIA' : (vetForm.entryBIP || 'MALAYSIA'),
                    conditionAmbient: !!vetForm.temperatureAmbient,
                    conditionChilled: !isFrozen && !!vetForm.temperatureChilled,
                    conditionFrozen: isFrozen || !!vetForm.temperatureFrozen,
                    containerSealIdentification: vetForm.containerId || (isFrozen ? 'OTPU 6672359\n225625' : ''),
                    departureDate: vetForm.dateOfDeparture ? new Date(vetForm.dateOfDeparture) : new Date(),
                    originFisheries: vetForm.productTypeWildCaught ?? true,
                    originAquaculture: !!vetForm.productTypeAquaculture,
                    certifiedProductFor: 'HUMAN CONSUMPTION',
                    treatmentType: isFrozen ? 'FROZEN' : (vetForm.treatmentLive ? 'Live' : vetForm.treatmentChilled ? 'Chilled' : 'Fresh'),
                    processingEstablishment: vetForm.processingEstName || '',
                    authorizationNo: vetForm.approvalNo || '',

                    // Quality Cert fields
                    processingEstablishmentName: vetForm.processingEstName || '',
                    processingEstablishmentAuthNumber: vetForm.approvalNo || '',
                    commercialInvoice: vetForm.docReferences || ''
                });

                if (vetForm.products && vetForm.products.length > 0) {
                    const arr = this.products;
                    arr.clear();
                    vetForm.products.forEach((p) => {
                        arr.push(
                            this.fb.group({
                                hsCode: [p.hsCode || vetForm.hsCode || ''],
                                description: [p.descCommon || ''],
                                scientificName: [p.descScientific || ''],
                                batchCode: [''],
                                numberOfPackages: [p.numPackages || ''],
                                netWeight: [p.netWeight || '']
                            })
                        );
                    });

                    const qArr = this.qualityProducts;
                    qArr.clear();
                    vetForm.products.forEach((p) => {
                        qArr.push(
                            this.fb.group({
                                productName: [p.descCommon || ''],
                                productType: [p.descCommon ? `FROZEN WHOLE ROUND ${p.descCommon}` : ''],
                                testResults: [
                                    'AEROBIC PLATE COUNT (CFU/g) -\n30°C – 5.5×102\n\n' +
                                    'Enterobacteriaceae (MPN/g) - NOT DETECTED\n\n' +
                                    'COLIFORMS (MPN/g) - NOT DETECTED\n\n' +
                                    'E.coli (MPN/g) -NOT DETECTED\n\n' +
                                    'Staphylococcus aureus (CFU/g) - < 10\n' +
                                    'Salmonella spp. in 25g - ABSENT\n\n' +
                                    'Listeria monocytogenes in 25g - ABSENT\n' +
                                    '~ Vibrio spp. in 25g - ABSENT\n' +
                                    '~ Shigella in 25g - ABSENT'
                                ],
                                lotCode: [''],
                                samplingDate: [''],
                                numberOfBoxes: [p.numPackages ? `${p.numPackages} CTNS` : ''],
                                lotWeight: [p.netWeight ? `${p.netWeight} KGS` : '']
                            })
                        );
                    });
                }
            }
        });
    }

    // ── FormArrays ──
    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    get qualityProducts(): FormArray {
        return this.form.get('qualityProducts') as FormArray;
    }

    get productRows() {
        return (this.form.get('products') as FormArray).getRawValue() ?? [];
    }

    createProductRow(): FormGroup {
        return this.fb.group({
            hsCode: [''],
            description: [''],
            scientificName: [''],
            batchCode: [''],
            numberOfPackages: [''],
            netWeight: ['']
        });
    }

    createQualityProductRow(): FormGroup {
        return this.fb.group({
            productName: [''],
            productType: [''],
            testResults: [''],
            lotCode: [''],
            samplingDate: [''],
            numberOfBoxes: [''],
            lotWeight: ['']
        });
    }

    addProductRow(): void {
        this.products.push(this.createProductRow());
    }

    addProduct(): void {
        this.addProductRow();
    }

    removeProductRow(index: number): void {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
    }

    removeProduct(index: number): void {
        this.removeProductRow(index);
    }

    addQualityProduct(): void {
        this.qualityProducts.push(this.createQualityProductRow());
    }

    removeQualityProduct(index: number): void {
        if (this.qualityProducts.length > 1) {
            this.qualityProducts.removeAt(index);
        }
    }

    // ── Calculations ──
    calculateTotalPackages(): string {
        const total = this.products.controls.reduce((sum, c) => {
            const val = c.get('numberOfPackages')?.value;
            if (typeof val === 'number') return sum + val;
            const parsed = parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total}` : '';
    }

    calculateTotalNetWeight(): string {
        const total = this.products.controls.reduce((sum, c) => {
            const val = c.get('netWeight')?.value;
            if (typeof val === 'number') return sum + val;
            const parsed = parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total.toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 })}` : '';
    }

    calculateTotalNumberOfBoxes(): string {
        const total = this.qualityProducts.controls.reduce((sum, c) => {
            const val = c.get('numberOfBoxes')?.value;
            if (typeof val === 'number') return sum + val;
            const parsed = parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total}CTNS` : '';
    }

    calculateTotalLotWeight(): string {
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
        return 3;
    }

    // ── Sample Loaders matching Official PDFs ──
    loadSample1(): void {
        this.onViewModeChange('fresh');
        this.form.patchValue({
            certificateReferenceNo: 'SZ 4268',
            certificateReferenceNoPage2: 'SZ 4268',
            certificateReferenceNoPage3: 'SZ 4268',
            documentReferenceNo: 'SZ 4268',
            qualityCertificateNo: 'N/A',
            exporterName: 'INTERNATIONAL CRAB COMPANY (PVT) LTD.\nNO 17 A 2/1, GALL FACE TERRACE, COLOMBO 03,\nSRI LANKA.',
            competentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            localAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            importerDetails: 'HOONG KIT ENTERPRISE\n(AS0162368-V)\nNO:68, JALAN BATU BELAH,\nTAMAN DESA PADU,68100 BATU CAVES,\nSELANGOR,MALAYSIA',
            countryOfOrigin: 'SRI LANKA',
            countryOfOriginIso: 'LK',
            countryOfDestination: 'MALAYSIA',
            countryOfDestinationIso: 'MY',
            processingEstablishment: 'ISABELA SEA FOODS\n.NO. 14/2, DUNGALPITIYA, THALAHENA\nNEGOMBO, SRI LANKA',
            authorizationNo: 'DFAR/FPE/98/59',
            placeOfLoading: 'COLOMBO – SRI LANKA',
            transportAir: true,
            transportShip: false,
            transportShipDetails: '',
            transportRail: false,
            transportRoad: false,
            transportOther: false,
            portOfEntry: 'MALAYSIA',
            transportCompany: 'N/A',
            conditionAmbient: false,
            conditionChilled: true,
            conditionFrozen: false,
            containerSealIdentification: '',
            invoiceNo: '',
            transitCountry: 'N/A',
            departureDate: new Date('2025-06-26'),
            certifyingOfficialDate: new Date('2025-06-26'),
            productBrand: '',
            originFisheries: true,
            originAquaculture: false,
            certifiedProductFor: 'HUMAN CONSUMPTION',
            treatmentType: 'Fresh',
            additionalInformation: 'N/A',
            signatoryName: 'H.M.U. BANDARA',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'
        });

        const arr = this.products;
        arr.clear();
        arr.push(this.fb.group({
            hsCode: [''],
            description: ['Fresh Black Tiger Prawns'],
            scientificName: ['Penaeus monodon'],
            batchCode: [''],
            numberOfPackages: [''],
            netWeight: ['']
        }));

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Sample 1',
            detail: 'Loaded Health Certificate (Fresh / Air Freight - SZ 4268) matching PDF 1.'
        });
    }

    loadSample2(): void {
        this.loadSampleQuality();
    }

    loadSampleQuality(): void {
        this.onViewModeChange('quality');
        this.form.patchValue({
            documentReferenceNo: 'TA 9637',
            certificateReferenceNo: 'TA 9637',
            certificateReferenceNoPage2: 'TA 9637',
            certificateReferenceNoPage3: 'TA 9637',
            humanConsumptionYes: true,
            humanConsumptionNo: false,
            processingEstablishmentName: 'ANNAI AND SONS (PRIVATE) LIMITED',
            processingEstablishmentAuthNumber: 'DFAR/FPE/98/91',
            testedBy: 'SGS LANKA (PVT) LTD., 141/6, VAUXHALL ST, COLOMBO 02, SRI LANKA.',
            countryOfDestination: 'MALAYSIA',
            commercialInvoice: 'ANNAI/2026/12FR',
            uniqueCode: '',
            observations: ''
        });

        const arr = this.qualityProducts;
        arr.clear();
        arr.push(this.fb.group({
            productName: ['CUTTLE\nFISH'],
            productType: ['FROZEN\nWHOLE\nROUND\nCUTTLE\nFISH'],
            testResults: [
                'AEROBIC PLATE\nCOUNT (CFU/g) -\n30°C – 5.5×102\n\n' +
                'Enterobacteriaceae\n(MPN/g) - NOT\nDETECTED\n\n' +
                'COLIFORMS (MPN/g)\n- NOT DETECTED\n\n' +
                'E.coli (MPN/g) -NOT\nDETECTED\n\n' +
                'Staphylococcus aureus\n(CFU/g) - < 10\n' +
                'Salmonella spp. in 25g\n- ABSENT\n\n' +
                'Listeria\nmonocytogenes in\n25g - ABSENT\n' +
                '~ Vibrio spp. in 25g -\nABSENT\n' +
                '~ Shigella in 25g -\nABSENT'
            ],
            lotCode: ['K 25323\n–\nB 26042'],
            samplingDate: ['20.11.2025–\n02.12.2025'],
            numberOfBoxes: ['1150CTNS'],
            lotWeight: ['23,000.000 KGS']
        }));

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Sample 2 (Quality)',
            detail: 'Loaded Quality Certificate sample (TA 9637) matching PDF 2.'
        });
    }

    loadSample3(): void {
        this.onViewModeChange('frozen');
        this.form.patchValue({
            certificateReferenceNo: 'TA 9637',
            certificateReferenceNoPage2: 'TA 9637',
            certificateReferenceNoPage3: 'TA 9637',
            documentReferenceNo: 'TA 9637',
            qualityCertificateNo: 'N/A',
            exporterName: 'ANNAI & SONS PVT LTD\n257,259 BEACH ROAD NAVANTHURAI NORTH\nJAFFNA, SRI LANKA',
            competentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            localAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            importerDetails: 'FISHERGOLD COLD STORAGE\nSDN.BHD\nPLOT 99B JALAN PERINDUSTRIAN\nBUKIT MINYAK 5,\nKAWASAN PERINDUSTRIAN BUKIT\nMINYAK,\n14100 SEBERANG PERAI TENGAH,\nPULAU PINANG MALAYSIA',
            countryOfOrigin: 'SRI LANKA',
            countryOfOriginIso: 'LK',
            countryOfDestination: 'MALAYSIA',
            countryOfDestinationIso: 'MY',
            processingEstablishment: 'ANNAI & SONS PVT LTD\n257,259 BEACH ROAD NAVANTHURAI NORTH\n JAFFNA, SRI LANKA',
            authorizationNo: 'DFAR/FPE/98/91',
            placeOfLoading: 'COLOMBO – SRI LANKA',
            transportAir: false,
            transportShip: true,
            transportShipDetails: ' - (CHARLOTTE\nSCHULTE / 0128E)',
            transportRail: false,
            transportRoad: false,
            transportOther: false,
            portOfEntry: 'PENANG - MALAYSIA',
            transportCompany: 'N/A',
            conditionAmbient: false,
            conditionChilled: false,
            conditionFrozen: true,
            containerSealIdentification: 'OTPU 6672359\n225625',
            invoiceNo: 'ANNAI/2026/12FR',
            transitCountry: 'N/A',
            departureDate: new Date('2026-02-24'),
            certifyingOfficialDate: new Date('2026-02-24'),
            productBrand: '',
            originFisheries: true,
            originAquaculture: false,
            certifiedProductFor: 'HUMAN CONSUMPTION',
            treatmentType: 'FROZEN',
            additionalInformation: 'N/A',
            signatoryName: 'A.N.S.SENEVIRATNE',
            qualification: 'QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).'
        });

        const arr = this.products;
        arr.clear();
        arr.push(this.fb.group({
            hsCode: ['0307.43'],
            description: ['FROZEN WHOLE ROUND CUTTLEFISH'],
            scientificName: ['Sepia pharaonis'],
            batchCode: ['K 25323 – B 26042'],
            numberOfPackages: ['1150 CTNS'],
            netWeight: ['23,000.000KGS']
        }));

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Sample 3 (Frozen / Sea)',
            detail: 'Loaded Health Certificate (Frozen / Sea Freight - TA 9637) matching PDF 3.'
        });
    }

    onSubmit() {
        if (this.viewMode !== 'quality') {
            if (!this.isCompany && !this.form.get('signatoryUserId')?.value && !this.form.get('signatoryName')?.value) {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Signatory Required',
                    detail: 'Please select and verify authorized signatory.'
                });
                return;
            }
        }

        if (this.isCompany || this.viewMode === 'quality') {
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

        let productPayload = [];
        if (this.viewMode === 'quality') {
            productPayload = (raw.qualityProducts || []).map((p: any) => {
                const pkgNum = typeof p.numberOfBoxes === 'number' ? p.numberOfBoxes : parseFloat(String(p.numberOfBoxes).replace(/[^0-9.]/g, ''));
                const wtNum = typeof p.lotWeight === 'number' ? p.lotWeight : parseFloat(String(p.lotWeight).replace(/[^0-9.]/g, ''));
                return {
                    description: p.productName || '',
                    scientificName: p.productType || '',
                    batchCode: p.lotCode || '',
                    numberOfPackages: isNaN(pkgNum) ? 0 : pkgNum,
                    netWeight: isNaN(wtNum) ? 0 : wtNum,
                    hsCode: ''
                };
            });
        } else {
            productPayload = (raw.products || []).map((p: any) => {
                const pkgNum = typeof p.numberOfPackages === 'number' ? p.numberOfPackages : parseFloat(String(p.numberOfPackages).replace(/[^0-9.]/g, ''));
                const wtNum = typeof p.netWeight === 'number' ? p.netWeight : parseFloat(String(p.netWeight).replace(/[^0-9.]/g, ''));
                return {
                    ...p,
                    numberOfPackages: isNaN(pkgNum) ? 0 : pkgNum,
                    netWeight: isNaN(wtNum) ? 0 : wtNum
                };
            });
        }

        const payload = {
            ...raw,
            certificateRequestId: this.certificateRequestId,
            certificateType: this.viewMode,
            certificateReferenceNo: this.viewMode === 'quality' ? (raw.documentReferenceNo || raw.certificateReferenceNo) : raw.certificateReferenceNo,
            departureDate: toLocalISOString(raw.departureDate),
            certifyingOfficialDate: toLocalISOString(raw.certifyingOfficialDate),
            products: productPayload
        };

        this.certificateService.submitMyCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                const formName = this.viewMode === 'quality' ? 'Malaysia Quality Certificate' : 'Malaysia Health Certificate';
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: `${formName} saved successfully!`
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
                    detail: 'Failed to save Malaysia Certificate.'
                });
            }
        });
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
                            certificateReferenceNo: req.referenceNumber,
                            certificateReferenceNoPage2: req.referenceNumber,
                            certificateReferenceNoPage3: req.referenceNumber,
                            documentReferenceNo: req.referenceNumber
                        });
                    }
                }
            },
            error: () => {}
        });
    }

    print(): void {
        if (!this.isAdmin) {
            this.messageService.add({
                severity: 'error',
                summary: 'Access Denied',
                detail: 'Only administrators have access to print health certificates.'
            });
            return;
        }

        const ref = (this.viewMode === 'quality' ? this.form.get('documentReferenceNo')?.value : this.form.get('certificateReferenceNo')?.value) || 'Malaysia_Certificate';
        let suffix = '';
        if (this.viewMode === 'quality') suffix = '_Quality_Certificate';
        else if (this.viewMode === 'frozen') suffix = '_Health_Certificate_Frozen';
        else suffix = '_Health_Certificate_Fresh';

        const originalTitle = document.title;
        document.title = `${ref}_Malaysia${suffix}`;
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.previousSignatoryUserId = null;
            this.setSelectedUserQualification(userId);
            return;
        }

        const user = this.users.find((u) => u.id === userId);
        if (!user) {
            return;
        }
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
            const qual = user.qualification || 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).';
            this.form.patchValue({
                signatoryName: user.name,
                qualification: qual
            });
        }
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
