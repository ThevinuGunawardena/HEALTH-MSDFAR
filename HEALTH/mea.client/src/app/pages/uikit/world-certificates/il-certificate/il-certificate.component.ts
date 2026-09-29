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
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';
import { UserService, User } from '@/pages/service/user.service';
import {
    CertificateRequestService,
    VetFormFieldResponse,
    VetProductFieldResponse,
    CreateIlCertificatePayload,
    CreateIlCertificateProductPayload,
    IlCertificateView,
    IlCertificateProductView
} from 'src/app/pages/service/certificate-request.service';
import { TableModule } from 'primeng/table';
import { TooltipModule } from 'primeng/tooltip';
import { toLocalISOString } from '@/shared/utils/date-utils';

export const DEFAULT_ISRAEL_SINGLE_PRODUCTS = [
    {
        descriptionOfCommodity: 'YELLOW FIN TUNA WHOLE, H&G',
        speciesScientificName: 'Thunnus albacares',
        natureOfCommodity: 'WILD SEA CAUGHT',
        treatmentType: 'CHILL ED (0 – 2 CENTR IGRAD E)',
        approvalNo: 'DFAR/FPE/98/107',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'YELLOW FIN TUNA FILLET, LOIN',
        speciesScientificName: 'Thunnus albacares',
        natureOfCommodity: 'WILD SEA CAUGHT',
        treatmentType: 'CHILL ED (0 – 2 CENTR IGRAD E)',
        approvalNo: 'DFAR/FPE/98/107',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    }
];

export const DEFAULT_ISRAEL_ATTACHMENT_PRODUCTS = [
    {
        descriptionOfCommodity: 'FRESH YELLOW FIN TUNA HEAD LESS & GUTTED',
        speciesScientificName: 'Thunnus albacares',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'FRESH YELLOW FIN TUNA HEAD LESS & GUTTED',
        speciesScientificName: 'Thunnus albacares',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'FRESH YELLOW FIN TUNA LOINS',
        speciesScientificName: 'Thunnus albacares',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'FRESH YELLOW FIN TUNA LOINS',
        speciesScientificName: 'Thunnus albacares',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'FRESH YELLOW FIN TUNA LOINS',
        speciesScientificName: 'Thunnus albacares',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'RABBIT FISH WHOLE',
        speciesScientificName: 'Siganus canaliculatus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'WHITE JOB FISH WHOLE',
        speciesScientificName: 'Pristipomoides typus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'DELAGOA THREADFIN BEAM WHOLE',
        speciesScientificName: 'Nemipterus bipunctatus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'BARRACUDA WHOLE',
        speciesScientificName: 'Sphyraena jello',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'WAVYLINED GROUPER WHOLE',
        speciesScientificName: 'Epinephelus undulosus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'MALABAR GROUPER WHOLE',
        speciesScientificName: 'Epinephelus malabaricus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'BARRAMUNDI WHOLE',
        speciesScientificName: 'Lates calcarifer',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'BROWN BACK TREVALLY WHOLE',
        speciesScientificName: 'Carangoides praeustus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'BLACK POMPFRET WHOLE',
        speciesScientificName: 'Parastromateus niger',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'MANGROVE RED SNAPPER WHOLE',
        speciesScientificName: 'Lutjanus argentimaculatus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'PINJALO SNAPPER WHOLE',
        speciesScientificName: 'Pinjalo pinjalo',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'ALMACO JACK WHOLE',
        speciesScientificName: 'Seriola rivoliana',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'WAVYLINED GROUPER FILLETS',
        speciesScientificName: 'Epinephelus undulosus',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'BARRAMUNDI FILLETS',
        speciesScientificName: 'Lates calcarifer',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    },
    {
        descriptionOfCommodity: 'BARRAMUNDI G&G',
        speciesScientificName: 'Lates calcarifer',
        natureOfCommodity: 'WILD ORIGIN',
        treatmentType: 'CHILLED(0-2 CENTRIGRADE)',
        approvalNo: 'DFAR/FPE/98/15',
        numberOfPackages: null,
        netWeight: null,
        harvestingDate: null,
        productionDate: null,
        bestBefore: null,
        lotNo: ''
    }
];

@Component({
    selector: 'app-il-certificate',
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
        TableModule,
        Select,
        RadioButton,
        TooltipModule,
        ConfirmPasswordDialogComponent,
        CertificateQrComponent
    ],
    providers: [MessageService],
    templateUrl: './il-certificate.component.html',
    styleUrls: ['./il-certificate.component.css', '../certificate-print.css']
})
export class IlCertificateComponent implements OnInit {
    form: FormGroup;
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isSaving = false;
    isCompany = false;
    isApproved = false;
    isSubmitted = false;

    // View mode: 'attachment' (6 pages / attachments) vs 'single' (4 pages)
    viewMode: 'attachment' | 'single' = 'attachment';

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

    readonly attachment1Limit = 12;

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
            viewMode: ['attachment'],
            // Page 1
            certificationNo: [''],
            centralCompetentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            centralCompetentAuthorityEmail: ['dgdfar@gmail.com'],
            localCompetentAuthority: ['NONE'],
            countryOfOrigin: ['SRI LANKA'],

            placeOfOriginName: [''],
            placeOfOriginAddress: [''],
            placeOfOriginApprovalNo: [''],

            consignorName: [''],
            consignorAddress: [''],
            postalCodeConsignor: [''],
            telNoConsignor: [''],
            emailConsignor: [''],

            consigneeName: [''],
            consigneeAddress: [''],
            postalCodeConsignee: [''],
            telNoConsignee: [''],
            emailConsignee: [''],

            placeOfLoading: ['COLOMBO – SRI LANKA'],
            portOfEntry: ['ISRAEL: TEL AVIV'],

            dateOfContainerization: [new Date()],
            dateOfDeparture: [new Date()],

            transportShip: [false],
            billOfLading: [''],
            transportAirplane: [false],
            awb: [''],
            transportLand: [false],
            transportOther: [false],

            containerNo1: [''],
            sealNo1: [''],
            containerNo2: [''],
            sealNo2: [''],
            containerNo3: [''],
            sealNo3: [''],
            containerNo4: [''],
            sealNo4: [''],

            commodities: this.fb.array([]),

            // Page 2
            readyToEat: [false],
            nonReadyToEat: [true],
            shipmentNumber: [''],
            remarks: [''],

            // Page 3 - Public and Animal Health Attestation Strikethrough Selections
            strikeSection1: [false],

            strikeSection2: [false],
            strike2_1: [false],
            strike2_2: [false],
            strike2_3: [false],
            strike2_4: [false],
            strike2_5: [false],
            strike2_6: [false],
            strike2_7: [false],

            strikeSection3: [true],
            strike3_1: [true],
            strike3_2: [true],
            strike3_3: [true],
            strike3_4: [true],

            // Page 4 - Signatory
            placeOfIssue: ['COLOMBO – SRI LANKA'],
            signatureDate: [new Date()],
            stamp: [''],
            signature: [''],
            signatoryUserId: [null, Validators.required],
            signatoryName: [''],
            qualification: ['']
        });

        this.form.get('viewMode')?.valueChanges.subscribe((mode: 'attachment' | 'single') => {
            this.viewMode = mode;
        });
    }

    get commodities(): FormArray {
        return this.form.get('commodities') as FormArray;
    }

    get commodityRows() {
        return (this.form.get('commodities') as FormArray).getRawValue() ?? [];
    }

    get attachment1Rows() {
        const rows = this.commodityRows;
        return rows.slice(0, this.attachment1Limit);
    }

    get attachment2Rows() {
        const rows = this.commodityRows;
        return rows.length > this.attachment1Limit ? rows.slice(this.attachment1Limit) : [];
    }

    get hasAttachment2(): boolean {
        return this.commodityRows.length > this.attachment1Limit;
    }

    getFillerRows(): number[] {
        const count = this.commodities.length;
        const desiredCount = 6;
        if (count < desiredCount) {
            return Array.from({ length: desiredCount - count }, (_, i) => i);
        }
        return [];
    }

    createCommodityRow(
        desc: string = '',
        species: string = '',
        nature: string = 'WILD SEA CAUGHT',
        treatment: string = 'CHILLED(0-2 CENTRIGRADE)',
        approval: string = 'DFAR/FPE/98/15',
        packages: number | null = null,
        weight: number | null = null,
        harvestDate: Date | string | null = null,
        prodDate: Date | string | null = null,
        bestBeforeDate: Date | string | null = null,
        lot: string = ''
    ): FormGroup {
        return this.fb.group({
            descriptionOfCommodity: [desc],
            speciesScientificName: [species],
            natureOfCommodity: [nature],
            treatmentType: [treatment],
            approvalNo: [approval],
            numberOfPackages: [packages],
            netWeight: [weight],
            harvestingDate: [harvestDate ? new Date(harvestDate) : null],
            productionDate: [prodDate ? new Date(prodDate) : null],
            bestBefore: [bestBeforeDate ? new Date(bestBeforeDate) : null],
            lotNo: [lot]
        });
    }

    addCommodity(
        desc: string = '',
        species: string = '',
        nature: string = 'WILD SEA CAUGHT',
        treatment: string = 'CHILLED(0-2 CENTRIGRADE)',
        approval: string = 'DFAR/FPE/98/15',
        packages: number | null = null,
        weight: number | null = null,
        harvestDate: Date | string | null = null,
        prodDate: Date | string | null = null,
        bestBeforeDate: Date | string | null = null,
        lot: string = ''
    ): void {
        this.commodities.push(
            this.createCommodityRow(desc, species, nature, treatment, approval, packages, weight, harvestDate, prodDate, bestBeforeDate, lot)
        );
    }

    loadDefaultAttachmentProducts(): void {
        this.commodities.clear();
        DEFAULT_ISRAEL_ATTACHMENT_PRODUCTS.forEach((p) => {
            this.addCommodity(
                p.descriptionOfCommodity,
                p.speciesScientificName,
                p.natureOfCommodity,
                p.treatmentType,
                p.approvalNo,
                p.numberOfPackages,
                p.netWeight,
                p.harvestingDate,
                p.productionDate,
                p.bestBefore,
                p.lotNo
            );
        });
        this.messageService.add({
            severity: 'info',
            summary: 'Template Loaded',
            detail: 'Loaded 20 default commodities for Israel Attachment.'
        });
    }

    loadDefaultSingleProducts(): void {
        this.commodities.clear();
        DEFAULT_ISRAEL_SINGLE_PRODUCTS.forEach((p) => {
            this.addCommodity(
                p.descriptionOfCommodity,
                p.speciesScientificName,
                p.natureOfCommodity,
                p.treatmentType,
                p.approvalNo,
                p.numberOfPackages,
                p.netWeight,
                p.harvestingDate,
                p.productionDate,
                p.bestBefore,
                p.lotNo
            );
        });
        this.messageService.add({
            severity: 'info',
            summary: 'Template Loaded',
            detail: 'Loaded standard commodities for Israel Single Certificate.'
        });
    }

    removeCommodity(index: number): void {
        if (this.commodities.length > 1) {
            this.commodities.removeAt(index);
        }
    }

    calculateTotalPackages(): number {
        return this.commodities.controls
            .map((control) => parseFloat(control.get('numberOfPackages')?.value) || 0)
            .reduce((acc, curr) => acc + curr, 0);
    }

    calculateTotalNetWeight(): number {
        return this.commodities.controls
            .map((control) => parseFloat(control.get('netWeight')?.value) || 0)
            .reduce((acc, curr) => acc + curr, 0);
    }

    getDesignationLine1(): string {
        const qual = this.form.get('qualification')?.value || '';
        const lines = qual.split('\n');
        return lines[0] || 'QUALITY CONTROL OFFICER (GRADE II)';
    }

    getDesignationLine2(): string {
        const qual = this.form.get('qualification')?.value || '';
        const lines = qual.split('\n');
        return lines.length > 1 ? lines.slice(1).join('\n') : 'B.Sc.(BIOLOGY), M.Sc.(FOOD SCI & TEC)(SRI LANKA).';
    }

    onToggleSection1(event?: any): void {
        const checked = event?.checked !== undefined ? event.checked : !this.form.get('strikeSection1')?.value;
        this.form.patchValue({ strikeSection1: checked });
    }

    onToggleSection2(event?: any): void {
        const checked = event?.checked !== undefined ? event.checked : !this.form.get('strikeSection2')?.value;
        this.form.patchValue({
            strikeSection2: checked,
            strike2_1: checked,
            strike2_2: checked,
            strike2_3: checked,
            strike2_4: checked,
            strike2_5: checked,
            strike2_6: checked,
            strike2_7: checked
        });
    }

    onToggleSection2Bullet(): void {
        const b1 = !!this.form.get('strike2_1')?.value;
        const b2 = !!this.form.get('strike2_2')?.value;
        const b3 = !!this.form.get('strike2_3')?.value;
        const b4 = !!this.form.get('strike2_4')?.value;
        const b5 = !!this.form.get('strike2_5')?.value;
        const b6 = !!this.form.get('strike2_6')?.value;
        const b7 = !!this.form.get('strike2_7')?.value;
        const allChecked = b1 && b2 && b3 && b4 && b5 && b6 && b7;
        this.form.patchValue({ strikeSection2: allChecked }, { emitEvent: false });
    }

    onToggleSection3(event?: any): void {
        const checked = event?.checked !== undefined ? event.checked : !this.form.get('strikeSection3')?.value;
        this.form.patchValue({
            strikeSection3: checked,
            strike3_1: checked,
            strike3_2: checked,
            strike3_3: checked,
            strike3_4: checked
        });
    }

    onToggleSubClause(): void {
        const s1 = !!this.form.get('strike3_1')?.value;
        const s2 = !!this.form.get('strike3_2')?.value;
        const s3 = !!this.form.get('strike3_3')?.value;
        const s4 = !!this.form.get('strike3_4')?.value;
        const allChecked = s1 && s2 && s3 && s4;
        this.form.patchValue(
            {
                strikeSection3: allChecked
            },
            { emitEvent: false }
        );
    }

    unstrikeAllAttestation(): void {
        this.form.patchValue({
            strikeSection1: false,
            strikeSection2: false,
            strike2_1: false,
            strike2_2: false,
            strike2_3: false,
            strike2_4: false,
            strike2_5: false,
            strike2_6: false,
            strike2_7: false,
            strikeSection3: false,
            strike3_1: false,
            strike3_2: false,
            strike3_3: false,
            strike3_4: false
        });
    }

    strikeAllAttestation(): void {
        this.form.patchValue({
            strikeSection1: true,
            strikeSection2: true,
            strike2_1: true,
            strike2_2: true,
            strike2_3: true,
            strike2_4: true,
            strike2_5: true,
            strike2_6: true,
            strike2_7: true,
            strikeSection3: true,
            strike3_1: true,
            strike3_2: true,
            strike3_3: true,
            strike3_4: true
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
                this.form.patchValue({ certificationNo: params['ref'] });
            }

            const requestId = params['requestId'];
            if (requestId) {
                this.certificateRequestId = Number(requestId);
                this.checkRequestApproval(this.certificateRequestId);
                this.loadSavedCertificateData(this.certificateRequestId);
            } else {
                this.loadDefaultAttachmentProducts();
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

    onViewModeChange(mode: 'attachment' | 'single') {
        this.viewMode = mode;
        this.form.get('viewMode')?.setValue(mode);
        if (mode === 'single' && this.commodities.length > 5) {
            this.loadDefaultSingleProducts();
        } else if (mode === 'attachment' && this.commodities.length <= 2) {
            this.loadDefaultAttachmentProducts();
        }
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateService.getIlCertificateByRequestId(requestId).subscribe({
            next: (data: IlCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }

                const mode = (data.certificateType as 'attachment' | 'single') || 'attachment';
                this.viewMode = mode;
                if (data.id || data.certificationNo || data.consignorName) {
                    this.isSubmitted = true;
                }

                this.form.patchValue({
                    viewMode: mode,
                    certificationNo: data.certificationNo || '',
                    centralCompetentAuthority: data.centralCompetentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    centralCompetentAuthorityEmail: data.centralCompetentAuthorityEmail || 'dgdfar@gmail.com',
                    localCompetentAuthority: data.localCompetentAuthority || 'NONE',
                    countryOfOrigin: data.countryOfOrigin || 'SRI LANKA',

                    placeOfOriginName: data.placeOfOriginName || '',
                    placeOfOriginAddress: data.placeOfOriginAddress || '',
                    placeOfOriginApprovalNo: data.placeOfOriginApprovalNo || 'DFAR/FPE/98/107',

                    consignorName: data.consignorName || '',
                    consignorAddress: data.consignorAddress || '',
                    postalCodeConsignor: data.postalCodeConsignor || '',
                    telNoConsignor: data.telNoConsignor || '',
                    emailConsignor: data.emailConsignor || '',

                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    postalCodeConsignee: data.postalCodeConsignee || '',
                    telNoConsignee: data.telNoConsignee || '',
                    emailConsignee: data.emailConsignee || '',

                    placeOfLoading: data.placeOfLoading || 'COLOMBO – SRI LANKA',
                    portOfEntry: data.portOfEntry || data.entryBIP || 'ISRAEL: TEL AVIV',

                    dateOfContainerization: data.dateOfContainerization ? new Date(data.dateOfContainerization) : new Date(),
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),

                    transportShip: data.transportSea ?? false,
                    billOfLading: data.billOfLading || '',
                    transportAirplane: data.transportAir ?? false,
                    awb: data.awb || '',
                    transportLand: data.transportRoad ?? false,
                    transportOther: data.transportOther ?? false,

                    containerNo1: data.containerNo || '',
                    sealNo1: data.sealNo || '',

                    readyToEat: data.readyToEat === 'Yes',
                    nonReadyToEat: data.nonReadyToEat !== 'No',
                    shipmentNumber: data.shipmentNumber || '',
                    remarks: data.remarks || '',

                    placeOfIssue: data.placeOfIssue || 'COLOMBO – SRI LANKA',
                    signatureDate: data.signatureDate ? new Date(data.signatureDate) : new Date(),
                    stamp: data.stamp || '',
                    signature: data.signature || '',
                    signatoryUserId: data.signatoryUserId || null,
                    signatoryName: data.signatoryName || '',
                    qualification: data.qualification || ''
                });

                this.updateSafeSignatureUrl();
                this.updateSafeStampUrl();

                this.commodities.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p: IlCertificateProductView) => {
                        this.addCommodity(
                            p.descriptionOfCommodity || '',
                            p.speciesScientificName || '',
                            p.natureOfCommodity || 'WILD ORIGIN',
                            p.treatmentType || 'CHILLED(0-2 CENTRIGRADE)',
                            p.approvalNo || 'DFAR/FPE/98/15',
                            p.numberOfPackages,
                            p.netWeight,
                            p.harvestingDate,
                            p.productionDate,
                            p.bestBefore,
                            p.lotNo || ''
                        );
                    });
                } else {
                    this.loadDefaultAttachmentProducts();
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

                const defaultRegNo = data.approvalNo || 'DFAR/FPE/98/107';
                const isShip = data.transportShip ?? false;
                const isPlane = data.transportAeroPlane ?? false;
                const certNo = data.healthCertNo || data.newHC || '';

                this.form.patchValue({
                    certificationNo: certNo,
                    centralCompetentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    centralCompetentAuthorityEmail: 'dgdfar@gmail.com',
                    localCompetentAuthority: 'NONE',
                    countryOfOrigin: data.countryOrigin || 'SRI LANKA',

                    placeOfOriginName: data.processingEstName || data.consignorName || '',
                    placeOfOriginAddress: data.processingEstAddress || `${data.consignorAddress || ''}, SRI LANKA`,
                    placeOfOriginApprovalNo: defaultRegNo,

                    consignorName: data.consignorName || '',
                    consignorAddress: `${data.consignorAddress || ''}, SRI LANKA`,
                    postalCodeConsignor: data.consignorPostal || '',
                    telNoConsignor: data.consignorTel || '',
                    emailConsignor: '',

                    consigneeName: data.consigneeName || '',
                    consigneeAddress: data.consigneeAddress || '',
                    postalCodeConsignee: data.consigneePostal || '',
                    telNoConsignee: data.consigneeTel || '',
                    emailConsignee: '',

                    placeOfLoading: data.placeOfLoading || 'COLOMBO – SRI LANKA',
                    portOfEntry: data.entryBIP || 'ISRAEL: TEL AVIV',

                    dateOfContainerization: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : new Date(),

                    transportShip: isShip,
                    billOfLading: '',
                    transportAirplane: isPlane,
                    awb: '',
                    transportLand: data.transportRoadVehicle ?? false,
                    transportOther: data.transportOther ?? false,

                    containerNo1: data.containerId || '',
                    sealNo1: data.sealNumber || '',

                    readyToEat: false,
                    nonReadyToEat: true,
                    shipmentNumber: '',
                    remarks: '',

                    strikeSection3: !data.productTypeAquaculture,
                    strike3_1: !data.productTypeAquaculture,
                    strike3_2: !data.productTypeAquaculture,
                    strike3_3: !data.productTypeAquaculture,
                    strike3_4: !data.productTypeAquaculture,

                    placeOfIssue: 'COLOMBO – SRI LANKA',
                    signatureDate: new Date()
                });

                this.commodities.clear();
                if (data.products && data.products.length > 0) {
                    data.products.forEach((p: VetProductFieldResponse) => {
                        const nature = data.productTypeAquaculture ? 'AQUACULTURE' : 'WILD ORIGIN';
                        const treat = data.temperatureFrozen
                            ? 'FROZEN (-20 CENTRIGRADE)'
                            : data.temperatureChilled
                            ? 'CHILLED(0-2 CENTRIGRADE)'
                            : 'CHILLED(0-2 CENTRIGRADE)';
                        const nw = p.netWeight ? parseFloat(p.netWeight) || null : null;
                        const pkgs = p.numPackages ? parseInt(p.numPackages, 10) || null : null;

                        this.addCommodity(
                            p.descCommon ? p.descCommon.toUpperCase() : '',
                            p.descScientific || '',
                            nature,
                            treat,
                            defaultRegNo,
                            pkgs,
                            nw,
                            data.harvestDate || null,
                            data.processingDate || null,
                            null,
                            ''
                        );
                    });
                } else {
                    this.loadDefaultAttachmentProducts();
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
            this.messageService.add({ severity: 'error', summary: 'Validation Error', detail: 'Please fill all required fields.' });
            return;
        }

        this.isSaving = true;
        const rawValue = this.form.getRawValue();

        const payload: CreateIlCertificatePayload = {
            certificateRequestId: this.certificateRequestId,
            certificateType: this.viewMode,
            certificationNo: rawValue.certificationNo,
            centralCompetentAuthority: rawValue.centralCompetentAuthority,
            centralCompetentAuthorityEmail: rawValue.centralCompetentAuthorityEmail,
            localCompetentAuthority: rawValue.localCompetentAuthority,
            countryOfOrigin: rawValue.countryOfOrigin,
            placeOfOriginName: rawValue.placeOfOriginName,
            placeOfOriginAddress: rawValue.placeOfOriginAddress,
            placeOfOriginApprovalNo: rawValue.placeOfOriginApprovalNo,
            consignorName: rawValue.consignorName,
            consignorAddress: rawValue.consignorAddress,
            postalCodeConsignor: rawValue.postalCodeConsignor,
            telNoConsignor: rawValue.telNoConsignor,
            emailConsignor: rawValue.emailConsignor,
            consigneeName: rawValue.consigneeName,
            consigneeAddress: rawValue.consigneeAddress,
            postalCodeConsignee: rawValue.postalCodeConsignee,
            telNoConsignee: rawValue.telNoConsignee,
            emailConsignee: rawValue.emailConsignee,
            placeOfLoading: rawValue.placeOfLoading,
            portOfEntry: rawValue.portOfEntry,
            dateOfArrival: null,
            placeOfArrival: rawValue.portOfEntry,
            placeOfArrivalAddress: '',
            placeOfDestinationName: rawValue.consigneeName,
            placeOfDestinationAddress: rawValue.consigneeAddress,
            placeOfDestinationApprovalNo: '',
            dateOfContainerization: rawValue.dateOfContainerization ? toLocalISOString(rawValue.dateOfContainerization) : null,
            dateOfDeparture: rawValue.dateOfDeparture ? toLocalISOString(rawValue.dateOfDeparture) : null,
            transportSea: rawValue.transportShip,
            transportAir: rawValue.transportAirplane,
            transportRail: false,
            transportRoad: rawValue.transportLand,
            transportOther: rawValue.transportOther,
            billOfLading: rawValue.billOfLading,
            awb: rawValue.awb,
            meansOfTransportIdentification: rawValue.billOfLading || rawValue.awb || '',
            containerNo: rawValue.containerNo1,
            sealNo: rawValue.sealNo1,
            meansOfTransportReference: rawValue.billOfLading || rawValue.awb || '',
            entryBIP: rawValue.portOfEntry,
            readyToEat: rawValue.readyToEat ? 'Yes' : 'No',
            nonReadyToEat: rawValue.nonReadyToEat ? 'Yes' : 'No',
            shipmentNumber: rawValue.shipmentNumber,
            remarks: rawValue.remarks,
            placeOfIssue: rawValue.placeOfIssue,
            signatoryName: rawValue.signatoryName,
            qualification: rawValue.qualification,
            signatureDate: rawValue.signatureDate ? toLocalISOString(rawValue.signatureDate) : null,
            stamp: rawValue.stamp,
            signature: rawValue.signature,
            signatoryUserId: rawValue.signatoryUserId,
            commodities: (rawValue.commodities || []).map((c: any) => ({
                descriptionOfCommodity: c.descriptionOfCommodity,
                speciesScientificName: c.speciesScientificName,
                natureOfCommodity: c.natureOfCommodity,
                treatmentType: c.treatmentType,
                approvalNo: c.approvalNo,
                numberOfPackages: c.numberOfPackages ? parseInt(c.numberOfPackages, 10) : null,
                netWeight: c.netWeight ? parseFloat(c.netWeight) : null,
                harvestingDate: c.harvestingDate ? toLocalISOString(c.harvestingDate) : null,
                productionDate: c.productionDate ? toLocalISOString(c.productionDate) : null,
                bestBefore: c.bestBefore ? toLocalISOString(c.bestBefore) : null,
                lotNo: c.lotNo
            }))
        };

        this.certificateService.submitIlCertificate(payload).subscribe({
            next: () => {
                this.isSaving = false;
                this.isSubmitted = true;
                this.messageService.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Israel Certificate saved successfully'
                });
            },
            error: () => {
                this.isSaving = false;
                this.messageService.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to save Israel Certificate'
                });
            }
        });
    }

    isPdf(dataUrl?: string | null): boolean {
        if (!dataUrl) return false;
        return dataUrl.startsWith('data:application/pdf') || dataUrl.toLowerCase().endsWith('.pdf');
    }

    updateSafeStampUrl() {
        const stamp = this.form.get('stamp')?.value;
        if (stamp && this.isPdf(stamp)) {
            this.safeStampPdfUrl = this.sanitizer.bypassSecurityTrustResourceUrl(stamp);
        } else {
            this.safeStampPdfUrl = null;
        }
    }

    updateSafeSignatureUrl() {
        const sig = this.form.get('signature')?.value;
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
                this.form.patchValue({ stamp: result });
                this.updateSafeStampUrl();
            } else {
                this.form.patchValue({ signature: result });
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
        this.form.patchValue({ stamp: '' });
        this.safeStampPdfUrl = null;
    }

    removeSignature(event?: Event) {
        if (event) event.stopPropagation();
        this.form.patchValue({ signature: '' });
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

        const ref = this.form.get('certificationNo')?.value || 'Draft';
        let pdfName = '';
        if (this.viewMode === 'attachment') {
            pdfName = `${ref}_Israel_Veterinary_Health_Certificate_Attachment.pdf`;
        } else {
            pdfName = `${ref}_Israel_Veterinary_Health_Certificate.pdf`;
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
