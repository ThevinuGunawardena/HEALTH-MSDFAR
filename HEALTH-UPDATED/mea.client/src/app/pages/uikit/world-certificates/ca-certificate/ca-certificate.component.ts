import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { CheckboxModule } from 'primeng/checkbox';
import { RadioButton } from 'primeng/radiobutton';
import { Select } from 'primeng/select';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateRequestService, CaCertificateView } from 'src/app/pages/service/certificate-request.service';
import { UserService, User } from '@/pages/service/user.service';
import { TooltipModule } from 'primeng/tooltip';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-ca-certificate',
    standalone: true,
    imports: [CommonModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        DatePicker,
        ToastModule,
        CheckboxModule,
        RadioButton,
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './ca-certificate.component.html',
    styleUrls: ['./ca-certificate.component.css', '../certificate-print.css']
})
export class CaCertificateComponent implements OnInit {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    form: FormGroup;
    refNumber: string = '';
    certificateRequestId: number | null = null;
    viewOnly = false;
    isSubmitted = false;
    isEmbedded = false;
    viewMode: 'letter' | 'live' | 'generic' = 'letter';

    isCompany = false;

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }
    isApproved = false;
    userOptions: { label: string; value: string }[] = [];
    users: User[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail = '';
    previousSignatoryUserId: string | null = null;

    isDraggingStamp = false;
    isDraggingSignature = false;
    safeStampPdfUrl: SafeResourceUrl | null = null;
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
        private route: ActivatedRoute,
        private router: Router,
        private messageService: MessageService,
        private certificateService: CertificateRequestService,
        private userService: UserService,
        private location: Location,
        private authService: AuthService,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['letter'],
            myRef: [''],
            yourRef: [''],
            date: [new Date(), Validators.required],
            certificateNumber: [''],
            competentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            certifyingBody: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            consignorName: ['', Validators.required],
            consignorAddress: ['', Validators.required],
            consigneeName: ['', Validators.required],
            consigneeAddress: ['', Validators.required],
            countryOfOrigin: ['SRI LANKA'],
            countryOfOriginISO: ['LK'],
            countryOfDestination: ['CANADA'],
            countryOfDestinationISO: ['CA'],
            placeOfLoading: ['SRI LANKA'],
            transportAeroPlane: [false],
            transportShip: [false],
            transportRailway: [false],
            transportRoad: [false],
            transportOther: [false],
            despatchFrom: ['COLOMBO, SRI LANKA'],
            despatchTo: ['TORONTO, CANADA'],
            despatchByShip: ['BY SEA FREIGHT'],
            itemName: [''],
            liveSampleType: ['live fishery products'],
            dateOfProcessing: [''],
            countryOfOriginText: ['COUNTRY OF ORIGIN SRI LANKA'],
            numberOfPackages: [''],
            netWeight: [''],
            processingPlantName: ['', Validators.required],
            processingPlantAddress: ['', Validators.required],
            competentAuthorityRegNo: ['', Validators.required],
            pointsOfEntry: [''],
            conditionsOfStorage: [''],
            totalQuantity: [''],
            sealNumber: [''],
            totalNumberOfPackages: [''],
            approvalNumberOfEstablishments: [''],
            descriptionOfCommodity: [''],
            signatoryName: [''],
            designation: ['QUALITY CONTROL OFFICER (GRADE II)', Validators.required],
            qualification: [''],
            companyRegistrationNo: [''],
            officialStamp: [''],
            officialSignature: [''],
            certificateType: ['letter'],
            signatoryUserId: [null, Validators.required],
            productsAttachment: this.fb.array([])
        });
    }

    get productsAttachment(): FormArray {
        return this.form.get('productsAttachment') as FormArray;
    }

    createProductAttachmentGroup(): FormGroup {
        return this.fb.group({
            product: [''],
            lotIdentifier: [''],
            typeOfPackaging: [''],
            numberOfKgs: [''],
            numberOfBoxes: [null]
        });
    }

    addProductAttachment() {
        this.productsAttachment.push(this.createProductAttachmentGroup());
    }

    removeProductAttachment(index: number) {
        this.productsAttachment.removeAt(index);
    }

    getTotalAttachmentKgs(): number {
        return this.productsAttachment.controls.reduce((sum, ctrl) => {
            const val = parseFloat(ctrl.get('numberOfKgs')?.value) || 0;
            return sum + val;
        }, 0);
    }

    getTotalAttachmentBoxes(): number {
        return this.productsAttachment.controls.reduce((sum, ctrl) => {
            const val = parseInt(ctrl.get('numberOfBoxes')?.value, 10) || 0;
            return sum + val;
        }, 0);
    }

    onViewModeChange(mode: 'letter' | 'live' | 'generic') {
        this.viewMode = mode;
        this.form.get('viewMode')?.setValue(mode);
        this.form.get('certificateType')?.setValue(mode);
    }

    ngOnInit() {
        this.isCompany = (this.authService.getUserRole() || '').toLowerCase() === 'company';
        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        this.form.get('viewMode')?.valueChanges.subscribe((mode: any) => {
            if (mode) {
                this.viewMode = mode;
                this.form.get('certificateType')?.setValue(mode, { emitEvent: false });
            }
        });

        this.userService.getAllUsers().subscribe((users) => {
            this.users = users;
            this.userOptions = users
                .filter((u) => u.roleName && (u.roleName.toLowerCase() === 'user' || u.roleName.toLowerCase() === 'admin'))
                .map((u) => ({ label: u.name, value: u.id }));
        });

        this.route.queryParams.subscribe((params) => {
            if (params['cancelsAndReplacesRef']) this.cancelsAndReplacesRef = params['cancelsAndReplacesRef'];
            if (params['cancelsAndReplacesDate']) this.cancelsAndReplacesDate = params['cancelsAndReplacesDate'];
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
            }
            if (params['ref']) {
                this.refNumber = params['ref'];
                this.form.patchValue({
                    myRef: params['ref'],
                    certificateNumber: params['ref']
                });
            }
            if (params['requestId']) {
                this.certificateRequestId = +params['requestId'];
                this.checkRequestApproval(this.certificateRequestId);
                this.loadSavedCertificateData(this.certificateRequestId);
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

    setSelectedUserQualification(userId: string) {
        const user = this.users.find((u) => u.id === userId);
        if (user) {
            this.selectedUserQualification = user.qualification || null;
            this.form.patchValue({
                signatoryName: user.name,
                qualification: user.qualification || ''
            });
        }
    }

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.selectedUserQualification = null;
            this.form.patchValue({ signatoryName: '', qualification: '' });
            return;
        }

        const selectedUser = this.users.find((u) => u.id === userId);
        this.pendingSignatoryUserId = userId;
        this.pendingSignatoryUserEmail = selectedUser?.email || '';
        this.showPasswordDialog = true;
    }

    onSignatoryConfirmed(userId: string) {
        this.showPasswordDialog = false;
        const targetUserId = userId || this.pendingSignatoryUserId;
        if (targetUserId) {
            this.form.patchValue({ signatoryUserId: targetUserId });
            this.setSelectedUserQualification(targetUserId);
            this.previousSignatoryUserId = targetUserId;
            this.messageService.add({
                severity: 'success',
                summary: 'Signatory Verified',
                detail: 'Signatory PIN successfully verified.'
            });
        }
        this.pendingSignatoryUserId = null;
        this.pendingSignatoryUserEmail = '';
    }

    onSignatoryCanceled() {
        this.showPasswordDialog = false;
        this.form.patchValue({ signatoryUserId: this.previousSignatoryUserId });
        if (this.previousSignatoryUserId) {
            this.setSelectedUserQualification(this.previousSignatoryUserId);
        } else {
            this.selectedUserQualification = null;
            this.form.patchValue({ signatoryName: '', qualification: '' });
        }
        this.messageService.add({
            severity: 'warn',
            summary: 'Verification Cancelled',
            detail: 'Signatory PIN was not entered.'
        });
        this.pendingSignatoryUserId = null;
        this.pendingSignatoryUserEmail = '';
    }

    loadSavedCertificateData(requestId: number) {
        this.certificateService.getCaCertificateByRequestId(requestId).subscribe((cert: any) => {
            if (cert) {
                this.isSubmitted = true;
                if (cert.certificateType === 'generic' || cert.certificateType === 'letter' || cert.certificateType === 'live') {
                    this.viewMode = cert.certificateType as any;
                } else if (cert.conditionsOfStorage || cert.totalQuantity || cert.sealNumber || cert.totalNumberOfPackages || cert.approvalNumberOfEstablishments || cert.descriptionOfCommodity) {
                    this.viewMode = 'generic';
                } else if (cert.myRef || cert.yourRef) {
                    this.viewMode = 'live';
                } else {
                    this.viewMode = 'letter';
                }
                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812'];
                let certNo = this.refNumber ||
                    (cert.certificateNumber?.startsWith('HC-') || cert.certificateNumber?.startsWith('*') ? cert.certificateNumber : '') ||
                    (cert.myRef?.startsWith('HC-') || cert.myRef?.startsWith('*') ? cert.myRef : '') ||
                    (cert.certificateNumber && !dummyValues.includes(cert.certificateNumber.trim()) ? cert.certificateNumber : '') ||
                    (cert.myRef && !dummyValues.includes(cert.myRef.trim()) ? cert.myRef : '') ||
                    this.refNumber || '';
                if (!certNo) certNo = this.refNumber || '';
                this.refNumber = certNo;

                this.form.patchValue({
                    viewMode: this.viewMode,
                    certificateType: this.viewMode,
                    myRef: certNo || cert.myRef || '',
                    yourRef: cert.yourRef || '',
                    date: cert.date ? new Date(cert.date) : new Date(),
                    certificateNumber: certNo || cert.certificateNumber || '',
                    competentAuthority: cert.competentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    certifyingBody: cert.certifyingBody || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    consignorName: cert.consignorName || '',
                    consignorAddress: cert.consignorAddress || '',
                    consigneeName: cert.consigneeName || '',
                    consigneeAddress: cert.consigneeAddress || '',
                    countryOfOrigin: cert.countryOfOrigin || 'SRI LANKA',
                    countryOfOriginISO: cert.countryOfOriginISO || 'LK',
                    countryOfDestination: cert.countryOfDestination || 'CANADA',
                    countryOfDestinationISO: cert.countryOfDestinationISO || 'CA',
                    placeOfLoading: cert.placeOfLoading || 'SRI LANKA',
                    transportAeroPlane: cert.transportAeroPlane || false,
                    transportShip: cert.transportShip || false,
                    transportRailway: cert.transportRailway || false,
                    transportRoad: cert.transportRoad || false,
                    transportOther: cert.transportOther || false,
                    despatchFrom: cert.despatchFrom || 'COLOMBO, SRI LANKA',
                    despatchTo: cert.despatchTo || 'TORONTO, CANADA',
                    despatchByShip: cert.despatchByShip || 'BY SEA FREIGHT',
                    itemName: cert.itemName || '',
                    numberOfPackages: cert.numberOfPackages || '',
                    netWeight: cert.netWeight || '',
                    processingPlantName: cert.processingPlantName || '',
                    processingPlantAddress: cert.processingPlantAddress || '',
                    competentAuthorityRegNo: cert.competentAuthorityRegNo || '',
                    pointsOfEntry: cert.pointsOfEntry || '',
                    conditionsOfStorage: cert.conditionsOfStorage || '',
                    totalQuantity: cert.totalQuantity || '',
                    sealNumber: cert.sealNumber || '',
                    totalNumberOfPackages: cert.totalNumberOfPackages || '',
                    approvalNumberOfEstablishments: cert.approvalNumberOfEstablishments || '',
                    descriptionOfCommodity: cert.descriptionOfCommodity || '',
                    signatoryName: cert.signatoryName || '',
                    designation: cert.designation || 'QUALITY CONTROL OFFICER (GRADE II)',
                    qualification: cert.qualification || '',
                    companyRegistrationNo: cert.companyRegistrationNo || '',
                    officialStamp: cert.officialStamp || '',
                    officialSignature: cert.officialSignature || '',
                    signatoryUserId: cert.signatoryUserId || null
                });

                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();

                if (cert.signatoryUserId) {
                    this.previousSignatoryUserId = cert.signatoryUserId;
                    this.setSelectedUserQualification(cert.signatoryUserId);
                }

                if (cert.productsAttachment && cert.productsAttachment.length > 0) {
                    this.productsAttachment.clear();
                    cert.productsAttachment.forEach((p: any) => {
                        this.productsAttachment.push(
                            this.fb.group({
                                product: [p.product || ''],
                                lotIdentifier: [p.lotIdentifier || ''],
                                typeOfPackaging: [p.typeOfPackaging || ''],
                                numberOfKgs: [p.numberOfKgs || ''],
                                numberOfBoxes: [p.numberOfBoxes || null]
                            })
                        );
                    });
                }
            } else {
                // Try load from VetForm (Company submitted form data)
                this.certificateService.getVetFormByRequestId(requestId).subscribe((vetForm: any) => {
                    if (vetForm) {
                        const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812'];
                        let certNo = this.refNumber ||
                            (vetForm.referenceNumber?.startsWith('HC-') || vetForm.referenceNumber?.startsWith('*') ? vetForm.referenceNumber : '') ||
                            (vetForm.healthCertNo && !dummyValues.includes(vetForm.healthCertNo.trim()) ? vetForm.healthCertNo : '') ||
                            (vetForm.newHC && !dummyValues.includes(vetForm.newHC.trim()) ? vetForm.newHC : '') ||
                            this.refNumber || '';
                        if (!certNo) certNo = this.refNumber || '';
                        this.refNumber = certNo;

                        const consignorName = vetForm.consignorName || '';
                        const consignorAddress = [vetForm.consignorAddress, vetForm.consignorPostal, vetForm.consignorTel].filter(Boolean).join(', ') || vetForm.consignorAddress || '';
                        const consigneeName = vetForm.consigneeName || '';
                        const consigneeAddress = [vetForm.consigneeAddress, vetForm.consigneePostal, vetForm.consigneeTel].filter(Boolean).join(', ') || vetForm.consigneeAddress || '';
                        const plantName = vetForm.processingEstName || vetForm.processingPlantName || '';
                        const plantAddress = vetForm.processingEstAddress || vetForm.processingPlantAddress || '';
                        const regNo = vetForm.approvalNo || vetForm.competentAuthorityRegNo || '';
                        const placeOfLoading = vetForm.placeOfLoading || 'SRI LANKA';
                        const totalQty = vetForm.quantity ? `${vetForm.quantity} kg` : (vetForm.totalQuantity ? `${vetForm.totalQuantity}` : '');
                        const numPkgs = vetForm.numPackages ? `${vetForm.numPackages}` : (vetForm.totalNumberOfPackages ? `${vetForm.totalNumberOfPackages}` : '');
                        const commodity = vetForm.descCommon ? (vetForm.descScientific ? `${vetForm.descCommon} (${vetForm.descScientific})` : vetForm.descCommon) : (vetForm.descriptionOfCommodity || '');
                        const storage = vetForm.temperature ? vetForm.temperature.toUpperCase() : (vetForm.temperatureFrozen ? 'FROZEN (-18°C)' : vetForm.temperatureChilled ? 'CHILLED (0-4°C)' : vetForm.temperatureAmbient ? 'AMBIENT' : (vetForm.conditionsOfStorage || ''));
                        const isAir = !!vetForm.transportAeroPlane;
                        const isShip = !!vetForm.transportShip;

                        this.form.patchValue({
                            myRef: certNo,
                            certificateNumber: certNo,
                            consignorName: consignorName,
                            consignorAddress: consignorAddress,
                            consigneeName: consigneeName,
                            consigneeAddress: consigneeAddress,
                            processingPlantName: plantName,
                            processingPlantAddress: plantAddress,
                            competentAuthorityRegNo: regNo,
                            approvalNumberOfEstablishments: regNo,
                            placeOfLoading: placeOfLoading,
                            despatchFrom: placeOfLoading,
                            despatchTo: 'TORONTO, CANADA',
                            totalQuantity: totalQty,
                            totalNumberOfPackages: numPkgs,
                            numberOfPackages: numPkgs,
                            netWeight: totalQty,
                            conditionsOfStorage: storage,
                            descriptionOfCommodity: commodity,
                            itemName: commodity,
                            pointsOfEntry: vetForm.entryBIP || '',
                            sealNumber: vetForm.containerId || '',
                            transportAeroPlane: isAir,
                            transportShip: isShip,
                            despatchByShip: isAir ? 'BY AIR FREIGHT' : 'BY SEA FREIGHT'
                        });

                        if (vetForm.products && vetForm.products.length > 0) {
                            this.productsAttachment.clear();
                            vetForm.products.forEach((p: any) => {
                                const prodName = p.descCommon ? (p.descScientific ? `${p.descCommon} (${p.descScientific})` : p.descCommon) : (p.product || '');
                                this.productsAttachment.push(
                                    this.fb.group({
                                        product: [prodName],
                                        lotIdentifier: [p.containerId || p.lotIdentifier || ''],
                                        typeOfPackaging: [p.packagingType || p.typeOfPackaging || ''],
                                        numberOfKgs: [p.netWeight || p.quantity || p.numberOfKgs || ''],
                                        numberOfBoxes: [p.numPackages || p.numberOfBoxes || null]
                                    })
                                );
                            });
                        }
                    }
                });
            }
        });
    }

    onSubmit() {
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
            this.form.markAllAsTouched();
            this.messageService.add({
                severity: 'error',
                summary: 'Validation Error',
                detail: 'Please fill in all required fields.'
            });
            return;
        }

        const raw = this.form.getRawValue();
        const payload = {
            certificateRequestId: this.certificateRequestId,
            myRef: raw.myRef,
            yourRef: raw.yourRef,
            date: raw.date ? toLocalISOString(raw.date) : null,
            certificateNumber: raw.certificateNumber || raw.myRef,
            competentAuthority: raw.competentAuthority,
            certifyingBody: raw.certifyingBody,
            consignorName: raw.consignorName,
            consignorAddress: raw.consignorAddress,
            consigneeName: raw.consigneeName,
            consigneeAddress: raw.consigneeAddress,
            countryOfOrigin: raw.countryOfOrigin,
            countryOfOriginISO: raw.countryOfOriginISO,
            countryOfDestination: raw.countryOfDestination,
            countryOfDestinationISO: raw.countryOfDestinationISO,
            placeOfLoading: raw.placeOfLoading,
            transportAeroPlane: !!raw.transportAeroPlane,
            transportShip: !!raw.transportShip,
            transportRailway: !!raw.transportRailway,
            transportRoad: !!raw.transportRoad,
            transportOther: !!raw.transportOther,
            despatchFrom: raw.despatchFrom,
            despatchTo: raw.despatchTo,
            despatchByShip: raw.despatchByShip,
            itemName: raw.itemName,
            numberOfPackages: raw.numberOfPackages,
            netWeight: raw.netWeight,
            processingPlantName: raw.processingPlantName,
            processingPlantAddress: raw.processingPlantAddress,
            competentAuthorityRegNo: raw.competentAuthorityRegNo,
            pointsOfEntry: raw.pointsOfEntry,
            conditionsOfStorage: raw.conditionsOfStorage,
            totalQuantity: raw.totalQuantity,
            sealNumber: raw.sealNumber,
            totalNumberOfPackages: raw.totalNumberOfPackages,
            approvalNumberOfEstablishments: raw.approvalNumberOfEstablishments,
            descriptionOfCommodity: raw.descriptionOfCommodity,
            signatoryUserId: raw.signatoryUserId,
            signatoryName: raw.signatoryName,
            designation: raw.designation,
            qualification: raw.qualification,
            companyRegistrationNo: raw.companyRegistrationNo,
            officialStamp: raw.officialStamp,
            officialSignature: raw.officialSignature,
            certificateType: this.viewMode,
            productsAttachment: []
        };

        this.certificateService.submitCaCertificate(payload).subscribe({
            next: () => {
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Canada Health Certificate submitted successfully.'
                });
                if (!this.isEmbedded) {
                    setTimeout(() => {
                        this.goBack();
                    }, 1200);
                }
            },
            error: (err) => {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: err?.error?.message || 'Failed to submit Canada Health Certificate.'
                });
            }
        });
    }

    isPdf(value: string | null | undefined): boolean {
        if (!value) return false;
        return value.startsWith('data:application/pdf') || value.toLowerCase().includes('.pdf');
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
        if (event) {
            event.stopPropagation();
        }
        this.form.patchValue({ officialStamp: '' });
        this.safeStampPdfUrl = null;
    }

    removeSignature(event?: Event) {
        if (event) {
            event.stopPropagation();
        }
        this.form.patchValue({ officialSignature: '' });
        this.safeSignaturePdfUrl = null;
    }

    private checkRequestApproval(requestId: number): void {
        this.certificateService.getRequestById(requestId).subscribe({
            next: (req) => {
                    if (req) {
                        if (req.cancelsAndReplacesRef) this.cancelsAndReplacesRef = req.cancelsAndReplacesRef;
                        if (req.cancelsAndReplacesDate) this.cancelsAndReplacesDate = req.cancelsAndReplacesDate;
                    }
                if (req) {
                    const st = typeof req.status === 'string' ? req.status.toLowerCase() : (req.status === 1 ? 'confirmed' : 'pending');
                    this.isApproved = (st === 'confirmed' || st === 'approved' || req.status === 1);
                    if (req.referenceNumber) {
                        this.refNumber = req.referenceNumber;
                        this.form.patchValue({
                            myRef: req.referenceNumber,
                            certificateNumber: req.referenceNumber
                        });
                    }
                }
            },
            error: () => {}
        });
    }

    print() {
        if (!this.isAdmin) {
            this.messageService.add({
                severity: 'error',
                summary: 'Access Denied',
                detail: 'Only administrators have access to print health certificates.'
            });
            return;
        }

        window.print();
    }

    goBack() {
        const userRole = (this.authService.getUserRole() || '').toLowerCase();
        const targetUrl = userRole === 'admin' ? '/uikit/admin/certificate-requests' : '/uikit/company-request';
        if (typeof window !== 'undefined' && window.self !== window.top && window.top) {
            window.top.location.href = targetUrl;
        } else {
            this.router.navigateByUrl(targetUrl);
        }
    }
}
