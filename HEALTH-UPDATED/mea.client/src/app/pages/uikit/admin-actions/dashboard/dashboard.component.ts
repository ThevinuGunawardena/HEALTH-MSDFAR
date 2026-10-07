import { DatePipe, NgFor, NgIf, SlicePipe } from '@angular/common';
import { Component, OnInit, inject, computed } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';
import { MessageService } from 'primeng/api';
import { TagModule } from 'primeng/tag';
import { ToastModule } from 'primeng/toast';
import { forkJoin, of } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateRequestResponse, CertificateRequestService } from '@/pages/service/certificate-request.service';
import { CompanyService } from '@/pages/service/company.service';
import { User, UserService } from '@/pages/service/user.service';

interface DashboardMetric {
    label: string;
    value: string;
    detail: string;
    accent: 'blue' | 'teal' | 'amber' | 'rose';
}

interface QuickAction {
    title: string;
    description: string;
    route: string;
    icon: string;
}

interface RecentRequest {
    id: number;
    company: string;
    referenceNumber: string;
    type: string;
    country: string;
    status: 'Pending' | 'Confirmed' | 'Rejected';
    createdAt: string;
}

interface RegisteredCompany {
    id: number;
    name: string;
    email: string;
    phone: string;
}

@Component({
    selector: 'app-admin-dashboard',
    standalone: true,
    imports: [NgIf, NgFor, DatePipe, SlicePipe, RouterLink, ButtonModule, CardModule, TagModule, ToastModule],
    providers: [MessageService],
    templateUrl: './dashboard.component.html',
    styleUrl: './dashboard.component.css'
})
export class AdminDashboardComponent implements OnInit {
    private readonly authService = inject(AuthService);
    private readonly userService = inject(UserService);
    private readonly companyService = inject(CompanyService);
    private readonly certificateRequestService = inject(CertificateRequestService);
    private readonly messageService = inject(MessageService);

    readonly userRole = this.authService.getUserRole().toLowerCase();
    readonly isAdmin = this.userRole === 'admin';
    readonly userName = computed(() => this.authService.getUserName() || 'Team');

    loading = true;
    error = '';
    lastUpdated: Date | null = null;

    metrics: DashboardMetric[] = [];
    quickActions: QuickAction[] = [];
    recentRequests: RecentRequest[] = [];
    registeredCompanies: RegisteredCompany[] = [];

    ngOnInit(): void {
        this.quickActions = this.buildQuickActions();
        this.loadDashboard();
    }

    private loadDashboard(): void {
        this.loading = true;
        this.error = '';

        forkJoin({
            users: this.userService.getAllUsers().pipe(catchError(() => of([] as User[]))),
            companies: this.companyService.getCompanies().pipe(
                map((response) => this.normalizeCompanies(response)),
                catchError(() => of([] as any[]))
            ),
            requests: this.certificateRequestService.getRequests().pipe(catchError(() => of([] as CertificateRequestResponse[])))
        }).subscribe({
            next: ({ users, companies, requests }) => {
                const normalizedRequests = requests.map((request) => this.toRecentRequest(request));
                this.registeredCompanies = companies.map((company: any) => ({
                    id: company.id ?? company.Id ?? 0,
                    name: company.companyName ?? company.CompanyName ?? 'N/A',
                    email: company.companyEmail ?? company.CompanyEmail ?? 'N/A',
                    phone: company.companyPhone ?? company.CompanyPhone ?? 'N/A'
                }));
                const pendingCount = normalizedRequests.filter((request) => request.status === 'Pending').length;
                const confirmedCount = normalizedRequests.filter((request) => request.status === 'Confirmed').length;
                const rejectedCount = normalizedRequests.filter((request) => request.status === 'Rejected').length;

                this.metrics = [
                    {
                        label: 'Total Requests',
                        value: this.formatCount(normalizedRequests.length),
                        detail: `${pendingCount} currently pending review`,
                        accent: 'blue'
                    },
                    {
                        label: 'Confirmed',
                        value: this.formatCount(confirmedCount),
                        detail: `${rejectedCount} rejected so far`,
                        accent: 'teal'
                    },
                    // {
                    //     label: 'Pending Queue',
                    //     value: this.formatCount(pendingCount),
                    //     detail: pendingCount > 0 ? 'Needs attention from the admin team' : 'No waiting approvals',
                    //     accent: 'amber'
                    // },
                    {
                        label: this.isAdmin ? 'Registered Companies' : 'Rejected',
                        value: this.formatCount(this.isAdmin ? this.registeredCompanies.length : rejectedCount),
                        detail: this.isAdmin ? `${users.length} active user accounts` : 'Closed requests across all submissions',
                        accent: 'rose'
                    }
                ];

                if (this.isAdmin) {
                    this.metrics.splice(2, 0, {
                        label: 'System Users',
                        value: this.formatCount(users.length),
                        detail: 'Accounts available for certificate operations',
                        accent: 'amber'
                    });
                }

                this.recentRequests = normalizedRequests.sort((left, right) => new Date(right.createdAt).getTime() - new Date(left.createdAt).getTime()).slice(0, 6);

                this.lastUpdated = new Date();
                this.loading = false;

                if (!requests.length && !users.length && !companies.length) {
                    this.error = 'Dashboard data is currently unavailable. ';
                    this.showErrorToast(this.error);
                }
            },
            error: () => {
                this.error = 'Failed to load the dashboard overview.';
                this.showErrorToast(this.error);
                this.loading = false;
            }
        });
    }

    private buildQuickActions(): QuickAction[] {
        const actions: QuickAction[] = [
            {
                title: 'Review certificate requests',
                description: 'Open the approval queue and process pending submissions.',
                route: '/uikit/admin/certificate-requests',
                icon: 'pi pi-inbox'
            }
        ];

        if (this.isAdmin) {
            actions.unshift(
                {
                    title: 'Manage users',
                    description: 'Create, edit, or deactivate user accounts.',
                    route: '/uikit/admin/user-list',
                    icon: 'pi pi-users'
                },
                {
                    title: 'Manage companies',
                    description: 'Update company registrations and their listed markets.',
                    route: '/uikit/admin/company-list',
                    icon: 'pi pi-building'
                }
            );
        }

        return actions;
    }

    private normalizeCompanies(response: unknown): any[] {
        if (Array.isArray(response)) {
            return response;
        }

        const data = response as { companies?: unknown[]; Companies?: unknown[] } | null;
        return data?.companies ?? data?.Companies ?? [];
    }

    private toRecentRequest(request: CertificateRequestResponse): RecentRequest {
        const type = this.normalizeType(request.certificateType);
        return {
            id: request.id,
            company: request.companyName ?? 'N/A',
            referenceNumber: request.referenceNumber,
            type: type,
            country: request.countryName ?? (type === 'EU' ? 'European Union' : 'N/A'),
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

        if (accent === 'amber') {
            return 'pi pi-bolt';
        }

        if (accent === 'rose') {
            return 'pi pi-chart-line';
        }

        return 'pi pi-verified';
    }

    getStatusSeverity(status: RecentRequest['status']): 'success' | 'warn' | 'danger' {
        if (status === 'Confirmed') {
            return 'success';
        }

        if (status === 'Pending') {
            return 'warn';
        }

        return 'danger';
    }

    showCompanyPanel(): boolean {
        return this.isAdmin;
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
