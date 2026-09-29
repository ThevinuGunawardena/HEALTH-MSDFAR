import { CommonModule } from '@angular/common';
import { Component, OnInit, ViewChild } from '@angular/core';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { ConfirmDialogModule } from 'primeng/confirmdialog';
import { ConfirmationService, MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { UserAddComponent } from '../user-add/user-add.component';
import { UserService, User } from '../../../service/user.service';

@Component({
    selector: 'app-user-list',
    standalone: true,
    imports: [CommonModule, DialogModule, ButtonModule, ConfirmDialogModule, ToastModule, UserAddComponent],
    templateUrl: './user-list.component.html',
    styleUrls: ['./user-list.component.css'],
    providers: [ConfirmationService, MessageService]
})
export class UserListComponent implements OnInit {
    @ViewChild(UserAddComponent) userAddComponent!: UserAddComponent;

    showAddModal = false;
    users: User[] = [];
    loading = false;
    error: string | null = null;

    constructor(
        private userService: UserService,
        private confirmationService: ConfirmationService,
        private messageService: MessageService
    ) {}

    ngOnInit() {
        this.getAllUsers();
    }

    getAllUsers() {
        this.loading = true;
        this.error = null;
        this.userService.getAllUsers().subscribe({
            next: (users) => {
                this.users = users;
                this.loading = false;
            },
            error: (err) => {
                console.error('Error loading users:', err);
                this.error = 'Failed to load users. Please try again.';
                this.loading = false;
            }
        });
    }

    openAddModal() {
        this.showAddModal = true;
        if (this.userAddComponent) {
            this.userAddComponent.editMode = false;
            this.userAddComponent.userId = null;
            this.userAddComponent.userForm.reset();
            this.userAddComponent.submitted = false;
        }
    }

    openEditModal(user: User) {
        this.showAddModal = true;
        if (this.userAddComponent) {
            this.userAddComponent.editMode = true;
            this.userAddComponent.userId = user.id;
            this.userAddComponent.userForm.patchValue({
                fullName: user.name,
                email: user.email,
                phone: user.phone,
                role: user.roleId ?? null,
                company: user.companyId ?? null,
                qualification: user.qualification ?? ''
            });
            this.userAddComponent.submitted = false;
        }
    }

    closeAddModal() {
        this.showAddModal = false;
    }

    onUserSaved(newUserData: any) {
        this.loading = true;
        this.error = null;
        const payload: any = {
            fullName: newUserData.fullName,
            email: newUserData.email,
            phone: newUserData.phone,
            roleId: String(newUserData.role ?? ''),
            companyId: newUserData.company ? Number(newUserData.company) : null,
            qualification: newUserData.qualification ?? ''
        };

        if (newUserData.password) {
            payload.password = newUserData.password;
        } else if (newUserData.newPassword) {
            payload.password = newUserData.newPassword;
        }

        if (this.userAddComponent?.editMode && this.userAddComponent.userId) {
            const editUserId = this.userAddComponent.userId;
            this.userService.updateUser(editUserId, payload).subscribe({
                next: (updatedUser) => {
                    const index = this.users.findIndex((u) => u.id === editUserId);
                    if (index !== -1) {
                        this.users[index] = updatedUser;
                    }
                    this.closeAddModal();
                    this.loading = false;
                },
                error: (err) => {
                    console.error('Error updating user:', err);
                    this.error = err.error?.Message || 'Failed to update user. Please try again.';
                    this.loading = false;
                }
            });
            return;
        }

        this.userService.insertUser(payload).subscribe({
            next: (user) => {
                this.users.push(user);
                this.closeAddModal();
                this.loading = false;
            },
            error: (err) => {
                console.error('Error creating user:', err);
                this.error = err.error?.Message || 'Failed to create user. Please try again.';
                this.loading = false;
            }
        });
    }

    deleteUser(event: Event, id: string) {
        this.confirmationService.confirm({
            target: event.target as EventTarget,
            message: 'Do you want to delete this user?',
            header: 'Delete Confirmation',
            icon: 'pi pi-info-circle',
            rejectLabel: 'Cancel',
            rejectButtonProps: {
                label: 'Cancel',
                severity: 'secondary',
                outlined: true
            },
            acceptButtonProps: {
                label: 'Delete',
                severity: 'danger'
            },
            accept: () => {
                this.loading = true;
                this.error = null;
                this.userService.deleteUser(id).subscribe({
                    next: () => {
                        this.users = this.users.filter((u) => u.id !== id);
                        this.loading = false;
                        this.messageService.add({ severity: 'info', summary: 'Confirmed', detail: 'User Delete Successful' });
                    },
                    error: (err) => {
                        console.error('Error deleting user:', err);
                        this.error = err.error?.Message || 'Failed to delete user. Please try again.';
                        this.loading = false;
                        this.messageService.add({ severity: 'error', summary: 'Delete failed', detail: this.error ?? 'Failed to delete user. Please try again.' });
                    }
                });
            },
            reject: () => {
                this.messageService.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected the deletion of this user.' });
            }
        });
    }
}
