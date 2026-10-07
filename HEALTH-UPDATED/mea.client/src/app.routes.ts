import { Routes } from '@angular/router';
import { AppLayout } from '@/layout/components/app.layout';
import { Notfound } from '@/pages/notfound/notfound';
import { AuthLayout } from '@/layout/components/app.authlayout';
import { authGuard } from '@/shared/auth-guard';

export const appRoutes: Routes = [
    {
        path: '',
        redirectTo: '/auth/vet-login',
        pathMatch: 'full'
    },
    {
        path: '',
        component: AppLayout,
        canActivate: [authGuard],
        children: [
            {
                path: '',
                redirectTo: '/uikit/admin/dashboard',
                pathMatch: 'full'
            },
            {
                path: 'uikit',
                data: { breadcrumb: 'DFAR System' },
                loadChildren: () => import('@/pages/uikit/uikit.routes')
            },
            {
                path: 'my-profile',
                loadComponent: () => import('@/pages/profile/profile').then((c) => c.ProfilePage),
                data: { breadcrumb: 'Profile' }
            },
            {
                path: 'company-log-dashboard',
                loadComponent: () => import('@/pages/auth/company-log-dashboard/company-log-dashboard.component').then((c) => c.CompanyLogDashboardComponent),
                canActivate: [authGuard],
                data: { roles: ['Company'] }
            },
            {
                path: 'company-request-history',
                loadComponent: () => import('@/pages/auth/company-request-history/company-request-history.component').then((c) => c.CompanyRequestHistoryComponent),
                canActivate: [authGuard],
                data: { roles: ['Company'], breadcrumb: 'Request History' }
            }
        ]
    },
    { path: 'notfound', component: Notfound },
    {
        path: 'auth',
        component: AuthLayout,
        children: [
            {
                path: 'vet-login',
                loadComponent: () => import('@/pages/auth/vet-login/vet-login').then((c) => c.VetLogin)
            },
            {
                path: 'vet-register',
                loadComponent: () => import('@/pages/auth/vet-register/vet-register').then((c) => c.VetRegister)
            },
            {
                path: 'verification',
                loadComponent: () => import('@/pages/auth/verification').then((c) => c.Verification)
            },
            {
                path: 'forgot-password',
                loadComponent: () => import('@/pages/auth/forgotpassword').then((c) => c.ForgotPassword)
            },
            {
                path: 'new-password',
                loadComponent: () => import('@/pages/auth/newpassword').then((c) => c.NewPassword)
            },
            {
                path: 'lock-screen',
                loadComponent: () => import('@/pages/auth/lockscreen').then((c) => c.LockScreen)
            },
            {
                path: 'access',
                loadComponent: () => import('@/pages/auth/access').then((c) => c.Access)
            },
            {
                path: 'sso',
                loadComponent: () => import('@/pages/auth/sso/sso.component').then((c) => c.SsoComponent)
            },
            {
                path: 'error',
                loadComponent: () => import('@/pages/notfound/notfound').then((c) => c.Notfound)
            }
        ]
    },
    {
        path: 'sso',
        loadComponent: () => import('@/pages/auth/sso/sso.component').then((c) => c.SsoComponent)
    },
    {
        path: 'verify-document',
        loadComponent: () => import('@/pages/public/verify-document/verify-document.component').then((c) => c.VerifyDocumentComponent)
    },
    { path: '**', redirectTo: '/notfound' }
];
