import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, FormArray, Validators } from '@angular/forms';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { DatePicker } from 'primeng/datepicker';
import { TextareaModule } from 'primeng/textarea';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { TooltipModule } from 'primeng/tooltip';
import { RadioButton } from 'primeng/radiobutton';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { UserService, User } from '@/pages/service/user.service';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { CertificateRequestService, CreateMvCertificatePayload, MvCertificateView } from 'src/app/pages/service/certificate-request.service';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-mv-certificate',
    standalone: true,
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        ButtonModule,
        InputTextModule,
        DatePicker,
        TextareaModule,
        ToastModule,
        Select,
        RadioButton,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './mv-certificate.component.html',
    styleUrls: ['./mv-certificate.component.css', '../certificate-print.css']
})
export class MvCertificateComponent implements OnInit {
    form: FormGroup;
    viewMode: 'generic' | 'letterhead' = 'generic';
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;

    isCompany = false;
    isApproved = false;
    users: User[] = [];
    userOptions: { label: string; value: string }[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail = '';
    previousSignatoryUserId: string | null = null;

    isDraggingSignature = false;
    isDraggingStamp = false;
    safeSignaturePdfUrl: SafeResourceUrl | null = null;
    safeStampPdfUrl: SafeResourceUrl | null = null;

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
        private route: ActivatedRoute,
        private router: Router,
        private certificateRequestService: CertificateRequestService,
        private userService: UserService,
        private messageService: MessageService,
        private location: Location,
        private authService: AuthService,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['generic'],
            consignorExporter: [''],
            certificateNumber: ['TC 4683'],
            myRef: ['TC 4683'],
            yourRef: [''],
            competentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            certifyingBody: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            consigneeImporter: [''],
            countryOfOrigin: ['INDIA'],
            countryOfOriginISO: ['IN'],
            countryOfDestination: ['MALDIVES – MALE'],
            countryOfDestinationISO: ['MV'],
            placeOfLoading: ['COLOMBO - SRI LANKA'],
            meansOfTransportText: ['AIR FREIGHT'],
            pointsOfEntry: ['Velana International Airport - MALDIVES'],
            conditionsOfStorage: ['FROZEN (-18 °C)'],
            totalQuantity: [''],
            sealNumber: [''],
            totalNumberOfPackages: [''],
            approvalNumberOfEstablishments: [''],
            descriptionOfCommodity: [''],

            // Letterhead Format Controls
            itemName: [''],
            packagesCountDesc: [''],
            netWeightDesc: [''],
            storageTempDesc: ['(0°C)-(-18) °C'],
            dispatchedFrom: ['COLOMBO – SRI LANKA'],
            dispatchedTo: ['Male Velana International Airport, Maldives'],
            dispatchTransport: ['BY AIR FREIGHT'],
            designation: ['QUALITY CONTROL OFFICER (GRADE II)'],

            // Shared Signatory Fields
            certifyingOfficerName: ['H.M.U. BANDARA'],
            certifyingOfficerDate: [new Date('2026-08-25')],
            signatoryUserId: [null, Validators.required],
            signatoryName: ['H.M.U. BANDARA'],
            qualification: ['QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'],
            companyRegistrationNo: [''],
            officialStamp: [''],
            officialSignature: [''],
            products: this.fb.array([this.createProductRow()]),
            productsSecond: this.fb.array([this.createProductRowSecond()])
        });
    }

    ngOnInit() {
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

        this.form.get('viewMode')?.valueChanges.subscribe((mode: 'generic' | 'letterhead') => {
            if (mode) {
                this.viewMode = mode;
            }
        });

        this.route.queryParams.subscribe((params) => {
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
            }
            if (params['ref']) {
                this.form.patchValue({ certificateNumber: params['ref'], myRef: params['ref'] });
            }
            if (params['requestId']) {
                this.certificateRequestId = Number(params['requestId']);
                this.checkRequestApproval(this.certificateRequestId);
                this.loadSavedCertificateData(this.certificateRequestId);
            } else {
                this.loadSampleBoxed();
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

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    get productsSecond(): FormArray {
        return this.form.get('productsSecond') as FormArray;
    }

    private createProductRow() {
        return this.fb.group({
            no: ['1'],
            natureOfCommodity: [''],
            species: [''],
            purposeOfUse: ['FOR HUMAN CONSUMPTION']
        });
    }

    private createProductRowSecond() {
        return this.fb.group({
            no: ['1'],
            nameOfTheProduct: [''],
            lotIdentifier: [''],
            typeOfPackaging: [''],
            numberOfPackages: [''],
            netWeight: ['']
        });
    }

    addProductRow(): void {
        this.products.push(this.createProductRow());
    }

    removeProductRow(index: number): void {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
    }

    addProductRowSecond(): void {
        this.productsSecond.push(this.createProductRowSecond());
    }

    removeProductRowSecond(index: number): void {
        if (this.productsSecond.length > 1) {
            this.productsSecond.removeAt(index);
        }
    }

    onViewModeChange(mode: 'generic' | 'letterhead'): void {
        this.viewMode = mode;
        this.form.patchValue({ viewMode: mode });
    }

    /**
     * Load Generic Boxed Model Certificate Sample (TC 4683 - Amagi Foods Supply / Vannamei Shrimps)
     */
    loadSampleBoxed(): void {
        this.viewMode = 'generic';
        this.form.patchValue({
            viewMode: 'generic',
            certificateNumber: 'TC 4683',
            myRef: 'TC 4683',
            yourRef: '',
            consignorExporter: 'AMAGI FOODS SUPPLY (PVT) LTD\nDP-1, INDUSTRIAL ESTATE\nMAITHREE MAWATHA, EKALA',
            competentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            certifyingBody: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            consigneeImporter: 'AMAGI MALDIVES, MALE, MALDIVES ISLANDS',
            countryOfOrigin: 'INDIA',
            countryOfOriginISO: 'IN',
            countryOfDestination: 'MALDIVES – MALE',
            countryOfDestinationISO: 'MV',
            placeOfLoading: 'COLOMBO - SRI LANKA',
            meansOfTransportText: 'AIR FREIGHT',
            pointsOfEntry: 'Velana International Airport - MALDIVES',
            conditionsOfStorage: 'FROZEN (-18 °C)',
            totalQuantity: '',
            sealNumber: '',
            totalNumberOfPackages: '',
            approvalNumberOfEstablishments: 'AMAGI FOODS SUPPLY (PVT) LTD, DP-1, INDUSTRIAL ESTATE\nMAITHREE MAWATHA, EKALA. DFAR/FPE/98/110',
            certifyingOfficerName: 'H.M.U. BANDARA',
            certifyingOfficerDate: new Date('2026-08-25'),
            signatoryName: 'H.M.U. BANDARA',
            designation: 'QUALITY CONTROL OFFICER (GRADE II)',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'
        });

        this.products.clear();
        this.products.push(
            this.fb.group({
                no: ['1'],
                natureOfCommodity: ['Vannamei Shrimps'],
                species: ['Litopenaeus vannamei'],
                purposeOfUse: ['FOR HUMAN CONSUMPTION']
            })
        );

        this.productsSecond.clear();
        this.productsSecond.push(
            this.fb.group({
                no: ['1'],
                nameOfTheProduct: ['Vannamei Shrimps\n(Litopenaeus vannamei)'],
                lotIdentifier: [''],
                typeOfPackaging: ['QUICK FROZEN PRODUCTS ARE\nPACKED IN INNER POLY BAGS\nAND THOSE ARE PACKED\nIN STYROFOAM BOX.'],
                numberOfPackages: [''],
                netWeight: ['']
            })
        );

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Sample',
            detail: 'Loaded Maldives Generic Boxed Certificate sample (TC 4683 - Amagi Foods Supply).'
        });
    }

    /**
     * Load Official Letterhead Certificate Sample (TC 4522 - Sadaharitha Agri / Sheraton Maldives)
     */
    loadSampleLetterhead(): void {
        this.viewMode = 'letterhead';
        this.form.patchValue({
            viewMode: 'letterhead',
            certificateNumber: 'TC 4522',
            myRef: 'TC 4522',
            yourRef: '',
            certifyingOfficerDate: new Date('2026-08-23'),
            itemName: 'Frozen Fish Chinese Rolls (Xiphias Gladius)',
            packagesCountDesc: '02 Styrofoam Box',
            netWeightDesc: '15 kg',
            storageTempDesc: '(0°C)-(-18) °C',
            consignorExporter: 'SADAHARITHA AGRI FARMS & EXPOTERS (PVT) LTD\nNO 6A, ALFRED PLACE, COLOMBO 03, SRI LANKA.',
            consigneeImporter: 'SHERATON MALDIVES FULL MOON RESORT & SPA\nMANTA RESORT HOLDINGS II PVT LTD – (C0447/2022)\nOPERATING REG NO (TRH-05)\nM. #39 ORCHID MAGU, K. MALE\'\nREPUBLIC OF MALDIVES',
            dispatchedFrom: 'COLOMBO – SRI LANKA',
            dispatchedTo: 'Male Velana International Airport, Maldives',
            dispatchTransport: 'BY AIR FREIGHT',
            certifyingOfficerName: 'H.M.U. BANDARA',
            signatoryName: 'H.M.U. BANDARA',
            designation: 'QUALITY CONTROL OFFICER (GRADE II)',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'
        });

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Sample',
            detail: 'Loaded Maldives Official Letterhead Health Certificate sample (TC 4522 - Sadaharitha Agri Farms).'
        });
    }

    loadVetFormData(requestId: number) {
        this.certificateRequestService.getVetFormByRequestId(requestId).subscribe({
            next: (data) => {
                if (!data) return;

                const consignorFull = data.consignorName && data.consignorAddress
                    ? `${data.consignorName}\n${data.consignorAddress}`
                    : (data.consignorName || '');

                const consigneeFull = data.consigneeName && data.consigneeAddress
                    ? `${data.consigneeName}\n${data.consigneeAddress}`
                    : (data.consigneeName || '');

                const approvalFull = data.processingEstName && data.approvalNo
                    ? `${data.processingEstName}. ${data.approvalNo}`
                    : (data.processingEstName || data.approvalNo || '');

                const itemDesc = data.descCommon ? (data.descScientific ? `${data.descCommon} (${data.descScientific})` : data.descCommon) : '';
                const transportMode = data.transportAeroPlane ? 'AIR FREIGHT' : (data.transportShip ? 'SEA FREIGHT' : 'AIR FREIGHT');

                this.form.patchValue({
                    consignorExporter: consignorFull,
                    consigneeImporter: consigneeFull,
                    countryOfOrigin: data.countryOrigin || 'INDIA',
                    countryOfOriginISO: data.countryOriginISO || 'IN',
                    countryOfDestination: data.countryDestination || 'MALDIVES – MALE',
                    countryOfDestinationISO: data.countryDestinationISO || 'MV',
                    placeOfLoading: data.placeOfLoading || 'COLOMBO - SRI LANKA',
                    totalQuantity: data.quantity ? `${data.quantity} kg` : '',
                    pointsOfEntry: data.entryBIP || 'Velana International Airport - MALDIVES',
                    conditionsOfStorage: data.temperature || 'FROZEN (-18 °C)',
                    totalNumberOfPackages: data.numPackages ? `${data.numPackages}` : '',
                    sealNumber: data.containerId || '',
                    approvalNumberOfEstablishments: approvalFull || '',
                    descriptionOfCommodity: itemDesc,
                    meansOfTransportText: transportMode,
                    itemName: itemDesc,
                    packagesCountDesc: data.numPackages ? `${data.numPackages} Styrofoam Box` : '',
                    netWeightDesc: data.quantity ? `${data.quantity} kg` : '',
                    storageTempDesc: data.temperature || 'FROZEN (-18 °C)',
                    dispatchedFrom: data.placeOfLoading || 'COLOMBO – SRI LANKA',
                    dispatchedTo: data.entryBIP || 'Male Velana International Airport, Maldives',
                    dispatchTransport: `BY ${transportMode}`
                });

                if (data.products && data.products.length > 0) {
                    this.products.clear();
                    this.productsSecond.clear();
                    data.products.forEach((p: any, idx: number) => {
                        this.products.push(
                            this.fb.group({
                                no: [(idx + 1).toString()],
                                natureOfCommodity: [p.descCommon || ''],
                                species: [p.descScientific || ''],
                                purposeOfUse: ['FOR HUMAN CONSUMPTION']
                            })
                        );
                        this.productsSecond.push(
                            this.fb.group({
                                no: [(idx + 1).toString()],
                                nameOfTheProduct: [p.descCommon ? (p.descScientific ? `${p.descCommon}\n(${p.descScientific})` : p.descCommon) : ''],
                                lotIdentifier: [''],
                                typeOfPackaging: [''],
                                numberOfPackages: [p.numPackages || ''],
                                netWeight: [p.netWeight || '']
                            })
                        );
                    });
                }
            },
            error: () => {
                this.messageService.add({ severity: 'warn', summary: 'Vet Form', detail: 'Could not auto-fill from Vet Form' });
            }
        });
    }

    loadSavedCertificateData(requestId: number) {
        this.certificateRequestService.getMvCertificateByRequestId(requestId).subscribe({
            next: (data: MvCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                if (data.certifyingOfficerDate) {
                    data.certifyingOfficerDate = new Date(data.certifyingOfficerDate) as any;
                }

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach(() => this.products.push(this.createProductRow()));
                } else {
                    this.products.push(this.createProductRow());
                }

                this.productsSecond.clear();
                if (data.productsSecond && data.productsSecond.length > 0) {
                    data.productsSecond.forEach(() => this.productsSecond.push(this.createProductRowSecond()));
                } else {
                    this.productsSecond.push(this.createProductRowSecond());
                }

                const mode: 'generic' | 'letterhead' = (data.certificateType === 'letterhead' || data.certificateType === 'letter') ? 'letterhead' : 'generic';
                this.viewMode = mode;

                this.form.patchValue({
                    ...data,
                    viewMode: mode,
                    myRef: data.certificateNumber || '',
                    itemName: data.descriptionOfCommodity || '',
                    packagesCountDesc: data.totalNumberOfPackages || (data.numberOfPackages ? `${data.numberOfPackages}` : ''),
                    netWeightDesc: data.totalQuantity || (data.netWeight ? `${data.netWeight} kg` : ''),
                    storageTempDesc: data.conditionsOfStorage || 'FROZEN (-18 °C)',
                    dispatchedFrom: data.placeOfLoading || 'COLOMBO - SRI LANKA',
                    dispatchedTo: data.pointsOfEntry || 'Velana International Airport - MALDIVES',
                    dispatchTransport: data.meansOfTransport || 'BY AIR FREIGHT',
                    designation: 'QUALITY CONTROL OFFICER (GRADE II)',
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || ''
                });
                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();
                this.selectedUserQualification = data.qualification || null;
                this.previousSignatoryUserId = data.signatoryUserId || null;

                if (!this.viewOnly) {
                    this.form.enable();
                }
            },
            error: () => {
                this.loadVetFormData(requestId);
            }
        });
    }

    onSubmit() {
        if (!this.isCompany && !this.form.get('signatoryUserId')?.value) {
            this.messageService.add({ severity: 'error', summary: 'Signatory Required', detail: 'Please select and verify an authorized signatory.' });
            return;
        }

        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        if (!this.form.valid) {
            this.form.markAllAsTouched();
            this.messageService.add({ severity: 'error', summary: 'Validation Error', detail: 'Please fill all required fields.' });
            return;
        }

        this.isSaving = true;
        const formValue = this.form.getRawValue();

        if (formValue.productsSecond) {
            formValue.productsSecond = formValue.productsSecond.map((p: any) => ({
                ...p,
                numberOfPackages: p.numberOfPackages ? parseInt(p.numberOfPackages, 10) : null,
                netWeight: p.netWeight ? p.netWeight.toString() : ''
            }));
        }

        const payload: CreateMvCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            ...formValue,
            certificateNumber: formValue.certificateNumber || formValue.myRef,
            descriptionOfCommodity: formValue.viewMode === 'letterhead' ? (formValue.itemName || formValue.descriptionOfCommodity) : formValue.descriptionOfCommodity,
            totalNumberOfPackages: formValue.viewMode === 'letterhead' ? (formValue.packagesCountDesc || formValue.totalNumberOfPackages) : formValue.totalNumberOfPackages,
            totalQuantity: formValue.viewMode === 'letterhead' ? (formValue.netWeightDesc || formValue.totalQuantity) : formValue.totalQuantity,
            conditionsOfStorage: formValue.viewMode === 'letterhead' ? (formValue.storageTempDesc || formValue.conditionsOfStorage) : formValue.conditionsOfStorage,
            placeOfLoading: formValue.viewMode === 'letterhead' ? (formValue.dispatchedFrom || formValue.placeOfLoading) : formValue.placeOfLoading,
            pointsOfEntry: formValue.viewMode === 'letterhead' ? (formValue.dispatchedTo || formValue.pointsOfEntry) : formValue.pointsOfEntry,
            certificateType: this.viewMode,
            certifyingOfficerDate: toLocalISOString(formValue.certifyingOfficerDate)
        };

        this.certificateRequestService.submitMvCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.messageService.add({ severity: 'success', summary: 'Success', detail: 'Maldives Certificate saved successfully' });
                if (this.certificateRequestId) {
                    try {
                        const submitted = JSON.parse(localStorage.getItem('dfar_submitted_requests') || '[]');
                        if (!submitted.includes(this.certificateRequestId)) {
                            submitted.push(this.certificateRequestId);
                            localStorage.setItem('dfar_submitted_requests', JSON.stringify(submitted));
                        }
                    } catch {}
                }
                setTimeout(() => this.goBack(), 1500);
            },
            error: (err) => {
                this.isSaving = false;
                console.error(err);
                const detail = err?.error?.message || err?.message || 'Failed to save Maldives certificate';
                this.messageService.add({ severity: 'error', summary: 'Error', detail });
            }
        });
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

    print() {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }

        const ref = this.form.get('certificateNumber')?.value || this.form.get('myRef')?.value || 'TC_4683';
        const originalTitle = document.title;
        const suffix = this.viewMode === 'letterhead' ? '_Letterhead' : '_Generic_Boxed';
        document.title = `${ref}_Maldives_Health_Certificate${suffix}.pdf`;
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }

    goBack() {
        const userRole = (this.authService.getUserRole() || '').toLowerCase();
        const targetUrl = userRole === 'admin' ? '/uikit/admin/certificate-requests' : '/uikit/company-request';
        if (typeof window !== 'undefined' && window.self !== window.top && window.top) {
            window.top.location.href = targetUrl;
        } else {
            this.router.navigate([targetUrl]);
        }
    }

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.previousSignatoryUserId = null;
            this.setSelectedUserQualification(userId);
            return;
        }

        const user = this.users.find((u) => u.id === userId);
        if (!user) {
            return;
        }
        this.pendingSignatoryUserId = userId;
        this.pendingSignatoryUserEmail = user.email;
        this.showPasswordDialog = true;
    }

    onSignatoryConfirmed(officerName: string) {
        if (this.pendingSignatoryUserId) {
            this.previousSignatoryUserId = this.pendingSignatoryUserId;
            this.setSelectedUserQualification(this.pendingSignatoryUserId);
            if (officerName) {
                this.form.patchValue({
                    signatoryName: officerName,
                    certifyingOfficerName: officerName
                });
            }
        }
        this.showPasswordDialog = false;
        this.pendingSignatoryUserId = null;
        this.messageService.add({ severity: 'success', summary: 'Verified', detail: 'Officer PIN confirmed successfully.' });
    }

    onSignatoryCanceled() {
        this.showPasswordDialog = false;
        this.form.get('signatoryUserId')?.setValue(this.previousSignatoryUserId, { emitEvent: false });
        if (this.previousSignatoryUserId) {
            this.setSelectedUserQualification(this.previousSignatoryUserId);
        } else {
            this.selectedUserQualification = null;
        }
        this.pendingSignatoryUserId = null;
    }

    setSelectedUserQualification(userId: string | null) {
        const user = this.users.find((u) => u.id === userId);
        this.selectedUserQualification = user?.qualification || null;
        if (user) {
            this.form.patchValue({
                signatoryName: user.name,
                certifyingOfficerName: user.name,
                qualification: user.qualification
            });
        }
    }

    isPdf(value: string | null | undefined): boolean {
        if (!value) return false;
        return value.startsWith('data:application/pdf') || value.endsWith('.pdf');
    }

    updateSafeStampUrl() {
        const stamp = this.form.get('officialStamp')?.value;
        if (stamp && this.isPdf(stamp)) {
            this.safeStampPdfUrl = this.sanitizer.bypassSecurityTrustResourceUrl(stamp);
        } else {
            this.safeStampPdfUrl = null;
        }
    }

    updateSafeSignatureUrl() {
        const sig = this.form.get('officialSignature')?.value;
        if (sig && this.isPdf(sig)) {
            this.safeSignaturePdfUrl = this.sanitizer.bypassSecurityTrustResourceUrl(sig);
        } else {
            this.safeSignaturePdfUrl = null;
        }
    }

    onStampFileSelected(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            this.processFile(input.files[0], 'stamp');
            input.value = '';
        }
    }

    onSignatureFileSelected(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            this.processFile(input.files[0], 'signature');
            input.value = '';
        }
    }

    onStampDrop(event: DragEvent) {
        event.preventDefault();
        this.isDraggingStamp = false;
        if (this.viewOnly) return;
        if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
            this.processFile(event.dataTransfer.files[0], 'stamp');
        }
    }

    onSignatureDrop(event: DragEvent) {
        event.preventDefault();
        this.isDraggingSignature = false;
        if (this.viewOnly) return;
        if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
            this.processFile(event.dataTransfer.files[0], 'signature');
        }
    }

    onStampDragOver(event: DragEvent) {
        event.preventDefault();
        if (!this.viewOnly) this.isDraggingStamp = true;
    }

    onStampDragLeave(event: DragEvent) {
        event.preventDefault();
        this.isDraggingStamp = false;
    }

    onSignatureDragOver(event: DragEvent) {
        event.preventDefault();
        if (!this.viewOnly) this.isDraggingSignature = true;
    }

    onSignatureDragLeave(event: DragEvent) {
        event.preventDefault();
        this.isDraggingSignature = false;
    }

    onStampPaste(event: ClipboardEvent) {
        if (this.viewOnly) return;
        const items = event.clipboardData?.items;
        if (items) {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1 || items[i].type.indexOf('pdf') !== -1) {
                    const file = items[i].getAsFile();
                    if (file) {
                        this.processFile(file, 'stamp');
                        event.preventDefault();
                        break;
                    }
                }
            }
        }
    }

    onSignaturePaste(event: ClipboardEvent) {
        if (this.viewOnly) return;
        const items = event.clipboardData?.items;
        if (items) {
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1 || items[i].type.indexOf('pdf') !== -1) {
                    const file = items[i].getAsFile();
                    if (file) {
                        this.processFile(file, 'signature');
                        event.preventDefault();
                        break;
                    }
                }
            }
        }
    }

    processFile(file: File, target: 'stamp' | 'signature') {
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
            if (target === 'stamp') {
                this.form.patchValue({ officialStamp: result });
                this.updateSafeStampUrl();
            } else {
                this.form.patchValue({ officialSignature: result });
                this.updateSafeSignatureUrl();
            }
            this.messageService.add({
                severity: 'success',
                summary: 'Uploaded Successfully',
                detail: `${target === 'stamp' ? 'Official Stamp' : 'Official Signature'} uploaded.`
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

    removeStamp(event?: Event) {
        if (event) event.stopPropagation();
        this.form.patchValue({ officialStamp: '' });
        this.safeStampPdfUrl = null;
    }

    removeSignature(event?: Event) {
        if (event) event.stopPropagation();
        this.form.patchValue({ officialSignature: '' });
        this.safeSignaturePdfUrl = null;
    }
}

