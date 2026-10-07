import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { CheckboxModule } from 'primeng/checkbox';
import { TooltipModule } from 'primeng/tooltip';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateRequestService, VetFormFieldResponse } from 'src/app/pages/service/certificate-request.service';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';

@Component({
    selector: 'app-my-quality-certificate',
    standalone: true,
    imports: [CommonModule,
        FormsModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        ToastModule,
        CheckboxModule,
        TooltipModule,
        CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './my-quality-certificate.component.html',
    styleUrls: ['./my-quality-certificate.component.css', '../certificate-print.css']
})
export class MyQualityCertificateComponent implements OnInit {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    form: FormGroup;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    certificateRequestId: number | null = null;
    isCompany = false;

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }
    isApproved = false;
    refNumber: string = '';

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
        private location: Location,
        private authService: AuthService,
        private certificateService: CertificateRequestService
    ) {
        this.form = this.fb.group({
            documentReferenceNo: [''],
            humanConsumptionYes: [true],
            humanConsumptionNo: [false],
            processingEstablishmentName: [''],
            processingEstablishmentAuthNumber: [''],
            testedBy: ['SGS LANKA (PVT) LTD., 141/6, VAUXHALL ST, COLOMBO 02, SRI LANKA.'],
            countryOfDestination: ['MALAYSIA'],
            commercialInvoice: [''],
            uniqueCode: [''],
            observations: [''],
            products: this.fb.array([this.createProductRow()])
        });
    }

    ngOnInit(): void {
        this.isCompany = (this.authService.getUserRole() || '').toLowerCase() === 'company';
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
                this.form.patchValue({
                    documentReferenceNo: params['ref']
                });
            }
            if (params['requestId']) {
                this.certificateRequestId = +params['requestId'];
                this.checkRequestApproval(this.certificateRequestId);
                this.loadVetFormData(this.certificateRequestId);
            }
            if (this.viewOnly) {
                this.form.disable();
            } else {
                this.form.enable();
            }
        });
    }

    private loadVetFormData(requestId: number) {
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
            next: (vetForm: VetFormFieldResponse) => {
                if (!vetForm) return;

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813'];
                const cleanCertNo = (vetForm.healthCertNo && !dummyValues.includes(vetForm.healthCertNo.trim())) ? vetForm.healthCertNo : 
                                    (vetForm.newHC && !dummyValues.includes(vetForm.newHC.trim())) ? vetForm.newHC : '';
                const certNo = this.refNumber || vetForm.referenceNumber || cleanCertNo || '';

                this.form.patchValue({
                    documentReferenceNo: certNo,
                    processingEstablishmentName: vetForm.processingEstName || '',
                    processingEstablishmentAuthNumber: vetForm.approvalNo || '',
                    countryOfDestination: vetForm.countryDestinationISO || 'MALAYSIA',
                    commercialInvoice: vetForm.docReferences || ''
                });

                if (vetForm.products && vetForm.products.length > 0) {
                    const arr = this.products;
                    arr.clear();
                    vetForm.products.forEach((p) => {
                        arr.push(
                            this.fb.group({
                                productName: [p.descCommon || ''],
                                productType: [p.descCommon ? `FROZEN WHOLE ROUND ${p.descCommon}` : ''],
                                testResults: [
                                    'AEROBIC PLATE COUNT (CFU/g) - 30°C – 5.5×102\n' +
                                    'Enterobacteriaceae (MPN/g) - NOT DETECTED\n' +
                                    'COLIFORMS (MPN/g) - NOT DETECTED\n' +
                                    'E.coli (MPN/g) - NOT DETECTED\n' +
                                    'Staphylococcus aureus (CFU/g) - < 10\n' +
                                    'Salmonella spp. in 25g - ABSENT\n' +
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

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    createProductRow(): FormGroup {
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

    addProduct(): void {
        this.products.push(this.createProductRow());
    }

    removeProduct(index: number): void {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
    }

    calculateTotalNumberOfBoxes(): string {
        const total = this.products.controls.reduce((sum, c) => {
            const val = c.get('numberOfBoxes')?.value;
            if (typeof val === 'number') return sum + val;
            const parsed = parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total}CTNS` : '';
    }

    calculateTotalLotWeight(): string {
        const total = this.products.controls.reduce((sum, c) => {
            const val = c.get('lotWeight')?.value;
            if (typeof val === 'number') return sum + val;
            const parsed = parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total.toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 })} KGS` : '';
    }

    loadSampleQuality(): void {
        this.form.patchValue({
            documentReferenceNo: 'TA 9637',
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

        const arr = this.products;
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
            summary: 'Loaded Quality Sample',
            detail: 'Loaded Annai & Sons Cuttlefish Quality Certificate (TA 9637).'
        });
    }

    onSubmit(): void {
        this.isSaving = true;
        setTimeout(() => {
            this.isSaving = false;
            this.messageService.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Malaysia Quality Certificate Saved Successfully',
                life: 3000
            });
            setTimeout(() => this.goBack(), 1500);
        }, 600);
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

        const ref = this.form.get('documentReferenceNo')?.value || 'Quality_Certificate';
        const originalTitle = document.title;
        document.title = `${ref}_Malaysia_Quality_Certificate`;
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
}
