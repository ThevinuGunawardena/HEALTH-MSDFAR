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
import { CheckboxModule } from 'primeng/checkbox';
import { RadioButton } from 'primeng/radiobutton';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { UserService, User } from '@/pages/service/user.service';
import { TooltipModule } from 'primeng/tooltip';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import {
    CertificateRequestService,
    KwCertificateView,
    CreateKwCertificatePayload,
    CreateKwCertificateProductPayload,
    VetFormFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-kw-certificate',
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
        CheckboxModule,
        RadioButton,
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './kw-certificate.component.html',
    styleUrls: ['./kw-certificate.component.css', '../certificate-print.css']
})
export class KwCertificateComponent implements OnInit {
    form: FormGroup;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;
    isApproved = false;
    isSubmitted = false;

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
            certificateReferenceNo: [''],
            placeOfIssue: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            dateOfIssue: [new Date()],

            consignorName: [''],
            consignorAddress: [''],

            consigneeName: [''],
            consigneeAddress: [''],

            competentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            competentAuthorityAddress: ['P.O. BOX 531, SECRETARIAT, MALIGAWATTA\nCOLOMBO – SRI LANKA'],

            countryOfOrigin: [''],
            countryOfOriginIso: [''],

            countryOfDestination: [''],
            countryOfDestinationIso: [''],

            producerName: [''],
            producerAddress: [''],

            packingEstName: [''],
            packingEstAddress: [''],
            packingEstApprovalNo: [''],

            borderOfEntry: [''],
            borderLoadingCountry: [''],
            borderLoadingPlace: [''],

            transportByAir: [false],
            transportBySea: [false],
            vehicleIdentificationNo: [''],

            tempChilled: [false],
            tempFrozen: [false],

            commoditiesOther: [false],
            commoditiesAfterFurtherProcess: [false],
            commoditiesHumanConsumption: [true],

            products: this.fb.array([]),

            officialStamp: [''],
            officialSignature: [''],
            signatureDate: [new Date()],
            signatoryUserId: [null, Validators.required],
            signatoryName: [''],
            qualification: ['']
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    createProductRow(data?: any): FormGroup {
        return this.fb.group({
            nameDescription: [data?.nameDescription || ''],
            hsCodes: [data?.hsCodes || ''],
            treatmentDerivedFrom: [data?.treatmentDerivedFrom || ''],
            brandName: [data?.brandName || ''],
            productionDate: [data?.productionDate ? new Date(data.productionDate) : null],
            expiryDate: [data?.expiryDate ? new Date(data.expiryDate) : null],
            numberPackages: [data?.numberPackages || 0],
            batchLotNo: [data?.batchLotNo || ''],
            totalWeight: [data?.totalWeight || 0]
        });
    }

    addProduct(): void {
        this.products.push(this.createProductRow());
    }

    removeProduct(index: number): void {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
    }

    calculateTotalPackages(): number {
        return this.products.controls.reduce((sum, c) => sum + (Number(c.get('numberPackages')?.value) || 0), 0);
    }

    calculateTotalWeight(): number {
        return this.products.controls.reduce((sum, c) => sum + (Number(c.get('totalWeight')?.value) || 0), 0);
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
                this.form.patchValue({ certificateReferenceNo: params['ref'] });
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

    private loadSavedCertificateData(requestId: number) {
        this.certificateService.getKwCertificateByRequestId(requestId).subscribe({
            next: (data: KwCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                if (data.id || data.certificateReferenceNo || data.consignorName) {
                    this.isSubmitted = true;
                }

                this.form.patchValue({
                    certificateReferenceNo: data.certificateReferenceNo || '',
                    placeOfIssue: data.placeOfIssue || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    dateOfIssue: data.dateOfIssue ? new Date(data.dateOfIssue) : new Date(),
                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    competentAuthorityAddress: data.competentAuthorityAddress || 'P.O. BOX 531, SECRETARIAT, MALIGAWATTA\nCOLOMBO – SRI LANKA',
                    countryOfOrigin: data.countryOfOrigin || 'SRI LANKA',
                    countryOfOriginIso: data.countryOfOriginIso || 'SRI LANKA',
                    countryOfDestination: data.countryOfDestination || 'KUWAIT',
                    countryOfDestinationIso: data.countryOfDestinationIso || 'KUWAIT',
                    producerName: data.producerName || '',
                    producerAddress: data.producerAddress || '',
                    packingEstName: data.packingEstName || '',
                    packingEstAddress: data.packingEstAddress || '',
                    packingEstApprovalNo: data.packingEstApprovalNo || 'DFAR/FPE/98/96',
                    borderOfEntry: data.borderOfEntry || 'KUWAIT',
                    borderLoadingCountry: data.borderLoadingCountry || 'SRI LANKA',
                    borderLoadingPlace: data.borderLoadingPlace || 'COLOMBO',
                    transportByAir: data.transportByAir ?? false,
                    transportBySea: data.transportBySea ?? true,
                    vehicleIdentificationNo: data.vehicleIdentificationNo || 'NIL',
                    tempChilled: data.tempChilled ?? false,
                    tempFrozen: data.tempFrozen ?? true,
                    commoditiesOther: data.commoditiesOther ?? false,
                    commoditiesAfterFurtherProcess: data.commoditiesAfterFurtherProcess ?? false,
                    commoditiesHumanConsumption: data.commoditiesHumanConsumption ?? true,
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || '',
                    signatureDate: data.signatureDate ? new Date(data.signatureDate) : (data.dateOfIssue ? new Date(data.dateOfIssue) : new Date()),
                    signatoryUserId: data.signatoryUserId || null,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || ''
                });

                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();

                this.selectedUserQualification = data.qualification || null;
                this.previousSignatoryUserId = data.signatoryUserId || null;

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p) => {
                        this.products.push(this.createProductRow(p));
                    });
                } else {
                    this.products.push(this.createProductRow());
                }

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

                const certNo = data.healthCertNo || data.newHC || '';
                const consignorAddr = `${data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim();
                const consigneeAddr = `${data.consigneeAddress || ''}\n${data.consigneePostal || ''}\nKUWAIT`.trim();
                const plantName = data.processingEstName || data.consignorName || 'AWP LANKA PVT LTD';
                const plantAddr = `${data.processingEstAddress || data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim();
                const approvalNo = data.approvalNo || 'DFAR/FPE/98/96';
                const isAir = data.transportAeroPlane ?? false;
                const isSea = data.transportShip ?? true;

                this.form.patchValue({
                    certificateReferenceNo: certNo,
                    dateOfIssue: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    consignorName: data.consignorName || 'EJILAN EXPORT (Pvt) Ltd,',
                    consignorAddress: consignorAddr || 'S K ROAD, KALPITIYA\nSRI LANKA',
                    consigneeName: data.consigneeName || 'ASHKANANI FISHERIES',
                    consigneeAddress: consigneeAddr || 'AL KUWAIT,SOUQ MUBARAKIYA\nAWTAD TOWER,\n3RD FLOOR\nOFFICE NO 16,KUWAIT',
                    countryOfOrigin: data.countryOrigin || 'SRI LANKA',
                    countryOfOriginIso: data.countryOriginISO || 'SRI LANKA',
                    countryOfDestination: data.countryDestinationISO ? 'KUWAIT' : 'KUWAIT',
                    countryOfDestinationIso: 'KUWAIT',
                    producerName: plantName,
                    producerAddress: plantAddr || 'NO:422/D, PARANAMBALAMA\nUSWETAKEIYAWA\nSRI LANKA',
                    packingEstName: plantName,
                    packingEstAddress: plantAddr || 'NO:422/D, PARANAMBALAMA\nUSWETAKEIYAWA SRI LANKA',
                    packingEstApprovalNo: approvalNo,
                    borderOfEntry: data.entryBIP || 'KUWAIT',
                    borderLoadingCountry: 'SRI LANKA',
                    borderLoadingPlace: data.placeOfLoading || 'COLOMBO',
                    transportByAir: isAir,
                    transportBySea: isSea,
                    vehicleIdentificationNo: data.transportId || 'NIL',
                    tempChilled: data.temperatureChilled ?? false,
                    tempFrozen: data.temperatureFrozen ?? true,
                    commoditiesOther: false,
                    commoditiesAfterFurtherProcess: false,
                    commoditiesHumanConsumption: true,
                    signatureDate: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date()
                });

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p) => {
                        const nameDesc = `${p.descCommon ? p.descCommon.toUpperCase() : ''}\n(${p.descScientific || ''})`.trim();
                        this.products.push(
                            this.fb.group({
                                nameDescription: [nameDesc],
                                hsCodes: [p.hsCode || '0302 89'],
                                treatmentDerivedFrom: [''],
                                brandName: [''],
                                productionDate: [null],
                                expiryDate: [null],
                                numberPackages: [parseInt(p.numPackages || '0', 10) || 0],
                                batchLotNo: [''],
                                totalWeight: [parseFloat(p.netWeight || '0') || 0]
                            })
                        );
                    });
                } else if (data.descCommon) {
                    const nameDesc = `${data.descCommon.toUpperCase()}\n(${data.descScientific || ''})`.trim();
                    this.products.push(
                        this.fb.group({
                            nameDescription: [nameDesc],
                            hsCodes: [data.hsCode || '0302 89'],
                            treatmentDerivedFrom: [''],
                            brandName: [''],
                            productionDate: [null],
                            expiryDate: [null],
                            numberPackages: [parseInt(data.numPackages || '0', 10) || 0],
                            batchLotNo: [''],
                            totalWeight: [parseFloat(data.netWeight || '0') || 0]
                        })
                    );
                } else {
                    this.products.push(this.createProductRow());
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
            this.selectedUserQualification = selected.qualification || 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).';
            this.showPasswordDialog = true;
        }
    }

    onSignatoryConfirmed(officerName: string) {
        if (this.pendingSignatoryUserId) {
            this.form.patchValue({
                signatoryUserId: this.pendingSignatoryUserId,
                signatoryName: officerName,
                qualification: this.selectedUserQualification || 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).'
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

        const productPayloads: CreateKwCertificateProductPayload[] = (rawValue.products || []).map((p: any) => ({
            nameDescription: p.nameDescription || '',
            hsCodes: p.hsCodes || '',
            treatmentDerivedFrom: p.treatmentDerivedFrom || '',
            brandName: p.brandName || '',
            productionDate: toLocalISOString(p.productionDate),
            expiryDate: toLocalISOString(p.expiryDate),
            numberPackages: Number(p.numberPackages) || 0,
            batchLotNo: p.batchLotNo || '',
            totalWeight: Number(p.totalWeight) || 0
        }));

        const payload: CreateKwCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            certificateReferenceNo: rawValue.certificateReferenceNo || '',
            placeOfIssue: rawValue.placeOfIssue || '',
            dateOfIssue: toLocalISOString(rawValue.dateOfIssue),
            consignorName: rawValue.consignorName || '',
            consignorAddress: rawValue.consignorAddress || '',
            consigneeName: rawValue.consigneeName || '',
            consigneeAddress: rawValue.consigneeAddress || '',
            competentAuthority: rawValue.competentAuthority || '',
            competentAuthorityAddress: rawValue.competentAuthorityAddress || '',
            countryOfOrigin: rawValue.countryOfOrigin || '',
            countryOfOriginIso: rawValue.countryOfOriginIso || '',
            countryOfDestination: rawValue.countryOfDestination || '',
            countryOfDestinationIso: rawValue.countryOfDestinationIso || '',
            producerName: rawValue.producerName || '',
            producerAddress: rawValue.producerAddress || '',
            packingEstName: rawValue.packingEstName || '',
            packingEstAddress: rawValue.packingEstAddress || '',
            packingEstApprovalNo: rawValue.packingEstApprovalNo || '',
            borderOfEntry: rawValue.borderOfEntry || '',
            borderLoadingCountry: rawValue.borderLoadingCountry || '',
            borderLoadingPlace: rawValue.borderLoadingPlace || '',
            transportByAir: !!rawValue.transportByAir,
            transportBySea: !!rawValue.transportBySea,
            vehicleIdentificationNo: rawValue.vehicleIdentificationNo || '',
            tempChilled: !!rawValue.tempChilled,
            tempFrozen: !!rawValue.tempFrozen,
            commoditiesOther: !!rawValue.commoditiesOther,
            commoditiesAfterFurtherProcess: !!rawValue.commoditiesAfterFurtherProcess,
            commoditiesHumanConsumption: !!rawValue.commoditiesHumanConsumption,
            officialStamp: rawValue.officialStamp || '',
            officialSignature: rawValue.officialSignature || '',
            signatureDate: toLocalISOString(rawValue.signatureDate),
            signatoryUserId: rawValue.signatoryUserId || null,
            signatoryName: rawValue.signatoryName || '',
            qualification: rawValue.qualification || '',
            certificateType: 'single',
            products: productPayloads
        };

        this.certificateService.submitKwCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Kuwait Health Certificate saved successfully'
                });
            },
            error: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to save Kuwait Health Certificate'
                });
            }
        });
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

    private checkRequestApproval(requestId: number): void {
        this.certificateService.getRequestById(requestId).subscribe({
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

        const ref = this.form.get('certificateReferenceNo')?.value || 'Draft';
        const pdfName = `${ref}_Kuwait_Veterinary_Certificate.pdf`;

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
