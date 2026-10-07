import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { SelectModule } from 'primeng/select';
import { TextareaModule } from 'primeng/textarea';
import { UserLookupOption, UserService } from '../../../service/user.service';
import { Button } from 'primeng/button';
import { Password } from 'primeng/password';

@Component({
    selector: 'app-user-add',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, InputTextModule, SelectModule, TextareaModule, Button, Password],
    templateUrl: './user-add.component.html',
    styleUrls: ['./user-add.component.css']
})
export class UserAddComponent implements OnInit {
    showPasswordChange = false;
    togglePasswordChange(): void {
        this.showPasswordChange = !this.showPasswordChange;
        if (!this.showPasswordChange) {
            this.userForm.get('newPassword')?.setValue('');
            this.userForm.get('confirmNewPassword')?.setValue('');
        }
    }
    @Output() userSaved = new EventEmitter<any>();
    @Output() userCancelled = new EventEmitter<void>();
    private _editMode = false;
    @Input()
    set editMode(val: boolean) {
        this._editMode = val;
        if (val) {
            this.showPasswordChange = false;
            if (this.userForm) {
                this.userForm.get('newPassword')?.setValue('');
                this.userForm.get('confirmNewPassword')?.setValue('');
            }
        }
    }
    get editMode() {
        return this._editMode;
    }
    @Input() userId: string | null = null;

    userForm: FormGroup;
    loading = false;
    submitted = false;
    roles: UserLookupOption[] = [];
    companies: UserLookupOption[] = [];

    constructor(
        private fb: FormBuilder,
        private userService: UserService
    ) {
        this.userForm = this.fb.group(
            {
                fullName: ['', Validators.required],
                email: ['', [Validators.required, Validators.email]],
                phone: ['', Validators.required],
                role: [null, Validators.required],
                company: [null],
                qualification: [''],
                // Add mode
                password: [''],
                confirmPassword: [''],
                // Edit mode
                newPassword: [''],
                confirmNewPassword: ['']
            },
            { validators: this.passwordsValidator.bind(this) }
        );
    }

    ngOnInit(): void {
        this.loadRoles();
        this.loadCompanies();

        this.userForm.get('role')!.valueChanges.subscribe(() => {
            this.updateConditionalValidators();
        });
        this.setPasswordValidators();
        if (this.editMode) {
            this.showPasswordChange = false;
        }
    }

    ngOnChanges(changes: any): void {
        this.setPasswordValidators();
        if (changes.editMode && changes.editMode.currentValue) {
            this.showPasswordChange = false;
            this.userForm.get('newPassword')?.setValue('');
            this.userForm.get('confirmNewPassword')?.setValue('');
        }
    }

    get currentPassword(): string {
        return this.userForm?.get('password')?.value || '';
    }

    get currentConfirmPassword(): string {
        return this.userForm?.get('confirmPassword')?.value || '';
    }

    get currentNewPassword(): string {
        return this.userForm?.get('newPassword')?.value || '';
    }

    get currentConfirmNewPassword(): string {
        return this.userForm?.get('confirmNewPassword')?.value || '';
    }

    // Add Mode checks
    get hasMinLength(): boolean {
        return this.currentPassword.length >= 6;
    }
    get hasUppercase(): boolean {
        return /[A-Z]/.test(this.currentPassword);
    }
    get hasLowercase(): boolean {
        return /[a-z]/.test(this.currentPassword);
    }
    get hasNumber(): boolean {
        return /\d/.test(this.currentPassword);
    }
    get hasSpecialChar(): boolean {
        return /[^a-zA-Z\d]/.test(this.currentPassword);
    }
    get passwordsMatch(): boolean {
        return !!this.currentPassword && this.currentPassword === this.currentConfirmPassword;
    }
    get isAddPasswordValid(): boolean {
        return this.hasMinLength && this.hasUppercase && this.hasLowercase && this.hasNumber && this.hasSpecialChar;
    }

    // Edit Mode checks
    get hasNewMinLength(): boolean {
        return this.currentNewPassword.length >= 6;
    }
    get hasNewUppercase(): boolean {
        return /[A-Z]/.test(this.currentNewPassword);
    }
    get hasNewLowercase(): boolean {
        return /[a-z]/.test(this.currentNewPassword);
    }
    get hasNewNumber(): boolean {
        return /\d/.test(this.currentNewPassword);
    }
    get hasNewSpecialChar(): boolean {
        return /[^a-zA-Z\d]/.test(this.currentNewPassword);
    }
    get newPasswordsMatch(): boolean {
        return !!this.currentNewPassword && this.currentNewPassword === this.currentConfirmNewPassword;
    }
    get isEditPasswordValid(): boolean {
        return this.hasNewMinLength && this.hasNewUppercase && this.hasNewLowercase && this.hasNewNumber && this.hasNewSpecialChar;
    }

    private setPasswordValidators(): void {
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,}$/;
        if (!this.editMode) {
            this.userForm.get('password')?.setValidators([Validators.required, Validators.pattern(passwordPattern)]);
            this.userForm.get('confirmPassword')?.setValidators([Validators.required]);
            this.userForm.get('newPassword')?.clearValidators();
            this.userForm.get('confirmNewPassword')?.clearValidators();
        } else {
            this.userForm.get('password')?.clearValidators();
            this.userForm.get('confirmPassword')?.clearValidators();
            if (this.showPasswordChange) {
                this.userForm.get('newPassword')?.setValidators([Validators.required, Validators.pattern(passwordPattern)]);
                this.userForm.get('confirmNewPassword')?.setValidators([Validators.required]);
            } else {
                this.userForm.get('newPassword')?.clearValidators();
                this.userForm.get('confirmNewPassword')?.clearValidators();
            }
        }
        this.userForm.get('password')?.updateValueAndValidity({ emitEvent: false });
        this.userForm.get('confirmPassword')?.updateValueAndValidity({ emitEvent: false });
        this.userForm.get('newPassword')?.updateValueAndValidity({ emitEvent: false });
        this.userForm.get('confirmNewPassword')?.updateValueAndValidity({ emitEvent: false });
    }

    private passwordsValidator(form: FormGroup) {
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{6,}$/;

        if (!this.editMode) {
            const password = form.get('password')?.value || '';
            const confirmPassword = form.get('confirmPassword')?.value || '';

            if (!password) {
                form.get('password')?.setErrors({ required: true });
            } else if (!passwordPattern.test(password)) {
                form.get('password')?.setErrors({ pattern: true });
            } else {
                form.get('password')?.setErrors(null);
            }

            if (!confirmPassword) {
                form.get('confirmPassword')?.setErrors({ required: true });
            } else if (password !== confirmPassword) {
                form.get('confirmPassword')?.setErrors({ mismatch: true });
            } else {
                form.get('confirmPassword')?.setErrors(null);
            }
        } else {
            if (this.showPasswordChange) {
                const newPassword = form.get('newPassword')?.value || '';
                const confirmNewPassword = form.get('confirmNewPassword')?.value || '';

                if (!newPassword) {
                    form.get('newPassword')?.setErrors({ required: true });
                } else if (!passwordPattern.test(newPassword)) {
                    form.get('newPassword')?.setErrors({ pattern: true });
                } else {
                    form.get('newPassword')?.setErrors(null);
                }

                if (!confirmNewPassword) {
                    form.get('confirmNewPassword')?.setErrors({ required: true });
                } else if (newPassword !== confirmNewPassword) {
                    form.get('confirmNewPassword')?.setErrors({ mismatch: true });
                } else {
                    form.get('confirmNewPassword')?.setErrors(null);
                }
            } else {
                form.get('newPassword')?.setErrors(null);
                form.get('confirmNewPassword')?.setErrors(null);
            }
        }
        return null;
    }

    get selectedRoleName(): string {
        const roleId = this.userForm.get('role')?.value;
        const role = this.roles.find((r) => r.id === roleId);
        return role?.name?.toLowerCase() ?? '';
    }

    get showCompany(): boolean {
        return this.selectedRoleName === 'company';
    }

    get showQualification(): boolean {
        return this.selectedRoleName === 'user';
    }

    private updateConditionalValidators(): void {
        const companyControl = this.userForm.get('company')!;
        const qualificationControl = this.userForm.get('qualification')!;
        if (this.showCompany) {
            companyControl.setValidators(Validators.required);
        } else {
            companyControl.clearValidators();
            companyControl.setValue(null);
        }
        companyControl.updateValueAndValidity();
        if (!this.showQualification) {
            qualificationControl.setValue('');
        }
    }

    private loadRoles(): void {
        this.userService.getRoles().subscribe({
            next: (res: any) => {
                const list = Array.isArray(res) ? res : (res?.roles ?? res?.Roles ?? []);
                this.roles = list.map((role: any) => ({
                    id: role.id ?? role.Id,
                    name: role.name ?? role.Name ?? ''
                }));
            },
            error: (err) => {
                console.error('Failed to load roles:', err);
                this.roles = [];
            }
        });
    }

    private loadCompanies(): void {
        this.userService.getCompanies().subscribe({
            next: (res: any) => {
                const list = Array.isArray(res) ? res : (res?.companies ?? res?.Companies ?? []);
                this.companies = list.map((company: any) => ({
                    id: company.id ?? company.Id,
                    name: company.companyName ?? company.CompanyName ?? company.name ?? company.Name ?? ''
                }));
            },
            error: (err) => {
                console.error('Failed to load companies:', err);
                this.companies = [];
            }
        });
    }

    submitForm() {
        this.submitted = true;
        this.setPasswordValidators();
        if (this.userForm.valid) {
            // Prepare payload
            const formValue = { ...this.userForm.value };
            if (!this.editMode) {
                // Only send password fields for add
                formValue.password = this.userForm.get('password')?.value;
                delete formValue.confirmPassword;
                delete formValue.newPassword;
                delete formValue.confirmNewPassword;
            } else {
                // Only send new password if filled
                if (!formValue.newPassword) {
                    delete formValue.newPassword;
                }
                delete formValue.confirmNewPassword;
                delete formValue.password;
                delete formValue.confirmPassword;
            }
            this.userSaved.emit(formValue);
        } else {
            this.userForm.markAllAsTouched();
        }
    }

    onCancel() {
        this.userForm.reset();
        this.submitted = false;
        this.showPasswordChange = false;
        this.userForm.get('newPassword')?.setValue('');
        this.userForm.get('confirmNewPassword')?.setValue('');
        this.userCancelled.emit();
    }
}
