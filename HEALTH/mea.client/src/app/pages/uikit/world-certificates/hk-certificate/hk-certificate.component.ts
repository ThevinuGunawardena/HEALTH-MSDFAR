import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { RadioButton } from 'primeng/radiobutton';
import {
    HkCertificateView,
    HkCertificateProductView,
    CertificateRequestService,
    CreateHkCertificatePayload,
    VetFormFieldResponse,
    VetProductFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { TableModule } from 'primeng/table';
import { Select } from 'primeng/select';
import { UserService, User } from '@/pages/service/user.service';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { TooltipModule } from 'primeng/tooltip';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-hk-certificate',
    standalone: true,
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        DatePicker,
        ToastModule,
        TableModule,
        Select,
        RadioButton,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './hk-certificate.component.html',
    styleUrls: ['./hk-certificate.component.css', '../certificate-print.css']
})
export class HkCertificateComponent implements OnInit {
    form: FormGroup;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;
    isApproved = false;
    isSubmitted = false;
    certificateRequestId: number | null = null;

    // View mode: 'attachment' (3 pages with attachment table), 'single' (2 pages with direct table)
    viewMode: 'attachment' | 'single' = 'attachment';

    userOptions: { label: string; value: string }[] = [];
    users: User[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    previousSignatoryUserId: string | null = null;

    isDraggingSignature = false;
    safeSignaturePdfUrl: SafeResourceUrl | null = null;

    onlyDigits(event: KeyboardEvent): boolean {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    onlyDecimals(event: KeyboardEvent): boolean {
        const charCode = event.which ? event.which : event.keyCode;
        const input = (event.target as HTMLInputElement).value || '';
        if (charCode === 46) {
            if (input.indexOf('.') !== -1) {
                event.preventDefault();
                return false;
            }
            return true;
        }
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    constructor(
        private fb: FormBuilder,
        private messageService: MessageService,
        private certificateRequestService: CertificateRequestService,
        private route: ActivatedRoute,
        private router: Router,
        private userService: UserService,
        private location: Location,
        private authService: AuthService,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['attachment'],
            certificateRequestId: [null],
            identificationNumber: ['TC 4293'],
            countryOfDispatch: ['SRI LANKA'],
            competentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCE'],
            certifyingBody: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],

            containerNumber: [''],
            sealNumber: [''],
            sealIdentificationNumber: [''],
            storageTemperature: [''],

            approvalNumber: ['DFAR/FPE/98/59'],
            processingEstablishment: [''],
            provenanceDetails: [''],

            consignorName: [''],
            consignorAddress: [''],
            placeOfDispatch: ['COLOMBO – SRI LANKA'],

            destinationCountryPlace: ['HONG KONG'],
            meansOfTransport: ['AIR FREIGHT'],
            consigneeName: [''],
            consigneeAddress: [''],

            dateOfAttachment: [new Date()],
            attachmentRegNo: ['DFAR/FPE/98/59'],

            placeOfIssue: ['COLOMBO – SRI LANKA'],
            dateOfIssue: [new Date()],
            officialStamp: [''],
            officialSignature: [''],
            signatoryUserId: [null, Validators.required],
            signatoryName: [''],
            qualification: [''],
            officerTel: ['+94-11-2449170, 2472186, 2472192'],
            officerFax: ['+94-11-2424086, 2449170'],
            officerEmail: ['dgdfar@gmail.com'],

            products: this.fb.array([])
        });

        this.form.get('viewMode')?.valueChanges.subscribe((mode: 'attachment' | 'single') => {
            this.viewMode = mode;
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    get productRows() {
        return (this.form.get('products') as FormArray).getRawValue() ?? [];
    }

    loadDefaultAttachmentProducts(): void {
        const defaults = [
            { desc: 'Black Grouper Fish', species: 'Epinephelus malabaricus,', proc: 'WILD CAUGHT' },
            { desc: 'King Fish.', species: 'Scomberomorus commerson', proc: 'WILD CAUGHT' },
            { desc: 'Brown Grouper Fish', species: 'Epinephelus fuscoguttatus', proc: 'WILD CAUGHT' },
            { desc: 'Plain Grouper', species: 'Epinephelus diacanthus', proc: 'WILD CAUGHT' },
            { desc: 'Pomfret fish', species: 'Pampus argenteus', proc: 'WILD CAUGHT' },
            { desc: 'Spotted Grouper', species: 'Epinephelus chlorostigma', proc: 'WILD CAUGHT' },
            { desc: 'Parrot Fish', species: 'Scarus sp', proc: 'WILD CAUGHT' },
            { desc: 'Lady Fish', species: 'Sillago sihama', proc: 'WILD CAUGHT' },
            { desc: 'Threadfin Bream', species: 'Nemipterus spp', proc: 'WILD CAUGHT' },
            { desc: 'Snapper Fish', species: 'Lutjanus fulviflamma', proc: 'WILD CAUGHT' },
            { desc: 'Indian Salmon', species: 'Eleutheronema tetradactylum', proc: 'WILD CAUGHT' }
        ];

        this.products.clear();
        defaults.forEach((item) => {
            this.addProduct(item.desc, item.species, item.proc);
        });
    }

    clearAttachmentQuantities(): void {
        this.products.controls.forEach((ctrl) => {
            ctrl.patchValue({
                numberOfPackages: null,
                netWeight: null,
                packagingType: '',
                lotCode: ''
            });
        });
    }

    createProductRow(
        desc: string = '',
        species: string = '',
        proc: string = 'FROZEN WILD CAUGHT',
        pkg: string = '',
        lot: string = '',
        packages: number | null = null,
        pkgUnit: string = 'CTNS',
        weight: number | null = null,
        weightUnit: string = 'KGS'
    ): FormGroup {
        return this.fb.group({
            description: [desc],
            species: [species],
            processingType: [proc],
            packagingType: [pkg],
            lotCode: [lot],
            numberOfPackages: [packages],
            packagesUnit: [pkgUnit],
            netWeight: [weight],
            netWeightUnit: [weightUnit]
        });
    }

    addProduct(
        desc: string = '',
        species: string = '',
        proc: string = 'WILD CAUGHT',
        pkg: string = '',
        lot: string = '',
        packages: number | null = null,
        pkgUnit: string = 'CTNS',
        weight: number | null = null,
        weightUnit: string = 'KGS'
    ): void {
        this.products.push(this.createProductRow(desc, species, proc, pkg, lot, packages, pkgUnit, weight, weightUnit));
    }

    removeProduct(index: number): void {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
    }

    updateProductField(index: number, field: string, value: any): void {
        const control = this.products.at(index)?.get(field);
        if (control) {
            if (field === 'numberOfPackages') {
                const num = value === '' || value == null ? null : parseInt(value, 10);
                control.setValue(isNaN(num as any) ? null : num);
            } else if (field === 'netWeight') {
                const num = value === '' || value == null ? null : parseFloat(value);
                control.setValue(isNaN(num as any) ? null : num);
            } else {
                control.setValue(value);
            }
        }
    }

    calculateTotalPackages(): number {
        return this.products.controls.reduce((sum, ctrl) => {
            const val = parseInt(ctrl.get('numberOfPackages')?.value, 10) || 0;
            return sum + val;
        }, 0);
    }

    calculateTotalNetWeight(): number {
        return this.products.controls.reduce((sum, ctrl) => {
            const val = parseFloat(ctrl.get('netWeight')?.value) || 0;
            return sum + val;
        }, 0);
    }

    loadDefaultSingleProducts(): void {
        const defaults = [
            { desc: 'TIGER SHRIMP', species: 'Penaeus monodon', proc: 'FROZEN\nWILD CAUGHT', pkgs: 30, weight: 272.40 },
            { desc: 'FROZEN FRESH\nWATER PRAWNS', species: 'Macrobrachium\nrosenbergii', proc: 'FROZEN\nWILD CAUGHT', pkgs: 43, weight: 258.00 },
            { desc: 'FROZEN CRAB MEAT', species: 'Portunus pelagicus', proc: 'FROZEN\nWILD CAUGHT', pkgs: 277, weight: 1385.00 },
            { desc: 'FROZEN SLIPPER\nLOBSTER', species: 'Thenus orientalis', proc: 'FROZEN WILD\nCAUGHT', pkgs: 71, weight: 426.00 }
        ];

        this.products.clear();
        defaults.forEach((item) => {
            this.addProduct(item.desc, item.species, item.proc, '', '', item.pkgs, 'CTNS', item.weight, 'KGS');
        });
    }

    ngOnInit(): void {
        this.isCompany = (this.authService.getUserRole() || '').toLowerCase() === 'company';
        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        this.userService.getAllUsers().subscribe((users) => {
            this.users = users;
            this.userOptions = users
                .filter((u) => u.roleName && (u.roleName.toLowerCase() === 'user' || u.roleName.toLowerCase() === 'admin'))
                .map((u) => ({ label: u.name, value: u.id }));
        });

        this.route.queryParams.subscribe((params) => {
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
            }

            if (params['ref']) {
                this.form.patchValue({ identificationNumber: params['ref'] });
            }

            const requestId = params['requestId'];
            if (requestId) {
                this.certificateRequestId = Number(requestId);
                this.checkRequestApproval(this.certificateRequestId);
                this.form.patchValue({ certificateRequestId: this.certificateRequestId });
                this.loadSavedCertificateData(this.certificateRequestId);
            } else {
                if (this.viewMode === 'single') {
                    this.loadDefaultSingleProducts();
                } else {
                    this.loadDefaultAttachmentProducts();
                }
            }

            if (this.viewOnly) {
                this.form.disable();
            } else {
                this.form.enable();
                if (this.isCompany) {
                    this.form.get('signatoryUserId')?.clearValidators();
                    this.form.get('signatoryUserId')?.updateValueAndValidity();
                }
            }
        });
    }

    onViewModeChange(mode: 'attachment' | 'single') {
        this.viewMode = mode;
        this.form.get('viewMode')?.setValue(mode);

        if (!this.certificateRequestId) {
            if (mode === 'single') {
                this.form.patchValue({
                    identificationNumber: 'TB 5752',
                    meansOfTransport: 'SEA FREIGHT',
                    approvalNumber: 'DFAR / FPE /98/08',
                    processingEstablishment: 'ALPEX MARINE (PVT) LTD, 68 CANAL ROAD, HEDALA, WATTALA, SRI LANKA.',
                    consignorAddress: 'ALPEX MARINE (PVT) LTD, 68 CANAL ROAD, HEDALA, WATTALA, SRI LANKA',
                    consigneeAddress: 'INDOGUNA LORDLY COMPANY LIMITED., UNIT B,7/F, SING MEI INDUSTRIAL BUILDING, 29-37 KWAI WING ROAD, KWAI CHUNG,NEW TERRITORIES, HONGKONG.',
                    containerNumber: 'CONTAINER NO: OOLU 3956208',
                    sealNumber: 'SEAL NO: OOL KAV0976',
                    storageTemperature: '-20'
                });
                this.loadDefaultSingleProducts();
            } else {
                this.form.patchValue({
                    identificationNumber: 'TC 4293',
                    meansOfTransport: 'AIR FREIGHT',
                    approvalNumber: 'DFAR/FPE/98/59',
                    attachmentRegNo: 'DFAR/FPE/98/59',
                    processingEstablishment: 'ISABELA SEA FOODS, NO. 14/2, DUNGALPITIYA, THALAHENA, NEGOMBO, SRI LANKA',
                    consignorAddress: 'ISABELA SEA FOODS, NO. 14/2, DUNGALPITIYA, THALAHENA, NEGOMBO, SRI LANKA',
                    consigneeAddress: 'REGAL OCEAN TRADING COMPANY LTD., RM 916B, 9F SOUTH MARK, TOWER B, 11 YIP HING STREET, WONG CHUK HANG, ABERDEEN, HONGKONG.',
                    containerNumber: 'CONTAINER NO:',
                    sealNumber: 'SEAL NO:',
                    storageTemperature: '0 to -4'
                });
                this.loadDefaultAttachmentProducts();
            }
        }
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateRequestService.getHkCertificateByRequestId(requestId).subscribe({
            next: (data: HkCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                const mode = (data.certificateType as 'attachment' | 'single') || 'attachment';
                this.viewMode = mode;
                if (data.id || data.identificationNumber || data.consignorName) {
                    this.isSubmitted = true;
                }

                this.form.patchValue({
                    viewMode: mode,
                    identificationNumber: data.identificationNumber || '',
                    countryOfDispatch: data.countryOfDispatch || 'SRI LANKA',
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCE',
                    certifyingBody: data.certifyingBody || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',

                    containerNumber: data.containerNumber || 'CONTAINER NO:',
                    sealNumber: data.sealNumber || 'SEAL NO:',
                    sealIdentificationNumber: data.sealIdentificationNumber || '',
                    storageTemperature: data.storageTemperature || '-20',

                    approvalNumber: data.approvalNumber || 'DFAR / FPE /98/08',
                    processingEstablishment: data.processingEstablishment || '',
                    provenanceDetails: data.provenanceDetails || '',

                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    placeOfDispatch: data.placeOfDispatch || 'COLOMBO – SRI LANKA',

                    destinationCountryPlace: data.destinationCountryPlace || 'HONG KONG',
                    meansOfTransport: data.meansOfTransport || 'AIR FREIGHT',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',

                    dateOfAttachment: data.dateOfAttachment ? new Date(data.dateOfAttachment) : new Date(),
                    attachmentRegNo: data.attachmentRegNo || 'DFAR/FPE/98/59',

                    placeOfIssue: data.placeOfIssue || 'COLOMBO – SRI LANKA',
                    dateOfIssue: data.dateOfIssue ? new Date(data.dateOfIssue) : new Date(),
                    officialSignature: data.officialSignature || '',
                    signatoryUserId: data.signatoryUserId || null,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || '',
                    officerTel: data.officerTel || '+94-11-2449170, 2472186, 2472192',
                    officerFax: data.officerFax || '+94-11-2424086, 2449170',
                    officerEmail: data.officerEmail || 'dgdfar@gmail.com'
                });

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p: HkCertificateProductView) => {
                        this.addProduct(
                            p.description || '',
                            p.species || '',
                            p.processingType || 'WILD CAUGHT',
                            p.packagingType || '',
                            p.lotCode || '',
                            p.numberOfPackages,
                            p.packagesUnit || 'CTNS',
                            p.netWeight,
                            p.netWeightUnit || 'KGS'
                        );
                    });
                } else {
                    this.loadDefaultAttachmentProducts();
                }

                this.updateSafeSignatureUrl();

                if (this.viewOnly) {
                    this.form.disable();
                }
            },
            error: () => {
                this.loadVetFormData(requestId);
            }
        });
    }

    private loadVetFormData(requestId: number) {
        this.certificateRequestService.getVetFormByRequestId(requestId).subscribe({
            next: (data: VetFormFieldResponse) => {
                if (!data) return;

                const defaultRegNo = data.approvalNo || 'DFAR/FPE/98/59';
                const plantAddress = `${data.processingEstName || data.consignorName || ''}\n${data.processingEstAddress || data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim();
                const isShip = data.transportShip ?? false;
                const meansTransport = isShip ? 'SEA FREIGHT' : 'AIR FREIGHT';
                const certNo = data.healthCertNo || data.newHC || 'TC 4293';

                this.form.patchValue({
                    identificationNumber: certNo,
                    countryOfDispatch: data.countryOrigin || 'SRI LANKA',
                    competentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCE',
                    certifyingBody: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',

                    containerNumber: data.containerId ? `CONTAINER NO: ${data.containerId}` : 'CONTAINER NO:',
                    sealNumber: data.sealNumber ? `SEAL NO: ${data.sealNumber}` : 'SEAL NO:',
                    sealIdentificationNumber: data.sealNumber ? `SEAL NO: ${data.sealNumber}` : '',
                    storageTemperature: data.temperatureFrozen ? '-20' : '0 to -4',

                    approvalNumber: defaultRegNo,
                    processingEstablishment: plantAddress,
                    provenanceDetails: `${defaultRegNo}\n${plantAddress}`,

                    consignorName: data.consignorName || '',
                    consignorAddress: `${data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim(),
                    placeOfDispatch: 'COLOMBO – SRI LANKA',

                    destinationCountryPlace: 'HONG KONG',
                    meansOfTransport: meansTransport,
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: `${data.consigneeAddress || ''}\n${data.consigneePostal || ''}`.trim(),

                    dateOfAttachment: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    attachmentRegNo: defaultRegNo,

                    placeOfIssue: 'COLOMBO – SRI LANKA',
                    dateOfIssue: new Date(),
                    officerTel: '+94-11-2449170, 2472186, 2472192',
                    officerFax: '+94-11-2424086, 2449170',
                    officerEmail: 'dgdfar@gmail.com'
                });

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p: VetProductFieldResponse) => {
                        const proc = (data.temperatureFrozen ? 'FROZEN ' : '') + 'WILD CAUGHT';
                        const nw = p.netWeight ? parseFloat(p.netWeight) || null : null;
                        const pkgs = p.numPackages ? parseInt(p.numPackages, 10) || null : null;
                        this.addProduct(
                            p.descCommon ? p.descCommon.toUpperCase() : '',
                            p.descScientific || '',
                            proc,
                            p.packagingType || '',
                            data.processingDate || '',
                            pkgs,
                            'CTNS',
                            nw,
                            'KGS'
                        );
                    });
                } else {
                    this.loadDefaultAttachmentProducts();
                }
            },
            error: () => {
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to load initial vet form data' });
            }
        });
    }

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.form.patchValue({ signatoryName: '', qualification: '' });
            return;
        }

        const selected = this.users.find((u) => u.id === userId);
        if (selected) {
            this.pendingSignatoryUserId = selected.id;
            this.pendingSignatoryUserEmail = selected.email;
            this.selectedUserQualification = selected.qualification || 'QUALITY CONTROL OFFICER (GRADE II)';
            this.showPasswordDialog = true;
        }
    }

    onSignatoryConfirmed(officerName: string) {
        if (this.pendingSignatoryUserId) {
            this.form.patchValue({
                signatoryUserId: this.pendingSignatoryUserId,
                signatoryName: officerName,
                qualification: this.selectedUserQualification || 'QUALITY CONTROL OFFICER (GRADE II)'
            });
            this.previousSignatoryUserId = this.pendingSignatoryUserId;
        }
        this.showPasswordDialog = false;
        this.pendingSignatoryUserId = null;
        this.messageService.add({ severity: 'success', summary: 'Verified', detail: 'Officer PIN confirmed successfully.' });
    }

    onSignatoryCanceled() {
        this.form.patchValue({ signatoryUserId: this.previousSignatoryUserId });
        this.showPasswordDialog = false;
        this.pendingSignatoryUserId = null;
    }

    onSubmit(): void {
        if (!this.isCompany && !this.form.get('signatoryUserId')?.value) {
            this.messageService.add({
                severity: 'error',
                summary: 'Signatory Required',
                detail: 'Please select and verify authorized signatory.'
            });
            return;
        }

        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        if (this.form.invalid) {
            this.messageService.add({ severity: 'error', summary: 'Validation Error', detail: 'Please fill all required fields.' });
            return;
        }

        this.isSaving = true;
        const rawValue = this.form.getRawValue();

        const payload: CreateHkCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            certificateType: this.viewMode,
            identificationNumber: rawValue.identificationNumber,
            countryOfDispatch: rawValue.countryOfDispatch,
            competentAuthority: rawValue.competentAuthority,
            certifyingBody: rawValue.certifyingBody,
            containerNumber: rawValue.containerNumber,
            sealNumber: rawValue.sealNumber,
            sealIdentificationNumber: rawValue.sealIdentificationNumber,
            storageTemperature: rawValue.storageTemperature,
            approvalNumber: rawValue.approvalNumber,
            processingEstablishment: rawValue.processingEstablishment,
            provenanceDetails: rawValue.provenanceDetails,
            consignorName: rawValue.consignorName,
            consignorAddress: rawValue.consignorAddress,
            placeOfDispatch: rawValue.placeOfDispatch,
            destinationCountryPlace: rawValue.destinationCountryPlace,
            meansOfTransport: rawValue.meansOfTransport,
            consigneeName: rawValue.consigneeName,
            consigneeAddress: rawValue.consigneeAddress,
            dateOfAttachment: rawValue.dateOfAttachment ? toLocalISOString(rawValue.dateOfAttachment) : null,
            attachmentRegNo: rawValue.attachmentRegNo,
            placeOfIssue: rawValue.placeOfIssue,
            dateOfIssue: rawValue.dateOfIssue ? toLocalISOString(rawValue.dateOfIssue) : null,
            officialSignature: rawValue.officialSignature,
            signatoryUserId: rawValue.signatoryUserId,
            signatoryName: rawValue.signatoryName,
            qualification: rawValue.qualification,
            officerTel: rawValue.officerTel,
            officerFax: rawValue.officerFax,
            officerEmail: rawValue.officerEmail,
            products: (rawValue.products || []).map((p: any) => ({
                description: p.description,
                species: p.species,
                processingType: p.processingType,
                packagingType: p.packagingType,
                lotCode: p.lotCode,
                numberOfPackages: p.numberOfPackages ? parseInt(p.numberOfPackages, 10) : null,
                packagesUnit: p.packagesUnit || 'CTNS',
                netWeight: p.netWeight ? parseFloat(p.netWeight) : null,
                netWeightUnit: p.netWeightUnit || 'KGS'
            }))
        };

        this.certificateRequestService.submitHkCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Hong Kong Certificate saved successfully'
                });
            },
            error: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to save Hong Kong Certificate'
                });
            }
        });
    }

    isPdf(dataUrl?: string | null): boolean {
        if (!dataUrl) return false;
        return dataUrl.startsWith('data:application/pdf') || dataUrl.toLowerCase().endsWith('.pdf');
    }

    updateSafeSignatureUrl() {
        const sig = this.form.get('officialSignature')?.value;
        if (sig && this.isPdf(sig)) {
            this.safeSignaturePdfUrl = this.sanitizer.bypassSecurityTrustResourceUrl(sig);
        } else {
            this.safeSignaturePdfUrl = null;
        }
    }

    onSignatureFileSelected(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            this.processSignatureFile(input.files[0]);
            input.value = '';
        }
    }

    onSignatureDrop(event: DragEvent) {
        event.preventDefault();
        this.isDraggingSignature = false;
        if (this.viewOnly) return;
        if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
            this.processSignatureFile(event.dataTransfer.files[0]);
        }
    }

    onSignatureDragOver(event: DragEvent) {
        event.preventDefault();
        if (!this.viewOnly) this.isDraggingSignature = true;
    }

    onSignatureDragLeave(event: DragEvent) {
        event.preventDefault();
        this.isDraggingSignature = false;
    }

    onSignaturePaste(event: ClipboardEvent) {
        if (this.viewOnly) return;
        const items = event.clipboardData?.items;
        if (items) {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1 || items[i].type.indexOf('pdf') !== -1) {
                    const file = items[i].getAsFile();
                    if (file) {
                        this.processSignatureFile(file);
                        event.preventDefault();
                        break;
                    }
                }
            }
        }
    }

    processSignatureFile(file: File) {
        const isValid = file.type.startsWith('image/') || file.type === 'application/pdf';
        if (!isValid) {
            this.messageService.add({
                severity: 'error',
                summary: 'Invalid File',
                detail: 'Please upload an image (PNG, JPG, Screenshot) or PDF file.'
            });
            return;
        }

        if (file.size > 15 * 1024 * 1024) {
            this.messageService.add({
                severity: 'error',
                summary: 'File Too Large',
                detail: 'File size should not exceed 15MB.'
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            const result = reader.result as string;
            this.form.patchValue({ officialSignature: result });
            this.updateSafeSignatureUrl();
            this.messageService.add({
                severity: 'success',
                summary: 'Uploaded Successfully',
                detail: 'Official Signature uploaded.'
            });
        };
        reader.onerror = () => {
            this.messageService.add({
                severity: 'error',
                summary: 'Read Error',
                detail: 'Failed to read the file.'
            });
        };
        reader.readAsDataURL(file);
    }

    removeSignature(event?: Event) {
        if (event) event.stopPropagation();
        this.form.patchValue({ officialSignature: '' });
        this.safeSignaturePdfUrl = null;
    }

    private checkRequestApproval(requestId: number): void {
        this.certificateRequestService.getRequestById(requestId).subscribe({
            next: (req) => {
                if (req) {
                    const st = typeof req.status === 'string' ? req.status.toLowerCase() : (req.status === 1 ? 'confirmed' : 'pending');
                    this.isApproved = (st === 'confirmed' || st === 'approved' || req.status === 1);
                }
            },
            error: () => {}
        });
    }

    printCertificate(): void {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }

        const ref = this.form.get('identificationNumber')?.value || 'Draft';
        let pdfName = '';
        if (this.viewMode === 'attachment') {
            pdfName = `${ref}_Hong_Kong_Aquatic_Animals_And_Aquatic_Products_Attachment_Health_Certificate.pdf`;
        } else {
            pdfName = `${ref}_Hong_Kong_Aquatic_Animals_And_Aquatic_Products_Health_Certificate.pdf`;
        }

        const originalTitle = document.title;
        document.title = pdfName;
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }

    goBack(): void {
        this.location.back();
    }
}
