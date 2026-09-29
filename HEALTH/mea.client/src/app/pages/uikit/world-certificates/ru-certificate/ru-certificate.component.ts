import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { CheckboxModule } from 'primeng/checkbox';
import { RadioButton } from 'primeng/radiobutton';
import { TooltipModule } from 'primeng/tooltip';
import { ActivatedRoute, Router } from '@angular/router';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { AuthService } from '@/pages/service/auth.service';
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { UserService, User } from '@/pages/service/user.service';
import {
    CertificateRequestService,
    RuCertificateView,
    CreateRuCertificatePayload,
    RuAttachmentPayload,
    RuPreExportCertificatePayload,
    VetFormFieldResponse
} from 'src/app/pages/service/certificate-request.service';
import { toLocalISOString } from '@/shared/utils/date-utils';

export const DEFAULT_RU_PRODUCTS = [
    { product: 'FRESH TUNA G & G (Thunnus albacares)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH TUNA H&G (Thunnus albacares)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH RED SNAPPER WHOLE (Lutjanus campechanus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SWORD FISH LOINS (Xiphius gladius)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SWORD FISH H & G (Xiphius gladius)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BARRAMUNDI WHOLE (Lates calcarifer)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH TUNA FISH LOINS (Thunnus albacares)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH ETELIS FISH WHOLE ( Etelis carbunculus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH YELLOW TAIL FUSILIER WHOLE (Caesio cuning)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH PARROT FISH WHOLE (Scarus ghobban)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH GRAY GROUPER WHOLE (Epinephelus undulosus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BLUBBERLIP SNAPPER ( Lutjanus rivulatus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH MILK SHARK WHOLE ( Rhizoprinodon actus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH EMPOROR FISH WHOLE(Lethrinus nebulosus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH MARLIN FISH GG (Makaira indica)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH TRAVELLY WHOLE(Caranx ignobilis)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH MAHI MAHI WHOLE (Coryphaena hippurus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH THREAD FIN BEAMS WHOLE(Nemipterus japonicus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SURGEON FISH WHOLE(Acanthurus xanthopterus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SICKEL FISH WHOLE (Drepane punctata)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SRI LANKAN SWEETLIPS WHOLE (Plectorhinchus ceylonens)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH GROUPER WHOLE (Epinephelus malabaricus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH COBIA H & G (Rachycentron canadum)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SOLE FISH WHOLE (Euryglossa orientalis )', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BIG EYE TUNA G & G (Thunnus obesus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH STING RAYS (Raja mamillidens)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH GOAT FISH (Parascolopsis eriomma)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH GREEN JOB FISH WHOLE (Aprion virescens)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH MAHI MAHI FILLET (Coryphaena hippurus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH STING RAYS WINGS (Raja mamillidens)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SWORD FISH GG (Xiphias gladius)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH COBIA WHOLE (Rachycentron canadum)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH ANCHOVY WHOLE (Stolephorus indicus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH BARRACUDA WHOLE (Sphyraena jello)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH KING FISH WHOLE (Scomberomorus commerson)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH RABBIT FISH WHOLE (Siganus guttatus)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH RAINBOW RUNNER WHOLE (Elagatis bipinnulata)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH SILVER WHITING FISH WHOLE(Silago sihama)', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH WAHOO FISH WHOLE (Acathocybium solandri )', numberOfKgs: '', numberOfBoxes: '' },
    { product: 'FRESH YELLOW STRIP SNAPPER (Lutjanus lutjanus)', numberOfKgs: '', numberOfBoxes: '' }
];

@Component({
    selector: 'app-ru-certificate',
    standalone: true,
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        ToastModule,
        CheckboxModule,
        RadioButton,
        Select,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './ru-certificate.component.html',
    styleUrls: ['./ru-certificate.component.css', '../certificate-print.css']
})
export class RuCertificateComponent implements OnInit {
    form: FormGroup;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;
    isSubmitted = false;
    isApproved = false;

    // View mode: 'full' (4 pages) | 'attachment_only' (1 page)
    viewMode: 'full' | 'attachment_only' = 'full';

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

    formatProductName(name: string): string {
        if (!name) return '';
        return name.replace(/(\([A-Za-z\s\.\-]+\))/g, '<span class="italic font-normal">$1</span>');
    }

    constructor(
        private fb: FormBuilder,
        private route: ActivatedRoute,
        private router: Router,
        private messageService: MessageService,
        private certificateService: CertificateRequestService,
        private userService: UserService,
        private authService: AuthService,
        private location: Location,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            viewMode: ['full'],
            includeAttachment: [true],

            // Section 1: Shipment details
            consignorNameAddress: ['TROPICAL NATURE SEAFOOD,\n41/1, MAHAGEDARA, PATHAMULLA,\nKANATHTHEWEWA,\nSRI LANKA.', Validators.required],
            consigneeNameAddress: ['FISHERIES LLC,\n5TH VERHNIY MIHAILOVSKY PROEZD, 6,\n115419, MOSCOW, RUSSIA', Validators.required],
            meansOfTransport: ['AIR', Validators.required],
            countryOfTransit: ['NONE'],
            certificateNo: ['TC 4471', Validators.required],
            countryOrigin: ['SRI LANKA', Validators.required],
            countryIssuing: ['SRI LANKA', Validators.required],
            competentAuthorityExporting: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],
            organizationIssuing: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],
            pointOfCrossingBorder: ['MOSCOW – DME/SVO/VKO', Validators.required],

            // Section 2: Identification of goods
            productName: ['FRESH CHILLED (SEE ATTACHMENT)', Validators.required],
            productionDate: [new Date('2026-08-22')],
            typeOfPackage: [
                'VACUUM PACKED BAGS WRAPPED IN A POLYTHENE COVER & PUT IN TO A STYROFOAM BOXES WITH GEL ICE, WRAPPED IN A POLYTHENE COVER & PUT IN TO A STYROFOAM BOXES WITH GEL ICE.',
                Validators.required
            ],
            numPackages: ['SEE ATTACHMENT', Validators.required],
            netWeight: ['SEE ATTACHMENT', Validators.required],
            numberOfSeal: ['NONE'],
            identificationMarks: ['DFAR/FPE/98/25', Validators.required],
            storageConditions: ['0/+4 DEGREES CELCIUS', Validators.required],

            // Section 3: Origin of products
            establishmentNameAddressRegNo: [
                'EAST GLOBE LANKA EXPORT COMPANY\nHETTIYAWATHTHA,PANNALA ROAD,\nDANKOTUWA, SRI LANKA.\nDFAR/FPE/98/25',
                Validators.required
            ],
            factoryVessel: ['XXXXXXXXXXXXXXXXXX'],
            coldStore: ['XXXXXXXXXXXXXXXXXXXXXXX'],
            administrativeUnit: ['COLOMBO, SRI LANKA', Validators.required],

            // Section 4: Pre-export certificates
            preExportCertificates: this.fb.array([
                this.fb.group({
                    date: ['XXX'],
                    number: ['XXX'],
                    countryOfOrigin: ['XXXXXXX'],
                    administrativeTerritory: ['XXXXXXX'],
                    approvalNumber: ['XXXXXXXXXXXX'],
                    productNameAndQuantity: ['XXXXXXXXXXXXX']
                }),
                this.fb.group({
                    date: ['XXX'],
                    number: ['XXX'],
                    countryOfOrigin: ['XXXXXXX'],
                    administrativeTerritory: ['XXXXXXX'],
                    approvalNumber: ['XXXXXXXXXXXX'],
                    productNameAndQuantity: ['XXXXXXXXXXXXX']
                })
            ]),

            // Issue details
            placeOfIssue: ['COLOMBO, SRI LANKA', Validators.required],
            dateOfIssue: [new Date('2026-08-22')],
            officialStamp: [''],
            officialSignature: [''],
            signatoryUserId: [null, Validators.required],
            signatoryName: ['H.M.U. BANDARA'],
            qualification: ['QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'],

            // Attachment details
            dateOfAttachment: [new Date('2026-08-22')],
            identificationMarksAttachment: ['DFAR/FPE/98/25'],
            attachments: this.fb.array(DEFAULT_RU_PRODUCTS.map((p) => this.createAttachmentRow(p)))
        });
    }

    get attachments(): FormArray {
        return this.form.get('attachments') as FormArray;
    }

    get preExportCertificates(): FormArray {
        return this.form.get('preExportCertificates') as FormArray;
    }

    createPreExportRow(data?: any): FormGroup {
        return this.fb.group({
            date: [data?.date || 'XXX'],
            number: [data?.number || 'XXX'],
            countryOfOrigin: [data?.countryOfOrigin || 'XXXXXXX'],
            administrativeTerritory: [data?.administrativeTerritory || 'XXXXXXX'],
            approvalNumber: [data?.approvalNumber || 'XXXXXXXXXXXX'],
            productNameAndQuantity: [data?.productNameAndQuantity || 'XXXXXXXXXXXXX']
        });
    }

    addPreExport(): void {
        this.preExportCertificates.push(this.createPreExportRow());
    }

    removePreExport(index: number): void {
        if (this.preExportCertificates.length > 1) {
            this.preExportCertificates.removeAt(index);
        }
    }

    createAttachmentRow(data?: any): FormGroup {
        return this.fb.group({
            product: [data?.product || ''],
            numberOfKgs: [data?.numberOfKgs !== undefined ? data.numberOfKgs : ''],
            numberOfBoxes: [data?.numberOfBoxes !== undefined ? data.numberOfBoxes : '']
        });
    }

    addAttachment(): void {
        this.attachments.push(this.createAttachmentRow());
    }

    removeAttachment(index: number): void {
        if (this.attachments.length > 1) {
            this.attachments.removeAt(index);
        }
    }

    loadDefaultProducts(): void {
        this.attachments.clear();
        DEFAULT_RU_PRODUCTS.forEach((p) => {
            this.attachments.push(this.createAttachmentRow(p));
        });
    }

    clearProducts(): void {
        this.attachments.clear();
        this.attachments.push(this.createAttachmentRow());
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

    get totalPages(): number {
        if (this.viewMode === 'attachment_only') return 1;
        return this.form.get('includeAttachment')?.value ? 4 : 3;
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

            if (params['mode'] === 'attachment_only' || this.router.url.includes('ru-attachment-certificate')) {
                this.viewMode = 'attachment_only';
            }

            if (params['ref']) {
                this.form.patchValue({
                    certificateNo: params['ref']
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

    onViewModeChange(mode: 'full' | 'attachment_only') {
        this.viewMode = mode;
        this.form.get('viewMode')?.setValue(mode);
    }

    loadSampleRussia(): void {
        this.viewMode = 'full';
        this.form.patchValue({
            viewMode: 'full',
            includeAttachment: true,
            certificateNo: 'TC 4471',
            consignorNameAddress: 'TROPICAL NATURE SEAFOOD,\n41/1, MAHAGEDARA, PATHAMULLA,\nKANATHTHEWEWA,\nSRI LANKA.',
            consigneeNameAddress: 'FISHERIES LLC,\n5TH VERHNIY MIHAILOVSKY PROEZD, 6,\n115419, MOSCOW, RUSSIA',
            meansOfTransport: 'AIR',
            countryOfTransit: 'NONE',
            countryOrigin: 'SRI LANKA',
            countryIssuing: 'SRI LANKA',
            competentAuthorityExporting: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            organizationIssuing: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            pointOfCrossingBorder: 'MOSCOW – DME/SVO/VKO',
            productName: 'FRESH CHILLED (SEE ATTACHMENT)',
            productionDate: new Date('2026-08-22'),
            typeOfPackage: 'VACUUM PACKED BAGS WRAPPED IN A POLYTHENE COVER & PUT IN TO A STYROFOAM BOXES WITH GEL ICE, WRAPPED IN A POLYTHENE COVER & PUT IN TO A STYROFOAM BOXES WITH GEL ICE.',
            numPackages: 'SEE ATTACHMENT',
            netWeight: 'SEE ATTACHMENT',
            numberOfSeal: 'NONE',
            identificationMarks: 'DFAR/FPE/98/25',
            storageConditions: '0/+4 DEGREES CELCIUS',
            establishmentNameAddressRegNo: 'EAST GLOBE LANKA EXPORT COMPANY\nHETTIYAWATHTHA,PANNALA ROAD,\nDANKOTUWA, SRI LANKA.\nDFAR/FPE/98/25',
            factoryVessel: 'XXXXXXXXXXXXXXXXXX',
            coldStore: 'XXXXXXXXXXXXXXXXXXXXXXX',
            administrativeUnit: 'COLOMBO, SRI LANKA',
            placeOfIssue: 'COLOMBO, SRI LANKA',
            dateOfIssue: new Date('2026-08-22'),
            dateOfAttachment: new Date('2026-08-22'),
            identificationMarksAttachment: 'DFAR/FPE/98/25',
            signatoryName: 'H.M.U. BANDARA',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).'
        });

        this.loadDefaultProducts();

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Russia Sample',
            detail: 'Loaded Russia Certificate sample (TC 4471 - Tropical Nature Seafood) with 40 Attachment products.'
        });
    }

    private loadSavedCertificateData(requestId: number): void {
        this.certificateService.getRuCertificateByRequestId(requestId).subscribe({
            next: (data: RuCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                const hasMultiple = (data.attachments && data.attachments.length > 0);
                const consignorFull = [data.consignorName, data.consignorAddress].filter(Boolean).join(',\n');
                const consigneeFull = [data.consigneeName, data.consigneeAddress].filter(Boolean).join(',\n');
                const estFull = [data.processingEstName, data.processingEstAddress, data.processingEstRegNo].filter(Boolean).join('\n');
                const transport = data.transportAeroPlane ? 'AIR' : (data.transportShip ? 'SHIP' : (data.transportId || 'AIR'));

                const rawMode = (data.certificateType || '').toLowerCase();
                const mode: 'full' | 'attachment_only' = rawMode === 'attachment_only' ? 'attachment_only' : 'full';
                this.viewMode = mode;
                if (data.id || data.certificateNo || data.consignorName) {
                    this.isSubmitted = true;
                }

                this.form.patchValue({
                    viewMode: mode,
                    includeAttachment: hasMultiple || mode === 'full',
                    consignorNameAddress: consignorFull || '',
                    consigneeNameAddress: consigneeFull || '',
                    meansOfTransport: transport,
                    countryOfTransit: data.countryOfTransit || 'NONE',
                    certificateNo: data.certificateNo || 'TC 4471',
                    countryOrigin: data.countryOrigin || 'SRI LANKA',
                    countryIssuing: data.countryIssuing || 'SRI LANKA',
                    competentAuthorityExporting: data.competentAuthorityExporting || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    organizationIssuing: data.organizationIssuing || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    pointOfCrossingBorder: data.pointOfCrossingBorder || 'MOSCOW – DME/SVO/VKO',
                    productName: data.productName || 'FRESH CHILLED (SEE ATTACHMENT)',
                    productionDate: data.productionDate ? new Date(data.productionDate) : new Date(),
                    typeOfPackage: data.packagingType || '',
                    numPackages: data.numPackages || 'SEE ATTACHMENT',
                    netWeight: data.netWeight || 'SEE ATTACHMENT',
                    numberOfSeal: data.numberOfSeal || 'NONE',
                    identificationMarks: data.identificationMarks || 'DFAR/FPE/98/25',
                    storageConditions: data.storageConditions || '0/+4 DEGREES CELCIUS',
                    establishmentNameAddressRegNo: estFull || '',
                    factoryVessel: data.factoryVessel || 'XXXXXXXXXXXXXXXXXX',
                    coldStore: data.coldStore || 'XXXXXXXXXXXXXXXXXXXXXXX',
                    administrativeUnit: data.administrativeUnit || 'COLOMBO, SRI LANKA',
                    placeOfIssue: data.placeOfIssue || 'COLOMBO, SRI LANKA',
                    dateOfIssue: data.dateOfIssue ? new Date(data.dateOfIssue) : new Date(),
                    officialStamp: data.officialStamp || '',
                    officialSignature: data.officialSignature || '',
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || '',
                    dateOfAttachment: data.dateOfAttachment ? new Date(data.dateOfAttachment) : new Date(),
                    identificationMarksAttachment: data.identificationMarksAttachment || 'DFAR/FPE/98/25',
                    signatoryUserId: data.signatoryUserId || null
                });

                this.updateSafeStampUrl();
                this.updateSafeSignatureUrl();

                this.selectedUserQualification = data.qualification || null;
                this.previousSignatoryUserId = data.signatoryUserId || null;

                this.attachments.clear();
                if (data.attachments && data.attachments.length > 0) {
                    data.attachments.forEach((a) => {
                        this.attachments.push(this.createAttachmentRow(a));
                    });
                } else {
                    this.loadDefaultProducts();
                }

                if (data.preExportCertificates && data.preExportCertificates.length > 0) {
                    this.preExportCertificates.clear();
                    data.preExportCertificates.forEach((p) => {
                        this.preExportCertificates.push(this.createPreExportRow(p));
                    });
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

    private loadVetFormData(requestId: number): void {
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
            next: (data: VetFormFieldResponse) => {
                if (!data) return;

                const certNo = data.healthCertNo || data.newHC || 'TC 4471';
                const consignorFull = `${data.consignorName || 'TROPICAL NATURE SEAFOOD'},\n${data.consignorAddress || '41/1, MAHAGEDARA, PATHAMULLA,\nKANATHTHEWEWA'},\nSRI LANKA.`;
                const consigneeFull = `${data.consigneeName || 'FISHERIES LLC'},\n${data.consigneeAddress || '5TH VERHNIY MIHAILOVSKY PROEZD, 6,\n115419, MOSCOW, RUSSIA'}`;
                const estFull = `${data.processingEstName || data.consignorName || 'EAST GLOBE LANKA EXPORT COMPANY'}\n${data.processingEstAddress || data.consignorAddress || 'HETTIYAWATHTHA,PANNALA ROAD,\nDANKOTUWA, SRI LANKA.'}\n${data.approvalNo || 'DFAR/FPE/98/25'}`;

                this.form.patchValue({
                    certificateNo: certNo,
                    consignorNameAddress: consignorFull,
                    consigneeNameAddress: consigneeFull,
                    meansOfTransport: data.transportAeroPlane ? 'AIR' : (data.transportShip ? 'SHIP' : 'AIR'),
                    countryOfTransit: 'NONE',
                    countryOrigin: data.countryOrigin || 'SRI LANKA',
                    countryIssuing: 'SRI LANKA',
                    pointOfCrossingBorder: data.entryBIP || 'MOSCOW – DME/SVO/VKO',
                    productName: data.descCommon ? `${data.descCommon} (SEE ATTACHMENT)` : 'FRESH CHILLED (SEE ATTACHMENT)',
                    productionDate: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    typeOfPackage: data.packagingType || 'VACUUM PACKED BAGS WRAPPED IN A POLYTHENE COVER & PUT IN TO A STYROFOAM BOXES WITH GEL ICE, WRAPPED IN A POLYTHENE COVER & PUT IN TO A STYROFOAM BOXES WITH GEL ICE.',
                    numPackages: 'SEE ATTACHMENT',
                    netWeight: 'SEE ATTACHMENT',
                    numberOfSeal: 'NONE',
                    identificationMarks: data.approvalNo || 'DFAR/FPE/98/25',
                    storageConditions: data.temperatureChilled ? '0/+4 DEGREES CELCIUS' : (data.temperatureFrozen ? '-18 DEGREES CELCIUS' : '0/+4 DEGREES CELCIUS'),
                    establishmentNameAddressRegNo: estFull,
                    administrativeUnit: 'COLOMBO, SRI LANKA',
                    placeOfIssue: 'COLOMBO, SRI LANKA',
                    dateOfIssue: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    dateOfAttachment: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    identificationMarksAttachment: data.approvalNo || 'DFAR/FPE/98/25'
                });

                this.attachments.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p) => {
                        const productTitle = p.descScientific
                            ? `${p.descCommon ? p.descCommon.toUpperCase() : ''} (${p.descScientific})`
                            : p.descCommon || '';
                        this.attachments.push(
                            this.fb.group({
                                product: [productTitle],
                                numberOfKgs: [p.netWeight || ''],
                                numberOfBoxes: [p.numPackages || '']
                            })
                        );
                    });
                } else {
                    this.loadDefaultProducts();
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
        if (!this.isCompany && !this.form.get('signatoryUserId')?.value && this.viewMode !== 'attachment_only') {
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

        if (this.form.invalid && this.viewMode !== 'attachment_only') {
            this.messageService.add({
                severity: 'error',
                summary: 'Validation Error',
                detail: 'Please fill in all required fields'
            });
            return;
        }

        this.isSaving = true;
        const rawValue = this.form.getRawValue();

        const attachmentPayloads: RuAttachmentPayload[] = (rawValue.attachments || []).map((a: any) => ({
            product: a.product || '',
            numberOfKgs: a.numberOfKgs ? Number(a.numberOfKgs) : 0,
            numberOfBoxes: a.numberOfBoxes ? Number(a.numberOfBoxes) : 0
        }));

        const preExportPayloads: RuPreExportCertificatePayload[] = (rawValue.preExportCertificates || []).map((p: any) => ({
            date: p.date || '',
            number: p.number || '',
            countryOfOrigin: p.countryOfOrigin || '',
            administrativeTerritory: p.administrativeTerritory || '',
            approvalNumber: p.approvalNumber || '',
            productNameAndQuantity: p.productNameAndQuantity || ''
        }));

        const consignorLines = (rawValue.consignorNameAddress || '').split('\n');
        const consignorName = consignorLines[0] || '';
        const consignorAddress = consignorLines.slice(1).join('\n') || '';

        const consigneeLines = (rawValue.consigneeNameAddress || '').split('\n');
        const consigneeName = consigneeLines[0] || '';
        const consigneeAddress = consigneeLines.slice(1).join('\n') || '';

        const estLines = (rawValue.establishmentNameAddressRegNo || '').split('\n');
        const processingEstName = estLines[0] || '';
        const processingEstRegNo = estLines[estLines.length - 1] || '';
        const processingEstAddress = estLines.slice(1, estLines.length - 1).join('\n') || '';

        const isAir = (rawValue.meansOfTransport || '').toUpperCase().includes('AIR') || (rawValue.meansOfTransport || '').toUpperCase().includes('PLANE');
        const isShip = (rawValue.meansOfTransport || '').toUpperCase().includes('SHIP') || (rawValue.meansOfTransport || '').toUpperCase().includes('VESSEL') || (rawValue.meansOfTransport || '').toUpperCase().includes('SEA');

        const payload: CreateRuCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            consignorName: consignorName,
            consignorAddress: consignorAddress,
            consigneeName: consigneeName,
            consigneeAddress: consigneeAddress,
            transportAeroPlane: isAir,
            transportShip: isShip,
            transportRailwayWagon: false,
            transportRoadVehicle: false,
            transportOther: false,
            transportId: rawValue.meansOfTransport || '',
            countryOfTransit: rawValue.countryOfTransit || '',
            certificateNo: rawValue.certificateNo || '',
            countryOrigin: rawValue.countryOrigin || '',
            countryIssuing: rawValue.countryIssuing || '',
            competentAuthorityExporting: rawValue.competentAuthorityExporting || '',
            organizationIssuing: rawValue.organizationIssuing || '',
            pointOfCrossingBorder: rawValue.pointOfCrossingBorder || '',
            productName: rawValue.productName || '',
            productionDate: toLocalISOString(rawValue.productionDate),
            packagingType: rawValue.typeOfPackage || '',
            numPackages: rawValue.numPackages || '',
            netWeight: rawValue.netWeight || '',
            numberOfSeal: rawValue.numberOfSeal || '',
            identificationMarks: rawValue.identificationMarks || '',
            storageConditions: rawValue.storageConditions || '',
            processingEstName: processingEstName,
            processingEstRegNo: processingEstRegNo,
            processingEstAddress: processingEstAddress,
            factoryVessel: rawValue.factoryVessel || '',
            coldStore: rawValue.coldStore || '',
            administrativeUnit: rawValue.administrativeUnit || '',
            placeOfIssue: rawValue.placeOfIssue || '',
            dateOfIssue: toLocalISOString(rawValue.dateOfIssue),
            officialStamp: rawValue.officialStamp || '',
            officialSignature: rawValue.officialSignature || '',
            signatoryUserId: rawValue.signatoryUserId || null,
            signatoryName: rawValue.signatoryName || '',
            qualification: rawValue.qualification || '',
            dateOfAttachment: toLocalISOString(rawValue.dateOfAttachment),
            identificationMarksAttachment: rawValue.identificationMarksAttachment || '',
            certificateType: this.viewMode,
            attachments: attachmentPayloads,
            preExportCertificates: preExportPayloads
        };

        this.certificateService.submitRuCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Russia Health Certificate saved successfully'
                });
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
            error: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to save Russia Health Certificate'
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

    printCertificate(): void {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }
        const ref = this.form.get('certificateNo')?.value || 'Draft';
        let pdfName = '';
        if (this.viewMode === 'attachment_only') {
            pdfName = `${ref}_Russia_Attachment.pdf`;
        } else {
            pdfName = `${ref}_Russia_Veterinary_Certificate.pdf`;
        }

        const originalTitle = document.title;
        document.title = pdfName;
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
