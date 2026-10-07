import { AuthService } from '@/pages/service/auth.service';
import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
    const authService = inject(AuthService);
    const router = inject(Router);

    let authReq = req;
    const token = authService.getToken();
    if (token) {
        authReq = req.clone({
            headers: req.headers.set('Authorization', 'Bearer ' + token)
        });
    }

    return next(authReq).pipe(
        catchError((error: unknown) => {
            if (
                error instanceof HttpErrorResponse &&
                error.status === 401 &&
                token &&
                !req.url.includes('/identityuser/signin') &&
                !req.url.includes('/identityuser/signup') &&
                !req.url.includes('/identityuser/sso-login')
            ) {
                authService.deleteToken();
                router.navigate(['/auth/vet-login']);
            }
            return throwError(() => error);
        })
    );
};
