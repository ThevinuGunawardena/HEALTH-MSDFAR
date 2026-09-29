import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { ButtonModule } from 'primeng/button';
import { TableModule } from 'primeng/table';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { CheckboxModule } from 'primeng/checkbox';
import { TooltipModule } from 'primeng/tooltip';
import { AuCertificateView, CertificateRequestService, CreateAuCertificatePayload, VetFormFieldResponse } from 'src/app/pages/service/certificate-request.service';
import { UserService, User } from '@/pages/service/user.service';
import { RadioButton } from 'primeng/radiobutton';
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { toLocalISOString } from '@/shared/utils/date-utils';

@Component({
    selector: 'app-au-certificate',
    standalone: true,
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        InputTextModule,
        TextareaModule,
        ButtonModule,
        TableModule,
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
    templateUrl: './au-certificate.component.html',
    styleUrls: ['./au-certificate.component.css', '../certificate-print.css']
})
export class AuCertificateComponent implements OnInit {
    form: FormGroup;
    requestId: number | null = null;
    get certificateRequestId(): number | null { return this.requestId; }
    viewOnly = false;
    isEmbedded = false;
    isCompany = false;
    isApproved = false;
    isSubmitted = false;
    userOptions: { label: string; value: string }[] = [];
    users: User[] = [];
    selectedUserQualification: string | null = null;
    showPasswordDialog = false;
    pendingSignatoryUserId: string | null = null;
    pendingSignatoryUserEmail = '';
    previousSignatoryUserId: string | null = null;

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
        private certificateRequestService: CertificateRequestService,
        private userService: UserService,
        private location: Location,
        private authService: AuthService
    ) {
        this.form = this.fb.group({
            consignorName: ['', Validators.required],
            consignorAddress: ['', Validators.required],
            consignorPostalCode: [''],
            consignorTelNo: [''],
            certificateReferenceNumber: ['TB 7676'],
            certificateReferenceNumberA: [''],
            centralCompetentAuthority: ['DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES', Validators.required],
            localCompetentAuthority: ['DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES', Validators.required],
            consigneeName: ['', Validators.required],
            consigneeAddress: ['', Validators.required],
            consigneePostalCode: [''],
            consigneeTelNo: [''],
            consignee6: [''],
            countryOfOrigin: ['SRI LANKA', Validators.required],
            countryOfOriginIsoCode: ['LK'],
            regionOfOrigin: ['INDIAN OCEAN'],
            regionOfOriginCode: ['57'],
            countryOfDestination: ['AUSTRALIA', Validators.required],
            countryOfDestinationIso: ['AU'],
            countryOfDestination110: [''],
            placeOfOriginName: ['', Validators.required],
            placeOfOriginAddress: [''],
            placeOfOriginApprovalNumber: ['DFAR/FPE/98/86'],
            countryOfDestination112: [''],
            placeOfLoading: ['COLOMBO/ SRI LANKA', Validators.required],
            dateOfDeparture: [new Date('2026-06-10')],
            transportAeroPlane: [true],
            transportShip: [false],
            transportRailwayWagon: [false],
            transportRoadVehicle: [false],
            transportOther: [false],
            identificationDocumentReferences: ['AWB NO:618-6141-3553\nFLIGHT:SQ469/SQ235'],
            identificationDocumentReferencesDoc: [''],
            entryBIP: ['AUSTRALIA - BRISBANE'],
            field117: [''],
            descriptionOfCommodity: ['FROZEN MUD CRAB (Scylla serrata)'],
            commodityCodeHS: ['0306 2410'],
            quantity: ['75.0 KGS'],
            temperatureAmbient: [false],
            temperatureChilled: [false],
            temperatureFrozen: [true],
            numberOfPackages: ['05 BOXES'],
            containerSealNumber: [''],
            typeOfPackaging: ['VACUUMED PACKED BAGS, WRAPPED IN\nPOLYTHENE COVER & PUT IN TO A\nSTYROFOAM BOXES'],
            commoditiesHumanConsumption: [true],
            field126: [''],
            forImportOrAdmissionInto: ['AUSTRALIA', Validators.required],
            products: this.fb.array([
                this.fb.group({
                    speciesScientificName: ['Scylla serrata', Validators.required],
                    natureOfCommodity: ['WILD ORIGIN', Validators.required],
                    treatmentType: ['FROZEN', Validators.required],
                    approvalNumberOfEstablishments: ['DFAR/FPE/98/86'],
                    manufacturingPlant: ['DFAR/FPE/98/86\nP.N.FERNANDO &\nCOMPANY (PVT) LTD,\n120/2, WEWALA, JA ELA\nSRI LANKA.'],
                    numberOfPackages: ['05 BOXES'],
                    netWeight: ['75.0 KGS']
                })
            ]),
            healthAttestationCertRefNumber: ['TB 7676'],
            healthAttestationCertRefNumberB: [''],
            exportApprovalNumber: ['DFAR/FPE/98/86'],
            nameInCapitals: ['A.N.S.SENEVIRATNE'],
            qualificationAndTitle: ['QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).'],
            qualification: ['QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).'],
            signatureDate: [new Date('2026-06-10')],
            stamp: [''],
            signature: [''],
            signatoryUserId: [null, Validators.required],
            viewMode: ['crustaceans']
        });

        this.form.get('certificateReferenceNumber')?.valueChanges.subscribe((val) => {
            if (val) {
                this.form.patchValue({ healthAttestationCertRefNumber: val }, { emitEvent: false });
            }
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
        this.route.queryParams.subscribe((params) => {
            this.isEmbedded = params['embedded'] === 'true' || (typeof window !== 'undefined' && window.self !== window.top);
            if (params['adminEdit'] === 'true') {
                this.viewOnly = false;
            } else {
                this.viewOnly = params['viewOnly'] === 'true' || params['viewOnly'] === true;
            }
            if (params['requestId']) {
                this.requestId = Number(params['requestId']);
                this.checkRequestApproval(this.requestId);
                this.loadSavedCertificateData(this.requestId);
            }
            if (params['ref'] && !this.viewOnly) {
                this.form.patchValue({ certificateReferenceNumber: params['ref'], healthAttestationCertRefNumber: params['ref'] });
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

    onViewModeChange(mode: string) {
        this.form.get('viewMode')?.setValue(mode);
    }

    setSelectedUserQualification(userId: string) {
        const user = this.users.find((u) => u.id === userId);
        this.selectedUserQualification = user?.qualification || null;
        if (user) {
            this.form.patchValue({
                nameInCapitals: user.name,
                qualificationAndTitle: user.qualification || '',
                qualification: user.qualification || ''
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
                nameInCapitals: '',
                qualificationAndTitle: '',
                qualification: ''
            });
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Sample Loaders (Matching Official DFAR Australia Scans)
    // ─────────────────────────────────────────────────────────────

    /**
     * 1. Wild Caught Crustaceans (TB 7676 - Mud Crab)
     */
    loadSampleCrustaceans(): void {
        this.form.patchValue({
            viewMode: 'crustaceans',
            certificateReferenceNumber: 'TB 7676',
            healthAttestationCertRefNumber: 'TB 7676',
            consignorName: 'P.N.FERNANDO & COMPANY (PVT) LTD',
            consignorAddress: '120/2, WEWALA, JA ELA.\nSRI LANKA.',
            consignorPostalCode: '',
            consignorTelNo: '',
            centralCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            localCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            consigneeName: 'PNF GLOBAL PTY LTD',
            consigneeAddress: 'NO:38/15,VIOLET CLOSE,EIGHT MILE\nPLAINS QLD 4113, ABN-669313642\nPHONE 040125397 6 / 04817 37 427\nEMAIL: iyande@yahoo.co.uk',
            consigneePostalCode: '',
            consigneeTelNo: '',
            countryOfOrigin: 'SRI LANKA',
            countryOfOriginIsoCode: 'LK',
            regionOfOrigin: 'INDIAN OCEAN',
            regionOfOriginCode: '57',
            countryOfDestination: 'AUSTRALIA',
            countryOfDestinationIso: 'AU',
            placeOfOriginName: 'P.N.FERNANDO & COMPANY (PVT) LTD',
            placeOfOriginApprovalNumber: 'DFAR/FPE/98/86',
            placeOfOriginAddress: '120/2, WEWALA, JA ELA.\nSRI LANKA.',
            placeOfLoading: 'COLOMBO/ SRI LANKA',
            dateOfDeparture: new Date('2026-06-10'),
            transportAeroPlane: true,
            transportShip: false,
            transportRailwayWagon: false,
            transportRoadVehicle: false,
            transportOther: false,
            identificationDocumentReferences: 'AWB NO:618-6141-3553\nFLIGHT:SQ469/SQ235',
            entryBIP: 'AUSTRALIA - BRISBANE',
            descriptionOfCommodity: 'FROZEN MUD CRAB (Scylla serrata)',
            commodityCodeHS: '0306 2410',
            quantity: '75.0 KGS',
            temperatureAmbient: false,
            temperatureChilled: false,
            temperatureFrozen: true,
            numberOfPackages: '05 BOXES',
            containerSealNumber: '',
            typeOfPackaging: 'VACUUMED PACKED BAGS, WRAPPED IN\nPOLYTHENE COVER & PUT IN TO A\nSTYROFOAM BOXES',
            commoditiesHumanConsumption: true,
            forImportOrAdmissionInto: 'AUSTRALIA',
            exportApprovalNumber: 'DFAR/FPE/98/86',
            nameInCapitals: 'A.N.S.SENEVIRATNE',
            qualificationAndTitle: 'QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).',
            qualification: 'QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).',
            signatureDate: new Date('2026-06-10')
        });

        const products = this.form.get('products') as FormArray;
        products.clear();
        products.push(
            this.fb.group({
                speciesScientificName: ['Scylla serrata', Validators.required],
                natureOfCommodity: ['WILD ORIGIN', Validators.required],
                treatmentType: ['FROZEN', Validators.required],
                approvalNumberOfEstablishments: ['DFAR/FPE/98/86'],
                manufacturingPlant: ['DFAR/FPE/98/86\nP.N.FERNANDO &\nCOMPANY (PVT) LTD,\n120/2, WEWALA, JA ELA\nSRI LANKA.'],
                numberOfPackages: ['05 BOXES'],
                netWeight: ['75.0 KGS']
            })
        );

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Crustaceans Sample',
            detail: 'Loaded Australia Wild Caught Crustaceans certificate sample (TB 7676 - Mud Crab).'
        });
    }

    /**
     * 2. Wild Caught Fish (TB 7669 - Spanish Mackerel)
     */
    loadSampleWildFish(): void {
        this.form.patchValue({
            viewMode: 'wild_fish',
            certificateReferenceNumber: 'TB 7669',
            healthAttestationCertRefNumber: 'TB 7669',
            consignorName: 'P.N.FERNANDO & COMPANY (PVT) LTD',
            consignorAddress: '120/2, WEWALA, JA ELA.\nSRI LANKA.',
            consignorPostalCode: '',
            consignorTelNo: '',
            centralCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            localCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            consigneeName: 'PNF GLOBAL PTY LTD',
            consigneeAddress: 'NO:38/15,VIOLET CLOSE,EIGHT MILE\nPLAINS QLD 4113, ABN-669313642\nPHONE 040125397 6 / 04817 37 427\nEMAIL: iyande@yahoo.co.uk',
            consigneePostalCode: '',
            consigneeTelNo: '',
            countryOfOrigin: 'SRI LANKA',
            countryOfOriginIsoCode: 'LK',
            regionOfOrigin: 'INDIAN OCEAN',
            regionOfOriginCode: '57',
            countryOfDestination: 'AUSTRALIA',
            countryOfDestinationIso: 'AU',
            placeOfOriginName: 'P.N.FERNANDO & COMPANY (PVT) LTD',
            placeOfOriginApprovalNumber: 'DFAR/FPE/98/86',
            placeOfOriginAddress: '120/2, WEWALA, JA ELA.\nSRI LANKA.',
            placeOfLoading: 'COLOMBO/ SRI LANKA',
            dateOfDeparture: new Date('2026-06-10'),
            transportAeroPlane: true,
            transportShip: false,
            transportRailwayWagon: false,
            transportRoadVehicle: false,
            transportOther: false,
            identificationDocumentReferences: 'AWB NO:618-6141-3553\nFLIGHT:SQ469/SQ235',
            entryBIP: 'AUSTRALIA - BRISBANE',
            descriptionOfCommodity: 'FROZEN SPANISH MACKEREL FISH (Scomberomorus commersoni)',
            commodityCodeHS: '0305 6900',
            quantity: '48.0 KGS',
            temperatureAmbient: false,
            temperatureChilled: false,
            temperatureFrozen: true,
            numberOfPackages: '02 BOXES',
            containerSealNumber: '',
            typeOfPackaging: 'VACUUMED PACKED BAGS, WRAPPED IN\nPOLYTHENE COVER & PUT IN TO A\nSTYROFOAM BOXES',
            commoditiesHumanConsumption: true,
            forImportOrAdmissionInto: 'AUSTRALIA',
            exportApprovalNumber: 'DFAR/FPE/98/86',
            nameInCapitals: 'A.N.S.SENEVIRATNE',
            qualificationAndTitle: 'QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).',
            qualification: 'QUALITY CONTROL OFFICER (GRADE I)\nB.Sc.(CHEMISTRY), SPECIAL HONS (SRI LANKA).',
            signatureDate: new Date('2026-06-10')
        });

        const products = this.form.get('products') as FormArray;
        products.clear();
        products.push(
            this.fb.group({
                speciesScientificName: ['Scomberomorus commersoni', Validators.required],
                natureOfCommodity: ['WILD ORIGIN', Validators.required],
                treatmentType: ['FROZEN', Validators.required],
                approvalNumberOfEstablishments: ['DFAR/FPE/98/86'],
                manufacturingPlant: ['DFAR/FPE/98/86\nP.N.FERNANDO &\nCOMPANY (PVT) LTD,\n120/2, WEWALA, JA ELA\nSRI LANKA.'],
                numberOfPackages: ['02 BOXES'],
                netWeight: ['48.0 KGS']
            })
        );

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Wild Fish Sample',
            detail: 'Loaded Australia Wild Caught Fish certificate sample (TB 7669 - Spanish Mackerel).'
        });
    }

    /**
     * 3. Aquaculture Fish (TC 2542 - Barramundi)
     */
    loadSampleAquaculture(): void {
        this.form.patchValue({
            viewMode: 'aquaculture',
            certificateReferenceNumber: 'TC 2542',
            healthAttestationCertRefNumber: 'TC 2542',
            consignorName: 'OCEANPICK PRIVATE LIMITED',
            consignorAddress: 'NO, 532/4F, SIRIKOTHA LANE, COLOMBO 03,\nSRI LANKA.',
            consignorPostalCode: '',
            consignorTelNo: '',
            centralCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            localCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            consigneeName: 'PACIFIC WEST FOODS AUSTRALIA PTY LTD',
            consigneeAddress: '3, TRENT RD, (PO BOX. 4358)\nNORTH ROCKS NSW 2151, AUSTRALIA\nTel No.: +61 408 086 907',
            consigneePostalCode: '',
            consigneeTelNo: '+61 408 086 907',
            countryOfOrigin: 'SRI LANKA',
            countryOfOriginIsoCode: 'LK',
            regionOfOrigin: 'INDIAN OCEAN',
            regionOfOriginCode: '57',
            countryOfDestination: 'AUSTRALIA',
            countryOfDestinationIso: 'AU',
            placeOfOriginName: 'CEYLON FRESH SEAFOOD (PVT) LTD',
            placeOfOriginApprovalNumber: 'DFAR/FPE/98/31',
            placeOfOriginAddress: 'NO.071, KUDAHAKAPOLA ROAD, TUDELLA,\nJA-ELA, SRI LANKA.',
            placeOfLoading: 'COLOMBO/ SRI LANKA',
            dateOfDeparture: new Date('2026-08-01'),
            transportAeroPlane: true,
            transportShip: false,
            transportRailwayWagon: false,
            transportRoadVehicle: false,
            transportOther: false,
            identificationDocumentReferences: 'Vessel name No :\nBill of lading no:',
            entryBIP: 'SYDNEY',
            descriptionOfCommodity: 'Aquacultured Frozen Barramundi (Lates calcarifer)\nSkin On Portions',
            commodityCodeHS: '0304',
            quantity: '400.0 KGS',
            temperatureAmbient: false,
            temperatureChilled: false,
            temperatureFrozen: true,
            numberOfPackages: '20 BOXES',
            containerSealNumber: '',
            typeOfPackaging: 'POLYBAGS IN CORRUGATED BOXES',
            commoditiesHumanConsumption: true,
            forImportOrAdmissionInto: 'AUSTRALIA',
            exportApprovalNumber: 'DFAR/FPE/98/31',
            nameInCapitals: 'I.M.W.D. ABEYSOORIYA',
            qualificationAndTitle: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc (BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc (BIOLOGY),M.Sc.(FOOD SCI & TEC)(SRI LANKA)\nPgDBM(BUSINESS MANAGEMENT)(SRI LANKA).',
            signatureDate: new Date('2026-08-01')
        });

        const products = this.form.get('products') as FormArray;
        products.clear();
        products.push(
            this.fb.group({
                speciesScientificName: ['Lates calcarifer', Validators.required],
                natureOfCommodity: ['AQUACULTURED', Validators.required],
                treatmentType: ['FROZEN', Validators.required],
                approvalNumberOfEstablishments: ['DFAR/FPE/98/31'],
                manufacturingPlant: ['DFAR/FPE/98/31\nCEYLON FRESH SEAFOOD (PVT) LTD,\nNO.071, KUDAHAKAPOLA ROAD, TUDELLA,\nJA-ELA, SRI LANKA.'],
                numberOfPackages: ['20 BOXES'],
                netWeight: ['400.0 KGS']
            })
        );

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Aquaculture Sample',
            detail: 'Loaded Australia Aquaculture Barramundi certificate sample (TC 2542).'
        });
    }

    /**
     * 4. Wild Caught Molluscs (TA 0554 - Baby Squid)
     */
    loadSampleMolluscs(): void {
        this.form.patchValue({
            viewMode: 'molluscs',
            certificateReferenceNumber: 'TA 0554',
            healthAttestationCertRefNumber: 'TA 0554',
            consignorName: 'P.N. FERNANDO & COMPANY (PVT) LTD',
            consignorAddress: '120/2, WEWALA, JA ELA.\nSRI LANKA.',
            consignorPostalCode: '',
            consignorTelNo: '',
            centralCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            localCompetentAuthority: 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
            consigneeName: 'PNF GLOBAL PTY LTD',
            consigneeAddress: 'NO:38/15,VIOLET CLOSE,EIGHT MILE\nPLATNS QLD 4113, ABN-669313642\nPHONE 040125397 6 / 04817 37 427\nEMAIL: iyande@yahoo.co.uk',
            consigneePostalCode: '',
            consigneeTelNo: '',
            countryOfOrigin: 'SRI LANKA',
            countryOfOriginIsoCode: 'LK',
            regionOfOrigin: 'INDIAN OCEAN',
            regionOfOriginCode: '57',
            countryOfDestination: 'AUSTRALIA',
            countryOfDestinationIso: 'AU',
            placeOfOriginName: 'P.N.FERNANDO & COMPANY (PVT) LTD',
            placeOfOriginApprovalNumber: 'DFAR/FPE/98/86',
            placeOfOriginAddress: '120/2, WEWALA, JA ELA.\nSRI LANKA.',
            placeOfLoading: 'COLOMBO/ SRI LANKA',
            dateOfDeparture: new Date('2025-10-05'),
            transportAeroPlane: true,
            transportShip: false,
            transportRailwayWagon: false,
            transportRoadVehicle: false,
            transportOther: false,
            identificationDocumentReferences: 'FLIGHT NO:SQ469/SQ235\nAWB NO:618-2461-5102',
            entryBIP: 'AUSTRALIA - BRISBANE',
            descriptionOfCommodity: 'FROZEN BABY SQUID (Uroteuthis singhalensis)',
            commodityCodeHS: '0307 4190',
            quantity: '96.0 KGS',
            temperatureAmbient: false,
            temperatureChilled: false,
            temperatureFrozen: true,
            numberOfPackages: '04 BOXES',
            containerSealNumber: '',
            typeOfPackaging: 'VACUUMED PACKED BAGS, WRAPPED IN\nPOLYTHENE COVER & PUT IN TO A STYROFOAM\nBOXES',
            commoditiesHumanConsumption: true,
            forImportOrAdmissionInto: 'AUSTRALIA',
            exportApprovalNumber: 'DFAR/FPE/98/86',
            nameInCapitals: 'H.M.U. BANDARA',
            qualificationAndTitle: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA)',
            qualification: 'QUALITY CONTROL OFFICER (GRADE II)\nB.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA)',
            signatureDate: new Date('2025-10-05')
        });

        const products = this.form.get('products') as FormArray;
        products.clear();
        products.push(
            this.fb.group({
                speciesScientificName: ['Uroteuthis singhalensis', Validators.required],
                natureOfCommodity: ['WILD ORIGIN', Validators.required],
                treatmentType: ['FROZEN', Validators.required],
                approvalNumberOfEstablishments: ['DFAR/FPE/98/86'],
                manufacturingPlant: ['DFAR/FPE/98/86\nP.N.FERNANDO &\nCOMPANY (PVT) LTD,\n120/2, WEWALA, JA ELA\nSRI LANKA.'],
                numberOfPackages: ['04 BOXES'],
                netWeight: ['96.0 KGS']
            })
        );

        this.messageService.add({
            severity: 'info',
            summary: 'Loaded Molluscs Sample',
            detail: 'Loaded Australia Wild Caught Molluscs certificate sample (TA 0554 - Baby Squid).'
        });
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateRequestService.getAuCertificateByRequestId(requestId).subscribe({
            next: (data: AuCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }
                this.form.patchValue({
                    consignorName: data.consignorName,
                    consignorAddress: data.consignorAddress,
                    consignorPostalCode: data.consignorPostal,
                    consignorTelNo: data.consignorTel,
                    certificateReferenceNumber: data.certRefNumber,
                    certificateReferenceNumberA: data.certRefNumberA,
                    centralCompetentAuthority: data.centralCompetentAuthority || 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
                    localCompetentAuthority: data.localCompetentAuthority || 'DEPARTMENT OF FISHERIES &\nAQUATIC RESOURCES',
                    consigneeName: data.consigneeName,
                    consigneeAddress: data.consigneeAddress,
                    consigneePostalCode: data.consigneePostal,
                    consigneeTelNo: data.consigneeTel,
                    consignee6: data.consignee6,
                    countryOfOrigin: data.countryOrigin || 'SRI LANKA',
                    countryOfOriginIsoCode: data.countryOriginISO || 'LK',
                    regionOfOrigin: data.regionOrigin || 'INDIAN OCEAN',
                    regionOfOriginCode: data.regionOriginISO || '57',
                    countryOfDestination: data.countryDestination || 'AUSTRALIA',
                    countryOfDestinationIso: data.countryDestinationISO || 'AU',
                    countryOfDestination110: data.countryDestination110,
                    placeOfOriginName: data.placeOfOriginName,
                    placeOfOriginAddress: data.placeOfOriginAddress,
                    placeOfOriginApprovalNumber: data.placeOfOriginApprovalNo,
                    countryOfDestination112: data.countryDestination112,
                    placeOfLoading: data.placeOfLoading || 'COLOMBO/ SRI LANKA',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : null,
                    transportAeroPlane: data.transportAeroPlane,
                    transportShip: data.transportShip,
                    transportRailwayWagon: data.transportRailwayWagon,
                    transportRoadVehicle: data.transportRoadVehicle,
                    transportOther: data.transportOther,
                    identificationDocumentReferences: data.docReferences,
                    entryBIP: data.entryBIP,
                    field117: data.field117,
                    descriptionOfCommodity: data.descCommon,
                    commodityCodeHS: data.hsCode,
                    quantity: data.quantity,
                    temperatureAmbient: data.temperatureAmbient,
                    temperatureChilled: data.temperatureChilled,
                    temperatureFrozen: data.temperatureFrozen,
                    numberOfPackages: data.numPackages,
                    containerSealNumber: data.containerId,
                    typeOfPackaging: data.packagingType,
                    commoditiesHumanConsumption: data.forHumanConsumption,
                    field126: data.field126,
                    forImportOrAdmissionInto: data.forImportEU || 'AUSTRALIA',
                    healthAttestationCertRefNumber: data.healthCertNo || data.certRefNumber,
                    healthAttestationCertRefNumberB: data.healthCertNoB,
                    exportApprovalNumber: data.exportApprovalNumber,
                    signatoryUserId: data.signatoryUserId,
                    nameInCapitals: data.signatoryName,
                    qualificationAndTitle: data.qualification,
                    qualification: data.qualification,
                    signatureDate: data.signatureDate ? new Date(data.signatureDate) : null,
                    stamp: data.stamp,
                    signature: data.signature,
                    viewMode: data.certificateType || 'crustaceans'
                });

                if (data.id || data.certRefNumber || data.consignorName) {
                    this.isSubmitted = true;
                }

                if (data.products && data.products.length > 0) {
                    const products = this.form.get('products') as FormArray;
                    products.clear();
                    data.products.forEach((p) => {
                        products.push(
                            this.fb.group({
                                speciesScientificName: [p.speciesScientificName, Validators.required],
                                natureOfCommodity: [p.natureOfCommodity, Validators.required],
                                treatmentType: [p.treatmentType, Validators.required],
                                approvalNumberOfEstablishments: [p.approvalNumberOfEstablishments],
                                manufacturingPlant: [p.manufacturingPlant],
                                numberOfPackages: [p.numberOfPackages],
                                netWeight: [p.netWeight]
                            })
                        );
                    });
                }

                if (data.signatoryUserId) {
                    this.previousSignatoryUserId = data.signatoryUserId;
                    this.setSelectedUserQualification(data.signatoryUserId);
                }

                if (this.viewOnly) {
                    this.form.disable();
                } else {
                    this.form.enable();
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
                if (this.viewOnly) {
                    this.form.disable();
                }
            },
            error: () => {
                console.error('Failed to load vet form data for autofill');
            }
        });
    }

    private autofillForm(data: VetFormFieldResponse) {
        if (!data) return;
        const vetProducts = Array.isArray(data.products) && data.products.length > 0
            ? data.products
            : [
                  {
                      descCommon: data.descCommon,
                      descScientific: data.descScientific,
                      processingType: data.processingType,
                      numPackages: data.numPackages,
                      netWeight: data.netWeight,
                      treatmentChilled: data.treatmentChilled,
                      treatmentFrozen: data.treatmentFrozen,
                      treatmentLive: data.treatmentLive
                  }
              ];

        const products = this.form.get('products') as FormArray;
        products.clear();
        vetProducts.forEach((product) => {
            const treatmentType = product.treatmentChilled
                ? 'CHILLED'
                : product.treatmentFrozen
                  ? 'FROZEN'
                  : product.treatmentLive
                    ? 'LIVE'
                    : product.processingType || 'FROZEN';

            products.push(
                this.fb.group({
                    speciesScientificName: [product.descScientific || '', Validators.required],
                    natureOfCommodity: [product.descCommon || 'WILD ORIGIN', Validators.required],
                    treatmentType: [treatmentType, Validators.required],
                    approvalNumberOfEstablishments: [data.approvalNo || ''],
                    manufacturingPlant: [`${data.approvalNo || ''}\n${data.processingEstName || ''}\n${data.processingEstAddress || ''}`],
                    numberOfPackages: [product.numPackages || ''],
                    netWeight: [product.netWeight || '']
                })
            );
        });

        const firstProduct = vetProducts[0];

        this.form.patchValue({
            certificateReferenceNumber: data.healthCertNo || 'TB 7676',
            healthAttestationCertRefNumber: data.healthCertNo || 'TB 7676',
            consignorName: data.consignorName,
            consignorAddress: data.consignorAddress,
            consignorPostalCode: data.consignorPostal,
            consignorTelNo: data.consignorTel,
            consigneeName: data.consigneeName,
            consigneeAddress: data.consigneeAddress,
            consigneePostalCode: data.consigneePostal,
            consigneeTelNo: data.consigneeTel,
            countryOfOrigin: data.countryOrigin || 'SRI LANKA',
            countryOfOriginIsoCode: data.countryOriginISO || 'LK',
            regionOfOrigin: 'INDIAN OCEAN',
            regionOfOriginCode: data.regionOriginISO || '57',
            countryOfDestination: 'AUSTRALIA',
            countryOfDestinationIso: data.countryDestinationISO || 'AU',
            placeOfLoading: data.placeOfLoading || 'COLOMBO/ SRI LANKA',
            dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : null,
            transportAeroPlane: data.transportAeroPlane ?? true,
            transportShip: data.transportShip ?? false,
            transportRailwayWagon: data.transportRailwayWagon ?? false,
            transportRoadVehicle: data.transportRoadVehicle ?? false,
            transportOther: data.transportOther ?? false,
            identificationDocumentReferences: data.docReferences || '',
            entryBIP: data.entryBIP || 'AUSTRALIA - BRISBANE',
            descriptionOfCommodity: firstProduct?.descCommon || data.descCommon,
            commodityCodeHS: data.hsCode,
            quantity: data.quantity || '',
            temperatureAmbient: data.temperatureAmbient ?? false,
            temperatureChilled: data.temperatureChilled ?? false,
            temperatureFrozen: data.temperatureFrozen ?? true,
            numberOfPackages: firstProduct?.numPackages || data.numPackages || '',
            containerSealNumber: data.containerId,
            typeOfPackaging: data.packagingType || 'VACUUMED PACKED BAGS, WRAPPED IN\nPOLYTHENE COVER & PUT IN TO A\nSTYROFOAM BOXES',
            commoditiesHumanConsumption: data.forHumanConsumption ?? true,
            placeOfOriginName: data.processingEstName,
            placeOfOriginAddress: data.processingEstAddress,
            placeOfOriginApprovalNumber: data.approvalNo || 'DFAR/FPE/98/86',
            exportApprovalNumber: data.approvalNo || 'DFAR/FPE/98/86',
            forImportOrAdmissionInto: data.forImportEU || 'AUSTRALIA'
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    get productRows() {
        return (this.form.get('products') as FormArray).getRawValue() ?? [];
    }

    createProductRow(): FormGroup {
        return this.fb.group({
            speciesScientificName: ['', Validators.required],
            natureOfCommodity: ['', Validators.required],
            treatmentType: ['FROZEN', Validators.required],
            approvalNumberOfEstablishments: [''],
            manufacturingPlant: [''],
            numberOfPackages: [''],
            netWeight: ['']
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

    calculateTotalPackages(): string {
        const total = this.products.controls.reduce((sum, control) => {
            const val = control.get('numberOfPackages')?.value;
            const parsed = typeof val === 'number' ? val : parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total} BOXES` : '';
    }

    calculateTotalNetWeight(): string {
        const total = this.products.controls.reduce((sum, control) => {
            const val = control.get('netWeight')?.value;
            const parsed = typeof val === 'number' ? val : parseFloat(String(val).replace(/[^0-9.]/g, ''));
            return sum + (isNaN(parsed) ? 0 : parsed);
        }, 0);
        return total > 0 ? `${total.toLocaleString('en-US', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} KGS` : '';
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

        const rawValue = this.form.getRawValue();

        const payload: CreateAuCertificatePayload = {
            certificateRequestId: this.requestId,
            consignorName: rawValue.consignorName || '',
            consignorAddress: rawValue.consignorAddress || '',
            consignorPostal: rawValue.consignorPostalCode || '',
            consignorTel: rawValue.consignorTelNo || '',
            certRefNumber: rawValue.certificateReferenceNumber || '',
            certRefNumberA: rawValue.certificateReferenceNumberA || '',
            centralCompetentAuthority: rawValue.centralCompetentAuthority || '',
            localCompetentAuthority: rawValue.localCompetentAuthority || '',
            consigneeName: rawValue.consigneeName || '',
            consigneeAddress: rawValue.consigneeAddress || '',
            consigneePostal: rawValue.consigneePostalCode || '',
            consigneeTel: rawValue.consigneeTelNo || '',
            consignee6: rawValue.consignee6 || '',
            countryOrigin: rawValue.countryOfOrigin || '',
            countryOriginISO: rawValue.countryOfOriginIsoCode || '',
            regionOrigin: rawValue.regionOfOrigin || '',
            regionOriginISO: rawValue.regionOfOriginCode || '',
            countryDestination: rawValue.countryOfDestination || '',
            countryDestinationISO: rawValue.countryOfDestinationIso || '',
            countryDestination110: rawValue.countryOfDestination110 || '',
            placeOfOriginName: rawValue.placeOfOriginName || '',
            placeOfOriginAddress: rawValue.placeOfOriginAddress || '',
            placeOfOriginApprovalNo: rawValue.placeOfOriginApprovalNumber || '',
            countryDestination112: rawValue.countryOfDestination112 || '',
            placeOfLoading: rawValue.placeOfLoading || '',
            dateOfDeparture: toLocalISOString(rawValue.dateOfDeparture),
            transportAeroPlane: !!rawValue.transportAeroPlane,
            transportShip: !!rawValue.transportShip,
            transportRailwayWagon: !!rawValue.transportRailwayWagon,
            transportRoadVehicle: !!rawValue.transportRoadVehicle,
            transportOther: !!rawValue.transportOther,
            docReferences: rawValue.identificationDocumentReferences || '',
            entryBIP: rawValue.entryBIP || '',
            field117: rawValue.field117 || '',
            descCommon: rawValue.descriptionOfCommodity || '',
            hsCode: rawValue.commodityCodeHS || '',
            quantity: rawValue.quantity || '',
            temperatureAmbient: !!rawValue.temperatureAmbient,
            temperatureChilled: !!rawValue.temperatureChilled,
            temperatureFrozen: !!rawValue.temperatureFrozen,
            numPackages: rawValue.numberOfPackages || '',
            containerId: rawValue.containerSealNumber || '',
            packagingType: rawValue.typeOfPackaging || '',
            forHumanConsumption: !!rawValue.commoditiesHumanConsumption,
            field126: rawValue.field126 || '',
            forImportEU: rawValue.forImportOrAdmissionInto || '',
            healthCertNo: rawValue.healthAttestationCertRefNumber || '',
            healthCertNoB: rawValue.healthAttestationCertRefNumberB || '',
            exportApprovalNumber: rawValue.exportApprovalNumber || '',
            signatoryUserId: rawValue.signatoryUserId || null,
            signatoryName: rawValue.nameInCapitals || '',
            qualification: rawValue.qualificationAndTitle || rawValue.qualification || '',
            signatureDate: toLocalISOString(rawValue.signatureDate),
            stamp: rawValue.stamp || '',
            signature: rawValue.signature || '',
            certificateType: rawValue.viewMode || 'crustaceans',
            products: (rawValue.products || []).map((product: any) => ({
                speciesScientificName: product.speciesScientificName ?? '',
                natureOfCommodity: product.natureOfCommodity ?? '',
                treatmentType: product.treatmentType ?? '',
                approvalNumberOfEstablishments: product.approvalNumberOfEstablishments ?? '',
                manufacturingPlant: product.manufacturingPlant ?? '',
                numberOfPackages: Number(String(product.numberOfPackages).replace(/[^0-9.]/g, '') || 0),
                netWeight: Number(String(product.netWeight).replace(/[^0-9.]/g, '') || 0)
            }))
        };

        this.certificateRequestService.submitAuCertificate(payload).subscribe({
            next: () => {
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Australia Certificate saved successfully'
                });
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
            error: () => {
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to save Australia Certificate'
                });
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

    onPrint(): void {
        if (this.isCompany && !this.isApproved) {
            this.messageService.add({
                severity: 'warn',
                summary: 'Print Disabled',
                detail: 'Printing is disabled until this certificate request is approved by DFAR Admin.'
            });
            return;
        }

        const ref = this.form.get('certificateReferenceNumber')?.value || 'Certificate';
        const mode = this.form.get('viewMode')?.value || 'crustaceans';
        let typeName = 'CRUSTACEAN - WILD CAUGHT';
        if (mode === 'wild_fish' || mode === 'edit') {
            typeName = 'FISH - WILD CAUGHT';
        } else if (mode === 'aquaculture') {
            typeName = 'FISH - AQUACULTURE';
        } else if (mode === 'molluscs' || mode === 'wild_molluscs') {
            typeName = 'MOLLUSCS - WILD CAUGHT';
        }

        const originalTitle = document.title;
        document.title = `${ref}_${typeName}`;
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
