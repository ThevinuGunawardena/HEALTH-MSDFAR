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
import { catchError, forkJoin, map, Observable, of, switchMap } from 'rxjs';
import { getCertificatePath } from '@/shared/country-certificate-templates';

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
        // Check if VetCertificateForm exists first (the standard form submitted by companies for both EU & NonEU)
        return this.toExistsResult(this.certificateService.getVetFormByRequestId(request.id)).pipe(
            switchMap((exists) => {
                if (exists) return of(true);

                if (request.certificateType === 'EU' || request.certificateType === 0 || !request.countryName) {
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
                    case 'hongkong':
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
                    case 'newzealand':
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
            })
        );
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
