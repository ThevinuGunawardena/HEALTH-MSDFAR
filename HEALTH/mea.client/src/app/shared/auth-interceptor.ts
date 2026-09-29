import { AuthService } from '@/pages/service/auth.service';
import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
    const authService = inject(AuthService);
    if (authService.isLoggedIn()) {
        const clonedReq = req.clone({
            headers: req.headers.set('Authorization', 'Bearer ' + authService.getToken())
        });
        return next(clonedReq);
    } else return next(req);
};
