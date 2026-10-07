import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../pages/service/auth.service';

export const authGuard: CanActivateFn = (route, state) => {
    const authService = inject(AuthService);
    const router = inject(Router);

    if (!authService.isLoggedIn()) {
        router.navigateByUrl('/auth/vet-login');
        return false;
    }

    const allowedRoles = (route.data?.['roles'] as string[] | undefined)?.map((role) => role.toLowerCase()) ?? [];
    if (allowedRoles.length === 0) {
        return true;
    }

    const userRole = authService.getUserRole().toLowerCase();
    if (allowedRoles.includes(userRole) || userRole === 'admin') {
        return true;
    }

    if (userRole === 'company') {
        router.navigateByUrl('/company-log-dashboard');
        return false;
    }

    router.navigateByUrl('/');
    return false;
};
