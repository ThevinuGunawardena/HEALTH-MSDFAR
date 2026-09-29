import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { InputTextModule } from 'primeng/inputtext';
import { ToastModule } from 'primeng/toast';
import { AppleWidget } from '@/pages/auth/components/applewidget';
import { GoogleWidget } from '@/pages/auth/components/googlewidget';
import { LazyImageWidget } from '@/shared/components/lazyimagewidget';
import { LogoWidget } from '@/shared/components/logowidget';
import { AuthService } from '@/pages/service/auth.service';
import { MessageService } from 'primeng/api';

@Component({
    selector: 'app-login',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, InputTextModule, LazyImageWidget, CheckboxModule, RouterLink, ToastModule],
    providers: [MessageService],
    templateUrl: './vet-login.html',
    styleUrls: ['./vet-login.scss']
})
export class VetLogin implements OnInit {
    loginForm: FormGroup;
    currentYear: number = new Date().getFullYear();
    submitted: boolean = false;
    showPassword: boolean = false;
    loading: boolean = false;

    togglePasswordVisibility() {
        this.showPassword = !this.showPassword;
    }

    private getRoleFromToken(token: string | null | undefined): string | null {
        if (!token) return null;
        const parts = token.split('.');
        if (parts.length !== 3) return null;

        try {
            const payload = parts[1].replace(/-/g, '+').replace(/_/g, '/');
            const json = decodeURIComponent(
                atob(payload)
                    .split('')
                    .map((c) => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
                    .join('')
            );
            const data = JSON.parse(json);
            return data?.role || data?.['http://schemas.microsoft.com/ws/2008/06/identity/claims/role'] || null;
        } catch {
            return null;
        }
    }

    constructor(
        public fb: FormBuilder,
        private service: AuthService,
        private router: Router,
        private messageService: MessageService
    ) {
        this.loginForm = this.fb.group({
            email: ['', [Validators.required, Validators.email]],
            password: ['', [Validators.required]],
            remember: [false]
        });
    }
    ngOnInit(): void {
        //if (this.service.isLoggedIn()) this.router.navigateByUrl('/auth/testdashboard');
    }

    onSubmit() {
        console.log(this.loginForm.value);
        this.submitted = true;
        if (this.loginForm.valid) {
            this.loading = true;
            this.service.signin(this.loginForm.value).subscribe({
                next: (res: any) => {
                    console.log(res);
                    this.loading = false;
                    // Save token and user information
                    this.service.saveToken(res.token);
                    this.service.saveUserInfo(res.email, res.userId, res.role, res.name);

                    // Navigate to dashboard based on role
                    const role = res.role?.toLowerCase() || '';
                    const redirect = role === 'company' ? '/company-log-dashboard' : '/uikit/admin/dashboard';
                    this.router.navigateByUrl(redirect);
                    this.messageService.add({ severity: 'success', summary: 'Login successful', detail: 'You have successfully logged in' });
                },
                error: (err) => {
                    this.loading = false;
                    if (err.status === 400) this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Invalid email or password' });
                    else this.messageService.add({ severity: 'error', summary: 'Error', detail: 'An unexpected error occurred' });
                }
            });
            return;
        }

        // proceed with login logic
    }
}
