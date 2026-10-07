import { Component, OnInit } from '@angular/core';
import { NgFor, NgIf, NgClass } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '@/pages/service/auth.service';
import { UserService } from '@/pages/service/user.service';
import { CardModule } from 'primeng/card';
import { AvatarModule } from 'primeng/avatar';
import { ButtonModule } from 'primeng/button';
import { DividerModule } from 'primeng/divider';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { TextareaModule } from 'primeng/textarea';
import { SkeletonModule } from 'primeng/skeleton';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';

@Component({
    selector: 'app-profile',
    standalone: true,
    imports: [NgIf, NgFor, NgClass, FormsModule, CardModule, AvatarModule, ButtonModule, DividerModule, InputTextModule, PasswordModule, TextareaModule, SkeletonModule, ToastModule],
    providers: [MessageService],
    template: `
        <p-toast />
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12">
                <div class="bg-white dark:bg-surface-900 shadow rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex flex-col sm:flex-row items-center gap-5">
                                <p-avatar [label]="initials" styleClass="w-24! h-24! text-4xl! font-bold shadow-sm border-2 border-surface-200 dark:border-surface-700 bg-surface-100 text-primary-500" shape="circle"></p-avatar>
                                <div class="mb-1 text-center sm:text-left mt-2 sm:mt-0">
                                    <h1 class="text-surface-900 dark:text-surface-0 text-3xl font-bold mb-0">{{ fullName || 'Profile' }}</h1>
                                    <span class="text-surface-500 dark:text-surface-400 text-lg flex items-center gap-2 justify-center sm:justify-start mt-1"> <i class="pi pi-briefcase shrink-0"></i> {{ roleName || 'User' }} </span>
                                </div>
                            </div>
                            <p-button type="button" icon="pi pi-pencil" label="Edit Profile" class="p-button-rounded p-button-outlined" (click)="onEdit()" *ngIf="!isEditing" [disabled]="loading"></p-button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 xl:col-span-8">
                <p-card>
                    <ng-template pTemplate="title">
                        <div class="flex items-center gap-2 text-xl mb-2">
                            <i class="pi pi-user text-primary-500"></i>
                            <span>Account Details</span>
                        </div>
                    </ng-template>
                    <ng-container *ngIf="!loading; else loadingBlock">
                        <div class="grid grid-cols-12 gap-5">
                            <div class="col-span-12 md:col-span-6 flex flex-col gap-2">
                                <label for="fullName" class="text-surface-700 dark:text-surface-100 font-medium">Full Name</label>
                                <input id="fullName" pInputText class="w-full" [(ngModel)]="editFullName" [readonly]="!isEditing" [ngClass]="{ 'bg-surface-50 dark:bg-surface-800 opacity-70 cursor-default': !isEditing }" />
                            </div>
                            <div class="col-span-12 md:col-span-6 flex flex-col gap-2">
                                <label for="email" class="text-surface-700 dark:text-surface-100 font-medium">Email</label>
                                <input id="email" pInputText class="w-full" [(ngModel)]="editEmail" [readonly]="!isEditing" [ngClass]="{ 'bg-surface-50 dark:bg-surface-800 opacity-70 cursor-default': !isEditing }" />
                            </div>
                            <div class="col-span-12 md:col-span-6 flex flex-col gap-2">
                                <label for="phone" class="text-surface-700 dark:text-surface-100 font-medium">Phone</label>
                                <input id="phone" pInputText class="w-full" [(ngModel)]="editPhone" [readonly]="!isEditing" [ngClass]="{ 'bg-surface-50 dark:bg-surface-800 opacity-70 cursor-default': !isEditing }" />
                            </div>
                            <div class="col-span-12 md:col-span-6 flex flex-col gap-2">
                                <label for="role" class="text-surface-700 dark:text-surface-100 font-medium">Role</label>
                                <input id="role" pInputText class="w-full bg-surface-50 dark:bg-surface-800 opacity-70 cursor-default" [value]="roleName" readonly />
                            </div>
                            <div class="col-span-12 flex flex-col gap-2" *ngIf="showCompany">
                                <label for="company" class="text-surface-700 dark:text-surface-100 font-medium">Company</label>
                                <input id="company" pInputText class="w-full bg-surface-50 dark:bg-surface-800 opacity-70 cursor-default" [value]="companyName || '-'" readonly />
                            </div>
                            <div class="col-span-12 flex flex-col gap-2" *ngIf="showQualification">
                                <label for="qualification" class="text-surface-700 dark:text-surface-100 font-medium">Qualification</label>
                                <textarea
                                    id="qualification"
                                    pTextarea
                                    class="w-full"
                                    rows="3"
                                    [(ngModel)]="editQualification"
                                    [autoResize]="true"
                                    [readonly]="!isEditing"
                                    [ngClass]="{ 'bg-surface-50 dark:bg-surface-800 opacity-70 cursor-default': !isEditing }"
                                ></textarea>
                            </div>

                            <div class="col-span-12 flex items-center justify-end mt-2">
                                <div class="flex gap-3" *ngIf="isEditing">
                                    <p-button type="button" label="Cancel" icon="pi pi-times" severity="secondary" (click)="onCancelEdit()"></p-button>
                                    <p-button type="button" label="Save Changes" icon="pi pi-check" severity="success" [loading]="loading" (click)="onSaveEdit()"></p-button>
                                </div>
                            </div>

                            <div class="col-span-12 flex justify-between items-center bg-surface-50 dark:bg-surface-800 p-4 rounded-lg flex-wrap gap-4">
                                <div>
                                    <h3 class="font-medium text-lg mb-1 text-surface-900 dark:text-surface-0">Security Setup</h3>
                                    <p class="text-surface-500 dark:text-surface-400 text-sm m-0">Update your password to keep your account secure.</p>
                                </div>
                                <p-button type="button" label="Change Password" icon="pi pi-lock" severity="info" [outlined]="true" (click)="togglePasswordChange()" [disabled]="loading || showPasswordChange"></p-button>
                            </div>

                            <div class="col-span-12 border border-surface-200 dark:border-surface-700 rounded-lg p-5 mt-2 bg-surface-50 dark:bg-surface-800/50" *ngIf="showPasswordChange">
                                <div class="grid grid-cols-12 gap-5">
                                    <div class="col-span-12 lg:col-span-4 flex flex-col gap-2">
                                        <label for="currentPassword" class="text-surface-700 dark:text-surface-100 font-medium">Current Password</label>
                                        <p-password id="currentPassword" class="w-full" [(ngModel)]="currentPassword" inputStyleClass="w-full" [feedback]="false" [toggleMask]="true" autocomplete="new-password"></p-password>
                                    </div>
                                    <div class="col-span-12 lg:col-span-4 flex flex-col gap-2">
                                        <label for="newPassword" class="text-surface-700 dark:text-surface-100 font-medium">New Password</label>
                                        <p-password
                                            id="newPassword"
                                            class="w-full"
                                            [(ngModel)]="newPassword"
                                            inputStyleClass="w-full"
                                            [feedback]="true"
                                            [toggleMask]="true"
                                            promptLabel="Enter new password"
                                            weakLabel="Weak"
                                            mediumLabel="Medium"
                                            strongLabel="Strong"
                                            autocomplete="new-password"
                                        ></p-password>
                                    </div>
                                    <div class="col-span-12 lg:col-span-4 flex flex-col gap-2">
                                        <label for="confirmPassword" class="text-surface-700 dark:text-surface-100 font-medium">Confirm Password</label>
                                        <p-password id="confirmPassword" class="w-full" [(ngModel)]="confirmPassword" inputStyleClass="w-full" [feedback]="false" [toggleMask]="true" autocomplete="new-password"></p-password>
                                    </div>
                                    <div class="col-span-12 flex items-center justify-end mt-2">
                                        <div class="flex gap-3">
                                            <p-button type="button" label="Cancel" severity="secondary" (click)="cancelPasswordChange()"></p-button>
                                            <p-button type="button" label="Update Password" icon="pi pi-shield" severity="success" [loading]="loading" (click)="onChangePassword()"></p-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ng-container>
                </p-card>
            </div>

            <!-- Side Card -->
            <div class="col-span-12 xl:col-span-4">
                <p-card>
                    <ng-template pTemplate="title">
                        <div class="flex items-center gap-2 text-xl mb-2">
                            <i class="pi pi-info-circle text-primary-500"></i>
                            <span>Account Status</span>
                        </div>
                    </ng-template>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4 p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                            <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 flex items-center justify-center shrink-0">
                                <i class="pi pi-check-circle text-2xl"></i>
                            </div>
                            <div>
                                <span class="block text-surface-500 dark:text-surface-400 text-sm font-medium">Status</span>
                                <span class="text-surface-900 dark:text-surface-0 font-semibold text-lg">Active</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-surface-50 dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-700">
                            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                <i class="pi pi-clock text-2xl"></i>
                            </div>
                            <div>
                                <span class="block text-surface-500 dark:text-surface-400 text-sm font-medium">Last Login</span>
                                <span class="text-surface-900 dark:text-surface-0 font-semibold">{{ lastUpdated }}</span>
                            </div>
                        </div>
                    </div>
                </p-card>
            </div>
        </div>

        <ng-template #loadingBlock>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-6" *ngFor="let _ of [1, 2, 3, 4]">
                    <p-skeleton height="2.5rem"></p-skeleton>
                </div>
                <div class="col-span-12">
                    <p-skeleton height="6rem"></p-skeleton>
                </div>
            </div>
        </ng-template>

        <ng-template #loadingSummaryBlock>
            <div class="flex flex-col gap-3">
                <p-skeleton height="1.5rem"></p-skeleton>
                <p-skeleton height="1.5rem"></p-skeleton>
                <p-skeleton height="1.5rem"></p-skeleton>
            </div>
        </ng-template>
    `
})
export class ProfilePage implements OnInit {
    fullName = '';
    email = '';
    phone = '';
    roleName = '';
    qualification = '';
    companyName = '';
    userId = '';
    loading = true;
    lastUpdated = '-';
    isEditing = false;
    editFullName = '';
    editEmail = '';
    editPhone = '';
    editQualification = '';

    showPasswordChange = false;
    currentPassword = '';
    newPassword = '';
    confirmPassword = '';

    constructor(
        private authService: AuthService,
        private userService: UserService,
        private messageService: MessageService
    ) {}
    togglePasswordChange(): void {
        this.showPasswordChange = true;
        this.currentPassword = '';
        this.newPassword = '';
        this.confirmPassword = '';
    }

    cancelPasswordChange(): void {
        this.showPasswordChange = false;
        this.currentPassword = '';
        this.newPassword = '';
        this.confirmPassword = '';
    }

    onChangePassword(): void {
        if (!this.currentPassword || !this.newPassword || !this.confirmPassword) {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'All password fields are required.' });
            return;
        }
        if (this.newPassword !== this.confirmPassword) {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'New passwords do not match.' });
            return;
        }
        if (this.newPassword.length < 6) {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'New password must be at least 6 characters.' });
            return;
        }

        this.loading = true;
        this.userService
            .updatePassword({
                currentPassword: this.currentPassword,
                newPassword: this.newPassword
            })
            .subscribe({
                next: (res) => {
                    this.loading = false;
                    this.messageService.add({ severity: 'success', summary: 'Success', detail: 'Password changed successfully!' });

                    // Clear fields upon success
                    this.currentPassword = '';
                    this.newPassword = '';
                    this.confirmPassword = '';

                    setTimeout(() => {
                        this.showPasswordChange = false;
                    }, 1500);
                },
                error: (err) => {
                    this.loading = false;
                    this.messageService.add({ severity: 'error', summary: 'Error', detail: err.error?.message || 'Failed to update password. Please check your current password.' });
                }
            });
    }

    get initials(): string {
        const name = (this.fullName || '').trim();
        if (!name) return 'U';

        const parts = name.split(/\s+/).filter(Boolean);
        if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
        return `${parts[0].charAt(0)}${parts[1].charAt(0)}`.toUpperCase();
    }

    get selectedRoleName(): string {
        return (this.roleName || '').toLowerCase();
    }

    get showCompany(): boolean {
        return this.selectedRoleName === 'company';
    }

    get showQualification(): boolean {
        return this.selectedRoleName !== 'company' && this.selectedRoleName !== 'admin';
    }

    ngOnInit(): void {
        this.loadProfile();
    }

    loadProfile(): void {
        this.loading = true;

        this.fullName = this.authService.getUserName();
        this.email = this.authService.getUserEmail();
        this.userId = this.authService.getUserId();
        this.roleName = this.authService.getUserRole() || 'User';

        this.userService.getUserProfile().subscribe({
            next: (response: any) => {
                this.fullName = response?.fullName ?? this.fullName;
                this.email = response?.email ?? this.email;
                this.phone = response?.phone ?? '';
                this.roleName = response?.roleName ?? this.roleName;
                this.qualification = response?.qualification ?? '';
                this.companyName = response?.companyName ?? response?.company ?? '';
                this.syncEditModel();
                this.loading = false;
                this.lastUpdated = new Date().toLocaleString();
            },
            error: () => {
                // Keep local-storage fallback values if profile API is unavailable.
                this.syncEditModel();
                this.loading = false;
                this.lastUpdated = new Date().toLocaleString();
            }
        });
    }

    onEdit(): void {
        this.syncEditModel();
        this.isEditing = true;
    }

    onCancelEdit(): void {
        this.syncEditModel();
        this.isEditing = false;
    }

    onSaveEdit(): void {
        if (!this.editFullName.trim() || !this.editEmail.trim()) {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Full Name and Email are required.' });
            return;
        }

        const payload = {
            fullName: this.editFullName.trim(),
            email: this.editEmail.trim(),
            phone: this.editPhone.trim(),
            qualification: this.showQualification ? this.editQualification.trim() : ''
        };

        this.loading = true;
        this.userService.updateUserProfile(payload).subscribe({
            next: (res) => {
                this.fullName = payload.fullName;
                this.email = payload.email;
                this.phone = payload.phone;

                if (this.showQualification) {
                    this.qualification = payload.qualification;
                } else {
                    this.qualification = '';
                    this.editQualification = '';
                }

                this.isEditing = false;
                this.loading = false;
                this.lastUpdated = new Date().toLocaleString();
                this.authService.updateUserName(payload.fullName);
                this.messageService.add({ severity: 'success', summary: 'Success', detail: 'Account information updated successfully.' });
                this.syncEditModel();
            },
            error: (err) => {
                this.loading = false;
                this.messageService.add({ severity: 'error', summary: 'Error', detail: err.error?.message || 'Failed to update account information.' });
            }
        });
    }

    private syncEditModel(): void {
        this.editFullName = this.fullName;
        this.editEmail = this.email;
        this.editPhone = this.phone;
        this.editQualification = this.qualification;
    }
}
