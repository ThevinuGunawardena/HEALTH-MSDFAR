import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, AbstractControl, ValidationErrors } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { CheckboxModule } from 'primeng/checkbox';
import { InputTextModule } from 'primeng/inputtext';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { AppleWidget } from '../components/applewidget';
import { GoogleWidget } from '../components/googlewidget';
import { AuthService } from '../../service/auth.service';
import { LazyImageWidget } from '@/shared/components/lazyimagewidget';

@Component({
    selector: 'app-login',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, InputTextModule, LazyImageWidget, CheckboxModule, RouterLink, ToastModule],
    templateUrl: './vet-register.html',
    styleUrls: ['./vet-register.scss'],
    providers: [MessageService]
})
export class VetRegister implements OnInit {
    loginForm: FormGroup;
    currentYear: number = new Date().getFullYear();
    submitted: boolean = false;
    showPassword: boolean = false;
    showConfirmPassword: boolean = false;

    togglePasswordVisibility() {
        this.showPassword = !this.showPassword;
    }

    toggleConfirmPasswordVisibility() {
        this.showConfirmPassword = !this.showConfirmPassword;
    }

    // Password validation helper methods
    hasMinLength(): boolean {
        const password = this.loginForm.get('password')?.value;
        return password && password.length >= 8;
    }

    hasLowerCase(): boolean {
        const password = this.loginForm.get('password')?.value;
        return password && /[a-z]/.test(password);
    }

    hasUpperCase(): boolean {
        const password = this.loginForm.get('password')?.value;
        return password && /[A-Z]/.test(password);
    }

    hasNumber(): boolean {
        const password = this.loginForm.get('password')?.value;
        return password && /\d/.test(password);
    }

    hasSpecialChar(): boolean {
        const password = this.loginForm.get('password')?.value;
        return password && /[@$!%*#?&]/.test(password);
    }

    passwordMatchValidator(control: AbstractControl): ValidationErrors | null {
        const password = control.get('password')?.value;
        const confirmPassword = control.get('confirmPassword')?.value;
        return password && confirmPassword && password === confirmPassword ? null : { passwordMismatch: true };
    }

    constructor(
        private fb: FormBuilder,
        private service: AuthService,
        private messageService: MessageService,
        private router: Router
    ) {
        this.loginForm = this.fb.group(
            {
                fullName: ['', [Validators.required]],
                email: ['', [Validators.required, Validators.email]],
                password: [
                    '',
                    [
                        Validators.required,
                        Validators.minLength(8),
                        Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/) // Lowercase, uppercase, number, and symbol
                    ]
                ],
                confirmPassword: ['', [Validators.required]],
                terms: [false, [Validators.requiredTrue]]
            },
            { validators: this.passwordMatchValidator }
        );
    }
    ngOnInit(): void {
       // if (this.service.isLoggedIn()) this.router.navigateByUrl('/auth/testdashboard');
    }

    onSubmit() {
        this.submitted = true;
        if (this.loginForm.valid) {
            this.service.createUser(this.loginForm.value).subscribe({
                next: (res: any) => {
                    const succeeded = res?.succeeded ?? res?.success ?? (typeof res?.message === 'string' && res.message.toLowerCase().includes('success'));

                    if (succeeded) {
                        const detail = res?.message ?? 'Registration successful.';
                        this.messageService.add({ severity: 'success', summary: 'Registration successful', detail });
                        this.loginForm.reset();
                        this.submitted = false;
                    } else {
                        const detail = res?.message ?? 'Registration failed.';
                        this.messageService.add({ severity: 'error', summary: 'Registration failed', detail });
                    }
                    console.log(res);
                },
                error: (err) => {
                    const detail = err?.error?.message ?? 'Please try again.';
                    this.messageService.add({ severity: 'error', summary: 'Registration failed', detail });
                    console.log('error', err);
                }
            });
        }
    }
}
