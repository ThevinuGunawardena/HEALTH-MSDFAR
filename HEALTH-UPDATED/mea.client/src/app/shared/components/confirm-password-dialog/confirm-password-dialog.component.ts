import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '@/pages/service/auth.service';
import { MessageService } from 'primeng/api';

@Component({
  selector: 'app-confirm-password-dialog',
  standalone: true,
  imports: [CommonModule, DialogModule, ButtonModule, InputTextModule, PasswordModule, ReactiveFormsModule],
  templateUrl: './confirm-password-dialog.component.html'
})
export class ConfirmPasswordDialogComponent {
  @Input() visible = false;
  @Input() userEmail: string = '';
  @Input() userId: string = '';

  @Output() visibleChange = new EventEmitter<boolean>();
  @Output() onConfirm = new EventEmitter<string>();
  @Output() onCancel = new EventEmitter<void>();

  confirmPasswordControl = new FormControl('');

  constructor(
    private authService: AuthService,
    private messageService: MessageService
  ) {}

  confirm() {
    const password = this.confirmPasswordControl.value;
    if (!password) {
      this.messageService.add({severity: 'error', summary: 'Error', detail: 'Password is required'});
      return;
    }

    if (!this.userEmail) {
       this.messageService.add({severity: 'error', summary: 'Error', detail: 'User email not found'});
       return;
    }

    this.authService.signin({ email: this.userEmail, password: password }).subscribe({
      next: (res) => {
        this.messageService.add({severity: 'success', summary: 'Success', detail: 'Signatory verified'});
        this.confirmPasswordControl.reset();
        this.visible = false;
        this.visibleChange.emit(this.visible);
        this.onConfirm.emit(this.userId);
      },
      error: (err) => {
        this.messageService.add({severity: 'error', summary: 'Error', detail: 'Invalid password. Cannot select this user.'});
      }
    });
  }

  cancel() {
    this.confirmPasswordControl.reset();
    this.visible = false;
    this.visibleChange.emit(this.visible);
    this.onCancel.emit();
  }
}
