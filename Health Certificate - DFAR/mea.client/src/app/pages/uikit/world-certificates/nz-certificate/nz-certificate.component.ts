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
import { TooltipModule } from 'primeng/tooltip';
import { ActivatedRoute, Router } from '@angular/router';
import {
    CertificateRequestService,
    NzCertificateView,
    CreateNzCertificatePayload,
    CreateNzCertificateProductPayload,
    VetFormFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { Select } from 'primeng/select';
import { AuthService } from '@/pages/service/auth.service';
import { UserService, User } from '@/pages/service/user.service';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';

export interface NzDeletions {
    c3?: boolean;
    c3_b_i?: boolean;
    c3_b_ii?: boolean;
    c4?: boolean;
    c4_b_i?: boolean;
    c4_b_ii?: boolean;
    c5?: boolean;
    c6?: boolean;
    c6_a_i?: boolean;
    c6_a_ii?: boolean;
    c6_b?: boolean;
    c7?: boolean;
    c7_a?: boolean;
    c7_b?: boolean;
    c7_b_i?: boolean;
    c7_b_ii?: boolean;
    c7_c?: boolean;
    c7_c_i?: boolean;
    c7_c_ii?: boolean;
    c8?: boolean;
    c9?: boolean;
    c9_a_i?: boolean;
    c9_a_ii?: boolean;
    c10?: boolean;
    c10_a_i?: boolean;
    c10_a_ii?: boolean;
    c10_b?: boolean;
    c11?: boolean;
    c11_a?: boolean;
    c11_b?: boolean;
    c12?: boolean;
    c13?: boolean;
    c13_c_i?: boolean;
    c13_c_ii?: boolean;
    c14?: boolean;
}

@Component({
    selector: 'app-nz-certificate',
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
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './nz-certificate.component.html',
    styleUrls: ['./nz-certificate.component.css', '../certificate-print.css']
})
export class NzCertificateComponent implements OnInit {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    form: FormGroup;
    products: FormArray;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;

    // Active preset: 'fish' | 'mollusc' | 'custom'
    activePreset: 'fish' | 'mollusc' | 'custom' = 'fish';

    // Interactive Strike-through / Deletion Flags
    deletions: NzDeletions = {
        c3: false,
        c3_b_i: false,
        c3_b_ii: true, // crossed by default in fish
        c4: true,
        c4_b_i: false,
        c4_b_ii: false,
        c5: true,
        c6: false,
        c6_a_i: false,
        c6_a_ii: true,
        c6_b: true,
        c7: true,
        c7_a: false,
        c7_b: false,
        c7_b_i: false,
        c7_b_ii: false,
        c7_c: false,
        c7_c_i: false,
        c7_c_ii: false,
        c8: true,
        c9: true,
        c9_a_i: false,
        c9_a_ii: false,
        c10: true,
        c10_a_i: false,
        c10_a_ii: false,
        c10_b: false,
        c11: true,
        c11_a: false,
        c11_b: false,
        c12: true,
        c13: true,
        c13_c_i: false,
        c13_c_ii: true,
        c14: true
    };

    users: User[] = [];
    userOptions: { label: string; value: string }[] = [];
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    selectedUserQualification: string | null = null;
    previousSignatoryUserId: string | null = null;
    isCompany = false;
    isApproved = false;
    refNumber: string = '';

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
        private authService: AuthService,
        private location: Location,
        private sanitizer: DomSanitizer
    ) {
        this.products = this.fb.array([]);

        this.form = this.fb.group({
            consignorName: ['CEYLON FRESH SEAFOOD (PVT) LTD', Validators.required],
            consignorAddress: ['71, KUDAHAKAPOLA ROAD, THUDELLA,\nJA-ELA, SRI LANKA.', Validators.required],
            certificateRefNumber: ['', Validators.required],
            consigneeName: ['SERANDIB NEW ZEALAND LTD', Validators.required],
            consigneeAddress: ['3 - 875 DOMINION ROAD, BALMORAL,\nAUCKLAND, NEW ZEALAND', Validators.required],
            countryOfOrigin: ['SRI LANKA', Validators.required],
            countryOfDestination: ['NEW ZEALAND - AUCKLAND', Validators.required],
            processorName: ['CEYLON FRESH SEAFOOD (PVT) LTD.', Validators.required],
            processorAddress: ['71, KUDAHAKAPOLA ROAD,\nTHUDELLA, JA-ELA, SRI LANKA.', Validators.required],
            processorEstablishmentNumber: ['DFAR/FPE/98/31', Validators.required],
            portDispatchedFrom: ['COLOMBO – SRI LANKA', Validators.required],
            dateOfDeparture: [new Date()],
            competentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES', Validators.required],
            meansOfTransport: ['AIR'],
            transportAeroplan: [true],
            transportShip: [false],
            temperatureOfCommodities: ['- 20°C'],
            containerNumber: [''],
            officialSealNumber: [''],
            officialStamp: [''],
            officialSignature: [''],
            signatoryUserId: [null, Validators.required],
            signatoryName: ['I.M.W.D. ABEYSOORIYA'],
            qualification: ['QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).'],
            signatureDate: [new Date()],
            certificateType: ['fish'],
            products: this.products
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

            if (params['ref']) {
                this.refNumber = params['ref'];
                this.form.patchValue({ certificateRefNumber: params['ref'] });
            }

            if (params['requestId']) {
                this.certificateRequestId = +params['requestId'];
                this.checkRequestApproval(this.certificateRequestId);
                this.loadSavedCertificateData(this.certificateRequestId);
            } else {
                this.loadSampleNewZealand();
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
                        this.form.patchValue({ certificateRefNumber: req.referenceNumber });
                    }
                }
            },
            error: () => {}
        });
    }

    /**
     * Preset 1: Fish Products (Clauses 3 & 6 Active, 4, 5, 7..14 Deleted)
     */
    applyFishPreset(): void {
        this.activePreset = 'fish';
        this.deletions = {
            c3: false,
            c3_b_i: false,
            c3_b_ii: true,
            c4: true,
            c4_b_i: false,
            c4_b_ii: false,
            c5: true,
            c6: false,
            c6_a_i: false,
            c6_a_ii: true,
            c6_b: true,
            c7: true,
            c7_a: false,
            c7_b: false,
            c7_b_i: false,
            c7_b_ii: false,
            c7_c: false,
            c7_c_i: false,
            c7_c_ii: false,
            c8: true,
            c9: true,
            c9_a_i: false,
            c9_a_ii: false,
            c10: true,
            c10_a_i: false,
            c10_a_ii: false,
            c10_b: false,
            c11: true,
            c11_a: false,
            c11_b: false,
            c12: true,
            c13: true,
            c13_c_i: false,
            c13_c_ii: true,
            c14: true
        };
        this.form.patchValue({ certificateType: 'fish' });
    }

    /**
     * Preset 2: Mollusc / Squid Products (Clause 13 Active, 3..12, 14 Deleted)
     */
    applyMolluscPreset(): void {
        this.activePreset = 'mollusc';
        this.deletions = {
            c3: true,
            c3_b_i: false,
            c3_b_ii: true,
            c4: true,
            c4_b_i: false,
            c4_b_ii: false,
            c5: true,
            c6: true,
            c6_a_i: false,
            c6_a_ii: true,
            c6_b: true,
            c7: true,
            c7_a: false,
            c7_b: false,
            c7_b_i: false,
            c7_b_ii: false,
            c7_c: false,
            c7_c_i: false,
            c7_c_ii: false,
            c8: true,
            c9: true,
            c9_a_i: false,
            c9_a_ii: false,
            c10: true,
            c10_a_i: false,
            c10_a_ii: false,
            c10_b: false,
            c11: true,
            c11_a: false,
            c11_b: false,
            c12: true,
            c13: false,
            c13_c_i: false,
            c13_c_ii: true,
            c14: true
        };
        this.form.patchValue({ certificateType: 'mollusc' });
    }

    /**
     * Clear all strike-throughs (Keep all clauses active)
     */
    clearAllCrossouts(): void {
        this.activePreset = 'custom';
        const cleared: any = {};
        Object.keys(this.deletions).forEach((k) => {
            cleared[k] = false;
        });
        this.deletions = cleared;
        this.form.patchValue({ certificateType: JSON.stringify(this.deletions) });
    }

    /**
     * Cross out all optional clauses (3 to 14)
     */
    crossOutAllClauses(): void {
        this.activePreset = 'custom';
        const allCrossed: any = {};
        Object.keys(this.deletions).forEach((k) => {
            allCrossed[k] = true;
        });
        this.deletions = allCrossed;
        this.form.patchValue({ certificateType: JSON.stringify(this.deletions) });
    }

    /**
     * Toggle specific clause or subclause deletion state
     */
    toggleDeletion(key: keyof NzDeletions, event?: Event): void {
        if (event) {
            event.stopPropagation();
        }
        this.deletions[key] = !this.deletions[key];
        this.activePreset = 'custom';
        this.form.patchValue({ certificateType: JSON.stringify(this.deletions) });
    }

    loadSampleNewZealand(): void {
        this.form.patchValue({
            consignorName: 'CEYLON FRESH SEAFOOD (PVT) LTD',
            consignorAddress: '71, KUDAHAKAPOLA ROAD, THUDELLA,\nJA-ELA, SRI LANKA.',
            certificateRefNumber: 'TB 9530',
            consigneeName: 'SERANDIB NEW ZEALAND LTD',
            consigneeAddress: '3 - 875 DOMINION ROAD, BALMORAL,\nAUCKLAND, NEW ZEALAND',
            countryOfOrigin: 'SRI LANKA',
            countryOfDestination: 'NEW ZEALAND - AUCKLAND',
            processorName: 'CEYLON FRESH SEAFOOD (PVT) LTD.',
            processorAddress: '71, KUDAHAKAPOLA ROAD,\nTHUDELLA, JA-ELA, SRI LANKA.',
            processorEstablishmentNumber: 'DFAR/FPE/98/31',
            portDispatchedFrom: 'COLOMBO – SRI LANKA',
            dateOfDeparture: new Date(),
            competentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            meansOfTransport: 'AIR',
            transportAeroplan: true,
            transportShip: false,
            temperatureOfCommodities: '- 20°C',
            containerNumber: 'AIR CARGO - AKL',
            officialSealNumber: 'NONE',
            signatoryName: 'I.M.W.D. ABEYSOORIYA',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).'
        });

        while (this.products.length) {
            this.products.removeAt(0);
        }

        const sampleProducts = [
            {
                productName: 'FROZEN YELLOW FIN TUNA (Thunnus albacares)',
                aquaticAnimalSpecies: 'Thunnus albacares',
                productionDate: new Date(),
                numberOfPackages: 15,
                netWeightKg: 450.0,
                hsCode: '0303.42.00'
            },
            {
                productName: 'FROZEN SWORD FISH (Xiphias gladius)',
                aquaticAnimalSpecies: 'Xiphias gladius',
                productionDate: new Date(),
                numberOfPackages: 10,
                netWeightKg: 300.0,
                hsCode: '0303.89.00'
            },
            {
                productName: 'FROZEN REEF COD (Epinephelus coioides)',
                aquaticAnimalSpecies: 'Epinephelus coioides',
                productionDate: new Date(),
                numberOfPackages: 8,
                netWeightKg: 240.0,
                hsCode: '0303.89.00'
            }
        ];

        sampleProducts.forEach((p) => {
            this.products.push(
                this.fb.group({
                    productName: [p.productName],
                    aquaticAnimalSpecies: [p.aquaticAnimalSpecies],
                    productionDate: [p.productionDate],
                    numberOfPackages: [p.numberOfPackages],
                    netWeightKg: [p.netWeightKg],
                    hsCode: [p.hsCode]
                })
            );
        });

        this.applyFishPreset();

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Sample',
            detail: 'Loaded New Zealand Certificate sample (TB 9530 - Ceylon Fresh Seafood).'
        });
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateService.getNzCertificateByRequestId(requestId).subscribe({
            next: (data: NzCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                if (data.certificateType) {
                    if (data.certificateType.startsWith('{')) {
                        try {
                            this.deletions = { ...this.deletions, ...JSON.parse(data.certificateType) };
                            this.activePreset = 'custom';
                        } catch {
                            this.applyFishPreset();
                        }
                    } else if (data.certificateType === 'mollusc') {
                        this.applyMolluscPreset();
                    } else {
                        this.applyFishPreset();
                    }
                }

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813', 'TB 9530'];
                const cleanCertNo = (data.certificateRefNumber && !dummyValues.includes(data.certificateRefNumber.trim())) ? data.certificateRefNumber : '';
                const finalCertNo = this.refNumber || data.referenceNumber || cleanCertNo || '';

                this.form.patchValue({
                    consignorName: data.consignorName || 'CEYLON FRESH SEAFOOD (PVT) LTD',
                    consignorAddress: data.consignorAddress || '71, KUDAHAKAPOLA ROAD, THUDELLA,\nJA-ELA, SRI LANKA.',
                    certificateRefNumber: finalCertNo,
                    consigneeName: data.consigneeName || 'SERANDIB NEW ZEALAND LTD',
                    consigneeAddress: data.consigneeAddress || '3 - 875 DOMINION ROAD, BALMORAL,\nAUCKLAND, NEW ZEALAND',
                    countryOfOrigin: data.countryOfOrigin || 'SRI LANKA',
                    countryOfDestination: data.countryOfDestination || 'NEW ZEALAND - AUCKLAND',
                    processorName: data.processorName || 'CEYLON FRESH SEAFOOD (PVT) LTD.',
                    processorAddress: data.processorAddress || '71, KUDAHAKAPOLA ROAD,\nTHUDELLA, JA-ELA, SRI LANKA.',
                    processorEstablishmentNumber: data.processorEstablishmentNumber || 'DFAR/FPE/98/31',
                    portDispatchedFrom: data.portDispatchedFrom || 'COLOMBO – SRI LANKA',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    meansOfTransport: data.meansOfTransport || 'AIR',
                    transportAeroplan: data.transportAeroplan ?? true,
                    transportShip: data.transportShip ?? false,
                    temperatureOfCommodities: data.temperatureOfCommodities || '- 20°C',
                    containerNumber: data.containerNumber || '',
                    officialSealNumber: data.officialSealNumber || '',
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || '',
                    signatoryUserId: data.signatoryUserId,
                    signatoryName: data.signatoryName,
                    qualification: data.qualification,
                    signatureDate: data.signatureDate ? new Date(data.signatureDate) : new Date()
                });
                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();

                if (data.signatoryUserId) {
                    this.previousSignatoryUserId = data.signatoryUserId;
                }

                if (data.products && data.products.length > 0) {
                    while (this.products.length) {
                        this.products.removeAt(0);
                    }
                    data.products.forEach((p) => {
                        this.products.push(
                            this.fb.group({
                                productName: [p.productName],
                                aquaticAnimalSpecies: [p.aquaticAnimalSpecies],
                                productionDate: [p.productionDate ? new Date(p.productionDate) : new Date()],
                                numberOfPackages: [p.numberOfPackages],
                                netWeightKg: [p.netWeightKg],
                                hsCode: [p.hsCode]
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
            next: (data) => {
                if (!data) {
                    if (this.products.length === 0) this.addProduct();
                    return;
                }
                this.autofillForm(data);
            },
            error: () => {
                if (this.products.length === 0) this.addProduct();
            }
        });
    }

    private autofillForm(data: VetFormFieldResponse) {
        if (!data) return;
        const vetProducts = Array.isArray(data.products) && data.products.length > 0
            ? data.products
            : [
                  {
                      descCommon: data.descCommon,
                      descScientific: data.descScientific,
                      numPackages: data.numPackages,
                      netWeight: data.netWeight,
                      hsCode: data.hsCode
                  }
              ];

        const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813', 'TB 9530'];
        const cleanCertNo = (data.healthCertNo && !dummyValues.includes(data.healthCertNo.trim())) ? data.healthCertNo : 
                            (data.newHC && !dummyValues.includes(data.newHC.trim())) ? data.newHC : '';
        const certNo = this.refNumber || data.referenceNumber || cleanCertNo || '';

        this.form.patchValue({
            consignorName: data.consignorName || 'CEYLON FRESH SEAFOOD (PVT) LTD',
            consignorAddress: data.consignorAddress || '71, KUDAHAKAPOLA ROAD, THUDELLA,\nJA-ELA, SRI LANKA.',
            certificateRefNumber: certNo,
            consigneeName: data.consigneeName || 'SERANDIB NEW ZEALAND LTD',
            consigneeAddress: data.consigneeAddress || '3 - 875 DOMINION ROAD, BALMORAL,\nAUCKLAND, NEW ZEALAND',
            countryOfOrigin: data.countryOrigin || 'SRI LANKA',
            countryOfDestination: data.countryDestinationISO || 'NEW ZEALAND - AUCKLAND',
            processorName: data.processingEstName || 'CEYLON FRESH SEAFOOD (PVT) LTD.',
            processorAddress: data.processingEstAddress || '71, KUDAHAKAPOLA ROAD,\nTHUDELLA, JA-ELA, SRI LANKA.',
            processorEstablishmentNumber: data.approvalNo || 'DFAR/FPE/98/31',
            portDispatchedFrom: data.placeOfLoading || 'COLOMBO – SRI LANKA',
            dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
            transportAeroplan: data.transportAeroPlane ?? true,
            transportShip: data.transportShip ?? false,
            containerNumber: data.containerId,
            temperatureOfCommodities: data.temperatureChilled ? '0/+4°C' : '- 20°C'
        });

        while (this.products.length) {
            this.products.removeAt(0);
        }

        vetProducts.forEach((product) => {
            this.products.push(
                this.fb.group({
                    productName: [product.descCommon || ''],
                    aquaticAnimalSpecies: [product.descScientific || ''],
                    productionDate: [data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date()],
                    numberOfPackages: [product.numPackages ? parseInt(product.numPackages, 10) : ''],
                    netWeightKg: [product.netWeight ? parseFloat(product.netWeight) : ''],
                    hsCode: [product.hsCode || data.hsCode || '']
                })
            );
        });

        if (this.products.length === 0) {
            this.addProduct();
        }
    }

    createProductRow(): FormGroup {
        return this.fb.group({
            productName: [''],
            aquaticAnimalSpecies: [''],
            productionDate: [new Date()],
            numberOfPackages: [''],
            netWeightKg: [''],
            hsCode: ['']
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

    calculateTotalPackages(): number {
        return this.products.value.reduce((sum: number, product: any) => {
            return sum + (parseInt(product.numberOfPackages, 10) || 0);
        }, 0);
    }

    calculateTotalWeight(): number {
        return this.products.value.reduce((sum: number, product: any) => {
            return sum + (parseFloat(product.netWeightKg) || 0);
        }, 0);
    }

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }

    get shouldShowSignatorySelect(): boolean {
        return (!this.viewOnly || this.isAdmin) && !this.isCompany;
    }

    get showReadonlySignatory(): boolean {
        return this.viewOnly && !this.isAdmin;
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
            this.selectedUserQualification =
                selected.qualification ||
                'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).';
            this.showPasswordDialog = true;
        }
    }

    onSignatoryConfirmed(officerName: string) {
        if (this.pendingSignatoryUserId) {
            this.form.patchValue({
                signatoryUserId: this.pendingSignatoryUserId,
                signatoryName: officerName,
                qualification: this.selectedUserQualification
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
        if (!this.isCompany && !this.form.get('signatoryUserId')?.value) {
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

        if (this.form.invalid) {
            this.messageService.add({
                severity: 'error',
                summary: 'Validation Error',
                detail: 'Please fill in all required fields'
            });
            return;
        }

        this.isSaving = true;
        const rawValue = this.form.getRawValue();

        const productPayloads: CreateNzCertificateProductPayload[] = (rawValue.products || []).map((p: any) => ({
            productName: p.productName || '',
            aquaticAnimalSpecies: p.aquaticAnimalSpecies || '',
            productionDate: toLocalISOString(p.productionDate),
            numberOfPackages: p.numberOfPackages ? parseInt(p.numberOfPackages, 10) : 0,
            netWeightKg: p.netWeightKg ? parseFloat(p.netWeightKg) : 0,
            hsCode: p.hsCode || ''
        }));

        const payload: CreateNzCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            consignorName: rawValue.consignorName,
            consignorAddress: rawValue.consignorAddress,
            certificateRefNumber: rawValue.certificateRefNumber,
            consigneeName: rawValue.consigneeName,
            consigneeAddress: rawValue.consigneeAddress,
            countryOfOrigin: rawValue.countryOfOrigin,
            countryOfDestination: rawValue.countryOfDestination,
            processorName: rawValue.processorName,
            processorAddress: rawValue.processorAddress,
            processorEstablishmentNumber: rawValue.processorEstablishmentNumber,
            portDispatchedFrom: rawValue.portDispatchedFrom,
            dateOfDeparture: toLocalISOString(rawValue.dateOfDeparture),
            competentAuthority: rawValue.competentAuthority,
            meansOfTransport: rawValue.meansOfTransport || (rawValue.transportAeroplan ? 'AIR' : 'SEA'),
            transportAeroplan: rawValue.transportAeroplan,
            transportShip: rawValue.transportShip,
            temperatureOfCommodities: rawValue.temperatureOfCommodities,
            containerNumber: rawValue.containerNumber,
            officialSealNumber: rawValue.officialSealNumber,
            officialStamp: rawValue.officialStamp,
            officialSignature: rawValue.officialSignature,
            signatureDate: toLocalISOString(rawValue.signatureDate),
            signatoryUserId: rawValue.signatoryUserId || null,
            signatoryName: rawValue.signatoryName || '',
            qualification: rawValue.qualification || '',
            certificateType: JSON.stringify(this.deletions),
            products: productPayloads
        };

        this.certificateService.submitNzCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'New Zealand Health Certificate saved successfully'
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
                    detail: 'Failed to save New Zealand Health Certificate'
                });
            }
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
        const ref = this.form.get('certificateRefNumber')?.value || 'Draft';
        const originalTitle = document.title;
        document.title = `${ref}_New_Zealand_Health_Certificate.pdf`;
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
