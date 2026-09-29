import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RadioButton } from 'primeng/radiobutton';
import { Select } from 'primeng/select';
import { TableModule } from 'primeng/table';
import { Card } from 'primeng/card';
import { Tag } from 'primeng/tag';
import { CertificateRequestService, CertificateRequestResponse } from '@/pages/service/certificate-request.service';
import { CompanyService } from '@/pages/service/company.service';
import { CountryService } from '@/pages/service/country.service';
import { Router } from '@angular/router';
import { Button } from 'primeng/button';
import { catchError, forkJoin, map, Observable, of } from 'rxjs';

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

    const matchKey = Object.keys(COUNTRY_CERTIFICATE_MAP).find(
        (k) => k.toLowerCase().trim() === normalized
    );
    return matchKey ? COUNTRY_CERTIFICATE_MAP[matchKey] : null;
}

@Component({
    selector: 'app-report',
    standalone: true,
    imports: [CommonModule, FormsModule, RadioButton, Select, TableModule, Card, Tag, Button],
    providers: [CountryService],
    templateUrl: './report.component.html',
    styleUrls: ['./report.component.css']
})
export class ReportComponent implements OnInit {
    reportType: 'country' | 'company' = 'country';

    countries: any[] = [];
    companies: any[] = [];

    selectedCountry: any = null;
    selectedCompany: any = null;

    allRequests: CertificateRequestResponse[] = [];
    filteredRequests: CertificateRequestResponse[] = [];
    submissionStatusByRequestId = new Map<number, boolean>();

    loading = false;

    constructor(
        private certificateService: CertificateRequestService,
        private companyService: CompanyService,
        private countryService: CountryService,
        private router: Router
    ) {}

    ngOnInit(): void {
        console.log('ReportComponent Initialized');
        this.loadInitialData();
    }

    private loadInitialData() {
        this.loading = true;

        // Load companies (works for both)
        this.companyService.getCompanies().subscribe({
            next: (res: any) => {
                const list = Array.isArray(res) ? res : (res?.companies ?? res?.Companies ?? []);
                this.companies = list.map((c: any) => ({
                    id: c.id ?? c.Id ?? 0,
                    name: c.companyName ?? c.CompanyName ?? ''
                }));
            },
            error: (err) => console.error('Error loading companies:', err)
        });

        // Load all certificate requests
        this.certificateService.getRequests().subscribe({
            next: (data) => {
                this.allRequests = data || [];
                this.hydrateSubmissionStatus();

                // Extract unique country names from the requests
                const uniqueNames = new Set(this.allRequests.map((r) => r.countryName).filter(Boolean));

                // Load and filter the country list
                this.countryService
                    .getCountries()
                    .then((allCountries) => {
                        if (uniqueNames.size > 0) {
                            this.countries = allCountries.filter((c) => uniqueNames.has(c.name));
                            uniqueNames.forEach((name) => {
                                if (name && !this.countries.some((c) => c.name === name)) {
                                    this.countries.push({ id: 0, name: name });
                                }
                            });
                        } else {
                            this.countries = [];
                        }
                        this.filterReports();
                        this.loading = false;
                    })
                    .catch((err) => {
                        console.error('Error with country service:', err);
                        this.countries = Array.from(uniqueNames).map((name) => ({ id: 0, name }));
                        this.filterReports();
                        this.loading = false;
                    });
            },
            error: (err) => {
                console.error('Error loading requests:', err);
                this.loading = false;
            }
        });
    }

    onTypeChange() {
        this.selectedCountry = null;
        this.selectedCompany = null;
        this.filterReports();
    }

    filterReports() {
        if (!this.allRequests || this.allRequests.length === 0) {
            this.filteredRequests = [];
            return;
        }

        if (this.reportType === 'country') {
            if (this.selectedCountry) {
                this.filteredRequests = this.allRequests.filter(
                    (r) => (r.countryId != null && r.countryId == this.selectedCountry.id) || (r.countryName && this.selectedCountry.name && r.countryName.toLowerCase().trim() === this.selectedCountry.name.toLowerCase().trim())
                );
            } else {
                this.filteredRequests = [];
            }
        } else {
            if (this.selectedCompany) {
                this.filteredRequests = this.allRequests.filter((r) => r.companyName && this.selectedCompany.name && r.companyName.toLowerCase().trim() === this.selectedCompany.name.toLowerCase().trim());
            } else {
                this.filteredRequests = [];
            }
        }
    }

    private hydrateSubmissionStatus() {
        const confirmedRequests = this.allRequests.filter((request) => this.isBackendConfirmed(request.status));
        if (confirmedRequests.length === 0) {
            return;
        }

        const checks = confirmedRequests.map((request) => this.hasSubmittedCertificate(request).pipe(map((submitted) => ({ requestId: request.id, submitted }))));

        forkJoin(checks).subscribe({
            next: (results) => {
                this.submissionStatusByRequestId = new Map<number, boolean>(results.map((result) => [result.requestId, result.submitted]));
            }
        });
    }

    private hasSubmittedCertificate(request: CertificateRequestResponse): Observable<boolean> {
        if (request.certificateType === 'EU' || request.certificateType === 0) {
            return this.toExistsResult(this.certificateService.getVetFormByRequestId(request.id));
        }

        if (!request.countryName) {
            return of(false);
        }

        switch (request.countryName.trim().toLowerCase()) {
            case 'australia':
                return this.toExistsResult(this.certificateService.getAuCertificateByRequestId(request.id));
            case 'armenia':
                return this.toExistsResult(this.certificateService.getAmCertificateByRequestId(request.id));
            case 'brazil':
                return this.toExistsResult(this.certificateService.getBrCertificateByRequestId(request.id));
            case 'china':
                return this.toExistsResult(this.certificateService.getChCertificateByRequestId(request.id));
            case 'hong kong':
                return this.toExistsResult(this.certificateService.getHkCertificateByRequestId(request.id));
            case 'india':
                return this.toExistsResult(this.certificateService.getIndCertificateByRequestId(request.id));
            case 'indonesia':
                return this.toExistsResult(this.certificateService.getIdCertificateByRequestId(request.id));
            case 'japan':
                return this.toExistsResult(this.certificateService.getJpCertificateByRequestId(request.id));
            case 'kuwait':
                return this.toExistsResult(this.certificateService.getKwCertificateByRequestId(request.id));
            case 'malaysia':
                return this.toExistsResult(this.certificateService.getMyCertificateByRequestId(request.id));
            case 'maldives':
                return this.toExistsResult(this.certificateService.getMvCertificateByRequestId(request.id));
            case 'new zealand':
                return this.toExistsResult(this.certificateService.getNzCertificateByRequestId(request.id));
            case 'russia':
                return this.toExistsResult(this.certificateService.getRuCertificateByRequestId(request.id));
            case 'taiwan':
                return this.toExistsResult(this.certificateService.getTwCertificateByRequestId(request.id));
            case 'ukraine':
                return this.toExistsResult(this.certificateService.getUaCertificateByRequestId(request.id));
            case 'uk':
            case 'united kingdom':
            case 'great britain':
                return this.toExistsResult(this.certificateService.getUkCertificateByRequestId(request.id));
            case 'usa':
            case 'united states of america':
            case 'united states':
                return this.toExistsResult(this.certificateService.getUsaCertificateByRequestId(request.id));
            case 'israel':
                return this.toExistsResult(this.certificateService.getIlCertificateByRequestId(request.id));
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

    private isBackendConfirmed(status: string | number): boolean {
        if (typeof status === 'number') {
            return status === 1;
        }
        return status.toLowerCase() === 'confirmed';
    }

    getEffectiveStatus(request: CertificateRequestResponse): string | number {
        if (this.isBackendConfirmed(request.status) && this.submissionStatusByRequestId.get(request.id) === false) {
            return 'Pending';
        }

        return request.status;
    }

    getStatusSeverity(status: string | number): 'success' | 'secondary' | 'info' | 'warn' | 'danger' | 'contrast' | undefined {
        const s = typeof status === 'string' ? status.toLowerCase() : status;
        switch (s) {
            case 'confirmed':
            case 1:
                return 'success';
            case 'pending':
            case 0:
                return 'warn';
            case 'rejected':
            case 2:
                return 'danger';
            default:
                return 'info';
        }
    }

    formatStatus(status: string | number): string {
        if (typeof status === 'number') {
            switch (status) {
                case 0:
                    return 'Pending';
                case 1:
                    return 'Confirmed';
                case 2:
                    return 'Rejected';
                default:
                    return 'Unknown';
            }
        }
        return status;
    }

    formatCertificateType(value: string | number | null | undefined): string {
        if (value === 0 || String(value).toLowerCase() === 'eu') {
            return 'EU';
        }

        if (value === 1 || String(value).toLowerCase() === 'noneu' || String(value).toLowerCase() === 'non-eu' || String(value).toLowerCase() === 'non_eu') {
            return 'Non-EU';
        }

        if (value == null || value === '') return 'N/A';
        return String(value);
    }

    isRequestConfirmed(request: CertificateRequestResponse): boolean {
        const status = this.getEffectiveStatus(request);
        if (typeof status === 'number') {
            return status === 1;
        }
        if (typeof status === 'string') {
            return status.toLowerCase() === 'confirmed';
        }
        return false;
    }

    viewRequest(request: CertificateRequestResponse) {
        if (!this.isRequestConfirmed(request)) {
            return;
        }
        const type = request.certificateType;
        const isEU = type === 'EU' || type === 0;
        const isNonEU = type === 'NonEU' || type === 1;

        if (isEU) {
            this.router.navigate(['/uikit/certificate'], {
                queryParams: {
                    requestId: request.id,
                    ref: request.referenceNumber,
                    type: 'EU',
                    viewOnly: true
                }
            });
            return;
        }

        if (isNonEU && request.countryName) {
            const certificatePath = getCertificatePath(request.countryName);
            if (certificatePath) {
                this.router.navigate([certificatePath], {
                    queryParams: {
                        requestId: request.id,
                        ref: request.referenceNumber,
                        viewOnly: true
                    }
                });
            }
        }
    }
}
