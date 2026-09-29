import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AppMenuitem } from './app.menuitem';
import { AuthService } from '@/pages/service/auth.service';

@Component({
    selector: '[app-menu]',
    standalone: true,
    imports: [CommonModule, AppMenuitem, RouterModule],
    template: `<ul class="layout-menu">
        <ng-container *ngFor="let item of model; let i = index">
            <li app-menuitem *ngIf="!item.separator" [item]="item" [index]="i" [root]="true"></li>
            <li *ngIf="item.separator" class="menu-separator"></li>
        </ng-container>
    </ul> `
})
export class AppMenu {
    private readonly role: string;

    constructor(private authService: AuthService) {
        this.role = (this.authService.getUserRole() || '').trim().toLowerCase();
        this.model = this.model.map((item) => (item.label === 'Companies' ? { ...item, visible: this.role === 'company' } : item));
        this.model = this.pruneByRole(this.model);
    }

    private pruneByRole(items: any[]): any[] {
        const canSee = (item: any) => {
            if (item?.visible === false) {
                return false;
            }

            const roles = item?.data?.roles as string[] | undefined;
            return !roles || this.role === 'admin' || roles.some((r) => r.toLowerCase() === this.role);
        };

        const filtered = items
            .filter((item) => item.separator || canSee(item))
            .map((item) => (Array.isArray(item.items) ? { ...item, items: this.pruneByRole(item.items) } : item))
            .filter((item) => item.separator || !Array.isArray(item.items) || item.items.length > 0);

        return filtered.filter((item, i, arr) => !item.separator || (i > 0 && i < arr.length - 1 && !arr[i - 1].separator && !arr[i + 1].separator));
    }

    model: any[] = [
        {
            label: 'Dashboards',
            icon: 'pi pi-home',
            items: [
                {
                    label: 'Dashboard',
                    icon: 'pi pi-fw pi-warehouse',
                    routerLink: ['/uikit/admin/dashboard'],
                    data: { roles: ['Admin', 'User'] }
                }
            ]
        },
        { separator: true },
        {
            label: 'Admin',
            icon: 'pi pi-fw pi-star-fill',
            data: { roles: ['Admin', 'User'] },
            items: [
                {
                    label: 'Certificate',
                    icon: 'pi pi-fw pi-file',
                    routerLink: ['/uikit/certificate'],
                    data: { roles: ['Admin'] }
                },
                {
                    label: 'User Register',
                    icon: 'pi pi-fw pi-users',
                    data: { roles: ['Admin'] },
                    routerLink: ['/uikit/admin/user-list']
                },
                {
                    label: 'Company Register',
                    icon: 'pi pi-fw pi-building',
                    data: { roles: ['Admin'] },
                    routerLink: ['/uikit/admin/company-list']
                },
                {
                    label: 'Certificate Requests',
                    icon: 'pi pi-fw pi-check-square',
                    data: { roles: ['Admin', 'User'] },
                    routerLink: ['/uikit/admin/certificate-requests']
                },
                {
                    label: 'Report',
                    icon: 'pi pi-fw pi-chart-bar',
                    data: { roles: ['Admin', 'User'] },
                    routerLink: ['/uikit/reports']
                },
                {
                    label: 'World Certificates',
                    icon: 'pi pi-fw pi-globe',
                    data: { roles: ['Admin'] },
                    items: [
                        {
                            label: 'Armenia Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/am-certificate']
                        },
                        {
                            label: 'Australia Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/au-certificate']
                        },
                        {
                            label: 'Brazil Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/br-certificate']
                        },
                        {
                            label: 'Canada Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/ca-certificate']
                        },
                        {
                            label: 'China Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/ch-certificate']
                        },
                        {
                            label: 'China Health Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/ch-health-certificate']
                        },
                        {
                            label: 'Hong Kong Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/hk-certificate']
                        },
                        {
                            label: 'India Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/in-certificate']
                        },
                        {
                            label: 'Indonesia Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/id-certificate']
                        },
                        {
                            label: 'Israel Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/il-certificate']
                        },
                        {
                            label: 'Japan Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/jp-certificate']
                        },
                        {
                            label: 'Kazakhstan Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/kz-certificate']
                        },
                        {
                            label: 'Kazakhstan Attachment Certificate',
                            icon: 'pi pi-fw pi-file',
                            routerLink: ['/uikit/world-certificates/kz-attachment-certificate']
                        },
                        {
                            label: 'Kuwait Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/kw-certificate']
                        },
                        {
                            label: 'Malaysia Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/my-certificate']
                        },
                        {
                            label: 'Malaysia Quality Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/my-quality-certificate']
                        },
                        {
                            label: 'Maldives Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/mv-certificate']
                        },
                        {
                            label: 'New Zealand Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/nz-certificate']
                        },
                        {
                            label: 'Russia Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/ru-certificate']
                        },
                        {
                            label: 'Russia Attachment Certificate',
                            icon: 'pi pi-fw pi-file',
                            routerLink: ['/uikit/world-certificates/ru-attachment-certificate']
                        },
                        {
                            label: 'Saudi Arabia Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/sa-certificate']
                        },
                        {
                            label: 'South Africa Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/za-certificate']
                        },
                        {
                            label: 'Taiwan Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/tw-certificate']
                        },
                        {
                            label: 'Taiwan Quality Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/tw-quality-certificate']
                        },
                        {
                            label: 'UK Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/uk-certificate']
                        },
                        {
                            label: 'Ukraine Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/ua-certificate']
                        },
                        {
                            label: 'Ukraine Attachment Certificate',
                            icon: 'pi pi-fw pi-file',
                            routerLink: ['/uikit/world-certificates/ua-attachment-certificate']
                        },
                        {
                            label: 'USA Certificate',
                            icon: 'pi pi-fw pi-check-square',
                            routerLink: ['/uikit/world-certificates/usa-certificate']
                        }
                    ]
                }
            ]
        },
        { separator: true },
        {
            label: 'Companies',
            icon: 'pi pi-fw pi-prime',
            data: { roles: ['Company'] },
            items: [
                {
                    label: 'Dashboard',
                    icon: 'pi pi-fw pi-chart-line',
                    routerLink: ['/company-log-dashboard'],
                    data: { roles: ['Company'] }
                },
                {
                    label: 'New Requests',
                    icon: 'pi pi-fw pi-file',
                    routerLink: ['/uikit/company-request'],
                    data: { roles: ['Company'] }
                },
                {
                    label: 'Request History',
                    icon: 'pi pi-fw pi-history',
                    routerLink: ['/company-request-history'],
                    data: { roles: ['Company'] }
                }
            ]
        }
    ];
}
