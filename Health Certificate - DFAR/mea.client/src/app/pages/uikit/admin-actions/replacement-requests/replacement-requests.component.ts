import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TableModule } from 'primeng/table';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { DialogModule } from 'primeng/dialog';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { SelectModule } from 'primeng/select';
import { TextareaModule } from 'primeng/textarea';
import { TagModule } from 'primeng/tag';
import { IconFieldModule } from 'primeng/iconfield';
import { InputIconModule } from 'primeng/inputicon';
import { Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateRequestService, ReplacementRequestItem, EligibleCertificateItem } from '@/pages/service/certificate-request.service';
import { getCertificateTemplates, getCertificatePath } from '@/shared/country-certificate-templates';

@Component({
    selector: 'app-replacement-requests',
    standalone: true,
    imports: [
        CommonModule,
        FormsModule,
        TableModule,
        InputTextModule,
        ButtonModule,
        DialogModule,
        ToastModule,
        SelectModule,
        TextareaModule,
        TagModule,
        IconFieldModule,
        InputIconModule
    ],
    providers: [MessageService],
    templateUrl: './replacement-requests.component.html',
    styleUrls: ['./replacement-requests.component.css']
})
export class ReplacementRequestsComponent implements OnInit {
    requests: ReplacementRequestItem[] = [];
    filteredRequests: ReplacementRequestItem[] = [];
    eligibleCertificates: EligibleCertificateItem[] = [];
    
    searchTerm = '';
    selectedStatusFilter: string = 'ALL';
    statusFilterOptions = [
        { label: 'All Requests', value: 'ALL' },
        { label: 'Pending', value: '0' },
        { label: 'Approved', value: '1' },
        { label: 'Rejected', value: '2' }
    ];

    isLoading = false;
    isCompanyUser = false;
    isAdmin = false;

    // Create Request Modal
    createDialogVisible = false;
    isSubmitting = false;
    newRequest = {
        selectedCert: null as EligibleCertificateItem | null,
        manualRefNumber: '',
        reasonType: 'Vessel / Voyage Details Changed',
        reasonDetails: '',
        remarks: ''
    };

    commonReasons = [
        { label: 'Vessel / Voyage Details Changed', value: 'Vessel / Voyage Details Changed' },
        { label: 'Consignee / Destination Address Amendment', value: 'Consignee / Destination Address Amendment' },
        { label: 'Typographical / Clerical Error in Data', value: 'Typographical / Clerical Error in Data' },
        { label: 'Original Hardcopy Certificate Damaged / Lost', value: 'Original Hardcopy Certificate Damaged / Lost' },
        { label: 'Port of Entry / Loading Route Changed', value: 'Port of Entry / Loading Route Changed' },
        { label: 'Other Special Exporter Amendment', value: 'Other Special Exporter Amendment' }
    ];

    // Approve Dialog
    approveDialogVisible = false;
    isApproving = false;
    selectedRequestForApprove: ReplacementRequestItem | null = null;

    // Reject Dialog
    rejectDialogVisible = false;
    isRejecting = false;
    selectedRequestForReject: ReplacementRequestItem | null = null;
    rejectionReasonText = '';

    // Details Dialog
    detailsDialogVisible = false;
    selectedRequestDetails: ReplacementRequestItem | null = null;

    constructor(
        private certificateService: CertificateRequestService,
        private messageService: MessageService,
        private authService: AuthService,
        private router: Router
    ) {}

    ngOnInit(): void {
        const role = (this.authService.getUserRole() || '').toLowerCase();
        this.isAdmin = role === 'admin';
        this.isCompanyUser = role === 'company';

        this.loadRequests();
        this.loadEligibleCertificates();
    }

    loadRequests(): void {
        this.isLoading = true;
        this.certificateService.getReplacementRequests().subscribe({
            next: (data) => {
                this.requests = data || [];
                this.applyFilter();
                this.isLoading = false;
            },
            error: () => {
                this.requests = [];
                this.filteredRequests = [];
                this.isLoading = false;
            }
        });
    }

    loadEligibleCertificates(): void {
        this.certificateService.getEligibleCertificatesForReplacement().subscribe({
            next: (data) => {
                this.eligibleCertificates = data || [];
            }
        });
    }

    applyFilter(): void {
        let list = [...this.requests];

        if (this.selectedStatusFilter !== 'ALL') {
            const targetStatus = Number(this.selectedStatusFilter);
            list = list.filter((r) => {
                const s = typeof r.status === 'number' ? r.status : (r.status === 'Approved' ? 1 : (r.status === 'Rejected' ? 2 : 0));
                return s === targetStatus;
            });
        }

        if (this.searchTerm && this.searchTerm.trim()) {
            const term = this.searchTerm.trim().toLowerCase();
            list = list.filter(
                (r) =>
                    (r.originalReferenceNumber && r.originalReferenceNumber.toLowerCase().includes(term)) ||
                    (r.replacementReferenceNumber && r.replacementReferenceNumber.toLowerCase().includes(term)) ||
                    (r.companyName && r.companyName.toLowerCase().includes(term)) ||
                    (r.country && r.country.toLowerCase().includes(term)) ||
                    (r.reason && r.reason.toLowerCase().includes(term))
            );
        }

        this.filteredRequests = list;
    }

    onSearch(): void {
        this.applyFilter();
    }

    onStatusFilterChange(): void {
        this.applyFilter();
    }

    openCreateDialog(): void {
        this.newRequest = {
            selectedCert: null,
            manualRefNumber: '',
            reasonType: 'Vessel / Voyage Details Changed',
            reasonDetails: '',
            remarks: ''
        };
        this.createDialogVisible = true;
    }

    submitNewRequest(): void {
        const originalRef = this.newRequest.selectedCert?.referenceNumber || this.newRequest.manualRefNumber.trim();
        if (!originalRef) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Required Field',
                detail: 'Please select an existing certificate or enter its reference number.'
            });
            return;
        }

        const reason = this.newRequest.reasonDetails.trim()
            ? `${this.newRequest.reasonType}: ${this.newRequest.reasonDetails.trim()}`
            : this.newRequest.reasonType;

        this.isSubmitting = true;
        this.certificateService
            .createReplacementRequest({
                originalCertificateRequestId: this.newRequest.selectedCert?.id,
                originalReferenceNumber: originalRef,
                reason,
                remarks: this.newRequest.remarks.trim()
            })
            .subscribe({
                next: () => {
                    this.isSubmitting = false;
                    this.createDialogVisible = false;
                    this.messageService.add({
                        severity: 'success',
                        summary: 'Request Submitted',
                        detail: `Replacement request for ${originalRef} created successfully.`
                    });
                    this.loadRequests();
                },
                error: (err) => {
                    this.isSubmitting = false;
                    this.messageService.add({
                        severity: 'error',
                        summary: 'Submission Failed',
                        detail: err?.error?.message || 'Failed to submit replacement request.'
                    });
                }
            });
    }

    openApproveDialog(req: ReplacementRequestItem): void {
        this.selectedRequestForApprove = req;
        this.approveDialogVisible = true;
    }

    confirmApprove(): void {
        if (!this.selectedRequestForApprove) return;
        this.isApproving = true;
        this.certificateService.approveReplacementRequest(this.selectedRequestForApprove.id).subscribe({
            next: (res) => {
                this.isApproving = false;
                this.approveDialogVisible = false;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Approved & Issued',
                    detail: `Replacement reference ${res.item?.replacementReferenceNumber || ''} generated successfully.`
                });
                this.loadRequests();
            },
            error: (err) => {
                this.isApproving = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Approval Failed',
                    detail: err?.error?.message || 'Failed to approve replacement request.'
                });
            }
        });
    }

    openRejectDialog(req: ReplacementRequestItem): void {
        this.selectedRequestForReject = req;
        this.rejectionReasonText = '';
        this.rejectDialogVisible = true;
    }

    confirmReject(): void {
        if (!this.selectedRequestForReject) return;
        if (!this.rejectionReasonText.trim()) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Reason Required',
                detail: 'Please specify the rejection reason.'
            });
            return;
        }

        this.isRejecting = true;
        this.certificateService
            .rejectReplacementRequest(this.selectedRequestForReject.id, this.rejectionReasonText.trim())
            .subscribe({
                next: () => {
                    this.isRejecting = false;
                    this.rejectDialogVisible = false;
                    this.messageService.add({
                        severity: 'info',
                        summary: 'Request Rejected',
                        detail: 'Replacement request has been rejected.'
                    });
                    this.loadRequests();
                },
                error: (err) => {
                    this.isRejecting = false;
                    this.messageService.add({
                        severity: 'error',
                        summary: 'Rejection Failed',
                        detail: err?.error?.message || 'Failed to reject replacement request.'
                    });
                }
            });
    }

    viewDetails(req: ReplacementRequestItem): void {
        this.selectedRequestDetails = req;
        this.detailsDialogVisible = true;
    }

    viewOriginalCertificate(req: ReplacementRequestItem): void {
        if (req.originalCertificateRequestId) {
            this.router.navigate(['/uikit/admin/certificate-requests/view'], {
                queryParams: {
                    requestId: req.originalCertificateRequestId,
                    ref: req.originalReferenceNumber
                }
            });
        }
    }

    openReplacementCertificate(req: ReplacementRequestItem): void {
        if (!req.replacementReferenceNumber) return;

        this.certificateService.getRequests().subscribe((requests) => {
            const match = requests.find((r) => r.referenceNumber === req.replacementReferenceNumber);
            const targetRequestId = match ? match.id : (req.originalCertificateRequestId || 0);

            const qParams: any = {
                requestId: targetRequestId,
                ref: req.replacementReferenceNumber,
                adminEdit: 'true'
            };
            if (req.originalReferenceNumber) {
                qParams['cancelsAndReplacesRef'] = req.originalReferenceNumber;
            }
            if (req.createdAt) {
                qParams['cancelsAndReplacesDate'] = req.createdAt;
            }

            if (req.certificateType === 'EU' || req.country === 'European Union') {
                qParams['type'] = 'EU';
                this.router.navigate(['/uikit/certificate'], { queryParams: qParams });
                return;
            }

            if (req.country) {
                const certPath = getCertificatePath(req.country);
                if (certPath) {
                    this.router.navigate([certPath], { queryParams: qParams });
                    return;
                }
            }

            this.router.navigate(['/uikit/certificate'], { queryParams: qParams });
        });
    }

    getStatusSeverity(status: number | string): 'warn' | 'success' | 'danger' | 'info' {
        const s = typeof status === 'number' ? status : (status === 'Approved' ? 1 : (status === 'Rejected' ? 2 : 0));
        if (s === 1) return 'success';
        if (s === 2) return 'danger';
        return 'warn';
    }

    getStatusLabel(status: number | string): string {
        const s = typeof status === 'number' ? status : (status === 'Approved' ? 1 : (status === 'Rejected' ? 2 : 0));
        if (s === 1) return 'Approved';
        if (s === 2) return 'Rejected';
        return 'Pending';
    }
}


