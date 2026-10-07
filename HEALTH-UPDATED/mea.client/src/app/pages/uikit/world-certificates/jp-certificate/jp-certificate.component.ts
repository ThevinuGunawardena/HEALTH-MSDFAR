import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { RadioButton } from 'primeng/radiobutton';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { UserService, User } from '@/pages/service/user.service';
import {
    JpCertificateView,
    CertificateRequestService,
    CreateJpCertificatePayload,
    VetFormFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { TooltipModule } from 'primeng/tooltip';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-jp-certificate',
    standalone: true,
    imports: [CommonModule,
        FormsModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        DatePicker,
        ToastModule,
        RadioButton,
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './jp-certificate.component.html',
    styleUrls: ['./jp-certificate.component.css', '../certificate-print.css']
})
export class JpCertificateComponent implements OnInit {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    form: FormGroup;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isSubmitted = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }
    isApproved = false;

    // View mode: 'vibrio' (Vibrio cholerae negative) vs 'cholera' (Cholera germs free)
    viewMode: 'vibrio' | 'cholera' = 'vibrio';
    refNumber: string = '';

    userOptions: { label: string; value: string }[] = [];
    users: User[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
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
        private messageService: MessageService,
        private route: ActivatedRoute,
        private router: Router,
        private certificateService: CertificateRequestService,
        private userService: UserService,
        private location: Location,
        private authService: AuthService,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['vibrio'],
            myRef: [''],
            yourRef: [''],
            date: [new Date()],
            itemName: [''],
            numberOfPackages: [''],
            netWeight: [''],
            processingPlantName: [''],
            processingPlantAddress: [''],
            competentAuthorityRegNo: [''],
            consignorName: [''],
            consignorAddress: [''],
            consigneeName: [''],
            consigneeAddress: [''],
            despatchFrom: [''],
            despatchTo: [''],
            despatchByShip: [''],
            officialStamp: [''],
            officialSignature: [''],
            signatoryUserId: [null, Validators.required],
            signatoryName: [''],
            qualification: ['']
        });

        this.form.get('viewMode')?.valueChanges.subscribe((mode: 'vibrio' | 'cholera') => {
            this.viewMode = mode;
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
                this.form.patchValue({ myRef: params['ref'] });
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

    onViewModeChange(mode: 'vibrio' | 'cholera') {
        this.viewMode = mode;
        this.form.get('viewMode')?.setValue(mode);
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateService.getJpCertificateByRequestId(requestId).subscribe({
            next: (data: JpCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                this.isSubmitted = true;
                const rawMode = (data.certificateType || '').toLowerCase();
                const mode: 'vibrio' | 'cholera' = rawMode === 'cholera' ? 'cholera' : 'vibrio';
                this.viewMode = mode;

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813'];
                const cleanMyRef = (data.myRef && !dummyValues.includes(data.myRef.trim())) ? data.myRef : '';
                const finalRef = this.refNumber || data.referenceNumber || cleanMyRef || '';

                this.form.patchValue({
                    viewMode: mode,
                    myRef: finalRef,
                    yourRef: data.yourRef || '',
                    date: data.date ? new Date(data.date) : new Date(),
                    itemName: data.itemName || '',
                    numberOfPackages: data.numberOfPackages || '',
                    netWeight: data.netWeight || '',
                    processingPlantName: data.processingPlantName || '',
                    processingPlantAddress: data.processingPlantAddress || '',
                    competentAuthorityRegNo: data.competentAuthorityRegNo || 'DFAR/FPE/98/08',
                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    despatchFrom: data.despatchFrom || 'COLOMBO – SRI LANKA',
                    despatchTo: data.despatchTo || 'TOKYO - JAPAN',
                    despatchByShip: data.despatchByShip || 'BY SEA FREIGHT',
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || '',
                    signatoryUserId: data.signatoryUserId || null,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || ''
                });

                this.updateSafeSignatureUrl();
                this.updateSafeStampUrl();

                this.selectedUserQualification = data.qualification || null;
                this.previousSignatoryUserId = data.signatoryUserId || null;

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
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
            next: (data: VetFormFieldResponse) => {
                if (!data) return;

                const defaultRegNo = data.approvalNo || 'DFAR/FPE/98/08';
                const plantName = data.processingEstName || data.consignorName || 'ALPEX MARINE (PVT) LIMITED.';
                const plantAddress = `${data.processingEstAddress || data.consignorAddress || '68, CANAL ROAD, HENDALA,'}\n${data.consignorPostal || 'WATTALA, SRI LANKA.'}`.trim();
                const isShip = data.transportShip ?? true;
                const meansTransport = isShip ? 'BY SEA FREIGHT' : 'BY AIR FREIGHT';
                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813'];
                const cleanCertNo = (data.healthCertNo && !dummyValues.includes(data.healthCertNo.trim())) ? data.healthCertNo : 
                                    (data.newHC && !dummyValues.includes(data.newHC.trim())) ? data.newHC : '';
                const certNo = this.refNumber || data.referenceNumber || cleanCertNo || '';

                let itemDesc = '';
                if (data.descCommon) {
                    itemDesc = data.descScientific ? `${data.descCommon} (${data.descScientific})` : data.descCommon;
                }

                this.form.patchValue({
                    myRef: certNo,
                    yourRef: data.docReferences || '',
                    date: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    itemName: itemDesc,
                    numberOfPackages: data.numPackages ? `${data.numPackages} ${data.packagingType || ''}`.trim() : '',
                    netWeight: data.netWeight ? `${data.netWeight} KGS`.trim() : '',
                    processingPlantName: plantName,
                    processingPlantAddress: plantAddress,
                    competentAuthorityRegNo: defaultRegNo,
                    consignorName: data.consignorName || plantName,
                    consignorAddress: data.consignorAddress ? `${data.consignorAddress}\n${data.consignorPostal || ''}`.trim() : '',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress ? `${data.consigneeAddress}\n${data.consigneePostal || ''}`.trim() : '',
                    despatchFrom: data.placeOfLoading || '',
                    despatchTo: data.entryBIP || '',
                    despatchByShip: meansTransport
                });
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
            this.selectedUserQualification = selected.qualification || 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).';
            this.showPasswordDialog = true;
        }
    }

    onSignatoryConfirmed(officerName: string) {
        if (this.pendingSignatoryUserId) {
            this.form.patchValue({
                signatoryUserId: this.pendingSignatoryUserId,
                signatoryName: officerName,
                qualification: this.selectedUserQualification || 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'
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
            this.messageService.add({
                severity: 'error',
                summary: 'Validation Error',
                detail: 'Please fill in all required fields'
            });
            return;
        }

        this.isSaving = true;
        const rawValue = this.form.getRawValue();

        const payload: CreateJpCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            myRef: rawValue.myRef || '',
            yourRef: rawValue.yourRef || '',
            date: toLocalISOString(rawValue.date),
            itemName: rawValue.itemName || '',
            numberOfPackages: rawValue.numberOfPackages || '',
            netWeight: rawValue.netWeight || '',
            processingPlantName: rawValue.processingPlantName || '',
            processingPlantAddress: rawValue.processingPlantAddress || '',
            competentAuthorityRegNo: rawValue.competentAuthorityRegNo || '',
            consignorName: rawValue.consignorName || '',
            consignorAddress: rawValue.consignorAddress || '',
            consigneeName: rawValue.consigneeName || '',
            consigneeAddress: rawValue.consigneeAddress || '',
            despatchFrom: rawValue.despatchFrom || '',
            despatchTo: rawValue.despatchTo || '',
            despatchByShip: rawValue.despatchByShip || '',
            officialStamp: rawValue.officialStamp || '',
            officialSignature: rawValue.officialSignature || '',
            signatoryUserId: rawValue.signatoryUserId || null,
            signatoryName: rawValue.signatoryName || '',
            qualification: rawValue.qualification || '',
            certificateType: this.viewMode
        };

        this.certificateService.submitJpCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Japan Health Certificate saved successfully'
                });
            },
            error: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to save Japan Health Certificate'
                });
            }
        });
    }

    isPdf(dataUrl?: string | null): boolean {
        if (!dataUrl) return false;
        return dataUrl.startsWith('data:application/pdf') || dataUrl.toLowerCase().endsWith('.pdf');
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
                        this.form.patchValue({ myRef: req.referenceNumber });
                    }
                }
            },
            error: () => {}
        });
    }

    printCertificate(): void {
        if (!this.isAdmin) {
            this.messageService.add({
                severity: 'error',
                summary: 'Access Denied',
                detail: 'Only administrators have access to print health certificates.'
            });
            return;
        }

        const ref = this.form.get('myRef')?.value || 'Draft';
        let pdfName = '';
        if (this.viewMode === 'vibrio') {
            pdfName = `${ref}_Japan_Health_Certificate.pdf`;
        } else {
            pdfName = `${ref}_Japan_Health_Certificate_Cholera_Germs.pdf`;
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
