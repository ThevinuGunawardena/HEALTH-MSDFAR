import { Component, OnInit, OnDestroy, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { Select } from 'primeng/select';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { forkJoin, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { CertificateRequestResponse, CertificateRequestService, CountryDto } from '../../service/certificate-request.service';

interface CertificateType {
    label: string;
    value: string;
}

interface Country {
    label: string;
    value: number;
}

export interface ActiveFormRequest {
    id: number;
    referenceNumber: string;
    certificateType: string;
    countryId: number | null;
    countryName: string;
    status: string | number;
    createdAt: Date;
    expiresAt: Date;
    isExpired: boolean;
    hasFormSubmitted: boolean;
    remainingTimeText: string;
    isExpiringSoon: boolean;
    slotIndex?: number;
    totalInBatch?: number;
}

export interface FormBatch {
    batchId: string;
    createdAt: Date;
    certificateType: string;
    countryName: string;
    totalForms: number;
    completedForms: number;
    isExpired: boolean;
    expiresAt: Date;
    remainingTimeText: string;
    forms: ActiveFormRequest[];
}

const COUNTRY_FORM_ROUTES: Record<string, string> = {
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
    'Republic of Kazakhstan': '/uikit/world-certificates/kz-certificate',
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

function getCountryCertificateRoute(countryName?: string | null): string | null {
    if (!countryName) return null;
    const normalized = countryName.trim().toLowerCase();

    if (normalized === 'australia') return '/uikit/world-certificates/au-certificate';
    if (normalized === 'usa' || normalized === 'united states' || normalized === 'united states of america') return '/uikit/world-certificates/usa-certificate';
    if (normalized === 'uk' || normalized === 'united kingdom' || normalized === 'great britain') return '/uikit/world-certificates/uk-certificate';
    if (normalized === 'brazil') return '/uikit/world-certificates/br-certificate';
    if (normalized === 'china') return '/uikit/world-certificates/ch-certificate';
    if (normalized === 'armenia') return '/uikit/world-certificates/am-certificate';
    if (normalized === 'hong kong' || normalized === 'hongkong') return '/uikit/world-certificates/hk-certificate';
    if (normalized === 'india') return '/uikit/world-certificates/in-certificate';
    if (normalized === 'indonesia') return '/uikit/world-certificates/id-certificate';
    if (normalized === 'malaysia') return '/uikit/world-certificates/my-certificate';
    if (normalized === 'kuwait') return '/uikit/world-certificates/kw-certificate';
    if (normalized === 'taiwan') return '/uikit/world-certificates/tw-certificate';
    if (normalized === 'ukraine') return '/uikit/world-certificates/ua-certificate';
    if (normalized === 'russia') return '/uikit/world-certificates/ru-certificate';
    if (normalized === 'kazakhstan' || normalized === 'republic of kazakhstan') return '/uikit/world-certificates/kz-certificate';
    if (normalized === 'japan') return '/uikit/world-certificates/jp-certificate';
    if (normalized === 'new zealand' || normalized === 'newzealand') return '/uikit/world-certificates/nz-certificate';
    if (normalized === 'israel') return '/uikit/world-certificates/il-certificate';
    if (normalized === 'maldives') return '/uikit/world-certificates/mv-certificate';
    if (normalized === 'canada') return '/uikit/world-certificates/ca-certificate';
    if (normalized === 'saudi arabia' || normalized === 'saudi') return '/uikit/world-certificates/sa-certificate';
    if (normalized === 'south africa' || normalized === 'southafrica') return '/uikit/world-certificates/za-certificate';

    const matchKey = Object.keys(COUNTRY_FORM_ROUTES).find(
        (k) => k.toLowerCase().trim() === normalized
    );
    return matchKey ? COUNTRY_FORM_ROUTES[matchKey] : null;
}

@Component({
    selector: 'app-company-request',
    standalone: true,
    imports: [CommonModule, FormsModule, ButtonModule, Select, ToastModule],
    providers: [MessageService],
    templateUrl: './company-request.component.html',
    styleUrls: ['./company-request.component.css']
})
export class CompanyRequestComponent implements OnInit, OnDestroy {
    private router = inject(Router);
    private certificateRequestService = inject(CertificateRequestService);
    private messageService = inject(MessageService);

    showDropdown = false;
    selectedCertificateType: string | null = null;
    selectedCountry: number | null = null;
    showPayment = false;
    errorMessage = '';
    isSubmitting = false;
    isLoadingForms = false;

    quantity: number = 1;
    readonly pricePerForm: number = 500;

    activeTab: 'active' | 'all' = 'active';
    allForms: ActiveFormRequest[] = [];
    activeBatches: FormBatch[] = [];

    private timerInterval: any = null;

    certificateTypes: CertificateType[] = [
        { label: 'EU', value: 'EU' },
        { label: 'NonEU', value: 'NonEU' }
    ];

    countries: Country[] = [];

    get totalAmount(): number {
        return this.quantity * this.pricePerForm;
    }

    get activeFormsCount(): number {
        return this.allForms.filter((f) => !f.hasFormSubmitted && !f.isExpired).length;
    }

    get totalFormsCount(): number {
        return this.allForms.length;
    }

    ngOnInit(): void {
        this.loadCountries();
        this.loadActiveRequests();
        this.startTimer();
    }

    ngOnDestroy(): void {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    private loadCountries() {
        this.certificateRequestService.getCountries().subscribe({
            next: (items) => {
                this.countries = (items || [])
                    .sort((a, b) => a.name.localeCompare(b.name))
                    .map((item) => ({
                        label: item.name,
                        value: item.id
                    }));
            }
        });
    }

    loadActiveRequests() {
        this.isLoadingForms = true;

        forkJoin({
            countries: this.certificateRequestService.getCountries().pipe(catchError(() => of([] as CountryDto[]))),
            requests: this.certificateRequestService.getMyRequests().pipe(catchError(() => of([] as CertificateRequestResponse[])))
        }).subscribe({
            next: ({ countries, requests }) => {
                const countryMap = new Map<number, string>(countries.map((c) => [c.id, c.name]));

                const locallySubmittedIds = new Set<number>(
                    JSON.parse(localStorage.getItem('dfar_submitted_requests') || '[]')
                );

                this.allForms = (requests || []).map((req) => {
                    const typeStr = req.certificateType === 0 || String(req.certificateType).toUpperCase() === 'EU' ? 'EU' : 'NonEU';
                    const countryName = req.countryName || (req.countryId != null ? countryMap.get(req.countryId) ?? 'N/A' : 'N/A');
                    const createdAt = this.parseUtcDate(req.createdAt);
                    const expiresAt = this.getMidnightExpiry(createdAt);
                    const hasSubmitted = req.hasFormSubmitted === true || req.status === 1 || req.status === 'Confirmed' || locallySubmittedIds.has(req.id);

                    const formItem: ActiveFormRequest = {
                        id: req.id,
                        referenceNumber: req.referenceNumber,
                        certificateType: typeStr,
                        countryId: req.countryId,
                        countryName,
                        status: req.status,
                        createdAt,
                        expiresAt,
                        isExpired: false,
                        hasFormSubmitted: hasSubmitted,
                        remainingTimeText: '',
                        isExpiringSoon: false
                    };

                    this.calculateCountdown(formItem);
                    return formItem;
                });

                this.allForms.sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime());
                this.groupFormsIntoBatches();
                this.isLoadingForms = false;
            },
            error: () => {
                this.allForms = [];
                this.activeBatches = [];
                this.isLoadingForms = false;
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

    private getMidnightExpiry(createdDate: Date): Date {
        const d = new Date(createdDate);
        return new Date(d.getFullYear(), d.getMonth(), d.getDate() + 1, 0, 0, 0, 0);
    }

    private groupFormsIntoBatches() {
        // Group forms created within 2 minutes of each other as a batch
        const batches: FormBatch[] = [];
        const processedIds = new Set<number>();

        for (let i = 0; i < this.allForms.length; i++) {
            const form = this.allForms[i];
            if (processedIds.has(form.id)) continue;

            const batchForms = this.allForms.filter((f) => {
                if (processedIds.has(f.id)) return false;
                const timeDiff = Math.abs(f.createdAt.getTime() - form.createdAt.getTime());
                return timeDiff < 120000 && f.certificateType === form.certificateType && f.countryName === form.countryName;
            });

            // Sort chronologically within the batch
            batchForms.sort((a, b) => a.id - b.id);

            batchForms.forEach((f, idx) => {
                f.slotIndex = idx + 1;
                f.totalInBatch = batchForms.length;
                processedIds.add(f.id);
            });

            const completedCount = batchForms.filter((f) => f.hasFormSubmitted).length;
            const latestExpiresAt = new Date(Math.max(...batchForms.map((f) => f.expiresAt.getTime())));
            const isAllExpired = batchForms.every((f) => f.isExpired);

            batches.push({
                batchId: `batch-${form.id}-${form.createdAt.getTime()}`,
                createdAt: form.createdAt,
                certificateType: form.certificateType,
                countryName: form.countryName,
                totalForms: batchForms.length,
                completedForms: completedCount,
                isExpired: isAllExpired,
                expiresAt: latestExpiresAt,
                remainingTimeText: batchForms[0]?.remainingTimeText || '',
                forms: batchForms
            });
        }

        this.activeBatches = batches;
    }

    private startTimer() {
        this.timerInterval = setInterval(() => {
            this.updateCountdowns();
        }, 1000);
    }

    private updateCountdowns() {
        if (!this.allForms.length) return;

        this.allForms.forEach((form) => {
            this.calculateCountdown(form);
        });

        this.activeBatches.forEach((batch) => {
            const firstActive = batch.forms.find((f) => !f.hasFormSubmitted && !f.isExpired);
            if (firstActive) {
                batch.remainingTimeText = firstActive.remainingTimeText;
            } else if (batch.completedForms === batch.totalForms) {
                batch.remainingTimeText = 'All Forms Completed';
            } else {
                batch.remainingTimeText = 'Expired';
            }
        });
    }

    private calculateCountdown(item: ActiveFormRequest) {
        if (item.hasFormSubmitted) {
            item.remainingTimeText = 'Submitted & Locked';
            item.isExpired = false;
            item.isExpiringSoon = false;
            return;
        }

        const now = Date.now();
        const expiryTime = item.expiresAt.getTime();
        const diffMs = expiryTime - now;

        if (diffMs <= 0) {
            item.remainingTimeText = 'Expired';
            item.isExpired = true;
            item.isExpiringSoon = false;
        } else {
            item.isExpired = false;
            const totalSecs = Math.floor(diffMs / 1000);
            const hours = Math.floor(totalSecs / 3600);
            const minutes = Math.floor((totalSecs % 3600) / 60);
            const seconds = totalSecs % 60;

            item.remainingTimeText = `${hours}h ${minutes.toString().padStart(2, '0')}m ${seconds.toString().padStart(2, '0')}s remaining`;
            item.isExpiringSoon = hours < 2;
        }
    }

    setTab(tab: 'active' | 'all') {
        this.activeTab = tab;
    }

    get filteredBatches(): FormBatch[] {
        if (this.activeTab === 'active') {
            // Show batches that have at least one unsubmitted form that is not expired
            return this.activeBatches.filter((b) => b.forms.some((f) => !f.hasFormSubmitted && !f.isExpired));
        }
        return this.activeBatches;
    }

    toggleRequestDropdown() {
        this.showDropdown = !this.showDropdown;
        if (this.showDropdown) {
            this.selectedCertificateType = null;
            this.selectedCountry = null;
            this.showPayment = false;
            this.errorMessage = '';
            this.quantity = 1;
        }
    }

    onRequestCertificate() {
        this.showDropdown = true;
        this.selectedCertificateType = null;
        this.selectedCountry = null;
        this.showPayment = false;
        this.errorMessage = '';
        this.quantity = 1;
    }

    onCertificateTypeChange() {
        this.errorMessage = '';
        if (this.selectedCertificateType === 'EU') {
            this.showPayment = true;
            this.selectedCountry = null;
        } else if (this.selectedCertificateType === 'NonEU') {
            this.selectedCountry = null;
            this.showPayment = false;
        }
    }

    onCountryChange() {
        this.errorMessage = '';
        if (this.selectedCountry) {
            this.showPayment = true;
        }
    }

    incrementQuantity() {
        if (this.quantity < 50) {
            this.quantity++;
        }
    }

    decrementQuantity() {
        if (this.quantity > 1) {
            this.quantity--;
        }
    }

    onPay() {
        if (!this.selectedCertificateType) {
            this.errorMessage = 'Please select a certificate type.';
            return;
        }

        if (this.selectedCertificateType === 'NonEU' && !this.selectedCountry) {
            this.errorMessage = 'Please select a country for NonEU requests.';
            return;
        }

        if (this.quantity < 1) {
            this.errorMessage = 'Quantity must be at least 1.';
            return;
        }

        this.isSubmitting = true;
        this.errorMessage = '';

        const requestCount = this.quantity;

        this.certificateRequestService
            .createRequest({
                certificateType: this.selectedCertificateType as 'EU' | 'NonEU',
                countryId: this.selectedCountry,
                quantity: requestCount
            })
            .subscribe({
                next: (createdResponse) => {
                    this.isSubmitting = false;
                    const totalCost = requestCount * this.pricePerForm;

                    this.messageService.add({
                        severity: 'success',
                        summary: 'Forms Generated Successfully',
                        detail: `Purchased ${requestCount} form(s) (Rs. ${totalCost.toLocaleString()}). All ${requestCount} forms are ready below to fill one by one within 12 hours.`,
                        life: 7000
                    });

                    this.showDropdown = false;
                    this.selectedCertificateType = null;
                    this.selectedCountry = null;
                    this.showPayment = false;
                    this.quantity = 1;

                    this.activeTab = 'active';
                    this.loadActiveRequests();
                },
                error: (err) => {
                    this.isSubmitting = false;
                    this.errorMessage = err?.error?.message || 'Failed to create certificate requests. Please try again.';
                    this.messageService.add({
                        severity: 'error',
                        summary: 'Request Failed',
                        detail: this.errorMessage
                    });
                }
            });
    }

    private getFormRoute(type: string, countryName?: string | null): string {
        if (type === 'EU' || !countryName || countryName === 'N/A') {
            return '/uikit/certificate';
        }

        return getCountryCertificateRoute(countryName) || '/uikit/certificate';
    }

    onFillForm(formItem: ActiveFormRequest) {
        if (formItem.hasFormSubmitted) {
            this.messageService.add({
                severity: 'info',
                summary: 'Form Already Submitted',
                detail: `Form ${formItem.referenceNumber} has already been submitted and is locked. Opening in read-only view.`
            });
            this.onViewForm(formItem);
            return;
        }

        if (formItem.isExpired) {
            this.messageService.add({
                severity: 'error',
                summary: 'Form Expired',
                detail: `Form ${formItem.referenceNumber} has passed its midnight validity limit and can no longer be filled.`
            });
            return;
        }

        const route = this.getFormRoute(formItem.certificateType, formItem.countryName);

        this.router.navigate([route], {
            queryParams: {
                requestId: formItem.id,
                ref: formItem.referenceNumber,
                type: formItem.certificateType,
                countryId: formItem.countryId,
                country: formItem.countryName !== 'N/A' ? formItem.countryName : null
            }
        });
    }

    onViewForm(formItem: ActiveFormRequest) {
        this.router.navigate(['/uikit/admin/certificate-requests/view'], {
            queryParams: {
                requestId: formItem.id,
                ref: formItem.referenceNumber,
                type: formItem.certificateType,
                country: formItem.countryName !== 'N/A' ? formItem.countryName : null
            }
        });
    }
}
