import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { TabsModule } from 'primeng/tabs';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateRequestService } from '@/pages/service/certificate-request.service';

import {
    CertificateTemplateOption,
    getCertificateTemplates,
    hasMultipleTemplates,
    getCertificatePath
} from '@/shared/country-certificate-templates';

interface CertificatePreviewTab {
    value: string;
    title: string;
    src: SafeResourceUrl | null;
    emptyMessage: string;
    path?: string;
    badge?: string;
}

@Component({
    selector: 'app-certificate-requests-view',
    standalone: true,
    imports: [CommonModule, ButtonModule, TabsModule, ToastModule],
    providers: [MessageService],
    templateUrl: './certificate-requests-view.component.html',
    styleUrls: ['./certificate-requests-view.component.css']
})
export class CertificateRequestsViewComponent implements OnInit {
    requestId: number | null = null;
    requestRef = 'N/A';
    requestType = 'N/A';
    requestCountry: string | null = null;
    activeTab: string = 'vet';
    countryPreviewTabs: CertificatePreviewTab[] = [];
    vetPreviewTab: CertificatePreviewTab | null = null;
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    isProcessing = false;

    onTabChange(val: string | number | undefined): void {
        if (val != null) {
            this.activeTab = String(val);
        }
    }

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private sanitizer: DomSanitizer,
        private authService: AuthService,
        private certificateRequestService: CertificateRequestService,
        private messageService: MessageService
    ) {}

    ngOnInit(): void {
        this.route.queryParams.subscribe((params) => {
            this.requestId = params['requestId'] ? Number(params['requestId']) : null;
            this.requestRef = params['ref'] || 'N/A';
            this.requestType = params['type'] || 'N/A';
            this.requestCountry = params['country'] || null;
            if (params['cancelsAndReplacesRef']) this.cancelsAndReplacesRef = params['cancelsAndReplacesRef'];
            if (params['cancelsAndReplacesDate']) this.cancelsAndReplacesDate = params['cancelsAndReplacesDate'];

            if (this.requestId) {
                this.certificateRequestService.getRequestById(this.requestId).subscribe({
                    next: (req) => {
                        if (req) {
                            if (req.referenceNumber) this.requestRef = req.referenceNumber;
                            if (req.certificateType) this.requestType = req.certificateType;
                            if (req.country) this.requestCountry = req.country;
                            if (req.cancelsAndReplacesRef) this.cancelsAndReplacesRef = req.cancelsAndReplacesRef;
                            if (req.cancelsAndReplacesDate) this.cancelsAndReplacesDate = req.cancelsAndReplacesDate;
                        }
                        this.setupPreviewTabs();
                    },
                    error: () => {
                        this.setupPreviewTabs();
                    }
                });
            } else {
                this.setupPreviewTabs();
            }
        });
    }

    private setupPreviewTabs(): void {
        if (this.isAdmin) {
            this.countryPreviewTabs = this.buildCountryPreviewTabs();
            this.vetPreviewTab = this.buildVetPreviewTab();
            if (this.countryPreviewTabs.length > 0 && this.countryPreviewTabs[0].src) {
                this.activeTab = this.countryPreviewTabs[0].value;
            } else {
                this.activeTab = 'vet';
            }
        } else {
            this.countryPreviewTabs = [];
            this.vetPreviewTab = this.buildVetPreviewTab();
            this.activeTab = 'vet';
        }
    }

    goBack(): void {
        const role = (this.authService.getUserRole() || '').toLowerCase();
        if (role === 'company') {
            this.router.navigate(['/uikit/company-request']);
        } else {
            this.router.navigate(['/uikit/admin/certificate-requests']);
        }
    }

    onConfirmRequest(): void {
        if (!this.isAdmin || !this.requestId || this.isProcessing) return;

        this.isProcessing = true;
        this.certificateRequestService.confirmRequest(this.requestId).subscribe({
            next: () => {
                this.isProcessing = false;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Confirmed',
                    detail: `Request ${this.requestRef} confirmed successfully.`
                });
                setTimeout(() => {
                    this.goBack();
                }, 1200);
            },
            error: () => {
                this.isProcessing = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: `Failed to confirm ${this.requestRef}.`
                });
            }
        });
    }

    onRejectRequest(): void {
        if (!this.isAdmin || !this.requestId || this.isProcessing) return;

        this.isProcessing = true;
        this.certificateRequestService.rejectRequest(this.requestId).subscribe({
            next: () => {
                this.isProcessing = false;
                this.messageService.add({
                    severity: 'warn',
                    summary: 'Rejected',
                    detail: `Request ${this.requestRef} has been rejected.`
                });
                setTimeout(() => {
                    this.goBack();
                }, 1200);
            },
            error: () => {
                this.isProcessing = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: `Failed to reject ${this.requestRef}.`
                });
            }
        });
    }

    onEditCurrentForm(): void {
        if (!this.isAdmin || !this.requestId) return;

        if (this.activeTab === 'vet') {
            this.router.navigate(['/uikit/certificate'], {
                queryParams: {
                    requestId: this.requestId,
                    ref: this.requestRef,
                    type: this.requestType,
                    adminEdit: 'true'
                }
            });
            return;
        }

        const currentTab = this.countryPreviewTabs.find((t) => t.value === this.activeTab);
        if (currentTab && currentTab.path) {
            const queryParams: Record<string, string | number> = {
                requestId: this.requestId,
                ref: this.requestRef,
                adminEdit: 'true'
            };
            if (this.cancelsAndReplacesRef) queryParams['cancelsAndReplacesRef'] = this.cancelsAndReplacesRef;
            if (this.cancelsAndReplacesDate) queryParams['cancelsAndReplacesDate'] = typeof this.cancelsAndReplacesDate === 'string' ? this.cancelsAndReplacesDate : (this.cancelsAndReplacesDate as Date).toISOString();
            this.router.navigate([currentTab.path], { queryParams });
            return;
        }

        if (this.requestCountry) {
            const templates = getCertificateTemplates(this.requestCountry, this.requestType);
            if (templates.length > 0) {
                const queryParams: Record<string, string | number> = {
                    requestId: this.requestId,
                    ref: this.requestRef,
                    adminEdit: 'true'
                };
                if (this.cancelsAndReplacesRef) queryParams['cancelsAndReplacesRef'] = this.cancelsAndReplacesRef;
                if (this.cancelsAndReplacesDate) queryParams['cancelsAndReplacesDate'] = typeof this.cancelsAndReplacesDate === 'string' ? this.cancelsAndReplacesDate : (this.cancelsAndReplacesDate as Date).toISOString();
                this.router.navigate([templates[0].path], { queryParams });
                return;
            }
        }

        // Fallback to Vet Certificate
        this.router.navigate(['/uikit/certificate'], {
            queryParams: {
                requestId: this.requestId,
                ref: this.requestRef,
                type: this.requestType,
                adminEdit: 'true'
            }
        });
    }

    onReplacementRequest(): void {
        if (!this.isAdmin || !this.requestId || this.isProcessing) return;

        if (!confirm(`Are you sure you want to create a Replacement Certificate for ${this.requestRef}? This will issue a new reference number and clone all certificate details.`)) {
            return;
        }

        this.isProcessing = true;
        this.certificateRequestService.createReplacement(this.requestId).subscribe({
            next: (res) => {
                this.isProcessing = false;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Replacement Created',
                    detail: `New replacement certificate ${res.newReferenceNumber} created successfully.`
                });

                if (res.certificateType === 'EU' && (!res.countryName || res.countryName.toLowerCase() === 'european union')) {
                    this.router.navigate(['/uikit/certificate'], {
                        queryParams: {
                            requestId: res.newRequestId,
                            ref: res.newReferenceNumber,
                            type: 'EU',
                            adminEdit: 'true'
                        }
                    });
                    return;
                }

                if (res.countryName) {
                    const templates = getCertificateTemplates(res.countryName, res.certificateType);
                    if (templates.length > 0) {
                        this.router.navigate([templates[0].path], {
                            queryParams: {
                                requestId: res.newRequestId,
                                ref: res.newReferenceNumber,
                                adminEdit: 'true'
                            }
                        });
                        return;
                    }
                }

                this.router.navigate(['/uikit/certificate'], {
                    queryParams: {
                        requestId: res.newRequestId,
                        ref: res.newReferenceNumber,
                        adminEdit: 'true'
                    }
                });
            },
            error: (err) => {
                this.isProcessing = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: err?.error?.message || `Failed to create replacement for ${this.requestRef}.`
                });
            }
        });
    }

    private buildCountryPreviewTabs(): CertificatePreviewTab[] {
        if (!this.requestCountry || !this.requestId) {
            return [];
        }

        const templates = getCertificateTemplates(this.requestCountry, this.requestType);
        return templates.map((tmpl) => ({
            value: tmpl.id,
            title: tmpl.label,
            path: tmpl.path,
            badge: tmpl.badge,
            src: this.createSafePreviewUrl(tmpl.path, {
                requestId: String(this.requestId),
                ref: this.requestRef,
                type: this.requestType,
                country: this.requestCountry || ''
            }),
            emptyMessage: `The certificate form for ${tmpl.label} is not available yet.`
        }));
    }

    private buildVetPreviewTab(): CertificatePreviewTab {
        return {
            value: 'vet',
            title: 'Vet Certificate',
            src: this.getVetCertificateUrl(),
            emptyMessage: 'No vet certificate is linked to this request.'
        };
    }


    private getVetCertificateUrl(): SafeResourceUrl | null {
        if (!this.requestId) {
            return null;
        }

        return this.createSafePreviewUrl('/uikit/certificate', {
            requestId: String(this.requestId),
            ref: this.requestRef,
            type: this.requestType
        });
    }

    private createSafePreviewUrl(path: string, extraQueryParams: Record<string, string> = {}): SafeResourceUrl {
        const queryParams = new URLSearchParams({
            requestId: String(this.requestId ?? ''),
            viewOnly: 'true',
            embedded: 'true',
            ...extraQueryParams
        });
        if (this.cancelsAndReplacesRef) {
            queryParams.set('cancelsAndReplacesRef', this.cancelsAndReplacesRef);
        }
        if (this.cancelsAndReplacesDate) {
            queryParams.set('cancelsAndReplacesDate', typeof this.cancelsAndReplacesDate === 'string' ? this.cancelsAndReplacesDate : (this.cancelsAndReplacesDate as Date).toISOString());
        }

        return this.sanitizer.bypassSecurityTrustResourceUrl(`${path}?${queryParams.toString()}`);
    }
}