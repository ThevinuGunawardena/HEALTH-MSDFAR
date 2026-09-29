import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { CheckboxModule } from 'primeng/checkbox';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { UserService, User } from '@/pages/service/user.service';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { AmCertificateView, CertificateRequestService, CreateAmCertificatePayload, VetFormFieldResponse, VetProductFieldResponse } from '../../../service/certificate-request.service';
import { Select } from 'primeng/select';
import { toLocalISOString } from '@/shared/utils/date-utils';
import { TooltipModule } from 'primeng/tooltip';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';

export const DEFAULT_AM_PRODUCTS = [
    { product: 'FRESH THONDI SQUID WHOLE (Sepioteuthis lessoniana)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH CUTTLE FISH WHOLE (Sepia spp)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH NEEDLE SQUID WHOLE (Uroteuthis singhalensis)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH YELLOW FIN TUNA FISH LOINS (Thunnus albacares)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BLACK SPOTTED GROUPER (Epinephelus malabaricus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BROWN SPOTTED GROUPER (Epinephelus chlorostigma)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH RED SNAPPER (Lutjanus sp)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH PARROT FISH (Scarus ghobban.)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH KING FISH (Scomberomorus commerson)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH THREAD FIN BEAMS (Nemipterus japonicus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH INDIAN MACKEREL (Rastrelliger kanagurata)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BLACK TIGER PRAWNS (Penaeus monodon)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH OCTOPUS (Octopus spp)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BLACK POMFRET WHOLE (Parastromateus niger)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH WATER PRAWNS (Macrobrachium rosenbergi)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BLUE SWIMMIMG CRABS (Portunus pelagicus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH YELLOW FIN TUNA FISH WHOLE (Thunnus albacares)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH INDIAN ANCHOVY FISH (Stolephorus indicus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH RED MULLET FISH (Parupeneus indicus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH LADY FISH (Silago sihama)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SILVER POMFRET FISH (Pampus argenteus)', numberOfKgs: '', numberOfBoxes: '' }
];

@Component({
    selector: 'app-am-certificate',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, InputTextModule, TextareaModule, ButtonModule, DatePicker, ToastModule, CheckboxModule, Select, TooltipModule, ConfirmPasswordDialogComponent, CertificateQrComponent],
    providers: [MessageService],
    templateUrl: './am-certificate.component.html',
    styleUrls: ['./am-certificate.component.css', '../certificate-print.css']
})
export class AmCertificateComponent implements OnInit {
    form: FormGroup;
    requestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isCompany = false;
    isApproved = false;
    isSaving = false;
    userOptions: { label: string; value: string }[] = [];
    users: User[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    previousSignatoryUserId: string | null = null;

    preExportCertificatesRows: any[] = [];
    attachmentRows: any[] = [];

    get shouldShowSignatorySelect(): boolean {
        return (!this.viewOnly || !this.form.get('signatoryName')?.value) && !this.isCompany;
    }

    get showReadonlySignatory(): boolean {
        return this.viewOnly && !!this.form.get('signatoryName')?.value;
    }

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
        private certificateRequestService: CertificateRequestService,
        private userService: UserService,
        private location: Location,
        private authService: AuthService
    ) {
        this.form = this.fb.group({
            consignorName: [''],
            consignorAddress: [''],
            consigneeName: [''],
            consigneeAddress: [''],
            transportAeroPlane: [false],
            transportShip: [false],
            transportRailwayWagon: [false],
            transportRoadVehicle: [false],
            transportOther: [false],
            transportId: ['AIR'],
            countryOfTransit: ['NONE'],
            certificateNo: ['SX 2008'],
            countryOrigin: ['SRI LANKA'],
            countryIssuing: ['SRI LANKA'],
            competentAuthorityExporting: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],
            organizationIssuing: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],
            pointOfCrossingBorder: ['YEREVAN (EVN)'],

            productName: ['FRESH CHILLED (SEE ATTACHMENT)'],
            productionDate: [new Date()],
            packagingType: ['PACKED WHOLE IN STYROFOAM BOXES WITH GEL ICE & RAW ICE.'],
            numPackages: ['SEE ATTACHMENT'],
            netWeight: ['SEE ATTACHMENT'],
            numberOfSeal: ['NONE'],
            identificationMarks: ['DFAR/FPE/98/07'],
            storageConditions: ['0/+4 DEGREES CELCIUS'],

            processingEstName: [''],
            processingEstRegNo: ['DFAR/FPE/98/07'],
            processingEstAddress: [''],
            factoryVessel: ['XXXXXXXXXXXXXXXXXX'],
            coldStore: ['XXXXXXXXXXXXXXXXXXXXXXX'],
            administrativeUnit: ['COLOMBO, SRI LANKA'],

            preExportCertificates: this.fb.array([this.createPreExportCertificateRow()]),

            placeOfIssue: ['COLOMBO, SRI LANKA'],
            dateOfIssue: [new Date()],
            signatoryName: [''],
            qualification: ['QUALITY CONTROL OFFICER (GRADE II)'],

            dateOfAttachment: [new Date()],
            identificationMarksAttachment: ['DFAR/FPE/98/07'],
            attachments: this.fb.array(DEFAULT_AM_PRODUCTS.map((p) => this.createAttachmentRow(p))),
            signatoryUserId: [null, Validators.required]
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
            this.userOptions = users.filter((u) => u.roleName && (u.roleName.toLowerCase() === 'user' || u.roleName.toLowerCase() === 'admin')).map((u) => ({ label: u.name, value: u.id }));
        });
        this.route.queryParams.subscribe((params) => {
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
            }
            if (params['ref']) {
                this.form.patchValue({ certificateNo: params['ref'] });
            }
            if (params['requestId']) {
                this.requestId = +params['requestId'];
                this.checkRequestApproval(this.requestId);
                this.loadSavedCertificateData(this.requestId);
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
        this.selectedUserQualification = user?.qualification || null;
        if (user) {
            this.form.patchValue({
                signatoryName: user.name,
                qualification: user.qualification || 'QUALITY CONTROL OFFICER (GRADE II)'
            });
        }
    }

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.previousSignatoryUserId = null;
            this.setSelectedUserQualification(userId);
            return;
        }

        const user = this.users.find((u) => u.id === userId);
        if (!user) return;

        this.pendingSignatoryUserId = userId;
        this.pendingSignatoryUserEmail = user.email;
        this.showPasswordDialog = true;
    }

    onSignatoryConfirmed(userId: string) {
        this.previousSignatoryUserId = userId;
        this.setSelectedUserQualification(userId);
        this.showPasswordDialog = false;
    }

    onSignatoryCanceled() {
        this.showPasswordDialog = false;
        this.form.get('signatoryUserId')?.setValue(this.previousSignatoryUserId, { emitEvent: false });

        if (this.previousSignatoryUserId) {
            this.setSelectedUserQualification(this.previousSignatoryUserId);
        } else {
            this.selectedUserQualification = null;
            this.form.patchValue({
                signatoryName: '',
                qualification: 'QUALITY CONTROL OFFICER (GRADE II)'
            });
        }
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateRequestService.getAmCertificateByRequestId(requestId).subscribe({
            next: (data: any) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }
                this.form.patchValue({
                    consignorName: data.consignorName,
                    consignorAddress: data.consignorAddress,
                    consigneeName: data.consigneeName,
                    consigneeAddress: data.consigneeAddress,
                    transportAeroPlane: data.transportAeroPlane,
                    transportShip: data.transportShip,
                    transportRailwayWagon: data.transportRailwayWagon,
                    transportRoadVehicle: data.transportRoadVehicle,
                    transportOther: data.transportOther,
                    transportId: data.transportId || 'AIR',
                    countryOfTransit: data.countryOfTransit || 'NONE',
                    certificateNo: data.certificateNo || data.certRefNumber || data.healthCertNo || '',
                    countryOrigin: data.countryOrigin || 'SRI LANKA',
                    countryIssuing: data.countryIssuing || 'SRI LANKA',
                    competentAuthorityExporting: data.competentAuthorityExporting || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    organizationIssuing: data.organizationIssuing || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    pointOfCrossingBorder: data.pointOfCrossingBorder || 'YEREVAN (EVN)',
                    productName: data.productName || 'FRESH CHILLED (SEE ATTACHMENT)',
                    productionDate: data.productionDate ? new Date(data.productionDate) : null,
                    packagingType: data.packagingType || 'PACKED WHOLE IN STYROFOAM BOXES WITH GEL ICE & RAW ICE.',
                    numPackages: data.numPackages || 'SEE ATTACHMENT',
                    netWeight: data.netWeight || 'SEE ATTACHMENT',
                    numberOfSeal: data.numberOfSeal || 'NONE',
                    identificationMarks: data.identificationMarks || 'DFAR/FPE/98/07',
                    storageConditions: data.storageConditions || '0/+4 DEGREES CELCIUS',
                    processingEstName: data.processingEstName,
                    processingEstRegNo: data.processingEstRegNo,
                    processingEstAddress: data.processingEstAddress,
                    factoryVessel: data.factoryVessel || 'XXXXXXXXXXXXXXXXXX',
                    coldStore: data.coldStore || 'XXXXXXXXXXXXXXXXXXXXXXX',
                    administrativeUnit: data.administrativeUnit || 'COLOMBO, SRI LANKA',
                    placeOfIssue: data.placeOfIssue || 'COLOMBO, SRI LANKA',
                    dateOfIssue: data.dateOfIssue ? new Date(data.dateOfIssue) : new Date(),
                    signatoryUserId: data.signatoryUserId,
                    signatoryName: data.signatoryName,
                    qualification: data.qualification || 'QUALITY CONTROL OFFICER (GRADE II)',
                    dateOfAttachment: data.dateOfAttachment ? new Date(data.dateOfAttachment) : new Date(),
                    identificationMarksAttachment: data.identificationMarksAttachment || data.identificationMarks || 'DFAR/FPE/98/07'
                });

                if (data.preExportCertificates && data.preExportCertificates.length > 0) {
                    this.preExportCertificatesRows = data.preExportCertificates;
                    const arr = this.form.get('preExportCertificates') as FormArray;
                    arr.clear();
                    data.preExportCertificates.forEach((p: any) => {
                        arr.push(
                            this.fb.group({
                                date: [p.date ? new Date(p.date) : null],
                                number: [p.number],
                                countryOfOrigin: [p.countryOfOrigin],
                                administrativeTerritory: [p.administrativeTerritory],
                                approvalNumber: [p.approvalNumber],
                                productNameAndQuantity: [p.productNameAndQuantity]
                            })
                        );
                    });
                } else {
                    this.preExportCertificatesRows = [];
                }

                if (data.attachments && data.attachments.length > 0) {
                    this.attachmentRows = data.attachments;
                    const arr = this.form.get('attachments') as FormArray;
                    arr.clear();
                    data.attachments.forEach((a: any) => {
                        arr.push(this.createAttachmentRow(a));
                    });
                } else {
                    this.loadDefaultProducts();
                }
            },
            error: () => {
                this.loadVetFormData(requestId);
            }
        });
    }

    private loadVetFormData(requestId: number) {
        this.certificateRequestService.getVetFormByRequestId(requestId).subscribe({
            next: (data) => {
                if (!data) return;
                this.autofillForm(data);
            },
            error: () => {
                console.error('Failed to load vet form data for autofill');
            }
        });
    }

    private autofillForm(data: VetFormFieldResponse) {
        if (!data) return;
        if (data.products && data.products.length > 0) {
            this.mapVetProductsToAttachments(data.products, data);
        } else {
            this.loadDefaultProducts();
        }

        const defaultRegNo = data.approvalNo || 'DFAR/FPE/98/07';

        this.form.patchValue({
            consignorName: data.consignorName || '',
            consignorAddress: data.consignorAddress || '',

            consigneeName: data.consigneeName || '',
            consigneeAddress: data.consigneeAddress || '',

            certificateNo: data.healthCertNo || data.newHC || '',
            countryOrigin: data.countryOrigin || 'SRI LANKA',
            countryIssuing: 'SRI LANKA',
            competentAuthorityExporting: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            organizationIssuing: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            pointOfCrossingBorder: 'YEREVAN (EVN)',
            countryOfTransit: 'NONE',

            transportAeroPlane: data.transportAeroPlane ?? true,
            transportShip: data.transportShip ?? false,
            transportRailwayWagon: data.transportRailwayWagon ?? false,
            transportRoadVehicle: data.transportRoadVehicle ?? false,
            transportOther: data.transportOther ?? false,
            transportId: data.transportId || 'AIR',

            productName: 'FRESH CHILLED (SEE ATTACHMENT)',
            packagingType: data.packagingType || 'PACKED WHOLE IN STYROFOAM BOXES WITH GEL ICE & RAW ICE.',
            numPackages: 'SEE ATTACHMENT',
            netWeight: 'SEE ATTACHMENT',
            numberOfSeal: 'NONE',
            identificationMarks: defaultRegNo,
            storageConditions: '0/+4 DEGREES CELCIUS',

            processingEstName: data.processingEstName || '',
            processingEstAddress: data.processingEstAddress || '',
            processingEstRegNo: defaultRegNo,
            factoryVessel: 'XXXXXXXXXXXXXXXXXX',
            coldStore: 'XXXXXXXXXXXXXXXXXXXXXXX',
            administrativeUnit: data.administrativeUnit || 'COLOMBO, SRI LANKA',

            productionDate: data.processingDate ? new Date(data.processingDate) : new Date(),

            placeOfIssue: 'COLOMBO, SRI LANKA',
            dateOfIssue: new Date(),
            dateOfAttachment: new Date(),
            identificationMarksAttachment: defaultRegNo,
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)'
        });

        // Initialize default preExportCertificates row
        const preArr = this.preExportCertificates;
        preArr.clear();
        preArr.push(
            this.fb.group({
                date: [new Date()],
                number: ['XXX'],
                countryOfOrigin: ['SRI LANKA'],
                administrativeTerritory: ['COLOMBO'],
                approvalNumber: [defaultRegNo],
                productNameAndQuantity: ['SEE ATTACHMENT']
            })
        );
    }

    private mapVetProductsToAttachments(products: VetProductFieldResponse[] | undefined, data: VetFormFieldResponse) {
        const mappedProducts = Array.isArray(products) && products.length > 0
            ? products
            : [
                  {
                      descCommon: data.descCommon,
                      descScientific: data.descScientific,
                      numPackages: data.numPackages,
                      netWeight: data.netWeight
                  }
              ];

        const attachmentArray = this.attachments;
        while (attachmentArray.length) {
            attachmentArray.removeAt(0);
        }

        mappedProducts.forEach((product) => {
            const common = (product.descCommon || '').trim().toUpperCase();
            const sci = (product.descScientific || '').trim();
            let prodTitle = '';
            if (common && sci) {
                prodTitle = `FRESH ${common} (${sci})`;
            } else if (common) {
                prodTitle = `FRESH ${common}`;
            } else if (sci) {
                prodTitle = `FRESH (${sci})`;
            } else {
                prodTitle = 'FRESH CHILLED FISH';
            }

            attachmentArray.push(
                this.fb.group({
                    product: [prodTitle],
                    numberOfKgs: [product.netWeight ?? product.quantity ?? ''],
                    numberOfBoxes: [product.numPackages ?? '']
                })
            );
        });

        if (attachmentArray.length === 0) {
            this.loadDefaultProducts();
        }

        this.attachmentRows = attachmentArray.value;
    }

    get attachments(): FormArray {
        return this.form.get('attachments') as FormArray;
    }

    get preExportCertificates(): FormArray {
        return this.form.get('preExportCertificates') as FormArray;
    }

    createAttachmentRow(data?: any): FormGroup {
        return this.fb.group({
            product: [data?.product || ''],
            numberOfKgs: [data?.numberOfKgs !== undefined && data?.numberOfKgs !== null && data?.numberOfKgs !== 0 ? data.numberOfKgs : ''],
            numberOfBoxes: [data?.numberOfBoxes !== undefined && data?.numberOfBoxes !== null && data?.numberOfBoxes !== 0 ? data.numberOfBoxes : '']
        });
    }

    loadDefaultProducts(): void {
        this.attachments.clear();
        DEFAULT_AM_PRODUCTS.forEach((p) => {
            this.attachments.push(this.createAttachmentRow(p));
        });
        this.attachmentRows = this.attachments.value;
    }

    clearProducts(): void {
        this.attachments.clear();
        this.attachments.push(this.createAttachmentRow());
        this.attachmentRows = this.attachments.value;
    }

    formatProductName(product: string): string {
        if (!product) return '';
        return product.replace(/\(([^)]+)\)/g, '<em>($1)</em>');
    }

    createPreExportCertificateRow(): FormGroup {
        return this.fb.group({
            date: [new Date()],
            number: [''],
            countryOfOrigin: ['SRI LANKA'],
            administrativeTerritory: ['COLOMBO'],
            approvalNumber: [''],
            productNameAndQuantity: ['']
        });
    }

    addAttachment(): void {
        this.attachments.push(this.createAttachmentRow());
    }

    addPreExportCertificate(): void {
        this.preExportCertificates.push(this.createPreExportCertificateRow());
    }

    removeAttachment(index: number): void {
        if (this.attachments.length > 1) {
            this.attachments.removeAt(index);
        }
    }

    removePreExportCertificate(index: number): void {
        if (this.preExportCertificates.length > 1) {
            this.preExportCertificates.removeAt(index);
        }
    }

    calculateTotalKgs(): string {
        const total = this.attachments.controls.reduce((sum, c) => {
            const val = c.get('numberOfKgs')?.value;
            const parsed = typeof val === 'number' ? val : parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
    }

    calculateTotalBoxes(): string {
        const total = this.attachments.controls.reduce((sum, c) => {
            const val = c.get('numberOfBoxes')?.value;
            const parsed = typeof val === 'number' ? val : parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total}` : '';
    }

    loadSampleData(): void {
        this.form.patchValue({
            certificateNo: 'SX 2008',
            consignorName: 'GLOBAL SEAFOODS (PVT) LTD',
            consignorAddress: 'NO. 42, BADALGAMA ROAD, NEGOMBO, SRI LANKA',
            consigneeName: 'ARMENIA SEAFOOD IMPORTS LLC',
            consigneeAddress: 'YEREVAN, REPUBLIC OF ARMENIA',
            countryOrigin: 'SRI LANKA',
            countryIssuing: 'SRI LANKA',
            competentAuthorityExporting: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            organizationIssuing: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            pointOfCrossingBorder: 'YEREVAN (EVN)',
            countryOfTransit: 'NONE',
            transportId: 'AIR / QR 663',
            productName: 'FRESH CHILLED (SEE ATTACHMENT)',
            packagingType: 'PACKED WHOLE IN STYROFOAM BOXES WITH GEL ICE & RAW ICE.',
            numPackages: 'SEE ATTACHMENT',
            netWeight: 'SEE ATTACHMENT',
            numberOfSeal: 'NONE',
            identificationMarks: 'DFAR/FPE/98/07',
            storageConditions: '0/+4 DEGREES CELCIUS',
            processingEstName: 'GLOBAL SEAFOODS (PVT) LTD',
            processingEstAddress: 'NO. 42, BADALGAMA ROAD, NEGOMBO, SRI LANKA',
            processingEstRegNo: 'DFAR/FPE/98/07',
            factoryVessel: 'XXXXXXXXXXXXXXXXXX',
            coldStore: 'XXXXXXXXXXXXXXXXXXXXXXX',
            administrativeUnit: 'COLOMBO, SRI LANKA',
            placeOfIssue: 'COLOMBO, SRI LANKA',
            dateOfIssue: new Date(),
            dateOfAttachment: new Date(),
            identificationMarksAttachment: 'DFAR/FPE/98/07',
            signatoryName: 'H.M.U. BANDARA',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'
        });
        this.loadDefaultProducts();
        this.messageService.add({
            severity: 'info',
            summary: 'Sample Loaded',
            detail: 'Loaded standard Armenia Certificate Sample (SX 2008).'
        });
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

        if (this.form.valid) {
            this.isSaving = true;
            const payload: any = {
                ...this.form.value,
                certificateRequestId: this.requestId,
                productionDate: toLocalISOString(this.form.value.productionDate),
                dateOfIssue: toLocalISOString(this.form.value.dateOfIssue),
                dateOfDeparture: toLocalISOString(this.form.value.dateOfDeparture),
                dateOfAttachment: toLocalISOString(this.form.value.dateOfAttachment),
                preExportCertificates: this.form.value.preExportCertificates.map((cert: any) => ({
                    ...cert,
                    date: toLocalISOString(cert.date)
                })),
                attachments: (this.form.value.attachments || []).map((a: any) => ({
                    product: a.product || '',
                    numberOfKgs: a.numberOfKgs ? Number(a.numberOfKgs) : 0,
                    numberOfBoxes: a.numberOfBoxes ? Number(a.numberOfBoxes) : 0
                }))
            };

            this.certificateRequestService.submitAmCertificate(payload).subscribe({
                next: (response) => {
                    this.isSaving = false;
                    this.messageService.add({ severity: 'success', summary: 'Success', detail: 'Armenia Certificate Form Submitted Successfully' });
                    if (this.requestId) {
                        try {
                            const submitted = JSON.parse(localStorage.getItem('dfar_submitted_requests') || '[]');
                            if (!submitted.includes(this.requestId)) {
                                submitted.push(this.requestId);
                                localStorage.setItem('dfar_submitted_requests', JSON.stringify(submitted));
                            }
                        } catch {}
                    }
                    setTimeout(() => this.goBack(), 1500);
                },
                error: (error) => {
                    this.isSaving = false;
                    this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Failed to submit certificate' });
                    console.error('Submission error:', error);
                }
            });
        } else {
            this.form.markAllAsTouched();
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields' });
        }
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

    print(): void {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }

        const ref = this.form.get('certificateNo')?.value || this.requestId || 'Armenia';
        const originalTitle = document.title;
        document.title = `${ref}_Armenia_Veterinary_Certificate.pdf`;
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }

    goBack(): void {
        const userRole = (this.authService.getUserRole() || '').toLowerCase();
        const targetUrl = userRole === 'admin' ? '/uikit/admin/certificate-requests' : '/uikit/company-request';
        if (typeof window !== 'undefined' && window.self !== window.top && window.top) {
            window.top.location.href = targetUrl;
        } else {
            this.router.navigate([targetUrl]);
        }
    }
}
