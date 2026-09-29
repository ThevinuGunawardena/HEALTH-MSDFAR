import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { AbstractControl, FormArray, FormBuilder, FormControl, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { DatePicker } from 'primeng/datepicker';
import { CheckboxModule } from 'primeng/checkbox';
import { RadioButton } from 'primeng/radiobutton';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { CertificateRequestService, UkCertificateView } from 'src/app/pages/service/certificate-request.service';
import { Select } from 'primeng/select';
import { TooltipModule } from 'primeng/tooltip';
import { AuthService } from '@/pages/service/auth.service';
import { UserService, User } from '@/pages/service/user.service';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';

export const DEFAULT_UK_ATTACHMENT_SPECIES = [
    { species: 'Scomberomorus commerson', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Scomberomorus commerson', codeCNTitle: '0304 49', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Selaroides leptulepis', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Siganus guttatus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Silago sihama', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Thunnus albacares', codeCNTitle: '0302 32', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Thunnus albacares', codeCNTitle: '0304 49', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Thunnus obesus', codeCNTitle: '0302 34', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Thunnus obesus', codeCNTitle: '0304 49', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Xiphias gladius', codeCNTitle: '0302 47', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Xiphias gladius', codeCNTitle: '0304 45', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Pinjalo pinjalo', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Raja mamillidens', codeCNTitle: '0304 48', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Makaira Indica', codeCNTitle: '0304 49', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Otolithus ruber', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Amplygaster sirm', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Portunus pelagicus', codeCNTitle: '0306 33', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Scylla serrata', codeCNTitle: '0306 33', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Penaeus indicus', codeCNTitle: '0306 36', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Penaeus monodon', codeCNTitle: '0306 36', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Uroteuthis singhalensis', codeCNTitle: '0307 42', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Pampus chinensis', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Octopus vulgaris', codeCNTitle: '0307 51', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Etroplus suratensis', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Brachirus orientalis', codeCNTitle: '0302 29', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Scarus ghobban', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Parexocoetus brachypterus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Tenualosa ilisha', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Strongylura leiura', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Stolephorus indicus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Raja mamillidens', codeCNTitle: '0302 82', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Acanthocybium solandri', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Ocyrus chrysurus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Uroteuthis singhalensis', codeCNTitle: '0307 42', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Lepturacanthus saval', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Macrobrachium rosenbergii', codeCNTitle: '0306 36', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Coryphena hippurus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Coryphena hippurus', codeCNTitle: '0304 49', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Epinephelus malabaricus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Epinephelus malabaricus', codeCNTitle: '0304 49', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Mullus surmuletus', codeCNTitle: '0304 49', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Mullus surmuletus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Acanthopagrus butcheri', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Lutjanus campechanus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Lutjanus ehrenbergii', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Nemipterus furcosus', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' },
    { species: 'Lutjanus fulviflamma', codeCNTitle: '0302 89', numberOfPackages: '', netWeight: '', batchNo: '' }
];

@Component({
    selector: 'app-uk-certificate',
    standalone: true,
    imports: [
        CommonModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        DatePicker,
        CheckboxModule,
        RadioButton,
        ToastModule,
        Select,
        FormsModule,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './uk-certificate.component.html',
    styleUrls: ['./uk-certificate.component.css', '../certificate-print.css']
})
export class UkCertificateComponent implements OnInit {
    form: FormGroup;
    certificateRequestId: number | null = null;
    isApproved = false;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;
    isSubmitted = false;
    viewMode: 'withoutAttachment' | 'withAttachment' = 'withoutAttachment';
    users: User[] = [];
    userOptions: { label: string; value: string }[] = [];
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail: string = '';
    selectedUserQualification: string | null = null;
    previousSignatoryUserId: string | null = null;
    readonly maxStandardProducts = 6;

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
        private authService: AuthService,
        private location: Location
    ) {
        this.form = this.fb.group({
            viewMode: ['withoutAttachment'],
            commodityNo: ['1604'],
            codeCNTitle: ['1604 : Food preparations not elsewhere specified or included'],
            certificateReferenceNo: [''],
            consignorName: [''],
            consignorAddress: [''],
            consignorTel: [''],
            consigneeName: [''],
            consigneeAddress: [''],
            consigneeTel: [''],
            operatorName: [''],
            operatorAddress: [''],
            operatorTel: [''],
            countryOfOrigin: ['SRI LANKA'],
            countryOfOriginISO: ['LK'],
            regionOfOrigin: ['INDIAN OCEAN'],
            regionOfOriginCode: ['57'],
            countryOfDestination: ['UNITED KINGDOM'],
            countryOfDestinationISO: ['GB'],
            regionOfDestination: [''],
            regionOfDestinationCode: [''],
            placeOfDispatchName: [''],
            placeOfDispatchApprovalNo: [''],
            placeOfDispatchAddress: [''],
            placeOfDestinationName: [''],
            placeOfDestinationAddress: [''],
            placeOfLoading: ['COLOMBO PORT / SRI LANKA'],
            dateOfDeparture: [null],
            timeOfDeparture: [''],
            transportAeroplane: [false],
            transportVessel: [true],
            transportRailway: [false],
            transportRoadVehicle: [false],
            transportOther: [false],
            transportIdentification: [''],
            entryBCP: [''],
            accompDocType: [''],
            accompDocNo: [''],
            tempAmbient: [false],
            tempChilled: [true],
            tempFrozen: [false],
            containerSealNo: [''],
            goodsCanningIndustry: [false],
            goodsHumanConsumption: [true],
            field21: ['withoutAttachment'],
            field22: [''],
            totalNumberOfPackages: [''],
            totalNetWeight: [''],
            totalGrossWeight: [''],
            finalConsumer: [false],
            // Animal Health Attestation Strikethrough Fields
            strikeAnimalHealthAll: [true],
            strikeAhT153: [true],
            strikeAhT154: [true],
            strikeAhT155: [true],
            strikeAhT155_Either: [true],
            strikeAhT155_D_Bkd: [true],
            strikeAhT155_D_SvcGs: [true],
            strikeAhT155_D_SvcBkd: [true],
            strikeAhT155_GsSalinity: [true],
            strikeAhT155_GsEggs: [true],
            strikeAhP502: [true],
            signatoryName: [''],
            qualification: [''],
            certifiedDate: [null],
            // Shared Attachment Columns (Merged Vertically)
            attNatureOfCommodity: ['WILD ORIGIN'],
            attTreatmentType: ['FRESH CHILLED LOINS/WHOLE/WHOLE H&G/WHOLE GG'],
            attManufacturingPlant: [''],
            attColdStore: [''],
            attTypeOfPacking: ['FRESH FISH IN POLYSTYRENE BOXES'],
            products: this.fb.array([this.createProductRow()]),
            signatoryUserId: [null, Validators.required]
        });
    }

    onToggleAnimalHealthAll(event?: any): void {
        const checked = event?.checked !== undefined ? event.checked : this.form.get('strikeAnimalHealthAll')?.value;
        this.form.patchValue({
            strikeAnimalHealthAll: checked,
            strikeAhT153: checked,
            strikeAhT154: checked,
            strikeAhT155: checked,
            strikeAhT155_Either: checked,
            strikeAhT155_D_Bkd: checked,
            strikeAhT155_D_SvcGs: checked,
            strikeAhT155_D_SvcBkd: checked,
            strikeAhT155_GsSalinity: checked,
            strikeAhT155_GsEggs: checked,
            strikeAhP502: checked
        });
    }

    onToggleT155(event?: any): void {
        const checked = event?.checked !== undefined ? event.checked : this.form.get('strikeAhT155')?.value;
        this.form.patchValue({
            strikeAhT155: checked,
            strikeAhT155_Either: checked,
            strikeAhT155_D_Bkd: checked,
            strikeAhT155_D_SvcGs: checked,
            strikeAhT155_D_SvcBkd: checked,
            strikeAhT155_GsSalinity: checked,
            strikeAhT155_GsEggs: checked
        });
        this.syncAnimalHealthMasterCheckbox();
    }

    onToggleT155SubClause(): void {
        const e = !!this.form.get('strikeAhT155_Either')?.value;
        const bkd = !!this.form.get('strikeAhT155_D_Bkd')?.value;
        const svcGs = !!this.form.get('strikeAhT155_D_SvcGs')?.value;
        const svcBkd = !!this.form.get('strikeAhT155_D_SvcBkd')?.value;
        const sal = !!this.form.get('strikeAhT155_GsSalinity')?.value;
        const eggs = !!this.form.get('strikeAhT155_GsEggs')?.value;

        const allT155 = e && bkd && svcGs && svcBkd && sal && eggs;
        this.form.patchValue({ strikeAhT155: allT155 }, { emitEvent: false });
        this.syncAnimalHealthMasterCheckbox();
    }

    onToggleSubClause(): void {
        this.syncAnimalHealthMasterCheckbox();
    }

    private syncAnimalHealthMasterCheckbox(): void {
        const t153 = !!this.form.get('strikeAhT153')?.value;
        const t154 = !!this.form.get('strikeAhT154')?.value;
        const t155 = !!this.form.get('strikeAhT155')?.value;
        const p502 = !!this.form.get('strikeAhP502')?.value;
        const allChecked = t153 && t154 && t155 && p502;
        this.form.patchValue({ strikeAnimalHealthAll: allChecked }, { emitEvent: false });
    }

    setAnimalHealthAll(struck: boolean): void {
        this.form.patchValue({
            strikeAnimalHealthAll: struck,
            strikeAhT153: struck,
            strikeAhT154: struck,
            strikeAhT155: struck,
            strikeAhT155_Either: struck,
            strikeAhT155_D_Bkd: struck,
            strikeAhT155_D_SvcGs: struck,
            strikeAhT155_D_SvcBkd: struck,
            strikeAhT155_GsSalinity: struck,
            strikeAhT155_GsEggs: struck,
            strikeAhP502: struck
        });
    }

    onViewModeChange(mode: 'withoutAttachment' | 'withAttachment'): void {
        this.viewMode = mode;
        this.form.patchValue({
            viewMode: mode,
            field21: mode
        });

        if (mode === 'withAttachment') {
            const currentNo = this.form.get('commodityNo')?.value;
            const currentTitle = this.form.get('codeCNTitle')?.value;
            if (!currentNo || currentNo === '1604') {
                this.form.patchValue({ commodityNo: '0302, 0304, 0306, 0307' });
            }
            if (!currentTitle || currentTitle.includes('Food preparations')) {
                this.form.patchValue({ codeCNTitle: 'SEE THE ATTACHMENT' });
            }

            // Sync shared plant / cold store defaults if empty
            this.syncAttachmentPlantDefaults();

            // Load 47 default species if products only has 1 empty row
            if (this.products.length <= 1) {
                const first = this.products.at(0)?.value;
                if (!first?.species && !first?.netWeight) {
                    this.loadDefaultAttachmentSpecies();
                }
            }
        } else {
            const currentNo = this.form.get('commodityNo')?.value;
            const currentTitle = this.form.get('codeCNTitle')?.value;
            if (!currentNo || currentNo === '0302, 0304, 0306, 0307') {
                this.form.patchValue({ commodityNo: '1604' });
            }
            if (!currentTitle || currentTitle === 'SEE THE ATTACHMENT') {
                this.form.patchValue({ codeCNTitle: '1604 : Food preparations not elsewhere specified or included' });
            }
        }
    }

    syncAttachmentPlantDefaults(): void {
        const dispatchName = this.form.get('placeOfDispatchName')?.value || '';
        const dispatchAddr = this.form.get('placeOfDispatchAddress')?.value || '';
        const dispatchApproval = this.form.get('placeOfDispatchApprovalNo')?.value || '';

        const formattedPlant = [dispatchName, dispatchAddr, dispatchApproval].filter(Boolean).join('\n');

        if (!this.form.get('attManufacturingPlant')?.value && formattedPlant) {
            this.form.patchValue({ attManufacturingPlant: formattedPlant });
        }
        if (!this.form.get('attColdStore')?.value && formattedPlant) {
            this.form.patchValue({ attColdStore: formattedPlant });
        }
    }

    loadDefaultAttachmentSpecies(): void {
        this.syncAttachmentPlantDefaults();
        while (this.products.length) {
            this.products.removeAt(0);
        }
        DEFAULT_UK_ATTACHMENT_SPECIES.forEach((item) => {
            this.products.push(
                this.fb.group({
                    no: [''],
                    codeCNTitle: [item.codeCNTitle || ''],
                    species: [item.species || ''],
                    natureOfCommodity: ['WILD ORIGIN'],
                    treatmentType: [''],
                    vesselPlant: [''],
                    coldStore: [''],
                    numberOfPackages: [item.numberOfPackages || ''],
                    netWeight: [item.netWeight || ''],
                    batchNo: [item.batchNo || ''],
                    typeOfPackaging: ['']
                })
            );
        });
    }

    clearAttachmentQuantities(): void {
        this.products.controls.forEach((ctrl: AbstractControl) => {
            ctrl.patchValue({
                numberOfPackages: '',
                netWeight: '',
                batchNo: ''
            });
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    getControl(name: string): FormControl {
        return this.form.get(name) as FormControl;
    }

    get showAttachmentPage(): boolean {
        return this.viewMode === 'withAttachment';
    }

    get totalPages(): number {
        return this.viewMode === 'withAttachment' ? 5 : 4;
    }

    get calculatedTotalPackages(): number {
        return (this.products.controls || []).reduce((sum: number, control: AbstractControl) => {
            const val = parseInt(control.get('numberOfPackages')?.value || '0', 10);
            return sum + (Number.isNaN(val) ? 0 : val);
        }, 0);
    }

    get calculatedTotalNetWeight(): number {
        return (this.products.controls || []).reduce((sum: number, control: AbstractControl) => {
            const val = parseFloat(control.get('netWeight')?.value || '0');
            return sum + (Number.isNaN(val) ? 0 : val);
        }, 0);
    }

    createProductRow(data?: any): FormGroup {
        return this.fb.group({
            no: [data?.no || ''],
            codeCNTitle: [data?.codeCNTitle || ''],
            species: [data?.species || ''],
            natureOfCommodity: [data?.natureOfCommodity || 'WILD ORIGIN'],
            treatmentType: [data?.treatmentType || ''],
            vesselPlant: [data?.vesselPlant || ''],
            coldStore: [data?.coldStore || ''],
            numberOfPackages: [data?.numberOfPackages || ''],
            netWeight: [data?.netWeight || ''],
            batchNo: [data?.batchNo || ''],
            typeOfPackaging: [data?.typeOfPackaging || '']
        });
    }

    addProductRow(): void {
        this.products.push(this.createProductRow());
    }

    removeProductRow(index: number): void {
        if (this.products.length <= 1) {
            return;
        }
        this.products.removeAt(index);
    }

    goToAttachmentPage(): void {
        if (!this.showAttachmentPage) {
            return;
        }
        setTimeout(() => {
            const el = document.getElementById('uk-attachment-page-0');
            el?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    ngOnInit(): void {
        this.isCompany = (this.authService.getUserRole() || '').toLowerCase() === 'company';
        if (this.isCompany) {
            this.form.get('signatoryUserId')?.clearValidators();
            this.form.get('signatoryUserId')?.updateValueAndValidity();
        }

        this.userService.getAllUsers().subscribe((users: User[]) => {
            this.users = users;
            this.userOptions = users
                .filter((u: User) => u.roleName && (u.roleName.toLowerCase() === 'user' || u.roleName.toLowerCase() === 'admin'))
                .map((u: User) => ({ label: u.name, value: u.id }));
        });

        this.route.queryParams.subscribe((params: any) => {
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
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

    private loadVetFormData(requestId: number): void {
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
            next: (data: any) => {
                if (!data) {
                    return;
                }

                const vetProducts = Array.isArray(data.products) && data.products.length > 0
                    ? data.products
                    : [
                          {
                              descScientific: data.descScientific,
                              descCommon: data.descCommon,
                              processingType: data.processingType,
                              treatment: data.treatment,
                              numPackages: data.numPackages,
                              netWeight: data.netWeight,
                              packagingType: data.packagingType
                          }
                      ];

                const totalPackages = vetProducts.reduce((sum: number, product: any) => sum + (parseInt(product?.numPackages || '0', 10) || 0), 0);
                const totalNetWeight = vetProducts.reduce((sum: number, product: any) => sum + (parseFloat(product?.netWeight || '0') || 0), 0);

                this.form.patchValue({
                    certificateReferenceNo: data.docReferences || '',
                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    consignorTel: data.consignorTel || '',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    consigneeTel: data.consigneeTel || '',
                    countryOfOrigin: data.countryOrigin || 'SRI LANKA',
                    countryOfOriginISO: data.countryOriginISO || 'LK',
                    countryOfDestination: data.countryDestinationISO || '',
                    countryOfDestinationISO: data.countryDestinationISO || '',
                    placeOfLoading: data.placeOfLoading || '',
                    placeOfDispatchName: data.placeOfLoading || '',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : null,
                    transportIdentification: data.transportId || '',
                    entryBCP: data.entryBIP || '',
                    accompDocNo: data.docReferences || '',
                    totalNumberOfPackages: totalPackages || '',
                    totalNetWeight: totalNetWeight || '',
                    totalGrossWeight: '',
                    transportAeroplane: !!data.transportAeroPlane,
                    transportVessel: !!data.transportShip,
                    transportRailway: !!data.transportRailwayWagon,
                    transportRoadVehicle: !!data.transportRoadVehicle,
                    transportOther: !!data.transportOther,
                    tempAmbient: !!data.temperatureAmbient,
                    tempChilled: !!data.temperatureChilled,
                    tempFrozen: !!data.temperatureFrozen,
                    goodsHumanConsumption: !!data.forHumanConsumption
                });

                while (this.products.length) {
                    this.products.removeAt(0);
                }

                vetProducts.forEach((product: any) => {
                    this.products.push(
                        this.fb.group({
                            species: [product?.descScientific || ''],
                            natureOfCommodity: [product?.descCommon || ''],
                            treatmentType: [product?.processingType || product?.treatment || data.treatment || ''],
                            vesselPlant: [data.processingEstName || ''],
                            numberOfPackages: [product?.numPackages || ''],
                            netWeight: [product?.netWeight || ''],
                            batchNo: [''],
                            typeOfPackaging: [product?.packagingType || data.packagingType || '']
                        })
                    );
                });
            },
            error: (err: any) => {
                console.error('Error loading vet form data:', err);
            }
        });
    }

    private loadSavedCertificateData(requestId: number): void {
        this.certificateService.getUkCertificateByRequestId(requestId).subscribe({
            next: (data: UkCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }
                const loadedViewMode = data.field21 === 'withAttachment' || (data.products && data.products.length > this.maxStandardProducts)
                    ? 'withAttachment'
                    : 'withoutAttachment';
                this.viewMode = loadedViewMode;
                if (data.id || data.certificateReferenceNo || data.consignorName) {
                    this.isSubmitted = true;
                }

                this.form.patchValue({
                    viewMode: loadedViewMode,
                    commodityNo: loadedViewMode === 'withAttachment' ? '0302, 0304, 0306, 0307' : '1604',
                    codeCNTitle: loadedViewMode === 'withAttachment' ? 'SEE THE ATTACHMENT' : '1604 : Food preparations not elsewhere specified or included',
                    certificateReferenceNo: data.certificateReferenceNo || '',
                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    consignorTel: data.consignorTel || '',
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    consigneeTel: data.consigneeTel || '',
                    operatorName: data.operatorName || '',
                    operatorAddress: data.operatorAddress || '',
                    operatorTel: data.operatorTel || '',
                    countryOfOrigin: data.countryOfOrigin || '',
                    countryOfOriginISO: data.countryOfOriginISO || '',
                    regionOfOrigin: data.regionOfOrigin || '',
                    regionOfOriginCode: data.regionOfOriginCode || '',
                    countryOfDestination: data.countryOfDestination || '',
                    countryOfDestinationISO: data.countryOfDestinationISO || '',
                    regionOfDestination: data.regionOfDestination || '',
                    regionOfDestinationCode: data.regionOfDestinationCode || '',
                    placeOfDispatchName: data.placeOfDispatchName || '',
                    placeOfDispatchApprovalNo: data.placeOfDispatchApprovalNo || '',
                    placeOfDispatchAddress: data.placeOfDispatchAddress || '',
                    placeOfDestinationName: data.placeOfDestinationName || '',
                    placeOfDestinationAddress: data.placeOfDestinationAddress || '',
                    placeOfLoading: data.placeOfLoading || '',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : null,
                    timeOfDeparture: data.timeOfDeparture || '',
                    transportAeroplane: data.transportAeroplane,
                    transportVessel: data.transportVessel,
                    transportRailway: data.transportRailway,
                    transportRoadVehicle: data.transportRoadVehicle,
                    transportOther: data.transportOther,
                    transportIdentification: data.transportIdentification || '',
                    entryBCP: data.entryBCP || '',
                    accompDocType: data.accompDocType || '',
                    accompDocNo: data.accompDocNo || '',
                    tempAmbient: data.tempAmbient,
                    tempChilled: data.tempChilled,
                    tempFrozen: data.tempFrozen,
                    containerSealNo: data.containerSealNo || '',
                    goodsCanningIndustry: data.goodsCanningIndustry,
                    goodsHumanConsumption: data.goodsHumanConsumption,
                    field21: loadedViewMode,
                    field22: data.field22 || '',
                    totalNumberOfPackages: data.totalNumberOfPackages || '',
                    totalNetWeight: data.totalNetWeight || '',
                    totalGrossWeight: data.totalGrossWeight || '',
                    finalConsumer: data.finalConsumer,
                    strikeAnimalHealthAll: data.strikeAnimalHealthAll !== undefined ? data.strikeAnimalHealthAll : true,
                    strikeAhT153: data.strikeAhT153 !== undefined ? data.strikeAhT153 : true,
                    strikeAhT154: data.strikeAhT154 !== undefined ? data.strikeAhT154 : true,
                    strikeAhT155: data.strikeAhT155 !== undefined ? data.strikeAhT155 : true,
                    strikeAhT155_Either: data.strikeAhT155_Either !== undefined ? data.strikeAhT155_Either : true,
                    strikeAhT155_D_Bkd: data.strikeAhT155_D_Bkd !== undefined ? data.strikeAhT155_D_Bkd : true,
                    strikeAhT155_D_SvcGs: data.strikeAhT155_D_SvcGs !== undefined ? data.strikeAhT155_D_SvcGs : true,
                    strikeAhT155_D_SvcBkd: data.strikeAhT155_D_SvcBkd !== undefined ? data.strikeAhT155_D_SvcBkd : true,
                    strikeAhT155_GsSalinity: data.strikeAhT155_GsSalinity !== undefined ? data.strikeAhT155_GsSalinity : true,
                    strikeAhT155_GsEggs: data.strikeAhT155_GsEggs !== undefined ? data.strikeAhT155_GsEggs : true,
                    strikeAhP502: data.strikeAhP502 !== undefined ? data.strikeAhP502 : true,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || '',
                    certifiedDate: data.certifiedDate ? new Date(data.certifiedDate) : null,
                    signatoryUserId: data.signatoryUserId
                });

                this.selectedUserQualification = data.qualification || '';

                if (data.products && data.products.length > 0) {
                    const firstP = data.products[0];
                    if (firstP?.natureOfCommodity) {
                        this.form.patchValue({ attNatureOfCommodity: firstP.natureOfCommodity });
                    }
                    if (firstP?.treatmentType) {
                        this.form.patchValue({ attTreatmentType: firstP.treatmentType });
                    }
                    if (firstP?.vesselPlant) {
                        this.form.patchValue({ attManufacturingPlant: firstP.vesselPlant });
                    }
                    if (firstP?.coldStore) {
                        this.form.patchValue({ attColdStore: firstP.coldStore });
                    }
                    if (firstP?.typeOfPackaging) {
                        this.form.patchValue({ attTypeOfPacking: firstP.typeOfPackaging });
                    }

                    while (this.products.length) {
                        this.products.removeAt(0);
                    }

                    data.products.forEach((p: any) => {
                        this.products.push(
                            this.fb.group({
                                no: [p.no || ''],
                                codeCNTitle: [p.codeCNTitle || ''],
                                species: [p.species || ''],
                                natureOfCommodity: [p.natureOfCommodity || 'WILD ORIGIN'],
                                treatmentType: [p.treatmentType || ''],
                                vesselPlant: [p.vesselPlant || ''],
                                coldStore: [p.coldStore || ''],
                                numberOfPackages: [p.numberOfPackages || ''],
                                netWeight: [p.netWeight || ''],
                                batchNo: [p.batchNo || ''],
                                typeOfPackaging: [p.typeOfPackaging || '']
                            })
                        );
                    });
                }

                if (!this.viewOnly) {
                    this.form.enable();
                } else {
                    this.form.enable();
                }
            },
            error: () => {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Load Failed',
                    detail: 'Could not load saved UK certificate data.'
                });
            }
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

        if (!this.form.valid) {
            this.form.markAllAsTouched();
            this.messageService.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Please fill in all required fields'
            });
            return;
        }

        const requestId = this.certificateRequestId;
        if (!requestId || Number.isNaN(requestId) || requestId <= 0) {
            this.messageService.add({
                severity: 'error',
                summary: 'Missing Request',
                detail: 'Certificate request id is missing or invalid. Open this form from a certificate request and try again.'
            });
            return;
        }

        this.isSaving = true;
        const raw = this.form.getRawValue();

        const normalizedProducts = (raw.products || []).map((p: any) => ({
            ...p,
            natureOfCommodity: raw.viewMode === 'withAttachment' ? (raw.attNatureOfCommodity || 'WILD ORIGIN') : (p?.natureOfCommodity || ''),
            treatmentType: raw.viewMode === 'withAttachment' ? (raw.attTreatmentType || '') : (p?.treatmentType || ''),
            vesselPlant: raw.viewMode === 'withAttachment' ? (raw.attManufacturingPlant || '') : (p?.vesselPlant || ''),
            coldStore: raw.viewMode === 'withAttachment' ? (raw.attColdStore || '') : (p?.coldStore || ''),
            typeOfPackaging: raw.viewMode === 'withAttachment' ? (raw.attTypeOfPacking || '') : (p?.typeOfPackaging || ''),
            numberOfPackages: p?.numberOfPackages != null ? String(p.numberOfPackages) : '',
            netWeight: p?.netWeight != null ? String(p.netWeight) : ''
        }));

        const payload = {
            ...raw,
            certificateRequestId: requestId,
            dateOfDeparture: toLocalISOString(raw.dateOfDeparture),
            certifiedDate: toLocalISOString(raw.certifiedDate),
            totalNumberOfPackages: raw.totalNumberOfPackages != null ? String(raw.totalNumberOfPackages) : '',
            totalNetWeight: raw.totalNetWeight != null ? String(raw.totalNetWeight) : '',
            totalGrossWeight: raw.totalGrossWeight != null ? String(raw.totalGrossWeight) : '',
            products: normalizedProducts
        };

        this.certificateService.submitUkCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'UK Certificate saved successfully'
                });
                if (requestId) {
                    try {
                        const submitted = JSON.parse(localStorage.getItem('dfar_submitted_requests') || '[]');
                        if (!submitted.includes(requestId)) {
                            submitted.push(requestId);
                            localStorage.setItem('dfar_submitted_requests', JSON.stringify(submitted));
                        }
                    } catch {}
                }
                setTimeout(() => this.goBack(), 1500);
            },
            error: (err: any) => {
                this.isSaving = false;
                console.error('Error submitting UK certificate:', err);
                const validationErrors = err?.error?.errors
                    ? Object.entries(err.error.errors)
                          .flatMap(([field, msgs]) => (Array.isArray(msgs) ? msgs.map((m) => `${field}: ${m}`) : [`${field}: ${msgs}`]))
                          .join(' | ')
                    : '';
                const detail = validationErrors || err?.error?.message || err?.error?.Message || err?.message || 'Failed to save UK Certificate';
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail
                });
            }
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
        window.print();
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

    onSignatoryChange(userId: string) {
        if (!userId) {
            this.previousSignatoryUserId = null;
            this.setSelectedUserQualification(userId);
            return;
        }

        const user = this.users.find((u) => u.id === userId);
        if (user) {
            this.pendingSignatoryUserId = user.id;
            this.pendingSignatoryUserEmail = user.email;
            this.showPasswordDialog = true;
        }
    }

    onSignatoryConfirmed(userId: string) {
        this.previousSignatoryUserId = userId;
        this.setSelectedUserQualification(userId);
        this.showPasswordDialog = false;

        const user = this.users.find((u) => u.id === userId);
        if (user) {
            this.form.patchValue({
                signatoryName: user.name,
                qualification: user.qualification
            });
        }
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

    setSelectedUserQualification(userId: string) {
        const user = this.users.find((u) => u.id === userId);
        this.selectedUserQualification = user?.qualification || null;
        if (user) {
            this.form.patchValue({
                signatoryName: user.name,
                qualification: user.qualification
            });
        }
    }
}
