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
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { UserService, User } from '@/pages/service/user.service';
import { IdCertificateView, CertificateRequestService, VetFormFieldResponse } from 'src/app/pages/service/certificate-request.service';
import { TooltipModule } from 'primeng/tooltip';
import { TableModule } from 'primeng/table';
import { toLocalISOString } from '@/shared/utils/date-utils';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';

@Component({
    selector: 'app-id-certificate',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, InputTextModule, TextareaModule, ButtonModule, DatePicker, ToastModule, CheckboxModule, TableModule, Select, TooltipModule, ConfirmPasswordDialogComponent, CertificateQrComponent],
    providers: [MessageService],
    templateUrl: './id-certificate.component.html',
    styleUrls: ['./id-certificate.component.css', '../certificate-print.css']
})
export class IdCertificateComponent implements OnInit {
    form: FormGroup;
    refNumber: string = 'ID 8813';
    certificateRequestId: number | null = null;
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
        private certificateService: CertificateRequestService,
        private userService: UserService,
        private location: Location,
        public authService: AuthService
    ) {
        this.form = this.fb.group({
            numberNomor: ['ID 8813'],

            products: this.fb.array([this.createProductRow()]),

            consignorName: [''],
            consignorAddress: [''],

            consigneeName: [''],
            consigneeAddress: [''],

            competentAuthority: ['DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES'],

            establishmentAquaculture: [false],
            establishmentProcessing: [true],
            establishmentOther: [false],
            establishmentName: [''],
            establishmentRegNo: [''],
            establishmentAddress: [''],

            countryRegionOrigin: ['SRI LANKA, INDIAN OCEAN (FAO 57)'],
            sourceFarmRaised: [false],
            sourceWildCaught: [true],

            portOfShipment: ['COLOMBO PORT / AIRPORT'],
            transportAir: [true],
            transportSea: [false],
            transportRoad: [false],

            commodityDescription: [''],
            tempAmbient: [false],
            tempFrozen: [false],
            tempChilled: [true],

            intendedHumanConsumption: [true],
            intendedCultureBreeding: [false],
            intendedTrade: [false],
            intendedResearch: [false],
            intendedFishFeed: [false],
            intendedExhibition: [false],
            intendedOther: [false],

            totalPackages: [''],
            packagingType: ['PACKED IN CORRUGATED CARTONS'],
            totalQuantityKg: [''],
            containerSealNumber: [''],
            portOfDestination: [''],
            transportVesselName: [''],
            transportVoyageNumber: [''],
            dateOfDeparture: [null],
            testingLaboratory: ['NATIONAL AQUATIC RESOURCES RESEARCH AND DEVELOPMENT AGENCY (NARA)'],
            laboratoryAddress: ['CROWH ISLAND, MATTAKKULIYA, COLOMBO 15, SRI LANKA'],
            approvingOfficerName: [''],
            testResultNumber: [''],

            attestationRefNumber: ['ID 8813'],
            attestFinfish: [false],
            attestMollusca: [false],
            attestCrustacea: [true],
            attestFisheryProducts: [true],
            attestOther: [false],

            attestClauseA: [true],
            attestClauseB: [true],
            attestClauseC: [true],
            attestClauseCCrustacean: [true],
            attestClauseCCyprinidae: [false],
            attestClauseCTilapia: [false],
            attestClauseCCatfish: [false],
            attestClauseCOtherFish: [false],
            attestClauseCVisibleSigns: [false],
            attestClauseCPackagedContainers: [false],
            attestClauseD: [false],
            attestClauseE: [false],

            additionalInformation: [''],

            certifiedName: [''],
            certifiedPosition: ['AUTHORIZED FISH INSPECTION OFFICER'],
            certifiedIssuedAt: ['COLOMBO, SRI LANKA'],
            certifiedDate: [new Date()],
            certifiedPhone: ['+94 (0) 11 2446183'],
            certifiedFax: ['+94 (0) 11 2446183'],
            certifiedEmail: ['dgdfar@gmail.com'],
            certifiedAddress: ['NEW SECRETARIAT, MALIGAWATTA, COLOMBO 10, SRI LANKA.'],

            signatoryName: [''],
            qualification: ['AUTHORIZED FISH INSPECTION OFFICER'],
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
                this.refNumber = params['ref'];
                this.form.patchValue({
                    numberNomor: params['ref'],
                    attestationRefNumber: params['ref']
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

        this.form.get('numberNomor')?.valueChanges.subscribe((val) => {
            if (val) {
                this.refNumber = val;
                this.form.get('attestationRefNumber')?.setValue(val, { emitEvent: false });
            }
        });

        this.form.get('attestationRefNumber')?.valueChanges.subscribe((val) => {
            if (val && !this.form.get('numberNomor')?.value) {
                this.refNumber = val;
                this.form.get('numberNomor')?.setValue(val, { emitEvent: false });
            }
        });
    }

    loadSampleData(): void {
        this.refNumber = 'ID 8813';
        this.form.patchValue({
            numberNomor: 'ID 8813',
            attestationRefNumber: 'ID 8813',
            consignorName: 'OCEANIC EXPORTS (PVT) LTD',
            consignorAddress: 'NO. 124, HARBOUR ROAD, COLOMBO 15, SRI LANKA',
            consigneeName: 'PT INDONESIA SEAFOOD NUSANTARA',
            consigneeAddress: 'JALAN MUARA BARU NO. 12, JAKARTA UTARA 14440, INDONESIA',
            competentAuthority: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
            establishmentAquaculture: false,
            establishmentProcessing: true,
            establishmentOther: false,
            establishmentName: 'OCEANIC EXPORTS PLANT',
            establishmentRegNo: 'DFAR/FQC/PP/042',
            establishmentAddress: 'MUTWAL FISHERY HARBOUR, COLOMBO 15',
            countryRegionOrigin: 'SRI LANKA, INDIAN OCEAN (FAO 57)',
            sourceFarmRaised: false,
            sourceWildCaught: true,
            portOfShipment: 'COLOMBO (CMB)',
            transportAir: true,
            transportSea: false,
            transportRoad: false,
            commodityDescription: 'FRESH CHILLED FISH AND CRUSTACEANS (Yellowfin Tuna & Malabar Grouper)',
            tempAmbient: false,
            tempFrozen: false,
            tempChilled: true,
            intendedHumanConsumption: true,
            totalPackages: '100 BOXES',
            packagingType: 'STYROFOAM CARTONS WITH GEL ICE',
            totalQuantityKg: '2000',
            containerSealNumber: 'SEAL-ID-9933',
            portOfDestination: 'SOEKARNO-HATTA AIRPORT, JAKARTA (CGK)',
            transportVesselName: 'GARUDA INDONESIA',
            transportVoyageNumber: 'GA 892',
            dateOfDeparture: new Date(),
            testingLaboratory: 'NATIONAL AQUATIC RESOURCES RESEARCH AND DEVELOPMENT AGENCY (NARA)',
            laboratoryAddress: 'CROWH ISLAND, MATTAKKULIYA, COLOMBO 15, SRI LANKA',
            approvingOfficerName: 'Dr. N. Fernando',
            testResultNumber: 'TR-DFAR-2026-098',
            attestFinfish: false,
            attestMollusca: false,
            attestCrustacea: true,
            attestFisheryProducts: true,
            attestOther: false,
            attestClauseA: true,
            attestClauseB: true,
            attestClauseC: true,
            attestClauseCCrustacean: true,
            certifiedName: 'Dr. N. Fernando',
            certifiedPosition: 'AUTHORIZED FISH INSPECTION OFFICER',
            certifiedIssuedAt: 'COLOMBO, SRI LANKA',
            certifiedDate: new Date(),
            certifiedPhone: '+94 11 243 5678',
            certifiedFax: '+94 11 243 5678',
            certifiedEmail: 'info@fisheries.gov.lk',
            certifiedAddress: 'NEW SECRETARIAT, MALIGAWATTA, COLOMBO 10, SRI LANKA.',
            qualification: 'AUTHORIZED FISH INSPECTION OFFICER'
        });

        const productsArray = this.form.get('products') as FormArray;
        productsArray.clear();
        productsArray.push(
            this.fb.group({
                no: '1',
                commonName: 'Yellowfin Tuna',
                scientificName: 'Thunnus albacares',
                hsCode: '0302.89',
                quantity: 1200,
                unit: 'KG'
            })
        );
        productsArray.push(
            this.fb.group({
                no: '2',
                commonName: 'Malabar Grouper',
                scientificName: 'Epinephelus malabaricus',
                hsCode: '0302.89',
                quantity: 800,
                unit: 'KG'
            })
        );

        this.messageService.add({
            severity: 'info',
            summary: 'Sample Loaded',
            detail: 'Loaded standard Indonesia certificate sample (ID 8813).'
        });
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateService.getIdCertificateByRequestId(requestId).subscribe({
            next: (data: IdCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }
                const ref = data.numberNomor || data.attestationRefNumber || this.refNumber || 'ID 8813';
                this.refNumber = ref;
                this.form.patchValue({
                    numberNomor: ref,
                    consignorName: data.consignorName,
                    consignorAddress: data.consignorAddress,
                    consigneeName: data.consigneeName,
                    consigneeAddress: data.consigneeAddress,
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    establishmentAquaculture: data.establishmentAquaculture,
                    establishmentProcessing: data.establishmentProcessing,
                    establishmentOther: data.establishmentOther,
                    establishmentName: data.establishmentName,
                    establishmentRegNo: data.establishmentRegNo,
                    establishmentAddress: data.establishmentAddress,
                    countryRegionOrigin: data.countryRegionOrigin || 'SRI LANKA, INDIAN OCEAN (FAO 57)',
                    sourceFarmRaised: data.sourceFarmRaised,
                    sourceWildCaught: data.sourceWildCaught,
                    portOfShipment: data.portOfShipment || 'COLOMBO PORT / AIRPORT',
                    transportAir: data.transportAir,
                    transportSea: data.transportSea,
                    transportRoad: data.transportRoad,
                    commodityDescription: data.commodityDescription,
                    tempAmbient: data.tempAmbient,
                    tempFrozen: data.tempFrozen,
                    tempChilled: data.tempChilled,
                    intendedHumanConsumption: data.intendedHumanConsumption,
                    intendedCultureBreeding: data.intendedCultureBreeding,
                    intendedTrade: data.intendedTrade,
                    intendedResearch: data.intendedResearch,
                    intendedFishFeed: data.intendedFishFeed,
                    intendedExhibition: data.intendedExhibition,
                    intendedOther: data.intendedOther,
                    totalPackages: data.totalPackages,
                    packagingType: data.packagingType,
                    totalQuantityKg: data.totalQuantityKg,
                    containerSealNumber: data.containerSealNumber,
                    portOfDestination: data.portOfDestination,
                    transportVesselName: data.transportVesselName,
                    transportVoyageNumber: data.transportVoyageNumber,
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : null,
                    testingLaboratory: data.testingLaboratory || 'NATIONAL AQUATIC RESOURCES RESEARCH AND DEVELOPMENT AGENCY (NARA)',
                    laboratoryAddress: data.laboratoryAddress || 'CROWH ISLAND, MATTAKKULIYA, COLOMBO 15, SRI LANKA',
                    approvingOfficerName: data.approvingOfficerName,
                    testResultNumber: data.testResultNumber,
                    attestationRefNumber: data.attestationRefNumber || ref,
                    attestFinfish: data.attestFinfish,
                    attestMollusca: data.attestMollusca,
                    attestCrustacea: data.attestCrustacea,
                    attestFisheryProducts: data.attestFisheryProducts,
                    attestOther: data.attestOther,
                    attestClauseA: data.attestClauseA,
                    attestClauseB: data.attestClauseB,
                    attestClauseC: data.attestClauseC,
                    attestClauseCCrustacean: data.attestClauseCCrustacean,
                    attestClauseCCyprinidae: data.attestClauseCCyprinidae,
                    attestClauseCTilapia: data.attestClauseCTilapia,
                    attestClauseCCatfish: data.attestClauseCCatfish,
                    attestClauseCOtherFish: data.attestClauseCOtherFish,
                    attestClauseCVisibleSigns: data.attestClauseCVisibleSigns,
                    attestClauseCPackagedContainers: data.attestClauseCPackagedContainers,
                    attestClauseD: data.attestClauseD,
                    attestClauseE: data.attestClauseE,
                    additionalInformation: data.additionalInformation,
                    certifiedName: data.signatoryName || '',
                    certifiedPosition: data.certifiedPosition || data.qualification || 'AUTHORIZED FISH INSPECTION OFFICER',
                    certifiedIssuedAt: data.certifiedIssuedAt || 'COLOMBO, SRI LANKA',
                    certifiedDate: data.certifiedDate ? new Date(data.certifiedDate) : new Date(),
                    certifiedPhone: data.certifiedPhone || '+94 (0) 11 2446183',
                    certifiedFax: data.certifiedFax || '+94 (0) 11 2446183',
                    certifiedEmail: data.certifiedEmail || 'dgdfar@gmail.com',
                    certifiedAddress: data.certifiedAddress || 'NEW SECRETARIAT, MALIGAWATTA, COLOMBO 10, SRI LANKA.',
                    signatoryUserId: data.signatoryUserId,
                    signatoryName: data.signatoryName,
                    qualification: data.qualification || 'AUTHORIZED FISH INSPECTION OFFICER'
                });
                if (data.products && data.products.length > 0) {
                    const arr = this.form.get('products') as FormArray;
                    arr.clear();
                    data.products.forEach((p) => {
                        arr.push(this.fb.group({ no: [p.no], commonName: [p.commonName], scientificName: [p.scientificName], hsCode: [p.hsCode], quantity: [p.quantity], unit: [p.unit] }));
                    });
                }
            },
            error: () => {
                this.loadVetFormData(requestId);
            }
        });
    }

    private loadVetFormData(requestId: number) {
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
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
        const vetProducts =
            Array.isArray(data.products) && data.products.length > 0
                ? data.products
                : [
                      {
                          descCommon: data.descCommon,
                          descScientific: data.descScientific,
                          hsCode: data.hsCode,
                          quantity: data.quantity
                      }
                  ];

        const totalQuantity = vetProducts.reduce((sum, product) => sum + (Number(product.quantity) || 0), 0);
        const firstProduct = vetProducts[0];

        const isCrabOrCrustacean = vetProducts.some(p => 
            (p.descCommon && /crab|prawn|shrimp|crustacea|lobster/i.test(p.descCommon)) ||
            (p.descScientific && /portunus|scylla|penaeus|crustacea/i.test(p.descScientific))
        );

        const ref = this.refNumber || this.form.get('numberNomor')?.value || 'ID 8813';
        this.refNumber = ref;

        this.form.patchValue({
            numberNomor: ref,
            attestationRefNumber: ref,
            consignorName: data.consignorName,
            consignorAddress: data.consignorAddress,
            consigneeName: data.consigneeName,
            consigneeAddress: data.consigneeAddress,
            countryRegionOrigin: data.countryOrigin || 'SRI LANKA, INDIAN OCEAN (FAO 57)',
            portOfShipment: data.placeOfLoading || 'COLOMBO PORT / AIRPORT',
            transportAir: data.transportAeroPlane,
            transportSea: data.transportShip ?? true,
            transportRoad: data.transportRoadVehicle,
            commodityDescription: `${firstProduct?.descCommon || data.descCommon || ''} ${firstProduct?.descScientific || data.descScientific || ''}`.trim(),
            tempAmbient: data.temperatureAmbient,
            tempFrozen: data.temperatureFrozen,
            tempChilled: data.temperatureChilled ?? true,
            totalPackages: data.numPackages,
            packagingType: data.packagingType || 'PACKED IN CORRUGATED CARTONS',
            totalQuantityKg: data.netWeight,
            dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : null,
            establishmentName: data.processingEstName,
            establishmentRegNo: data.approvalNo,
            establishmentAddress: data.processingEstAddress,
            containerSealNumber: data.containerId,
            intendedHumanConsumption: data.forHumanConsumption ?? true,
            establishmentProcessing: true,
            sourceWildCaught: true,
            attestCrustacea: isCrabOrCrustacean,
            attestClauseCCrustacean: isCrabOrCrustacean
        });

        const productsArray = this.form.get('products') as FormArray;
        productsArray.clear();

        vetProducts.forEach((product, index) => {
            productsArray.push(
                this.fb.group({
                    no: String(index + 1),
                    commonName: product.descCommon || '',
                    scientificName: product.descScientific || '',
                    hsCode: product.hsCode || '',
                    quantity: Number(product.quantity) || 0,
                    unit: 'MC'
                })
            );
        });

        this.form.patchValue({
            totalQuantityKg: totalQuantity > 0 ? String(totalQuantity) : data.netWeight
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    get productRows() {
        return (this.form.get('products') as FormArray).getRawValue() ?? [];
    }

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }

    createProductRow(): FormGroup {
        return this.fb.group({
            no: [''],
            commonName: [''],
            scientificName: [''],
            hsCode: [''],
            quantity: [0],
            unit: ['']
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

    calculateTotalQuantity(): number {
        return this.products.controls.map((control) => control.get('quantity')?.value || 0).reduce((acc, curr) => acc + curr, 0);
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
            const rawValue = this.form.getRawValue();
            const payload = {
                ...rawValue,
                certificateRequestId: this.certificateRequestId,
                dateOfDeparture: toLocalISOString(rawValue.dateOfDeparture),
                certifiedDate: toLocalISOString(rawValue.certifiedDate)
            };

            this.certificateService.submitIdCertificate(payload).subscribe({
                next: () => {
                    this.isSaving = false;
                    this.messageService.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Indonesia Certificate saved successfully'
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
                        detail: 'Failed to save Indonesia Certificate'
                    });
                }
            });
        } else {
            this.form.markAllAsTouched();
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields' });
        }
    }

    setSelectedUserQualification(userId: string) {
        const user = this.users.find((u) => u.id === userId);
        this.selectedUserQualification = user?.qualification || null;
        if (user) {
            this.form.patchValue({
                signatoryName: user.name,
                certifiedName: user.name,
                qualification: user.qualification || 'AUTHORIZED FISH INSPECTION OFFICER'
            });
        } else {
            this.form.patchValue({
                signatoryName: '',
                certifiedName: '',
                qualification: ''
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
            this.form.patchValue({ signatoryName: '', certifiedName: '', qualification: '' });
        }
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

    print(): void {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }

        const originalTitle = document.title;
        const ref = this.form.get('numberNomor')?.value || this.form.get('attestationRefNumber')?.value || 'Indonesia_Health_Certificate';
        document.title = `${ref}_Indonesia_Health_Certificate`;
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

