import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

@Component({
    selector: 'app-sso',
    standalone: true,
    imports: [CommonModule, ToastModule],
    providers: [MessageService],
    template: `
        <div class="sso-container">
            <p-toast></p-toast>
            <div class="sso-card">
                <div class="sso-logo mb-4">
                    <img src="/demo/images/srilanka-emblem.png" alt="DFAR Logo" class="w-16 h-auto mx-auto" />
                </div>
                <div class="spinner-wrap mb-4">
                    <div class="sso-spinner"></div>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">Authenticating Session</h2>
                <p class="text-sm text-slate-500 mb-4">{{ statusMessage }}</p>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="sso-progress-bar"></div>
                </div>
            </div>
        </div>
    `,
    styles: [`
        .sso-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            padding: 1.5rem;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .sso-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }
        .spinner-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .sso-spinner {
            width: 44px;
            height: 44px;
            border: 3.5px solid #e2e8f0;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        .sso-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #6366f1);
            width: 60%;
            animation: progressIndeterminate 1.4s infinite ease-in-out;
            border-radius: 9999px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes progressIndeterminate {
            0% { transform: translateX(-100%); width: 30%; }
            50% { width: 70%; }
            100% { transform: translateX(200%); width: 30%; }
        }
    `]
})
export class SsoComponent implements OnInit {
    statusMessage = 'Verifying credentials from main portal...';

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private authService: AuthService,
        private messageService: MessageService
    ) {}

    ngOnInit(): void {
        this.route.queryParams.subscribe((params) => {
            const token = params['token'] || this.extractTokenFromHash();

            if (!token) {
                this.statusMessage = 'No Single Sign-On token provided.';
                this.messageService.add({
                    severity: 'error',
                    summary: 'SSO Error',
                    detail: 'No SSO token detected. Redirecting to login...'
                });
                setTimeout(() => this.router.navigate(['/auth/vet-login']), 2000);
                return;
            }

            this.authService.ssoLogin(token).subscribe({
                next: (res: any) => {
                    this.statusMessage = `Welcome, ${res.name || 'User'}! Redirecting...`;
                    this.authService.saveToken(res.token);
                    this.authService.saveUserInfo(res.email, res.userId, res.role, res.name);

                    const role = (res.role || '').toLowerCase();
                    const returnUrl = params['returnUrl'];
                    
                    let targetUrl = returnUrl;
                    if (!targetUrl) {
                        targetUrl = role === 'company' ? '/company-log-dashboard' : '/uikit/admin/dashboard';
                    }

                    this.messageService.add({
                        severity: 'success',
                        summary: 'SSO Authenticated',
                        detail: 'Single Sign-On successful. Welcome!'
                    });

                    setTimeout(() => {
                        this.router.navigateByUrl(targetUrl);
                    }, 800);
                },
                error: (err: any) => {
                    this.statusMessage = 'SSO verification failed. Invalid or expired token.';
                    this.messageService.add({
                        severity: 'error',
                        summary: 'SSO Failed',
                        detail: err?.error?.message || 'Invalid or expired SSO token. Redirecting to login...'
                    });
                    setTimeout(() => {
                        this.router.navigate(['/auth/vet-login'], { queryParams: { ssoError: 'failed' } });
                    }, 2500);
                }
            });
        });
    }

    private extractTokenFromHash(): string | null {
        try {
            const hash = window.location.hash || '';
            const match = hash.match(/[?&]token=([^&]+)/);
            return match ? decodeURIComponent(match[1]) : null;
        } catch {
            return null;
        }
    }
}
