import { CommonModule } from '@angular/common';
import { Component, Output, EventEmitter, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { InputText } from 'primeng/inputtext';
import { Checkbox } from 'primeng/checkbox';
import { CompanyService } from '@/pages/service/company.service';
import { MessageService } from 'primeng/api';
import { Toast } from 'primeng/toast';
import { Select } from 'primeng/select';

@Component({
    selector: 'app-company-add',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, InputText, Toast, Select],
    templateUrl: './company-add.component.html',
    styleUrls: ['./company-add.component.css'],
    providers: [MessageService]
})
export class CompanyAddComponent implements OnInit {
    @Output() companySaved = new EventEmitter<any>();
    @Output() companyCancelled = new EventEmitter<void>();
    @Input() editMode = false;
    @Input() companyId: number | null = null;

    companyForm: FormGroup;
    loading = false;
    submitted = false;
    certificateOptions: { label: string; value: number }[] = [];
    countryOptions: { label: string; value: number }[] = [];
    statusOptions: { label: string; value: number }[] = [];

    constructor(
        private fb: FormBuilder,
        private companyService: CompanyService,
        private messageService: MessageService
    ) {
        this.companyForm = this.fb.group({
            companyName: ['', Validators.required],
            companyEmail: ['', [Validators.required, Validators.email]],
            companyPhone: ['', Validators.required],
            companyAddress: ['', Validators.required],
            registrationNo: ['', Validators.required],
            productCertificate: [null],
            status: [null, Validators.required],
            listedCountry: [null, Validators.required]
        });
    }

    ngOnInit(): void {
        this.fetchProductCertificates();
        this.fetchCompanyStatuses();
        this.fetchCountries();
    }

    fetchProductCertificates() {
        this.companyService.getProductCertificates().subscribe({
            next: (certificates) => {
                this.certificateOptions = certificates.map((c) => ({ label: c.name, value: c.id }));
            },
            error: (err) => {
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to load product certificates.' });
            }
        });
    }

    fetchCompanyStatuses() {
        this.companyService.getCompanyStatuses().subscribe({
            next: (statuses) => {
                this.statusOptions = statuses.map((s) => ({ label: s.name, value: s.id }));
            },
            error: (err) => {
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to load company statuses.' });
            }
        });
    }

    fetchCountries() {
        this.companyService.getListedCountries().subscribe({
            next: (countries) => {
                this.countryOptions = countries.map((c) => ({ label: c.name, value: c.id }));
            },
            error: (err) => {
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to load countries.' });
            }
        });
    }

    submitForm() {
        this.submitted = true;
        if (this.companyForm.valid) {
            this.loading = true;
            const apiCall = this.editMode && this.companyId ? this.companyService.editCompany(this.companyId, this.companyForm.value) : this.companyService.createCompany(this.companyForm.value);

            apiCall.subscribe({
                next: (res: any) => {
                    const company = res?.company ?? res?.Company ?? this.companyForm.value;
                    this.companySaved.emit({
                        companyName: company.companyName ?? company.CompanyName,
                        companyEmail: company.companyEmail ?? company.CompanyEmail,
                        companyPhone: company.companyPhone ?? company.CompanyPhone,
                        companyAddress: company.companyAddress ?? company.CompanyAddress,
                        registrationNo: company.registrationNo ?? company.RegistrationNo
                    });
                    this.messageService.add({
                        severity: 'success',
                        summary: this.editMode ? 'Company updated' : 'Company created',
                        detail: this.editMode ? 'Company updated successfully.' : 'Company saved successfully.'
                    });
                    this.loading = false;
                    this.companyForm.reset();
                    this.submitted = false;
                },
                error: (err) => {
                    const detail = err?.error?.message ?? (this.editMode ? 'Failed to update company.' : 'Failed to create company.');
                    this.messageService.add({
                        severity: 'error',
                        summary: this.editMode ? 'Update failed' : 'Create failed',
                        detail
                    });
                    console.log('Company create error:', err);
                    this.loading = false;
                }
            });
        } else {
            this.companyForm.markAllAsTouched();
        }
    }

    onCancel() {
        this.companyForm.reset();
        this.submitted = false;
        this.companyCancelled.emit();
    }
}
