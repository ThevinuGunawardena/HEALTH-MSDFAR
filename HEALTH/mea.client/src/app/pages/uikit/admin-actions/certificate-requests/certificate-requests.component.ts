import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { TableModule } from 'primeng/table';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { DialogModule } from 'primeng/dialog';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateRequestResponse, CertificateRequestService, PaymentSlipPreview } from 'src/app/pages/service/certificate-request.service';
import { catchError, forkJoin, map, Observable, of, switchMap } from 'rxjs';

interface CertificateRequestPaymentSlip {
    mimeType: string;
    imageSrc: string | null;
    pdfSrc: SafeResourceUrl | null;
    isImage: boolean;
    isPdf: boolean;
}

interface CertificateRequest {
    id: number;
    date: string;
    company: string;
    refNumber: string;
    type: 'EU' | 'NONEU';
    country?: string;
    amount: number;
    status: 'Pending' | 'Confirmed' | 'Rejected';
    paymentSlip: CertificateRequestPaymentSlip | null;
    hasSubmittedCertificate: boolean | null;
    cancelsAndReplacesRef?: string | null;
    cancelsAndReplacesDate?: string | Date | null;
}

import {
    CertificateTemplateOption,
    getCertificateTemplates,
    hasMultipleTemplates,
    getCertificatePath
} from '@/shared/country-certificate-templates';

@Component({
    selector: 'app-certificate-requests',
    standalone: true,
    imports: [CommonModule, FormsModule, TableModule, InputTextModule, ButtonModule, DialogModule, ToastModule],
    providers: [MessageService],
    templateUrl: './certificate-requests.component.html',
    styleUrls: ['./certificate-requests.component.css']
})
export class CertificateRequestsComponent implements OnInit {
    requests: CertificateRequest[] = [];
    filteredRequests: CertificateRequest[] = [];
    searchTerm = '';
    readonly fixedAmount = 500;
    imageZoom = 1;
    confirmDialogVisible = false;
    confirmingRequest = false;
    selectedRequest: CertificateRequest | null = null;
    rejectDialogVisible = false;
    rejectingRequest = false;
    selectedRejectRequest: CertificateRequest | null = null;
    isCompanyUser = false;

    // Template selection dialog state (Option B)
    templateDialogVisible = false;
    templateOptions: CertificateTemplateOption[] = [];
    selectedRequestForTemplate: CertificateRequest | null = null;
    templateDialogAction: 'continue' | 'edit' = 'continue';

    constructor(
        private messageService: MessageService,
        private certificateRequestService: CertificateRequestService,
        private sanitizer: DomSanitizer,
        private router: Router
        ,private authService: AuthService
    ) {}

    ngOnInit() {
        this.isCompanyUser = (this.authService.getUserRole() || '').toLowerCase() === 'company';
        this.loadRequests();
    }

    private loadRequests() {
        this.certificateRequestService.getRequests().subscribe({
            next: (response) => {
                const rows = response.map((item) => this.toRow(item));
                this.requests = rows;
                this.filteredRequests = [...this.requests];
                this.hydrateSubmissionStatus();
            },
            error: () => {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Load failed',
                    detail: 'Failed to load certificate requests.',
                    life: 4000
                });
            }
        });
    }

    private toRow(item: CertificateRequestResponse): CertificateRequest {
        const type = this.normalizeType(item.certificateType);
        return {
            id: item.id,
            date: item.createdAt,
            company: item.companyName ?? 'N/A',
            refNumber: item.referenceNumber,
            type: type,
            country: item.countryName || (type === 'EU' ? 'European Union' : undefined),
            amount: this.fixedAmount,
            status: this.normalizeStatus(item.status),
            paymentSlip: this.toPaymentSlip(item.paymentSlip),
            hasSubmittedCertificate: item.hasFormSubmitted ?? null,
            cancelsAndReplacesRef: item.cancelsAndReplacesRef,
            cancelsAndReplacesDate: item.cancelsAndReplacesDate
        };
    }

    private hydrateSubmissionStatus() {
        if (this.requests.length === 0) {
            return;
        }

        const checks = this.requests.map((request) =>
            this.hasSubmittedCertificate(request).pipe(
                map((submitted) => ({ requestId: request.id, submitted }))
            )
        );

        forkJoin(checks).subscribe({
            next: (results) => {
                const statusByRequestId = new Map<number, boolean>(results.map((result) => [result.requestId, result.submitted]));
                this.requests = this.requests
                    .map((request) => {
                        const isSubmitted = statusByRequestId.get(request.id) ?? (request.hasSubmittedCertificate === true);
                        return {
                            ...request,
                            hasSubmittedCertificate: isSubmitted
                        };
                    })
                    .filter((r) => r.hasSubmittedCertificate === true);

                this.applySearch();
            }
        });
    }

    private hasSubmittedCertificate(request: CertificateRequest): Observable<boolean> {
        if (request.hasSubmittedCertificate === true) {
            return of(true);
        }

        // Check if VetCertificateForm exists first (the standard form submitted by companies for both EU & NonEU)
        return this.toExistsResult(this.certificateRequestService.getVetFormByRequestId(request.id)).pipe(
            switchMap((exists) => {
                if (exists) return of(true);

                if (request.type === 'EU' || !request.country) {
                    return of(false);
                }

                switch (request.country.trim().toLowerCase()) {
                    case 'australia':
                        return this.toExistsResult(this.certificateRequestService.getAuCertificateByRequestId(request.id));
                    case 'armenia':
                        return this.toExistsResult(this.certificateRequestService.getAmCertificateByRequestId(request.id));
                    case 'brazil':
                        return this.toExistsResult(this.certificateRequestService.getBrCertificateByRequestId(request.id));
                    case 'china':
                        return this.toExistsResult(this.certificateRequestService.getChCertificateByRequestId(request.id));
                    case 'hong kong':
                    case 'hongkong':
                        return this.toExistsResult(this.certificateRequestService.getHkCertificateByRequestId(request.id));
                    case 'india':
                        return this.toExistsResult(this.certificateRequestService.getIndCertificateByRequestId(request.id));
                    case 'indonesia':
                        return this.toExistsResult(this.certificateRequestService.getIdCertificateByRequestId(request.id));
                    case 'japan':
                        return this.toExistsResult(this.certificateRequestService.getJpCertificateByRequestId(request.id));
                    case 'kuwait':
                        return this.toExistsResult(this.certificateRequestService.getKwCertificateByRequestId(request.id));
                    case 'malaysia':
                        return this.toExistsResult(this.certificateRequestService.getMyCertificateByRequestId(request.id));
                    case 'maldives':
                        return this.toExistsResult(this.certificateRequestService.getMvCertificateByRequestId(request.id));
                    case 'new zealand':
                    case 'newzealand':
                        return this.toExistsResult(this.certificateRequestService.getNzCertificateByRequestId(request.id));
                    case 'russia':
                        return this.toExistsResult(this.certificateRequestService.getRuCertificateByRequestId(request.id));
                    case 'kazakhstan':
                    case 'republic of kazakhstan':
                        return this.toExistsResult(this.certificateRequestService.getKzCertificateByRequestId(request.id));
                    case 'taiwan':
                        return this.toExistsResult(this.certificateRequestService.getTwCertificateByRequestId(request.id));
                    case 'ukraine':
                        return this.toExistsResult(this.certificateRequestService.getUaCertificateByRequestId(request.id));
                    case 'uk':
                    case 'united kingdom':
                    case 'great britain':
                        return this.toExistsResult(this.certificateRequestService.getUkCertificateByRequestId(request.id));
                    case 'usa':
                    case 'united states of america':
                    case 'united states':
                        return this.toExistsResult(this.certificateRequestService.getUsaCertificateByRequestId(request.id));
                    case 'canada':
                        return this.toExistsResult(this.certificateRequestService.getCaCertificateByRequestId(request.id));
                    case 'saudi arabia':
                    case 'saudi':
                        return this.toExistsResult(this.certificateRequestService.getSaCertificateByRequestId(request.id));
                    case 'south africa':
                    case 'southafrica':
                        return this.toExistsResult(this.certificateRequestService.getZaCertificateByRequestId(request.id));
                    case 'israel':
                        return this.toExistsResult(this.certificateRequestService.getIlCertificateByRequestId(request.id));
                    default:
                        return of(false);
                }
            })
        );
    }

    private toExistsResult<T>(request$: Observable<T>): Observable<boolean> {
        return request$.pipe(
            map((res) => Boolean(res)),
            catchError(() => of(false))
        );
    }

    private toPaymentSlip(paymentSlip?: PaymentSlipPreview | null): CertificateRequestPaymentSlip | null {
        if (!paymentSlip?.contentBase64 || !paymentSlip.mimeType) {
            return null;
        }

        const downloadUrl = `data:${paymentSlip.mimeType};base64,${paymentSlip.contentBase64}`;
        const isImage = paymentSlip.mimeType.startsWith('image/');
        const isPdf = paymentSlip.mimeType === 'application/pdf';

        return {
            mimeType: paymentSlip.mimeType,
            imageSrc: isImage ? downloadUrl : null,
            pdfSrc: isPdf ? this.sanitizer.bypassSecurityTrustResourceUrl(downloadUrl) : null,
            isImage,
            isPdf
        };
    }

    private normalizeType(value: string | number): 'EU' | 'NONEU' {
        if (value === 0 || value === 'EU') {
            return 'EU';
        }
        return 'NONEU';
    }

    private normalizeStatus(value: string | number): 'Pending' | 'Confirmed' | 'Rejected' {
        if (value === 0 || value === 'Pending') {
            return 'Pending';
        }
        if (value === 1 || value === 'Confirmed') {
            return 'Confirmed';
        }
        return 'Rejected';
    }

    onConfirm(request: CertificateRequest) {
        this.selectedRequest = request;
        this.resetImageZoom();
        this.confirmDialogVisible = true;
    }

    closeConfirmDialog() {
        if (this.confirmingRequest) {
            return;
        }

        this.confirmDialogVisible = false;
        this.selectedRequest = null;
        this.resetImageZoom();
    }

    zoomInImage() {
        this.imageZoom = Math.min(this.imageZoom + 0.25, 4);
    }

    zoomOutImage() {
        this.imageZoom = Math.max(this.imageZoom - 0.25, 1);
    }

    resetImageZoom() {
        this.imageZoom = 1;
    }

    confirmSelectedRequest() {
        if (!this.selectedRequest || this.confirmingRequest) {
            return;
        }

        const request = this.selectedRequest;
        this.confirmingRequest = true;

        this.certificateRequestService.confirmRequest(request.id).subscribe({
            next: () => {
                this.confirmingRequest = false;
                this.confirmDialogVisible = false;
                this.selectedRequest = null;
                request.status = 'Confirmed';
                request.hasSubmittedCertificate = false;

                this.hydrateSubmissionStatus();

                if (request.type === 'NONEU' && request.country) {
                    const templates = getCertificateTemplates(request.country, request.type);

                    if (templates.length === 1) {
                        this.messageService.add({
                            severity: 'success',
                            summary: 'Confirmed',
                            detail: `Payment of Rs. 500 confirmed for ${request.refNumber}. Navigating to ${request.country} certificate form...`,
                            life: 2000
                        });

                        setTimeout(() => {
                            this.router.navigate([templates[0].path], {
                                queryParams: {
                                    requestId: request.id,
                                    ref: request.refNumber
                                }
                            });
                        }, 1500);
                    } else if (templates.length > 1) {
                        this.messageService.add({
                            severity: 'success',
                            summary: 'Confirmed',
                            detail: `Payment of Rs. 500 confirmed for ${request.refNumber}. Please select the certificate template to fill.`,
                            life: 4000
                        });
                        this.selectedRequestForTemplate = request;
                        this.templateOptions = templates;
                        this.templateDialogAction = 'continue';
                        this.templateDialogVisible = true;
                    } else {
                        this.messageService.add({
                            severity: 'warn',
                            summary: 'Confirmed',
                            detail: `Payment of Rs. 500 confirmed for ${request.refNumber}. Certificate form for ${request.country} not yet available.`,
                            life: 5000
                        });
                    }
                } else {
                    this.messageService.add({
                        severity: 'success',
                        summary: 'Confirmed',
                        detail: `Payment of Rs. 500 confirmed for ${request.refNumber}.`,
                        life: 5000
                    });
                }
            },
            error: () => {
                this.confirmingRequest = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Update failed',
                    detail: `Failed to confirm ${request.refNumber}.`,
                    life: 4000
                });
            }
        });
    }

    onReject(request: CertificateRequest) {
        this.selectedRejectRequest = request;
        this.rejectDialogVisible = true;
    }

    closeRejectDialog() {
        if (this.rejectingRequest) {
            return;
        }

        this.rejectDialogVisible = false;
        this.selectedRejectRequest = null;
    }

    confirmRejectSelectedRequest() {
        if (!this.selectedRejectRequest || this.rejectingRequest) {
            return;
        }

        const request = this.selectedRejectRequest;
        this.rejectingRequest = true;

        this.certificateRequestService.rejectRequest(request.id).subscribe({
            next: () => {
                this.rejectingRequest = false;
                this.rejectDialogVisible = false;
                this.selectedRejectRequest = null;
                request.status = 'Rejected';
                this.messageService.add({
                    severity: 'error',
                    summary: 'Rejected',
                    detail: `Request ${request.refNumber} rejected. Email notification sent to ${request.company}.`,
                    life: 5000
                });
            },
            error: () => {
                this.rejectingRequest = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Update failed',
                    detail: `Failed to reject ${request.refNumber}.`,
                    life: 4000
                });
            }
        });
    }

    onReplacement(request: CertificateRequest) {
        if (!confirm(`Are you sure you want to create a Replacement Certificate for ${request.refNumber}? This will issue a new reference number and clone the existing certificate details.`)) {
            return;
        }

        this.certificateRequestService.createReplacement(request.id).subscribe({
            next: (res) => {
                this.messageService.add({
                    severity: 'success',
                    summary: 'Replacement Created',
                    detail: `New replacement certificate ${res.newReferenceNumber} created successfully. Loaded into Replacement Requests section.`,
                    life: 4000
                });

                this.router.navigate(['/uikit/admin/replacement-requests']);
            },
            error: (err) => {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Replacement Failed',
                    detail: err?.error?.message || `Failed to create replacement for ${request.refNumber}.`,
                    life: 4000
                });
            }
        });
    }

    onView(request: CertificateRequest) {
        const queryParams: Record<string, string | number> = {
            requestId: request.id,
            ref: request.refNumber,
            type: request.type
        };
        if (request.country) queryParams['country'] = request.country;
        if (request.cancelsAndReplacesRef) queryParams['cancelsAndReplacesRef'] = request.cancelsAndReplacesRef;
        if (request.cancelsAndReplacesDate) queryParams['cancelsAndReplacesDate'] = typeof request.cancelsAndReplacesDate === 'string' ? request.cancelsAndReplacesDate : request.cancelsAndReplacesDate.toISOString();

        this.router.navigate(['/uikit/admin/certificate-requests/view'], {
            queryParams
        });
    }

    onEdit(request: CertificateRequest) {
        if (request.type === 'EU') {
            const queryParams: Record<string, string | number> = {
                requestId: request.id,
                ref: request.refNumber,
                type: 'EU',
                adminEdit: 'true'
            };
            if (request.cancelsAndReplacesRef) queryParams['cancelsAndReplacesRef'] = request.cancelsAndReplacesRef;
            if (request.cancelsAndReplacesDate) queryParams['cancelsAndReplacesDate'] = typeof request.cancelsAndReplacesDate === 'string' ? request.cancelsAndReplacesDate : request.cancelsAndReplacesDate.toISOString();
            this.router.navigate(['/uikit/certificate'], { queryParams });
            return;
        }

        if (request.country) {
            const templates = getCertificateTemplates(request.country, request.type);
            if (templates.length === 1) {
                const queryParams: Record<string, string | number> = {
                    requestId: request.id,
                    ref: request.refNumber,
                    adminEdit: 'true'
                };
                if (request.cancelsAndReplacesRef) queryParams['cancelsAndReplacesRef'] = request.cancelsAndReplacesRef;
                if (request.cancelsAndReplacesDate) queryParams['cancelsAndReplacesDate'] = typeof request.cancelsAndReplacesDate === 'string' ? request.cancelsAndReplacesDate : request.cancelsAndReplacesDate.toISOString();
                this.router.navigate([templates[0].path], { queryParams });
                return;
            }

            if (templates.length > 1) {
                this.selectedRequestForTemplate = request;
                this.templateOptions = templates;
                this.templateDialogAction = 'edit';
                this.templateDialogVisible = true;
                return;
            }
        }

        // Fallback to Vet Form
        this.router.navigate(['/uikit/certificate'], {
            queryParams: {
                requestId: request.id,
                ref: request.refNumber,
                adminEdit: 'true'
            }
        });
    }

    onContinue(request: CertificateRequest) {
        if (request.type === 'EU') {
            const queryParams: Record<string, string | number> = {
                requestId: request.id,
                ref: request.refNumber,
                type: 'EU'
            };
            if (request.cancelsAndReplacesRef) queryParams['cancelsAndReplacesRef'] = request.cancelsAndReplacesRef;
            if (request.cancelsAndReplacesDate) queryParams['cancelsAndReplacesDate'] = typeof request.cancelsAndReplacesDate === 'string' ? request.cancelsAndReplacesDate : request.cancelsAndReplacesDate.toISOString();
            this.router.navigate(['/uikit/certificate'], { queryParams });
            return;
        }

        if (!request.country) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Country Missing',
                detail: `Unable to continue request ${request.refNumber} because country is missing.`,
                life: 4000
            });
            return;
        }

        const templates = getCertificateTemplates(request.country, request.type);
        if (templates.length === 1) {
            const queryParams: Record<string, string | number> = {
                requestId: request.id,
                ref: request.refNumber
            };
            if (request.cancelsAndReplacesRef) queryParams['cancelsAndReplacesRef'] = request.cancelsAndReplacesRef;
            if (request.cancelsAndReplacesDate) queryParams['cancelsAndReplacesDate'] = typeof request.cancelsAndReplacesDate === 'string' ? request.cancelsAndReplacesDate : request.cancelsAndReplacesDate.toISOString();
            this.router.navigate([templates[0].path], { queryParams });
            return;
        }

        if (templates.length > 1) {
            this.selectedRequestForTemplate = request;
            this.templateOptions = templates;
            this.templateDialogAction = 'continue';
            this.templateDialogVisible = true;
            return;
        }

        this.messageService.add({
            severity: 'warn',
            summary: 'Route Missing',
            detail: `Certificate form for ${request.country} is not available yet.`,
            life: 4000
        });
    }

    selectTemplate(template: CertificateTemplateOption) {
        if (!this.selectedRequestForTemplate) return;
        const request = this.selectedRequestForTemplate;
        const queryParams: Record<string, string | number> = {
            requestId: request.id,
            ref: request.refNumber
        };
        if (this.templateDialogAction === 'edit') {
            queryParams['adminEdit'] = 'true';
        }
        if (request.cancelsAndReplacesRef) {
            queryParams['cancelsAndReplacesRef'] = request.cancelsAndReplacesRef;
        }
        if (request.cancelsAndReplacesDate) {
            queryParams['cancelsAndReplacesDate'] = typeof request.cancelsAndReplacesDate === 'string'
                ? request.cancelsAndReplacesDate
                : request.cancelsAndReplacesDate.toISOString();
        }

        this.templateDialogVisible = false;
        this.selectedRequestForTemplate = null;
        this.templateOptions = [];

        this.router.navigate([template.path], { queryParams });
    }

    closeTemplateDialog() {
        this.templateDialogVisible = false;
        this.selectedRequestForTemplate = null;
        this.templateOptions = [];
    }

    applySearch() {
        const filter = this.searchTerm.trim().toLowerCase();

        this.filteredRequests = this.requests.filter((request) => {
            if (!filter) {
                return true;
            }

            const refMatch = request.refNumber.toLowerCase().includes(filter);
            const companyMatch = request.company.toLowerCase().includes(filter);
            return refMatch || companyMatch;
        });
    }

    clearSearch() {
        this.searchTerm = '';
        this.filteredRequests = [...this.requests];
    }
}
