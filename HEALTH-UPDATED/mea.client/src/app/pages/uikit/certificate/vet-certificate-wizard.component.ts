import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { FormsModule } from '@angular/forms';
import { CommonModule, Location } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { ButtonModule } from 'primeng/button';
import { FileUploadModule } from 'primeng/fileupload';
import { RadioButton } from 'primeng/radiobutton';
import { Checkbox } from 'primeng/checkbox';
import { DatePicker } from 'primeng/datepicker';
import { TextareaModule } from 'primeng/textarea';
import { InputTextModule } from 'primeng/inputtext';
import { ToastModule } from 'primeng/toast';
import { DialogModule } from 'primeng/dialog';
import { AccordionModule } from 'primeng/accordion';
import { Select } from 'primeng/select';
import { MessageService } from 'primeng/api';
import { CertificateRequestService, PaymentSlipPreview, VetAttachmentFieldResponse, VetFormFieldResponse, VetProductFieldResponse } from '../../service/certificate-request.service';
import { AuthService } from '@/pages/service/auth.service';

interface ViewModePaymentSlip {
    mimeType: string;
    previewUrl: string;
    pdfSrc: SafeResourceUrl | null;
    isImage: boolean;
    isPdf: boolean;
}

interface AttachmentThumbnail {
    mimeType: string;
    previewUrl: string | null;
}

import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';

@Component({
    selector: 'app-vet-certificate-wizard',
    standalone: true,
    imports: [CommonModule, FormsModule, ReactiveFormsModule, ButtonModule, FileUploadModule, RadioButton, Checkbox, DatePicker, TextareaModule, InputTextModule, ToastModule, DialogModule, AccordionModule, Select, CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './vet-certificate-form.component.html',
    styleUrls: ['./vet-certificate-form.component.css', '../world-certificates/certificate-print.css']
})
export class VetCertificateWizardComponent implements OnInit, OnDestroy {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    step = 1;
    form: FormGroup;
    uploadedCertificateFiles: File[] = [];
    certificateSecondaryNames: Record<string, string> = {};
    private certificatePreviewUrls = new Map<string, string>();
    paymentSlipFile: File | null = null;
    paymentSlipSecondaryName = '';
    private paymentSlipPreviewUrl: string | null = null;
    viewModePaymentSlip: ViewModePaymentSlip | null = null;
    viewModeAttachments: VetAttachmentFieldResponse[] = [];
    private attachmentThumbnailCache = new Map<number, AttachmentThumbnail>();
    attachmentPreviewDialogVisible = false;
    attachmentPreviewTitle = '';
    attachmentPreviewUrl: string | null = null;
    attachmentPreviewPdfSrc: SafeResourceUrl | null = null;
    attachmentPreviewMimeType: string | null = null;
    certificateTitle = 'APPLICATION FOR THE ISSUE OF VETERINARY CERTIFICATE TO EU';
    certificateType: 'EU' | 'NonEU' = 'EU';
    countryId: number | null = null;

    weightUnits = [
        { label: 'Kilograms (kg)', value: 'kg' },
        { label: 'Grams (g)', value: 'g' },
        { label: 'Metric Tonnes (MT / t)', value: 'MT' },
        { label: 'Pounds (lbs)', value: 'lbs' },
        { label: 'Ounces (oz)', value: 'oz' },
        { label: 'Milligrams (mg)', value: 'mg' },
        { label: 'Hundredweight (cwt)', value: 'cwt' },
        { label: 'Short Tons (US Ton)', value: 'ST' },
        { label: 'Long Tons (UK Ton)', value: 'LT' }
    ];
    referenceNumber: string | null = null;
    viewOnly = false;
    isEmbedded = false;
    isLoading = false;

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    constructor(
        private fb: FormBuilder,
        private messageService: MessageService,
        private route: ActivatedRoute,
        private router: Router,
        private certificateRequestService: CertificateRequestService,
        private sanitizer: DomSanitizer,
        private location: Location,
        private authService: AuthService
    ) {
        this.form = this.fb.group({
            certificateRequestId: [null],
            // Step1
            oldHC: [''],
            newHC: [''],
            landingSite: [''],
            boatRegistration: [''],
            boatNumber: [''],
            supplierNameAddress: [''],
            arrivalAtFactory: [''],
            processingDate: [''],
            // Step1 aquaculture
            farmLocation: [''],
            farmOwnerName: [''],
            farmOwnerAddress: [''],
            harvestDate: [''],
            arrivalTimeProduct: [''],
            processingDates: [''],
            aquaSupplier: [''],
            // Step1 imported
            countryOrigin: [''],
            arrivalConsignment: [''],
            healthCertNo: [''],
            productTypeAquaculture: [false],
            productTypeWildCaught: [false],
            uploadedCertificateFile: [[]],
            // Step2
            consignorName: [''],
            consignorAddress: [''],
            consignorPostal: [''],
            consignorTel: [''],
            consigneeName: [''],
            consigneeAddress: [''],
            consigneePostal: [''],
            consigneeTel: [''],
            countryOriginISO: [''],
            regionOriginISO: [''],
            countryDestinationISO: [''],
            processingEstName: [''],
            processingEstAddress: [''],
            approvalNo: [''],
            placeOfLoading: [''],
            dateOfDeparture: [''],
            // Step3
            transportAeroPlane: [false],
            transportShip: [false],
            transportRailwayWagon: [false],
            transportRoadVehicle: [false],
            transportOther: [false],
            transportId: [''],
            docReferences: [''],
            entryBIP: [''],
            descCommon: [''],
            descScientific: [''],
            processingType: [''],
            hsCode: [''],
            temperatureAmbient: [false],
            temperatureChilled: [false],
            temperatureFrozen: [false],
            quantity: [''],
            numPackages: [''],
            packagingType: [''],
            containerId: [''],
            products: this.fb.array([this.createProductGroup()]),
            forHumanConsumption: [''],
            forImportEU: ['EU'],
            natureAquaculture: [false],
            natureWildOrigin: [false],
            treatmentChilled: [false],
            treatmentFrozen: [false],
            treatmentLive: [false],
            netWeight: [''],
            // Step4
            signatureDate: [''],
            signatureTime: [''],
            signature: [''],
            signatoryName: [''],
            designation: [''],
            // Attestations
            attestation61_1: [true],
            attestation61_2: [true],
            attestation61_3: [true],
            attestation61_4: [true],
            attestation61_5: [true],
            attestation62_1: [true],
            attestation62_2: [true],
            // Payment Slip
            paymentSlip: [''],
            paymentSlipFile: [null]
        });
    }

    isExpired = false;

    ngOnInit() {
        this.route.queryParams.subscribe((params) => {
            if (params['cancelsAndReplacesRef']) this.cancelsAndReplacesRef = params['cancelsAndReplacesRef'];
            if (params['cancelsAndReplacesDate']) this.cancelsAndReplacesDate = params['cancelsAndReplacesDate'];
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['ref']) {
                this.referenceNumber = params['ref'];
                this.form.patchValue({ newHC: params['ref'] });
            }
            this.viewOnly = params['viewOnly'] === true || params['viewOnly'] === 'true';
            if (params['type']) {
                this.certificateType = params['type'];
                this.form.patchValue({ forImportEU: params['type'] });
                this.products.at(0)?.patchValue({ forImportEU: params['type'] }, { emitEvent: false });
                const dest = params['country'] ? String(params['country']).toUpperCase() : params['type'].toUpperCase();
                this.certificateTitle = params['type'].toUpperCase() === 'EU'
                    ? 'APPLICATION FOR THE ISSUE OF VETERINARY CERTIFICATE TO EU'
                    : `APPLICATION FOR THE ISSUE OF HEALTH CERTIFICATE TO ${dest}`;
            }
            if (params['countryId']) {
                this.countryId = Number(params['countryId']);
            }
            if (params['country']) {
                if (this.certificateType !== 'EU') {
                    this.certificateTitle = `APPLICATION FOR THE ISSUE OF HEALTH CERTIFICATE TO ${String(params['country']).toUpperCase()}`;
                    this.form.patchValue({ countryDestinationISO: params['country'] });
                }
            }
            const userRole = (this.authService.getUserRole() || '').toLowerCase();
            if (params['adminEdit'] === 'true' && userRole === 'admin') {
                this.viewOnly = false;
            }
            if (params['requestId']) {
                const requestId = Number(params['requestId']);
                if (!Number.isNaN(requestId)) {
                    this.form.patchValue({ certificateRequestId: requestId });
                    this.loadSavedForm(requestId);
                    this.loadViewModePaymentSlip(requestId);
                    if (!this.viewOnly) {
                        this.checkFormStatus(requestId);
                    }
                }
            } else if (this.viewOnly) {
                this.form.disable({ emitEvent: false });
            }
        });
    }

    private parseUtcDate(dateVal: string | Date | undefined): Date {
        if (!dateVal) return new Date();
        if (dateVal instanceof Date) return dateVal;
        let s = String(dateVal).trim();
        if (!s.endsWith('Z') && !s.includes('+') && !s.match(/-\d{2}:\d{2}$/)) {
            s += 'Z';
        }
        return new Date(s);
    }

    private checkFormStatus(requestId: number) {
        const userRole = (this.authService.getUserRole() || '').toLowerCase();
        if (userRole === 'admin') {
            this.viewOnly = false;
            this.form.enable();
            return;
        }

        try {
            const locallySubmittedIds = new Set<number>(
                JSON.parse(localStorage.getItem('dfar_submitted_requests') || '[]')
            );
            if (locallySubmittedIds.has(requestId)) {
                this.viewOnly = true;
                this.form.disable({ emitEvent: false });
                this.messageService.add({
                    severity: 'info',
                    summary: 'Submitted & Locked',
                    detail: 'This form has already been submitted and is locked. Opening in read-only view.',
                    sticky: true
                });
                return;
            }
        } catch {}

        this.certificateRequestService.getMyRequests().subscribe({
            next: (requests) => {
                const req = requests.find((r) => r.id === requestId);
                if (!req) return;
                if (!this.referenceNumber && req.referenceNumber) {
                    this.referenceNumber = req.referenceNumber;
                }

                if (req.hasFormSubmitted || req.status === 1 || req.status === 'Confirmed') {
                    this.viewOnly = true;
                    this.form.disable({ emitEvent: false });
                    this.messageService.add({
                        severity: 'info',
                        summary: 'Submitted & Locked',
                        detail: 'This form has already been submitted and is locked. Opening in read-only view.',
                        sticky: true
                    });
                    return;
                }

                const createdAt = this.parseUtcDate(req.createdAt);
                const expiresAt = new Date(createdAt.getFullYear(), createdAt.getMonth(), createdAt.getDate() + 1, 0, 0, 0, 0);
                const now = Date.now();
                if (now > expiresAt.getTime()) {
                    this.isExpired = true;
                    this.form.disable({ emitEvent: false });
                    this.messageService.add({
                        severity: 'error',
                        summary: 'Form Expired',
                        detail: 'This certificate request has expired (midnight validity limit reached). You can no longer fill or submit this form.',
                        sticky: true
                    });
                }
            }
        });
    }

    goBackToPortal() {
        const userRole = (this.authService.getUserRole() || '').toLowerCase();
        const targetUrl = userRole === 'admin' ? '/uikit/admin/certificate-requests' : '/uikit/company-request';
        if (typeof window !== 'undefined' && window.self !== window.top && window.top) {
            window.top.location.href = targetUrl;
        } else {
            this.router.navigate([targetUrl]);
        }
    }

    ngOnDestroy(): void {
        this.revokeAllCertificatePreviewUrls();
        this.revokeAllAttachmentThumbnailUrls();
        this.revokePaymentSlipPreviewUrl();
        this.revokeAttachmentPreviewUrl();
    }

    private loadSavedForm(requestId: number) {
        this.certificateRequestService.getRequestById(requestId).subscribe({
            next: (r) => {
                if (r) {
                    if (r.cancelsAndReplacesRef) this.cancelsAndReplacesRef = r.cancelsAndReplacesRef;
                    if (r.cancelsAndReplacesDate) this.cancelsAndReplacesDate = r.cancelsAndReplacesDate;
                    if (!this.referenceNumber && r.referenceNumber) {
                        this.referenceNumber = r.referenceNumber;
                        this.form.patchValue({ newHC: r.referenceNumber });
                    }
                }
            }
        });

        this.certificateRequestService.getVetFormByRequestId(requestId).subscribe({
            next: (savedForm) => {
                if (!savedForm) return;
                if (!this.referenceNumber && savedForm.referenceNumber) {
                    this.referenceNumber = savedForm.referenceNumber;
                }
                const mapped = this.mapSavedFormToViewModel(savedForm);
                const ref = this.referenceNumber || savedForm.referenceNumber || savedForm.newHC;
                if (ref) {
                    this.referenceNumber = ref;
                    mapped['newHC'] = ref;
                }
                this.form.patchValue(mapped, { emitEvent: false });
                this.initializeProductsFromSavedForm(savedForm);
                this.viewModeAttachments = Array.isArray(savedForm.uploadedCertificateFiles) ? savedForm.uploadedCertificateFiles : [];
                this.preloadAttachmentThumbnails();

                if (savedForm.paymentSlip) {
                    this.viewModePaymentSlip = this.toViewModePaymentSlip(savedForm.paymentSlip);
                }

                if (this.viewOnly) {
                    this.form.disable({ emitEvent: false });
                }
            },
            error: () => {
                if (this.viewOnly) {
                    this.messageService.add({
                        severity: 'error',
                        summary: 'Load failed',
                        detail: 'Unable to load the saved EU certificate form.'
                    });

                    if (!this.isEmbedded) {
                        this.router.navigate(['/uikit/admin/certificate-requests']);
                    }
                    this.form.disable({ emitEvent: false });
                }
                // When filling a new unsubmitted form, 404 is expected because no form was saved yet.
            }
        });
    }

    private loadViewModePaymentSlip(requestId: number) {
        this.certificateRequestService.getRequests().subscribe({
            next: (requests) => {
                const request = requests.find((item) => item.id === requestId);
                this.viewModePaymentSlip = this.toViewModePaymentSlip(request?.paymentSlip);
            },
            error: () => {
                this.viewModePaymentSlip = null;
            }
        });
    }

    private toViewModePaymentSlip(paymentSlip?: PaymentSlipPreview | null): ViewModePaymentSlip | null {
        if (!paymentSlip?.contentBase64 || !paymentSlip.mimeType) {
            return null;
        }

        const previewUrl = `data:${paymentSlip.mimeType};base64,${paymentSlip.contentBase64}`;
        const isImage = paymentSlip.mimeType.startsWith('image/');
        const isPdf = paymentSlip.mimeType === 'application/pdf';

        return {
            mimeType: paymentSlip.mimeType,
            previewUrl,
            pdfSrc: isPdf ? this.sanitizer.bypassSecurityTrustResourceUrl(previewUrl) : null,
            isImage,
            isPdf
        };
    }

    private mapSavedFormToViewModel(savedForm: VetFormFieldResponse) {
        const dateFields = new Set(['arrivalAtFactory', 'processingDate', 'harvestDate', 'arrivalTimeProduct', 'arrivalConsignment', 'dateOfDeparture', 'signatureDate', 'signatureTime']);

        const patch: Record<string, unknown> = {};

        Object.entries(savedForm).forEach(([key, value]) => {
            if (value === null || value === undefined || key === 'id' || key === 'certificateRequestId' || key === 'temperature' || key === 'nature' || key === 'treatment' || key === 'products') {
                return;
            }

            if (key === 'processingDates' && typeof value === 'string' && value.includes(' - ')) {
                patch[key] = value
                    .split(' - ')
                    .map((item) => new Date(item))
                    .filter((item) => !Number.isNaN(item.getTime()));
                return;
            }

            if (dateFields.has(key) && typeof value === 'string') {
                const parsed = new Date(value);
                patch[key] = Number.isNaN(parsed.getTime()) ? value : parsed;
                return;
            }

            patch[key] = value;
        });

        return patch;
    }

    next() {
        if (!this.validateStep(this.step)) {
            return;
        }
        if (this.step < 4) this.step++;
    }

    prev() {
        if (this.step > 1) this.step--;
    }

    validateStep(stepNumber: number): boolean {
        if (this.viewOnly) return true;
        const val = this.form.value;

        if (stepNumber === 2) {
            if (!val.consignorName || !val.consignorName.trim()) {
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Required Field',
                    detail: 'Please enter Consignor Name in Step 2.'
                });
                return false;
            }
            if (!val.consigneeName || !val.consigneeName.trim()) {
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Required Field',
                    detail: 'Please enter Consignee Name in Step 2.'
                });
                return false;
            }
            return true;
        }

        if (stepNumber === 3) {
            const rawProducts = this.products.getRawValue();
            const hasProductDesc = rawProducts.some(
                (p: any) => (p.descCommon && p.descCommon.trim()) || (p.descScientific && p.descScientific.trim()) || (p.hsCode && p.hsCode.trim())
            );
            if (!hasProductDesc) {
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Required Field',
                    detail: 'Please describe at least one fish/seafood commodity in Step 3.'
                });
                return false;
            }
            return true;
        }

        if (stepNumber === 4) {
            if (!val.signature || !val.signature.trim()) {
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Required Field',
                    detail: 'Please enter Signature in Step 4.'
                });
                return false;
            }
            if (!val.signatoryName || !val.signatoryName.trim()) {
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Required Field',
                    detail: 'Please enter Signatory Name in Step 4.'
                });
                return false;
            }
            if (!val.designation || !val.designation.trim()) {
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Required Field',
                    detail: 'Please enter Designation in Step 4.'
                });
                return false;
            }
            if (!this.paymentSlipFile && !val.paymentSlip && !this.viewModePaymentSlip) {
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Required Field',
                    detail: 'Please upload the Payment Slip in Step 4.'
                });
                return false;
            }
            return true;
        }

        return true;
    }

    private validateFormFields(): boolean {
        if (!this.validateStep(2)) {
            this.step = 2;
            return false;
        }
        if (!this.validateStep(3)) {
            this.step = 3;
            return false;
        }
        if (!this.validateStep(4)) {
            this.step = 4;
            return false;
        }
        return true;
    }

    submit() {
        if (this.viewOnly || this.isLoading || this.isExpired) {
            if (this.isExpired) {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Cannot Submit Expired Form',
                    detail: 'This certificate request has expired (midnight limit reached). You can no longer submit this form.'
                });
            }
            return;
        }

        if (!this.validateFormFields()) {
            return;
        }

        this.isLoading = true;
        if (this.referenceNumber) {
            this.form.patchValue({ newHC: this.referenceNumber });
        }
        this.syncLegacyProductFields();

        const existingRequestId = Number(this.form.get('certificateRequestId')?.value);
        if (Number.isInteger(existingRequestId) && existingRequestId > 0) {
            this.submitVetForm(existingRequestId, this.referenceNumber ?? undefined);
            return;
        }

        this.certificateRequestService
            .createRequest({
                certificateType: this.certificateType,
                countryId: this.countryId,
                referenceNumber: this.referenceNumber ?? undefined
            })
            .subscribe({
                next: (certResponse) => {
                    this.form.patchValue({ certificateRequestId: certResponse.id }, { emitEvent: false });
                    this.submitVetForm(certResponse.id, certResponse.referenceNumber);
                },
                error: () => {
                    this.isLoading = false;
                    this.messageService.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to create certificate request.'
                    });
                }
            });
    }

    private submitVetForm(certificateRequestId: number, referenceNumber?: string) {
        const formData = new FormData();
        const formValue = this.form.value;

        formData.append('certificateRequestId', String(certificateRequestId));

        Object.keys(formValue).forEach((key) => {
            if (key === 'certificateRequestId' || key === 'products') {
                return;
            }

            const value = formValue[key];
            if (value === null || value === undefined || value === '') {
                return;
            }

            if (key === 'uploadedCertificateFile' || key === 'paymentSlipFile') {
                if (Array.isArray(value) && key === 'uploadedCertificateFile') {
                    value.filter((file): file is File => file instanceof File).forEach((file) => {
                        formData.append(key, file, file.name);
                    });
                } else if (value instanceof File) {
                    formData.append(key, value, value.name);
                }
                return;
            }

            if (value instanceof Date) {
                formData.append(key, value.toISOString());
                return;
            }

            if (Array.isArray(value)) {
                if (key === 'processingDates') {
                    const serializedRange = value
                        .filter((x) => x)
                        .map((x) => (x instanceof Date ? x.toISOString() : String(x)))
                        .join(' - ');

                    if (serializedRange) {
                        formData.append(key, serializedRange);
                    }
                }

                return;
            }

            if (typeof value === 'boolean') {
                formData.append(key, value ? 'true' : 'false');
                return;
            }

            formData.append(key, String(value));
        });

        formData.append('productsJson', JSON.stringify(this.products.getRawValue()));

        const uploadedCertificateFileSecondaryNames = this.uploadedCertificateFiles
            .map((file) => ({
                originalFileName: file.name,
                secondaryFileName: this.getCertificateSecondaryName(file)
            }))
            .filter((item) => item.secondaryFileName.length > 0);

        if (uploadedCertificateFileSecondaryNames.length > 0) {
            formData.append('uploadedCertificateFileSecondaryNamesJson', JSON.stringify(uploadedCertificateFileSecondaryNames));
        }

        this.certificateRequestService.submitVetCertificateForm(formData).subscribe({
            next: () => {
                this.isLoading = false;

                try {
                    const localList: number[] = JSON.parse(localStorage.getItem('dfar_submitted_requests') || '[]');
                    if (!localList.includes(certificateRequestId)) {
                        localList.push(certificateRequestId);
                        localStorage.setItem('dfar_submitted_requests', JSON.stringify(localList));
                    }
                } catch {}

                const successMessage = referenceNumber
                    ? `Certificate request ${referenceNumber} submitted successfully!`
                    : 'Certificate submitted successfully!';

                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: successMessage
                });

                this.form.reset();
                this.revokeAllCertificatePreviewUrls();
                this.uploadedCertificateFiles = [];
                this.certificateSecondaryNames = {};
                this.removePaymentSlipFile(false);
                this.step = 1;
                const userRole = (this.authService.getUserRole() || '').toLowerCase();
                if (userRole === 'admin') {
                    this.router.navigate(['/uikit/admin/certificate-requests']);
                } else {
                    this.router.navigate(['/uikit/company-request']);
                }
            },
            error: (err) => {
                this.isLoading = false;
                const detailMsg = err?.error?.message || err?.error?.title || (typeof err?.error === 'string' ? err.error : null) || 'Failed to submit form.';
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: detailMsg
                });
            }
        });
    }

    onPaymentSlipSelect(event: any) {
        const files = this.normalizeCertificateFiles(event);
        const file = files[0];
        if (file) {
            this.revokePaymentSlipPreviewUrl();
            this.paymentSlipFile = file;
            this.form.patchValue({
                paymentSlip: file.name,
                paymentSlipFile: file
            });
            this.messageService.add({
                severity: 'info',
                summary: 'File Selected',
                detail: `Payment slip "${file.name}" ready for upload.`
            });
        }
    }

    getPaymentSlipPreviewUrl(): string | null {
        if (!this.paymentSlipFile || !this.isImageFile(this.paymentSlipFile)) {
            return null;
        }

        if (this.paymentSlipPreviewUrl) {
            return this.paymentSlipPreviewUrl;
        }

        this.paymentSlipPreviewUrl = URL.createObjectURL(this.paymentSlipFile);
        return this.paymentSlipPreviewUrl;
    }

    removePaymentSlipFile(showMessage = true) {
        if (!this.paymentSlipFile && !this.form.get('paymentSlip')?.value) {
            return;
        }

        this.revokePaymentSlipPreviewUrl();
        this.paymentSlipFile = null;
        this.paymentSlipSecondaryName = '';
        this.form.patchValue({
            paymentSlip: '',
            paymentSlipFile: null
        });

        if (showMessage) {
            this.messageService.add({
                severity: 'info',
                summary: 'Removed',
                detail: 'Payment slip removed.'
            });
        }
    }

    private revokePaymentSlipPreviewUrl() {
        if (!this.paymentSlipPreviewUrl) {
            return;
        }

        URL.revokeObjectURL(this.paymentSlipPreviewUrl);
        this.paymentSlipPreviewUrl = null;
    }

    onCertificateSelect(event: any) {
        const selectedFiles = this.normalizeCertificateFiles(event);
        if (!selectedFiles.length) {
            return;
        }

        const existing = new Set(this.uploadedCertificateFiles.map((file) => `${file.name}-${file.size}-${file.lastModified}`));
        const newUniqueFiles = selectedFiles.filter((file: File) => !existing.has(`${file.name}-${file.size}-${file.lastModified}`));

        if (!newUniqueFiles.length) {
            return;
        }

        this.uploadedCertificateFiles = [...this.uploadedCertificateFiles, ...newUniqueFiles];
        this.form.patchValue({
            uploadedCertificateFile: this.uploadedCertificateFiles
        });

        this.messageService.add({
            severity: 'info',
            summary: 'Files Selected',
            detail: `${newUniqueFiles.length} certificate file(s) ready for upload.`
        });
    }

    private normalizeCertificateFiles(event: any): File[] {
        const rawFiles = event?.files ?? event?.currentFiles ?? [];
        const filesArray = Array.isArray(rawFiles) ? rawFiles : Array.from(rawFiles as FileList);
        return filesArray.filter((file): file is File => file instanceof File);
    }

    getCertificatePreviewUrl(file: File): string | null {
        if (!this.isImageFile(file)) {
            return null;
        }

        const key = this.getCertificateFileKey(file);
        const existingUrl = this.certificatePreviewUrls.get(key);
        if (existingUrl) {
            return existingUrl;
        }

        const previewUrl = URL.createObjectURL(file);
        this.certificatePreviewUrls.set(key, previewUrl);
        return previewUrl;
    }

    isImageFile(file: File): boolean {
        return file.type.startsWith('image/');
    }

    isPdfFile(file: File): boolean {
        return file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
    }

    getCertificateSecondaryName(file: File): string {
        return this.certificateSecondaryNames[this.getCertificateFileKey(file)] ?? '';
    }

    setCertificateSecondaryName(file: File, value: string) {
        const key = this.getCertificateFileKey(file);
        const normalizedValue = value.trim();
        if (!normalizedValue) {
            delete this.certificateSecondaryNames[key];
            return;
        }

        this.certificateSecondaryNames[key] = normalizedValue;
    }

    removeCertificateFile(index: number) {
        if (index < 0 || index >= this.uploadedCertificateFiles.length) {
            return;
        }

        const fileToRemove = this.uploadedCertificateFiles[index];
        this.revokeCertificatePreviewUrl(fileToRemove);
        delete this.certificateSecondaryNames[this.getCertificateFileKey(fileToRemove)];
        this.uploadedCertificateFiles.splice(index, 1);
        this.uploadedCertificateFiles = [...this.uploadedCertificateFiles];
        this.form.patchValue({
            uploadedCertificateFile: this.uploadedCertificateFiles
        });
    }

    private revokeCertificatePreviewUrl(file: File) {
        const key = this.getCertificateFileKey(file);
        const previewUrl = this.certificatePreviewUrls.get(key);
        if (!previewUrl) {
            return;
        }

        URL.revokeObjectURL(previewUrl);
        this.certificatePreviewUrls.delete(key);
    }

    private revokeAllCertificatePreviewUrls() {
        this.certificatePreviewUrls.forEach((previewUrl) => {
            URL.revokeObjectURL(previewUrl);
        });
        this.certificatePreviewUrls.clear();
    }

    getAttachmentDisplayName(attachment: VetAttachmentFieldResponse, index: number): string {
        const secondaryName = (attachment.secondaryFileName ?? '').trim();
        if (secondaryName.length > 0) {
            return secondaryName;
        }

        const originalName = (attachment.originalFileName ?? '').trim();
        if (originalName.length > 0) {
            return originalName;
        }

        return `Attachment ${index + 1}`;
    }

    getAttachmentSmallPreviewUrl(attachment: VetAttachmentFieldResponse): string | null {
        if (!attachment.id) {
            return null;
        }

        return this.attachmentThumbnailCache.get(attachment.id)?.previewUrl ?? null;
    }

    isAttachmentImageItem(attachment: VetAttachmentFieldResponse): boolean {
        const mimeType = this.getAttachmentResolvedMimeType(attachment);
        return mimeType.startsWith('image/');
    }

    isAttachmentPdfItem(attachment: VetAttachmentFieldResponse): boolean {
        const mimeType = this.getAttachmentResolvedMimeType(attachment);
        if (mimeType === 'application/pdf') {
            return true;
        }

        const originalName = (attachment.originalFileName ?? '').toLowerCase();
        return originalName.endsWith('.pdf');
    }

    private getAttachmentResolvedMimeType(attachment: VetAttachmentFieldResponse): string {
        if (attachment.id && this.attachmentThumbnailCache.has(attachment.id)) {
            return this.attachmentThumbnailCache.get(attachment.id)?.mimeType?.toLowerCase() ?? '';
        }

        return (attachment.contentType ?? '').toLowerCase();
    }

    private preloadAttachmentThumbnails() {
        if (this.viewModeAttachments.length === 0) {
            return;
        }

        this.viewModeAttachments.forEach((attachment) => {
            this.loadAttachmentThumbnail(attachment);
        });
    }

    private loadAttachmentThumbnail(attachment: VetAttachmentFieldResponse) {
        if (!attachment.id || this.attachmentThumbnailCache.has(attachment.id)) {
            return;
        }

        this.certificateRequestService.getVetCertificateAttachmentContent(attachment.id).subscribe({
            next: (response) => {
                const mimeType = (response.body?.type || attachment.contentType || 'application/octet-stream').toLowerCase();
                const previewUrl = mimeType.startsWith('image/') ? URL.createObjectURL(response.body as Blob) : null;

                this.attachmentThumbnailCache.set(attachment.id as number, {
                    mimeType,
                    previewUrl
                });
            },
            error: () => {
                // Keep list rendering resilient if thumbnail fetch fails.
            }
        });
    }

    private revokeAllAttachmentThumbnailUrls() {
        this.attachmentThumbnailCache.forEach((thumbnail) => {
            if (thumbnail.previewUrl) {
                URL.revokeObjectURL(thumbnail.previewUrl);
            }
        });
        this.attachmentThumbnailCache.clear();
    }

    openAttachmentPreview(attachment: VetAttachmentFieldResponse, index: number) {
        if (!attachment.id) {
            return;
        }

        this.certificateRequestService.getVetCertificateAttachmentContent(attachment.id).subscribe({
            next: (response) => {
                this.revokeAttachmentPreviewUrl();

                this.attachmentPreviewMimeType = response.body?.type || attachment.contentType || 'application/octet-stream';
                this.attachmentPreviewUrl = URL.createObjectURL(response.body as Blob);
                this.attachmentPreviewPdfSrc = this.isAttachmentPreviewPdf() && this.attachmentPreviewUrl ? this.sanitizer.bypassSecurityTrustResourceUrl(this.attachmentPreviewUrl) : null;
                this.attachmentPreviewTitle = this.getAttachmentDisplayName(attachment, index);
                this.attachmentPreviewDialogVisible = true;
            },
            error: () => {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Preview failed',
                    detail: 'Unable to open attachment preview.'
                });
            }
        });
    }

    closeAttachmentPreview() {
        this.attachmentPreviewDialogVisible = false;
        this.revokeAttachmentPreviewUrl();
    }

    openPaymentSlipPreview() {
        if (this.viewModePaymentSlip) {
            this.revokeAttachmentPreviewUrl();
            this.attachmentPreviewMimeType = this.viewModePaymentSlip.mimeType;
            this.attachmentPreviewUrl = this.viewModePaymentSlip.previewUrl;
            this.attachmentPreviewPdfSrc = this.viewModePaymentSlip.isPdf ? this.viewModePaymentSlip.pdfSrc : null;
            this.attachmentPreviewTitle = 'Payment Slip Preview';
            this.attachmentPreviewDialogVisible = true;
            return;
        }

        if (this.paymentSlipFile) {
            this.revokeAttachmentPreviewUrl();
            const isPdf = this.isPdfFile(this.paymentSlipFile);
            const blobUrl = URL.createObjectURL(this.paymentSlipFile);
            this.attachmentPreviewMimeType = this.paymentSlipFile.type || (isPdf ? 'application/pdf' : 'image/png');
            this.attachmentPreviewUrl = blobUrl;
            this.attachmentPreviewPdfSrc = isPdf ? this.sanitizer.bypassSecurityTrustResourceUrl(blobUrl) : null;
            this.attachmentPreviewTitle = `Payment Slip (${this.paymentSlipFile.name})`;
            this.attachmentPreviewDialogVisible = true;
        }
    }

    isAttachmentPreviewImage(): boolean {
        return (this.attachmentPreviewMimeType ?? '').startsWith('image/');
    }

    isAttachmentPreviewPdf(): boolean {
        return (this.attachmentPreviewMimeType ?? '').toLowerCase() === 'application/pdf';
    }

    private revokeAttachmentPreviewUrl() {
        if (!this.attachmentPreviewUrl) {
            return;
        }

        if (this.attachmentPreviewUrl.startsWith('blob:')) {
            URL.revokeObjectURL(this.attachmentPreviewUrl);
        }
        this.attachmentPreviewUrl = null;
        this.attachmentPreviewPdfSrc = null;
        this.attachmentPreviewMimeType = null;
    }

    private getCertificateFileKey(file: File): string {
        return `${file.name}-${file.size}-${file.lastModified}`;
    }

    setExclusiveRadio(selectedControl: string, groupControls: string[]) {
        const patch: Record<string, boolean> = {};
        for (const control of groupControls) {
            patch[control] = control === selectedControl;
        }

        this.form.patchValue(patch, { emitEvent: false });
    }

    addProduct() {
        this.products.push(this.createProductGroup());
    }

    removeProduct(index: number) {
        if (this.products.length <= 1) {
            return;
        }

        this.products.removeAt(index);
        this.syncLegacyProductFields();
    }

    setExclusiveProductRadio(index: number, selectedControl: string, groupControls: string[]) {
        const productGroup = this.products.at(index);
        if (!productGroup) {
            return;
        }

        const patch: Record<string, boolean> = {};
        for (const control of groupControls) {
            patch[control] = control === selectedControl;
        }

        productGroup.patchValue(patch, { emitEvent: false });
    }

    private createProductGroup(initialValue?: Partial<Record<string, unknown>>) {
        return this.fb.group({
            descCommon: [String(initialValue?.['descCommon'] ?? '')],
            descScientific: [String(initialValue?.['descScientific'] ?? '')],
            processingType: [String(initialValue?.['processingType'] ?? '')],
            hsCode: [String(initialValue?.['hsCode'] ?? '')],
            temperatureAmbient: [Boolean(initialValue?.['temperatureAmbient'])],
            temperatureChilled: [Boolean(initialValue?.['temperatureChilled'])],
            temperatureFrozen: [Boolean(initialValue?.['temperatureFrozen'])],
            quantity: [String(initialValue?.['quantity'] ?? '')],
            numPackages: [String(initialValue?.['numPackages'] ?? '')],
            packagingType: [String(initialValue?.['packagingType'] ?? '')],
            containerId: [String(initialValue?.['containerId'] ?? '')],
            forHumanConsumption: [Boolean(initialValue?.['forHumanConsumption'])],
            forImportEU: [String(initialValue?.['forImportEU'] ?? this.form?.get('forImportEU')?.value ?? 'EU')],
            natureAquaculture: [Boolean(initialValue?.['natureAquaculture'])],
            natureWildOrigin: [Boolean(initialValue?.['natureWildOrigin'])],
            treatmentChilled: [Boolean(initialValue?.['treatmentChilled'])],
            treatmentFrozen: [Boolean(initialValue?.['treatmentFrozen'])],
            treatmentLive: [Boolean(initialValue?.['treatmentLive'])],
            netWeight: [String(initialValue?.['netWeight'] ?? '')],
            netWeightUnit: [String(initialValue?.['netWeightUnit'] ?? 'kg')]
        });
    }

    private initializeProductsFromSavedForm(savedForm: VetFormFieldResponse) {
        const savedProducts = savedForm.products as VetProductFieldResponse[] | undefined;
        if (Array.isArray(savedProducts) && savedProducts.length > 0) {
            this.products.clear();
            savedProducts.forEach((product) => {
                this.products.push(this.createProductGroup(product as Partial<Record<string, unknown>>));
            });
            return;
        }

        this.initializeProductsFromLegacyFields();
    }

    private initializeProductsFromLegacyFields() {
        const legacyProduct = {
            descCommon: this.form.get('descCommon')?.value,
            descScientific: this.form.get('descScientific')?.value,
            processingType: this.form.get('processingType')?.value,
            hsCode: this.form.get('hsCode')?.value,
            temperatureAmbient: this.form.get('temperatureAmbient')?.value,
            temperatureChilled: this.form.get('temperatureChilled')?.value,
            temperatureFrozen: this.form.get('temperatureFrozen')?.value,
            quantity: this.form.get('quantity')?.value,
            numPackages: this.form.get('numPackages')?.value,
            packagingType: this.form.get('packagingType')?.value,
            containerId: this.form.get('containerId')?.value,
            forHumanConsumption: this.form.get('forHumanConsumption')?.value,
            forImportEU: this.form.get('forImportEU')?.value,
            natureAquaculture: this.form.get('natureAquaculture')?.value,
            natureWildOrigin: this.form.get('natureWildOrigin')?.value,
            treatmentChilled: this.form.get('treatmentChilled')?.value,
            treatmentFrozen: this.form.get('treatmentFrozen')?.value,
            treatmentLive: this.form.get('treatmentLive')?.value,
            netWeight: this.form.get('netWeight')?.value,
            netWeightUnit: this.form.get('netWeightUnit')?.value ?? 'kg'
        };

        if (this.hasAnyProductValue(legacyProduct)) {
            this.products.clear();
            this.products.push(this.createProductGroup(legacyProduct));
            return;
        }

        if (this.products.length === 0) {
            this.products.push(this.createProductGroup());
        }
    }

    private hasAnyProductValue(product: Record<string, unknown>): boolean {
        return Object.values(product).some((value) => {
            if (typeof value === 'boolean') {
                return value;
            }

            return String(value ?? '').trim().length > 0;
        });
    }

    private syncLegacyProductFields() {
        const firstProduct = this.products.at(0)?.value ?? {};

        this.form.patchValue(
            {
                descCommon: firstProduct.descCommon ?? '',
                descScientific: firstProduct.descScientific ?? '',
                processingType: firstProduct.processingType ?? '',
                hsCode: firstProduct.hsCode ?? '',
                temperatureAmbient: Boolean(firstProduct.temperatureAmbient),
                temperatureChilled: Boolean(firstProduct.temperatureChilled),
                temperatureFrozen: Boolean(firstProduct.temperatureFrozen),
                quantity: firstProduct.quantity ?? '',
                numPackages: firstProduct.numPackages ?? '',
                packagingType: firstProduct.packagingType ?? '',
                containerId: firstProduct.containerId ?? '',
                forHumanConsumption: Boolean(firstProduct.forHumanConsumption),
                forImportEU: firstProduct.forImportEU ?? 'EU',
                natureAquaculture: Boolean(firstProduct.natureAquaculture),
                natureWildOrigin: Boolean(firstProduct.natureWildOrigin),
                treatmentChilled: Boolean(firstProduct.treatmentChilled),
                treatmentFrozen: Boolean(firstProduct.treatmentFrozen),
                treatmentLive: Boolean(firstProduct.treatmentLive),
                netWeight: firstProduct.netWeight ?? '',
                netWeightUnit: firstProduct.netWeightUnit ?? 'kg'
            },
            { emitEvent: false }
        );
    }

    goBack(): void {
        this.location.back();
    }
}
