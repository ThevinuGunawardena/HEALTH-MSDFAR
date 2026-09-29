import { Routes } from '@angular/router';

export default [
    { path: 'vet-login', data: { breadcrumb: 'Vet Login' }, loadComponent: () => import('./vet-login/vet-login').then((c) => c.VetLogin) },
    { path: 'vet-register', data: { breadcrumb: 'Vet Register' }, loadComponent: () => import('./vet-register/vet-register').then((c) => c.VetRegister) },
    { path: 'login', data: { breadcrumb: 'Login' }, loadComponent: () => import('./login').then((c) => c.Login) },
    { path: 'register', data: { breadcrumb: 'Register' }, loadComponent: () => import('./register').then((c) => c.Register) },
    { path: 'verification', data: { breadcrumb: 'Verification' }, loadComponent: () => import('./verification').then((c) => c.Verification) },
    { path: 'forgot-password', data: { breadcrumb: 'Forgot Password' }, loadComponent: () => import('./forgotpassword').then((c) => c.ForgotPassword) },
    { path: 'new-password', data: { breadcrumb: 'New Password' }, loadComponent: () => import('./newpassword').then((c) => c.NewPassword) },
    { path: 'lock-screen', data: { breadcrumb: 'Lock Screen' }, loadComponent: () => import('./lockscreen').then((c) => c.LockScreen) },
    { path: 'access', data: { breadcrumb: 'Access' }, loadComponent: () => import('./access').then((c) => c.Access) },
    { path: '**', redirectTo: '/notfound' }
] as Routes;
