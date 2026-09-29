import { CommonModule } from '@angular/common';
import { Component, OnInit, ViewChild } from '@angular/core';
import { DialogModule } from 'primeng/dialog';
import { ConfirmDialogModule } from 'primeng/confirmdialog';
import { ButtonModule } from 'primeng/button';
import { ConfirmationService, MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
import { CompanyAddComponent } from '../company-add/company-add.component';
import { CompanyService } from '@/pages/service/company.service';

@Component({
    selector: 'app-company-list',
    standalone: true,
    imports: [CommonModule, DialogModule, ButtonModule, CompanyAddComponent, ConfirmDialogModule, ToastModule],
    templateUrl: './company-list.component.html',
    styleUrls: ['./company-list.component.css'],
    providers: [ConfirmationService, MessageService]
})
export class CompanyListComponent implements OnInit {
    @ViewChild(CompanyAddComponent) companyAddComponent!: CompanyAddComponent;

    showAddModal = false;
    loading = false;
    error: string | null = null;
    companies: Array<{
        id: number;
        name: string;
        email: string;
        phone: string;
        address: string;
        registrationNo: string;
        productCertificate?: { id: number; name: string };
        status?: { id: number; name: string };
        listedCountry?: { id: number; name: string };
    }> = [];

    constructor(
        private companyService: CompanyService,
        private confirmationService: ConfirmationService,
        private messageService: MessageService
    ) {}

    ngOnInit(): void {
        this.loadCompanies();
    }

    private loadCompanies() {
        this.companyService.getCompanies().subscribe({
            next: (res: any) => {
                const list = Array.isArray(res) ? res : (res?.companies ?? res?.Companies ?? []);
                this.companies = list.map((company: any) => ({
                    id: company.id ?? company.Id ?? 0,
                    name: company.companyName ?? company.CompanyName ?? '',
                    email: company.companyEmail ?? company.CompanyEmail ?? '',
                    phone: company.companyPhone ?? company.CompanyPhone ?? '',
                    address: company.companyAddress ?? company.CompanyAddress ?? '',
                    registrationNo: company.registrationNo ?? company.RegistrationNo ?? '',
                    productCertificate: company.productCertificate ?? company.ProductCertificate,
                    status: company.companyStatus ?? company.CompanyStatus,
                    listedCountry: company.listedCountry ?? company.ListedCountry
                }));
            },
            error: (err) => {
                console.log('Company list error:', err);
            }
        });
    }

    openAddModal() {
        this.showAddModal = true;
        if (this.companyAddComponent) {
            this.companyAddComponent.editMode = false;
            this.companyAddComponent.companyId = null;
            this.companyAddComponent.companyForm.reset();
            this.companyAddComponent.submitted = false;
        }
    }

    openEditModal(company: any) {
        this.showAddModal = true;
        if (this.companyAddComponent) {
            this.companyAddComponent.editMode = true;
            this.companyAddComponent.companyId = company.id;
            this.companyAddComponent.companyForm.patchValue({
                companyName: company.name,
                companyEmail: company.email,
                companyPhone: company.phone,
                companyAddress: company.address,
                registrationNo: company.registrationNo,
                productCertificate: company.productCertificate?.id ?? company.productCertificate?.Id,
                status: company.status?.id ?? company.status?.Id,
                listedCountry: company.listedCountry?.id ?? company.listedCountry?.Id
            });
            this.companyAddComponent.submitted = false;
        }
    }

    closeAddModal() {
        this.showAddModal = false;
    }

    onCompanySaved(newCompanyData: any) {
        this.loadCompanies();
        this.closeAddModal();
    }

    deleteCompany(event: Event, id: number) {
        this.confirmationService.confirm({
            target: event.target as EventTarget,
            message: 'Do you want to delete this company?',
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
                this.companyService.deleteCompany(id).subscribe({
                    next: () => {
                        this.companies = this.companies.filter((c) => c.id !== id);
                        this.loading = false;
                        this.messageService.add({ severity: 'info', summary: 'Confirmed', detail: 'Company delete successful' });
                    },
                    error: (err) => {
                        console.error('Error deleting company:', err);
                        this.error = err.error?.Message || 'Failed to delete company. Please try again.';
                        this.loading = false;
                        this.messageService.add({ severity: 'error', summary: 'Delete failed', detail: this.error ?? 'Failed to delete company. Please try again.' });
                    }
                });
            },
            reject: () => {
                this.messageService.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected the deletion of this company.' });
            }
        });
    }
}
