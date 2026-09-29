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
import { catchError, forkJoin, map, Observable, of } from 'rxjs';

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
}

const COUNTRY_CERTIFICATE_MAP: Record<string, string> = {
    Australia: '/uikit/world-certificates/au-certificate',
    Brazil: '/uikit/world-certificates/br-certificate',
    China: '/uikit/world-certificates/ch-certificate',
    Armenia: '/uikit/world-certificates/am-certificate',
    'Hong Kong': '/uikit/world-certificates/hk-certificate',
    India: '/uikit/world-certificates/in-certificate',
    Indonesia: '/uikit/world-certificates/id-certificate',
    Malaysia: '/uikit/world-certificates/my-certificate',
    Kuwait: '/uikit/world-certificates/kw-certificate',
    Taiwan: '/uikit/world-certificates/tw-certificate',
    Ukraine: '/uikit/world-certificates/ua-certificate',
    Russia: '/uikit/world-certificates/ru-certificate',
    Kazakhstan: '/uikit/world-certificates/kz-certificate',
    Japan: '/uikit/world-certificates/jp-certificate',
    'New Zealand': '/uikit/world-certificates/nz-certificate',
    USA: '/uikit/world-certificates/usa-certificate',
    'United States of America': '/uikit/world-certificates/usa-certificate',
    'United States': '/uikit/world-certificates/usa-certificate',
    UK: '/uikit/world-certificates/uk-certificate',
    'United Kingdom': '/uikit/world-certificates/uk-certificate',
    'Great Britain': '/uikit/world-certificates/uk-certificate',
    Israel: '/uikit/world-certificates/il-certificate',
    Maldives: '/uikit/world-certificates/mv-certificate',
    Canada: '/uikit/world-certificates/ca-certificate',
    'Saudi Arabia': '/uikit/world-certificates/sa-certificate',
    'South Africa': '/uikit/world-certificates/za-certificate'
};

function getCertificatePath(countryName?: string | null): string | null {
    if (!countryName) return null;
    const normalized = countryName.trim().toLowerCase();
    
    if (normalized === 'australia') return '/uikit/world-certificates/au-certificate';
    if (normalized === 'usa' || normalized === 'united states' || normalized === 'united states of america') return '/uikit/world-certificates/usa-certificate';
    if (normalized === 'uk' || normalized === 'united kingdom' || normalized === 'great britain') return '/uikit/world-certificates/uk-certificate';
    if (normalized === 'brazil') return '/uikit/world-certificates/br-certificate';
    if (normalized === 'china') return '/uikit/world-certificates/ch-certificate';
    if (normalized === 'armenia') return '/uikit/world-certificates/am-certificate';
    if (normalized === 'hong kong') return '/uikit/world-certificates/hk-certificate';
    if (normalized === 'india') return '/uikit/world-certificates/in-certificate';
    if (normalized === 'indonesia') return '/uikit/world-certificates/id-certificate';
    if (normalized === 'malaysia') return '/uikit/world-certificates/my-certificate';
    if (normalized === 'kuwait') return '/uikit/world-certificates/kw-certificate';
    if (normalized === 'taiwan') return '/uikit/world-certificates/tw-certificate';
    if (normalized === 'ukraine') return '/uikit/world-certificates/ua-certificate';
    if (normalized === 'russia') return '/uikit/world-certificates/ru-certificate';
    if (normalized === 'kazakhstan' || normalized === 'republic of kazakhstan') return '/uikit/world-certificates/kz-certificate';
    if (normalized === 'japan') return '/uikit/world-certificates/jp-certificate';
    if (normalized === 'new zealand') return '/uikit/world-certificates/nz-certificate';
    if (normalized === 'israel') return '/uikit/world-certificates/il-certificate';
    if (normalized === 'maldives') return '/uikit/world-certificates/mv-certificate';
    if (normalized === 'canada') return '/uikit/world-certificates/ca-certificate';
    if (normalized === 'saudi arabia') return '/uikit/world-certificates/sa-certificate';
    if (normalized === 'south africa') return '/uikit/world-certificates/za-certificate';

    const matchKey = Object.keys(COUNTRY_CERTIFICATE_MAP).find(
        (k) => k.toLowerCase().trim() === normalized
    );
    return matchKey ? COUNTRY_CERTIFICATE_MAP[matchKey] : null;
}

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
            hasSubmittedCertificate: item.hasFormSubmitted ?? null
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

        if (request.type === 'EU') {
            return this.toExistsResult(this.certificateRequestService.getVetFormByRequestId(request.id));
        }

        if (!request.country) {
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
                return this.toExistsResult(this.certificateRequestService.getSaCertificateByRequestId(request.id));
            case 'south africa':
                return this.toExistsResult(this.certificateRequestService.getZaCertificateByRequestId(request.id));
            case 'israel':
                return this.toExistsResult(this.certificateRequestService.getIlCertificateByRequestId(request.id));
            default:
                return of(false);
        }
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
                    const certificatePath = getCertificatePath(request.country);

                    if (certificatePath) {
                        this.messageService.add({
                            severity: 'success',
                            summary: 'Confirmed',
                            detail: `Payment of Rs. 500 confirmed for ${request.refNumber}. Navigating to ${request.country} certificate form...`,
                            life: 2000
                        });

                        setTimeout(() => {
                            this.router.navigate([certificatePath], {
                                queryParams: {
                                    requestId: request.id,
                                    ref: request.refNumber
                                }
                            });
                        }, 1500);
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

    onView(request: CertificateRequest) {
        this.router.navigate(['/uikit/admin/certificate-requests/view'], {
            queryParams: {
                requestId: request.id,
                ref: request.refNumber,
                type: request.type,
                country: request.country ?? null
            }
        });
    }

    onEdit(request: CertificateRequest) {
        if (request.type === 'EU') {
            this.router.navigate(['/uikit/certificate'], {
                queryParams: {
                    requestId: request.id,
                    ref: request.refNumber,
                    type: 'EU',
                    adminEdit: 'true'
                }
            });
            return;
        }

        if (request.country) {
            const certificatePath = getCertificatePath(request.country);
            if (certificatePath) {
                this.router.navigate([certificatePath], {
                    queryParams: {
                        requestId: request.id,
                        ref: request.refNumber,
                        adminEdit: 'true'
                    }
                });
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
            this.router.navigate(['/uikit/certificate'], {
                queryParams: {
                    requestId: request.id,
                    ref: request.refNumber,
                    type: 'EU'
                }
            });
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

        const certificatePath = getCertificatePath(request.country);
        if (!certificatePath) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Route Missing',
                detail: `Certificate form for ${request.country} is not available yet.`,
                life: 4000
            });
            return;
        }

        this.router.navigate([certificatePath], {
            queryParams: {
                requestId: request.id,
                ref: request.refNumber
            }
        });
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
