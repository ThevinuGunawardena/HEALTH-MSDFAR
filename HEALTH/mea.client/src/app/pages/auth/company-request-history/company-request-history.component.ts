import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TableModule } from 'primeng/table';
import { InputTextModule } from 'primeng/inputtext';
import { Card } from 'primeng/card';
import { Tag } from 'primeng/tag';
import { ButtonModule } from 'primeng/button';
import { CertificateRequestResponse, CertificateRequestService, CountryDto } from '../../service/certificate-request.service';
import { Router } from '@angular/router';

interface CompanyRequestRow {
    id: number;
    referenceNumber: string;
    certificateType: string;
    countryName: string;
    createdAt: string;
    status: string | number;
}

@Component({
    selector: 'app-company-request-history',
    standalone: true,
    imports: [CommonModule, FormsModule, InputTextModule, TableModule, Card, Tag, ButtonModule],
    templateUrl: './company-request-history.component.html',
    styleUrls: ['./company-request-history.component.css']
})
export class CompanyRequestHistoryComponent implements OnInit {
    loading = false;
    requests: CompanyRequestRow[] = [];
    filteredRequests: CompanyRequestRow[] = [];
    searchTerm = '';

    constructor(private certificateRequestService: CertificateRequestService, private router: Router) {}

    ngOnInit(): void {
        this.loadRequests();
    }

    private loadRequests() {
        this.loading = true;

        this.certificateRequestService.getCountries().subscribe({
            next: (countries) => {
                const countryNameById = new Map<number, string>(countries.map((country: CountryDto) => [country.id, country.name]));

                this.certificateRequestService.getMyRequests().subscribe({
                    next: (rows) => {
                        this.requests = (rows ?? []).map((requests: CertificateRequestResponse) => ({
                            id: requests.id,
                            referenceNumber: requests.referenceNumber,
                            certificateType: this.formatType(requests.certificateType),
                            countryName: this.getCountryName(requests, countryNameById),
                            createdAt: requests.createdAt,
                            status: requests.status
                        }));
                        this.filteredRequests = [...this.requests];
                        this.loading = false;
                    },
                    error: () => {
                        this.requests = [];
                        this.loading = false;
                    }
                });
            },
            error: () => {
                this.requests = [];
                this.loading = false;
            }
        });
    }

    private formatType(value: string | number): string {
        if (value === 0 || String(value).toLowerCase() === 'eu') {
            return 'EU';
        }

        return 'NonEU';
    }

    private getCountryName(request: CertificateRequestResponse, countryNameById: Map<number, string>): string {
        if (request.countryName) {
            return request.countryName;
        }

        if (this.formatType(request.certificateType) === 'EU') {
            return 'European Union';
        }

        if (request.countryId == null) {
            return 'N/A';
        }

        return countryNameById.get(request.countryId) ?? 'N/A';
    }

    formatStatus(status: string | number): string {
        if (typeof status === 'number') {
            switch (status) {
                case 0:
                    return 'pending';
                case 1:
                    return 'confirmed';
                case 2:
                    return 'rejected';
                default:
                    return 'unknown';
            }
        }
        return status;
    }

    getStatusSeverity(status: string | number): 'success' | 'secondary' | 'info' | 'warn' | 'danger' | 'contrast' | undefined {
        const normalized = typeof status === 'string' ? status.toLowerCase() : status;

        switch (normalized) {
            case 'confirmed':
            case 1:
                return 'success';
            case 'pending':
            case 0:
                return 'info';
            case 'rejected':
            case 2:
                return 'danger';
            default:
                return 'secondary';
        }
    }

    onView(request: CompanyRequestRow) {
        this.router.navigate(['/uikit/admin/certificate-requests/view'], {
            queryParams: {
                requestId: request.id,
                ref: request.referenceNumber,
                type: request.certificateType,
                country: request.countryName !== 'N/A' ? request.countryName : null
            }
        });
    }

    applySearch() {
        const filter = this.searchTerm.trim().toLowerCase();

        this.filteredRequests = this.requests.filter((request) => {
            if (!filter) {
                return true;
            }

            return request.referenceNumber.toLowerCase().includes(filter);
        });
    }

    clearSearch() {
        this.searchTerm = '';
        this.filteredRequests = [...this.requests];
    }
}
