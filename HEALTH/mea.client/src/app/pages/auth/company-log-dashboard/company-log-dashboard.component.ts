import { DatePipe, NgFor, NgIf, SlicePipe } from '@angular/common';
import { Component, OnInit, computed, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';
import { TagModule } from 'primeng/tag';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { forkJoin, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { AuthService } from '../../service/auth.service';
import { CertificateRequestResponse, CertificateRequestService, CountryDto } from '../../service/certificate-request.service';

interface DashboardMetric {
    label: string;
    value: string;
    detail: string;
    accent: 'blue' | 'teal' | 'amber' | 'rose' | 'indigo' | 'green';
}

interface QuickAction {
    title: string;
    description: string;
    route: string;
    icon: string;
}

interface CompanyRecentRequest {
    id: number;
    referenceNumber: string;
    type: string;
    country: string;
    status: 'Pending' | 'Confirmed' | 'Rejected';
    createdAt: string;
}

@Component({
    selector: 'app-company-log-dashboard',
    standalone: true,
    imports: [NgIf, NgFor, DatePipe, SlicePipe, RouterLink, ButtonModule, CardModule, TagModule, ToastModule],
    providers: [MessageService],
    templateUrl: './company-log-dashboard.component.html',
    styleUrls: ['./company-log-dashboard.component.css']
})
export class CompanyLogDashboardComponent implements OnInit {
    private readonly authService = inject(AuthService);
    private readonly certificateRequestService = inject(CertificateRequestService);
    private readonly messageService = inject(MessageService);

    readonly userName = computed(() => this.authService.getUserName() || 'Company Team');

    loading = true;
    error = '';
    lastUpdated: Date | null = null;

    metrics: DashboardMetric[] = [];
    quickActions: QuickAction[] = [
        {
            title: 'Request a new certificate',
            description: 'Start a fresh certificate request for your company.',
            route: '/uikit/company-request',
            icon: 'pi pi-plus-circle'
        },
        {
            title: 'Review request history',
            description: 'Track all your submitted requests and statuses.',
            route: '/company-request-history',
            icon: 'pi pi-history'
        }
    ];

    recentRequests: CompanyRecentRequest[] = [];

    ngOnInit(): void {
        this.loadDashboard();
    }

    private loadDashboard(): void {
        this.loading = true;
        this.error = '';

        forkJoin({
            countries: this.certificateRequestService.getCountries().pipe(catchError(() => of([] as CountryDto[]))),
            requests: this.certificateRequestService.getMyRequests().pipe(catchError(() => of([] as CertificateRequestResponse[])))
        }).subscribe({
            next: ({ countries, requests }) => {
                const countryNameById = new Map<number, string>(countries.map((country) => [country.id, country.name]));
                const normalizedRequests = requests.map((request) => this.toRecentRequest(request, countryNameById));

                const totalCount = normalizedRequests.length;
                const pendingCount = normalizedRequests.filter((request) => request.status === 'Pending').length;
                const confirmedCount = normalizedRequests.filter((request) => request.status === 'Confirmed').length;
                const rejectedCount = normalizedRequests.filter((request) => request.status === 'Rejected').length;
                const completionRate = totalCount > 0 ? Math.round((confirmedCount / totalCount) * 100) : 0;

                this.metrics = [
                    {
                        label: 'Total Requests',
                        value: this.formatCount(totalCount),
                        detail: 'All requests submitted by your company',
                        accent: 'blue'
                    },
                    {
                        label: 'Confirmed',
                        value: this.formatCount(confirmedCount),
                        detail: `${completionRate}% success rate`,
                        accent: 'green'
                    },
                    {
                        label: 'Pending',
                        value: this.formatCount(pendingCount),
                        detail: pendingCount > 0 ? 'Awaiting review from the admin team' : 'No requests waiting now',
                        accent: 'amber'
                    },
                    {
                        label: 'Rejected',
                        value: this.formatCount(rejectedCount),
                        detail: rejectedCount > 0 ? 'Review details and re-submit if needed' : 'No rejected requests',
                        accent: 'teal'
                    }
                ];

                this.recentRequests = normalizedRequests.sort((left, right) => new Date(right.createdAt).getTime() - new Date(left.createdAt).getTime()).slice(0, 8);
                this.lastUpdated = new Date();
                this.loading = false;

                if (!requests.length) {
                    this.error = 'No certificate requests available yet for this company.';
                }
            },
            error: () => {
                this.error = 'Failed to load company dashboard details.';
                this.showErrorToast(this.error);
                this.loading = false;
            }
        });
    }

    private toRecentRequest(request: CertificateRequestResponse, countryNameById: Map<number, string>): CompanyRecentRequest {
        const type = this.normalizeType(request.certificateType);
        return {
            id: request.id,
            referenceNumber: request.referenceNumber,
            type: type,
            country: request.countryName ?? (type === 'EU' ? 'European Union' : (request.countryId == null ? 'N/A' : countryNameById.get(request.countryId) ?? 'N/A')),
            status: this.normalizeStatus(request.status),
            createdAt: request.createdAt
        };
    }

    private normalizeType(value: string | number): string {
        return value === 0 || String(value).toLowerCase() === 'eu' ? 'EU' : 'Non-EU';
    }

    private normalizeStatus(value: string | number): 'Pending' | 'Confirmed' | 'Rejected' {
        if (value === 0 || String(value).toLowerCase() === 'pending') {
            return 'Pending';
        }

        if (value === 1 || String(value).toLowerCase() === 'confirmed') {
            return 'Confirmed';
        }

        return 'Rejected';
    }

    private formatCount(value: number): string {
        return new Intl.NumberFormat('en-US').format(value);
    }

    getMetricIcon(accent: DashboardMetric['accent']): string {
        if (accent === 'blue') {
            return 'pi pi-inbox';
        }

        if (accent === 'green') {
            return 'pi pi-check-circle';
        }

        if (accent === 'teal') {
            return 'pi pi-check-circle';
        }

        if (accent === 'amber') {
            return 'pi pi-clock';
        }

        if (accent === 'rose') {
            return 'pi pi-times-circle';
        }

        return 'pi pi-chart-line';
    }

    getStatusSeverity(status: CompanyRecentRequest['status']): 'success' | 'warn' | 'danger' {
        if (status === 'Confirmed') {
            return 'success';
        }

        if (status === 'Pending') {
            return 'warn';
        }

        return 'danger';
    }

    private showErrorToast(detail: string): void {
        this.messageService.add({
            severity: 'error',
            summary: 'Dashboard error',
            detail,
            life: 4000
        });
    }
}
