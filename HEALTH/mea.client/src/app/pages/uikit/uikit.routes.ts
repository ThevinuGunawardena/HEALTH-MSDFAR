import { Routes } from '@angular/router';
import { authGuard } from '@/shared/auth-guard';

export default [
    {
        path: 'certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Certificate', roles: ['Admin', 'Company', 'User'] },
        loadComponent: () => import('./certificate/vet-certificate-wizard.component').then((c) => c.VetCertificateWizardComponent)
    },
    {
        path: 'admin/dashboard',
        canActivate: [authGuard],
        data: { breadcrumb: 'Admin Dashboard', roles: ['Admin', 'User'] },
        loadComponent: () => import('./admin-actions/dashboard/dashboard.component').then((c) => c.AdminDashboardComponent)
    },
    {
        path: 'admin/user-add',
        canActivate: [authGuard],
        data: { breadcrumb: 'User Add', roles: ['Admin'] },
        loadComponent: () => import('./admin-actions/user-add/user-add.component').then((c) => c.UserAddComponent)
    },
    {
        path: 'admin/user-list',
        canActivate: [authGuard],
        data: { breadcrumb: 'User List', roles: ['Admin'] },
        loadComponent: () => import('./admin-actions/user-list/user-list.component').then((c) => c.UserListComponent)
    },
    {
        path: 'admin/company-add',
        canActivate: [authGuard],
        data: { breadcrumb: 'Company Add', roles: ['Admin'] },
        loadComponent: () => import('./admin-actions/company-add/company-add.component').then((c) => c.CompanyAddComponent)
    },
    {
        path: 'admin/company-list',
        canActivate: [authGuard],
        data: { breadcrumb: 'Company List', roles: ['Admin'] },
        loadComponent: () => import('./admin-actions/company-list/company-list.component').then((c) => c.CompanyListComponent)
    },
    {
        path: 'admin/certificate-requests/view',
        canActivate: [authGuard],
        data: { breadcrumb: 'Certificate Preview', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./admin-actions/certificate-requests-view/certificate-requests-view.component').then((c) => c.CertificateRequestsViewComponent)
    },
    {
        path: 'admin/certificate-requests',
        canActivate: [authGuard],
        data: { breadcrumb: 'Certificate Requests', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./admin-actions/certificate-requests/certificate-requests.component').then((c) => c.CertificateRequestsComponent)
    },
    {
        path: 'reports',
        canActivate: [authGuard],
        data: { breadcrumb: 'Reports', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./reports/report.component').then((c) => c.ReportComponent)
    },
    {
        path: 'world-certificates/ch-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'China Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ch-certificate/ch-certificate.component').then((c) => c.ChCertificateComponent)
    },
    {
        path: 'world-certificates/br-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Brazil Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/br-certificate/br-certificate.component').then((m) => m.BrCertificateComponent)
    },
    {
        path: 'world-certificates/am-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Armenia Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/am-certificate/am-certificate.component').then((m) => m.AmCertificateComponent)
    },
    {
        path: 'world-certificates/hk-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Hong Kong Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/hk-certificate/hk-certificate.component').then((m) => m.HkCertificateComponent)
    },
    {
        path: 'world-certificates/in-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'India Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ind-certificate/ind-certificate.component').then((m) => m.IndCertificateComponent)
    },
    {
        path: 'world-certificates/id-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Indonesia Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/id-certificate/id-certificate.component').then((m) => m.IdCertificateComponent)
    },
    {
        path: 'world-certificates/il-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Israel Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/il-certificate/il-certificate.component').then((m) => m.IlCertificateComponent)
    },
    {
        path: 'world-certificates/my-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Malaysia Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/my-certificate/my-certificate.component').then((m) => m.MyCertificateComponent)
    },
    {
        path: 'world-certificates/my-quality-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Malaysia Quality Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/my-quality-certificate/my-quality-certificate.component').then((m) => m.MyQualityCertificateComponent)
    },
    {
        path: 'world-certificates/kw-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Kuwait Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/kw-certificate/kw-certificate.component').then((m) => m.KwCertificateComponent)
    },
    {
        path: 'world-certificates/tw-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Taiwan Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/tw-certificate/tw-certificate.component').then((m) => m.TwCertificateComponent)
    },
    {
        path: 'world-certificates/tw-quality-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Taiwan Quality Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/tw-certificate/tw-certificate.component').then((m) => m.TwCertificateComponent)
    },
    {
        path: 'world-certificates/au-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Australia Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/au-certificate/au-certificate.component').then((m) => m.AuCertificateComponent)
    },
    {
        path: 'world-certificates/ua-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Ukraine Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ua-certificate/ua-certificate.component').then((m) => m.UaCertificateComponent)
    },
    {
        path: 'world-certificates/ua-attachment-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Ukraine Attachment Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ua-certificate/ua-certificate.component').then((m) => m.UaCertificateComponent)
    },
    {
        path: 'world-certificates/ru-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Russia Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ru-certificate/ru-certificate.component').then((m) => m.RuCertificateComponent)
    },
    {
        path: 'world-certificates/ru-attachment-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Russia Attachment Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ru-certificate/ru-certificate.component').then((m) => m.RuCertificateComponent)
    },
    {
        path: 'world-certificates/kz-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Kazakhstan Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/kz-certificate/kz-certificate.component').then((m) => m.KzCertificateComponent)
    },
    {
        path: 'world-certificates/kz-attachment-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Kazakhstan Attachment Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/kz-certificate/kz-certificate.component').then((m) => m.KzCertificateComponent)
    },
    {
        path: 'world-certificates/jp-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Japan Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/jp-certificate/jp-certificate.component').then((m) => m.JpCertificateComponent)
    },
    {
        path: 'world-certificates/nz-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'New Zealand Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/nz-certificate/nz-certificate.component').then((m) => m.NzCertificateComponent)
    },
    {
        path: 'world-certificates/mv-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Maldives Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/mv-certificate/mv-certificate.component').then((m) => m.MvCertificateComponent)
    },
    {
        path: 'world-certificates/usa-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'USA Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/usa-certificate/usa-certificate.component').then((m) => m.UsaCertificateComponent)
    },
    {
        path: 'world-certificates/uk-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'UK Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/uk-certificate/uk-certificate.component').then((m) => m.UkCertificateComponent)
    },
    {
        path: 'world-certificates/ch-health-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'China Health Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ch-health-certificate/ch-health-certificate.component').then((m) => m.ChHealthCertificateComponent)
    },
    {
        path: 'world-certificates/ca-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Canada Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/ca-certificate/ca-certificate.component').then((m) => m.CaCertificateComponent)
    },
    {
        path: 'world-certificates/sa-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'Saudi Arabia Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/sa-certificate/sa-certificate.component').then((m) => m.SaCertificateComponent)
    },
    {
        path: 'world-certificates/za-certificate',
        canActivate: [authGuard],
        data: { breadcrumb: 'South Africa Certificate', roles: ['Admin', 'User', 'Company'] },
        loadComponent: () => import('./world-certificates/za-certificate/za-certificate.component').then((m) => m.ZaCertificateComponent)
    },
    {
        path: 'company-request',
        canActivate: [authGuard],
        data: { breadcrumb: 'Company Request', roles: ['Company'] },
        loadComponent: () => import('../auth/company-request.component/company-request.component').then((c) => c.CompanyRequestComponent)
    },
    { path: '**', redirectTo: '/notfound' }
] as Routes;
