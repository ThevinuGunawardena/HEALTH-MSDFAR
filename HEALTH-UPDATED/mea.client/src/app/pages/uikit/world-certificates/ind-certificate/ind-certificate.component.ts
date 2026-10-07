import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
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
import { ActivatedRoute, Router } from '@angular/router';
import {
    IndCertificateView,
    CertificateRequestService,
    CreateIndCertificatePayload,
    VetFormFieldResponse,
    VetProductFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { TableModule } from 'primeng/table';
import { Select } from 'primeng/select';
import { TooltipModule } from 'primeng/tooltip';
import { AuthService } from '@/pages/service/auth.service';
import { UserService, User } from '@/pages/service/user.service';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-ind-certificate',
    standalone: true,
    imports: [CommonModule,
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
        CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './ind-certificate.component.html',
    styleUrls: ['./ind-certificate.component.css', '../certificate-print.css']
})
export class IndCertificateComponent implements OnInit {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    form: FormGroup;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }
    isApproved = false;
    isSubmitted = false;

    // View mode: 'import_fish' (PDF 1 - 3 pages), 'processed_seafood' (PDF 2 - 2 pages)
    viewMode: 'import_fish' | 'processed_seafood' = 'import_fish';
    refNumber: string = '';

    users: User[] = [];
    userOptions: { label: string; value: string }[] = [];
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    selectedUserQualification: string | null = null;
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
        private authService: AuthService,
        private location: Location,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['import_fish'],
            certificateNumber: [''],
            myRef: [''],
            yourRef: [''],
            date: [new Date()],
            countryOfDispatch: [''],
            competentAuthorityDetails: [
                'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES\nP.O. BOX 531, SECRETARIAT, MALIGAWATTA,\nCOLOMBO 10, SRI LANKA\nTELEPHONE: +94-2449170, 2472186, 2472192\nFAX: +94-11-2424086,2449170\nE-MAIL: dgdfar@gmail.com'
            ],

            consignorName: [''],
            consignorAddress: [''],
            consignorTel: [''],
            consigneeName: [''],
            consigneeAddress: [''],
            consigneeTel: [''],

            countryOfOrigin: [''],
            countryOfOriginIso: [''],
            countryOfDestination: [''],
            countryOfDestinationIso: [''],
            placeOfLoading: [''],

            meansOfTransport: [''],
            declaredPointOfEntry: [''],
            conditionsForTransportStorage: [''],
            totalQuantity: [''],
            invoiceNoDate: [''],

            foodDescription: [''],
            intendedPurpose: ['HUMAN CONSUMPTION'],
            producerNameAddress: [''],
            approvalNumberDetails: [''],

            products: this.fb.array([]),

            dateOfManufacture: [new Date()],
            bestBefore: [null],
            dateOfExpiry: [null],

            // Processed seafood specific fields
            itemDescription: ['FROZEN TUNA SAKU (Thunnus albacares) - WILD CAUGHT'],
            numberOfPackagesStr: [''],
            netWeightStr: [''],
            processingPlantNameAddress: [''],
            processingPlantRegNo: ['DFAR/FPE/98/31'],
            dispatchFrom: ['COLOMBO – SRI LANKA'],
            dispatchTo: ['NHAVA SHEVA - INDIA'],
            modeOfTransport: ['BY SEA FREIGHT'],
            hsCode: ['0304 49 40'],
            speciesName: ['Thunnus albacares'],
            previousCertRef: ['TA 8846 dated 12.02.2026'],
            consignmentIdentificationDetails: [''],
            productDescription: ['FROZEN YF TUNA SAKU'],

            attestationPlace: ['COLOMBO – SRI LANKA'],
            attestationDate: [new Date()],

            signatoryName: [''],
            qualification: ['QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).'],
            authorizedOfficialDate: [new Date()],
            authorizedOfficialSignature: [''],
            officialStamp: [''],
            signatoryUserId: [null, Validators.required]
        });

        this.form.get('viewMode')?.valueChanges.subscribe((mode: 'import_fish' | 'processed_seafood') => {
            this.viewMode = mode;
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    createProductRow(name: string = '', lot: string = '', pkg: string = 'VACUUMED PACKED BAGS WRAPPED IN A POLYTHENE COVER & PUT IN TO A MASTER CARTONS', numPackages: number | null = null, netWeight: number | null = null): FormGroup {
        return this.fb.group({
            nameOfProduct: [name],
            lotNo: [lot],
            typeOfPackaging: [pkg],
            numberOfPackages: [numPackages],
            netWeight: [netWeight]
        });
    }

    addProduct(name: string = '', lot: string = '', pkg: string = '', numPackages: number | null = null, netWeight: number | null = null) {
        this.products.push(this.createProductRow(name, lot, pkg, numPackages, netWeight));
    }

    removeProduct(index: number) {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
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
                this.form.patchValue({ certificateNumber: params['ref'], myRef: params['ref'] });
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

    onViewModeChange(mode: 'import_fish' | 'processed_seafood') {
        this.viewMode = mode;
        this.form.get('viewMode')?.setValue(mode);
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateService.getIndCertificateByRequestId(requestId).subscribe({
            next: (data: IndCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                const mode = (data.certificateType as 'import_fish' | 'processed_seafood') || 'import_fish';
                this.viewMode = mode;
                if (data.id || data.certificateNumber || data.consignorName) {
                    this.isSubmitted = true;
                }

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813'];
                const cleanCertNo = (data.certificateNumber && !dummyValues.includes(data.certificateNumber.trim())) ? data.certificateNumber : '';
                const finalCertNo = this.refNumber || data.referenceNumber || cleanCertNo || '';

                this.form.patchValue({
                    viewMode: mode,
                    countryOfDispatch: data.countryOfDispatch || 'SRI LANKA',
                    certificateNumber: finalCertNo,
                    myRef: finalCertNo || data.myRef || '',
                    yourRef: data.yourRef || '',
                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    consignorTel: data.consignorTel || '',
                    competentAuthorityDetails: data.competentAuthorityDetails || this.form.get('competentAuthorityDetails')?.value,
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    consigneeTel: data.consigneeTel || '',
                    countryOfOrigin: data.countryOfOrigin || 'SRI LANKA',
                    countryOfOriginIso: data.countryOfOriginIso || 'LK',
                    countryOfDestination: data.countryOfDestination || 'INDIA',
                    countryOfDestinationIso: data.countryOfDestinationIso || 'IND',
                    placeOfLoading: data.placeOfLoading || 'COLOMBO',
                    meansOfTransport: data.meansOfTransport || 'AIR FREIGHT',
                    declaredPointOfEntry: data.declaredPointOfEntry || 'NHAVA SHEVA - INDIA',
                    conditionsForTransportStorage: data.conditionsForTransportStorage || '-18°C',
                    totalQuantity: data.totalQuantity || '',
                    invoiceNoDate: data.invoiceNoDate || '',
                    foodDescription: data.foodDescription || '',
                    intendedPurpose: data.intendedPurpose || 'HUMAN CONSUMPTION',
                    producerNameAddress: data.producerNameAddress || '',
                    approvalNumberDetails: data.approvalNumberDetails || '',

                    dateOfManufacture: data.dateOfManufacture ? new Date(data.dateOfManufacture) : new Date(),
                    bestBefore: data.bestBefore ? new Date(data.bestBefore) : null,
                    dateOfExpiry: data.dateOfExpiry ? new Date(data.dateOfExpiry) : null,

                    itemDescription: data.itemDescription || '',
                    numberOfPackagesStr: data.numberOfPackagesStr || '',
                    netWeightStr: data.netWeightStr || '',
                    processingPlantNameAddress: data.processingPlantNameAddress || '',
                    processingPlantRegNo: data.processingPlantRegNo || 'DFAR/FPE/98/31',
                    dispatchFrom: data.dispatchFrom || 'COLOMBO – SRI LANKA',
                    dispatchTo: data.dispatchTo || 'NHAVA SHEVA - INDIA',
                    modeOfTransport: data.modeOfTransport || 'BY SEA FREIGHT',
                    hsCode: data.hsCode || '0304 49 40',
                    speciesName: data.speciesName || '',
                    previousCertRef: data.previousCertRef || '',
                    consignmentIdentificationDetails: data.consignmentIdentificationDetails || '',
                    productDescription: data.productDescription || '',

                    attestationPlace: data.attestationPlace || 'COLOMBO – SRI LANKA',
                    attestationDate: data.attestationDate ? new Date(data.attestationDate) : new Date(),
                    signatoryUserId: data.signatoryUserId || null,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || '',
                    authorizedOfficialDate: data.authorizedOfficialDate ? new Date(data.authorizedOfficialDate) : new Date(),
                    authorizedOfficialSignature: data.authorizedOfficialSignature || '',
                    officialStamp: data.officialStamp || ''
                });

                this.updateSafeSignatureUrl();
                this.updateSafeStampUrl();

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p) => {
                        this.addProduct(p.nameOfProduct || '', p.lotNo || '', p.typeOfPackaging || '', p.numberOfPackages, p.netWeight);
                    });
                } else {
                    this.addProduct('FROZEN YELLOFIN TUNA SAKU');
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

                const defaultRegNo = data.approvalNo || 'DFAR/FPE/98/31';
                const plantAddress = `${data.processingEstName || data.consignorName || ''}\n${data.processingEstAddress || data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim();

                const isShip = data.transportShip ?? false;
                const meansTransport = isShip ? 'BY SEA FREIGHT' : 'AIR FREIGHT';
                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'ID 8813'];
                const cleanCertNo = (data.healthCertNo && !dummyValues.includes(data.healthCertNo.trim())) ? data.healthCertNo : 
                                    (data.newHC && !dummyValues.includes(data.newHC.trim())) ? data.newHC : '';
                const certNo = this.refNumber || data.referenceNumber || cleanCertNo || '';

                const common = data.products?.[0]?.descCommon || data.descCommon || 'TUNA SAKU';
                const sci = data.products?.[0]?.descScientific || data.descScientific || 'Thunnus albacares';
                const typePrefix = data.temperatureFrozen ? 'FROZEN ' : 'CHILLED ';

                this.form.patchValue({
                    certificateNumber: certNo,
                    myRef: certNo,
                    yourRef: '',
                    countryOfDispatch: data.countryOrigin || 'SRI LANKA',
                    consignorName: data.consignorName || '',
                    consignorAddress: `${data.consignorAddress || ''}\n${data.consignorPostal || ''}\nSRI LANKA`.trim(),
                    consignorTel: data.consignorTel || '',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: `${data.consigneeAddress || ''}\n${data.consigneePostal || ''}`.trim(),
                    consigneeTel: data.consigneeTel || '',

                    countryOfOrigin: data.countryOrigin || 'SRI LANKA',
                    countryOfOriginIso: 'LK',
                    countryOfDestination: 'INDIA',
                    countryOfDestinationIso: 'IND',
                    placeOfLoading: 'COLOMBO',

                    meansOfTransport: meansTransport,
                    declaredPointOfEntry: data.countryDestinationISO ? `${data.countryDestinationISO} - INDIA` : 'NHAVA SHEVA - INDIA',
                    conditionsForTransportStorage: data.temperatureFrozen ? '-18°C' : '+4°C',
                    totalQuantity: `${data.netWeight || ''} kg`,
                    invoiceNoDate: data.docReferences || '',

                    foodDescription: `${data.hsCode || '0304 49 40'}- ${sci}`,
                    intendedPurpose: 'HUMAN CONSUMPTION',
                    producerNameAddress: plantAddress,
                    approvalNumberDetails: `${defaultRegNo}\nVALID FROM - 05.08.2024 to 04.08.2025`,

                    dateOfManufacture: data.processingDate ? new Date(data.processingDate) : new Date(),
                    bestBefore: null,
                    dateOfExpiry: null,

                    itemDescription: `${typePrefix}${common.toUpperCase()} (${sci}) - WILD CAUGHT`,
                    productDescription: `${typePrefix}${common.toUpperCase()}`,
                    numberOfPackagesStr: `${data.numPackages || ''} Cartons`,
                    netWeightStr: `${data.netWeight || ''} kg`,
                    processingPlantNameAddress: plantAddress,
                    processingPlantRegNo: defaultRegNo,
                    dispatchFrom: 'COLOMBO – SRI LANKA',
                    dispatchTo: data.countryDestinationISO ? `${data.countryDestinationISO} - INDIA` : 'NHAVA SHEVA - INDIA',
                    modeOfTransport: meansTransport,
                    hsCode: data.hsCode || '0304 49 40',
                    speciesName: sci,
                    previousCertRef: certNo ? `Health Certificate No. ${certNo}` : '',
                    consignmentIdentificationDetails: data.processingDate || '',

                    attestationPlace: 'COLOMBO – SRI LANKA',
                    attestationDate: new Date(),
                    authorizedOfficialDate: new Date()
                });

                this.products.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p: VetProductFieldResponse) => {
                        const prodName = p.descCommon ? `${typePrefix}${p.descCommon.toUpperCase()}` : '';
                        const nw = p.netWeight ? parseFloat(p.netWeight) || null : null;
                        const pkgs = p.numPackages ? parseInt(p.numPackages, 10) || null : null;
                        this.addProduct(prodName, data.processingDate || '', 'VACUUMED PACKED BAGS WRAPPED IN A POLYTHENE COVER & PUT IN TO A MASTER CARTONS', pkgs, nw);
                    });
                } else {
                    this.addProduct('FROZEN YELLOFIN TUNA SAKU');
                }
            },
            error: () => {
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to load vet form data' });
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
            this.messageService.add({ severity: 'error', summary: 'Validation Error', detail: 'Please fill in all required fields.' });
            return;
        }

        this.isSaving = true;
        const v = this.form.getRawValue();

        const payload: CreateIndCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            certificateType: this.viewMode,
            countryOfDispatch: v.countryOfDispatch,
            certificateNumber: v.certificateNumber,
            myRef: v.myRef,
            yourRef: v.yourRef,
            consignorName: v.consignorName,
            consignorAddress: v.consignorAddress,
            consignorTel: v.consignorTel,
            competentAuthorityDetails: v.competentAuthorityDetails,
            consigneeName: v.consigneeName,
            consigneeAddress: v.consigneeAddress,
            consigneeTel: v.consigneeTel,
            countryOfOrigin: v.countryOfOrigin,
            countryOfOriginIso: v.countryOfOriginIso,
            countryOfDestination: v.countryOfDestination,
            countryOfDestinationIso: v.countryOfDestinationIso,
            placeOfLoading: v.placeOfLoading,
            meansOfTransport: v.meansOfTransport,
            declaredPointOfEntry: v.declaredPointOfEntry,
            conditionsForTransportStorage: v.conditionsForTransportStorage,
            totalQuantity: v.totalQuantity,
            invoiceNoDate: v.invoiceNoDate,
            foodDescription: v.foodDescription,
            intendedPurpose: v.intendedPurpose,
            producerNameAddress: v.producerNameAddress,
            approvalNumberDetails: v.approvalNumberDetails,
            dateOfManufacture: v.dateOfManufacture ? toLocalISOString(v.dateOfManufacture) : null,
            bestBefore: v.bestBefore ? toLocalISOString(v.bestBefore) : null,
            dateOfExpiry: v.dateOfExpiry ? toLocalISOString(v.dateOfExpiry) : null,

            itemDescription: v.itemDescription,
            numberOfPackagesStr: v.numberOfPackagesStr,
            netWeightStr: v.netWeightStr,
            processingPlantNameAddress: v.processingPlantNameAddress,
            processingPlantRegNo: v.processingPlantRegNo,
            dispatchFrom: v.dispatchFrom,
            dispatchTo: v.dispatchTo,
            modeOfTransport: v.modeOfTransport,
            hsCode: v.hsCode,
            speciesName: v.speciesName,
            previousCertRef: v.previousCertRef,
            consignmentIdentificationDetails: v.consignmentIdentificationDetails,
            productDescription: v.productDescription,

            attestationPlace: v.attestationPlace,
            attestationDate: v.attestationDate ? toLocalISOString(v.attestationDate) : null,
            signatoryUserId: v.signatoryUserId,
            signatoryName: v.signatoryName,
            qualification: v.qualification,
            authorizedOfficialDate: v.authorizedOfficialDate ? toLocalISOString(v.authorizedOfficialDate) : null,
            authorizedOfficialSignature: v.authorizedOfficialSignature || '',
            officialStamp: v.officialStamp || '',
            products: (v.products || []).map((p: any) => ({
                nameOfProduct: p.nameOfProduct,
                lotNo: p.lotNo,
                typeOfPackaging: p.typeOfPackaging,
                numberOfPackages: p.numberOfPackages ? parseInt(p.numberOfPackages, 10) : null,
                netWeight: p.netWeight ? parseFloat(p.netWeight) : null
            }))
        };

        this.certificateService.submitIndCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({ severity: 'success', summary: 'Saved', detail: 'India Certificate saved successfully.' });
            },
            error: () => {
                this.isSaving = false;
                this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to save certificate.' });
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
        const sig = this.form.get('authorizedOfficialSignature')?.value;
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
                this.form.patchValue({ authorizedOfficialSignature: result });
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
        this.form.patchValue({ authorizedOfficialSignature: '' });
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
                            certificateNumber: req.referenceNumber,
                            myRef: req.referenceNumber
                        });
                    }
                }
            },
            error: () => {}
        });
    }

    printCertificate() {
        if (!this.isAdmin) {
            this.messageService.add({
                severity: 'error',
                summary: 'Access Denied',
                detail: 'Only administrators have access to print health certificates.'
            });
            return;
        }

        const ref = this.form.get('certificateNumber')?.value || this.form.get('myRef')?.value || 'Draft';
        let pdfName = '';
        if (this.viewMode === 'import_fish') {
            pdfName = `${ref}_Health_Certificate_For_Import_Of_Fish_And_Fish_Products_Into_India.pdf`;
        } else {
            pdfName = `${ref}_Health_Certificate_Processed_Seafood_India.pdf`;
        }

        const originalTitle = document.title;
        document.title = pdfName;
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }

    goBack() {
        this.location.back();
    }
}
