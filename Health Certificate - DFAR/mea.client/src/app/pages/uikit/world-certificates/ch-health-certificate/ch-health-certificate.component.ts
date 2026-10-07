import { ReplacementBannerComponent } from '@/shared/components/replacement-banner/replacement-banner.component';
import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormBuilder, FormGroup, FormArray, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { TextareaModule } from 'primeng/textarea';
import { RadioButtonModule } from 'primeng/radiobutton';
import { CheckboxModule } from 'primeng/checkbox';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { CertificateRequestService } from 'src/app/pages/service/certificate-request.service';
import { TooltipModule } from 'primeng/tooltip';
import { toLocalISOString } from '@/shared/utils/date-utils';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';

import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';

@Component({
    selector: 'app-ch-health-certificate',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, InputTextModule, ButtonModule, DatePicker, ToastModule, TextareaModule, RadioButtonModule, CheckboxModule, TooltipModule, CertificateQrComponent, ReplacementBannerComponent],
    providers: [MessageService],
    templateUrl: './ch-health-certificate.component.html',
    styleUrls: ['./ch-health-certificate.component.css', '../certificate-print.css']
})
export class ChHealthCertificateComponent implements OnInit {
    cancelsAndReplacesRef: string | null = null;
    cancelsAndReplacesDate: string | Date | null = null;
    form: FormGroup;
    refNumber: string = '';
    certificateRequestId: number | null = null;
    viewOnly = false;
    isEmbedded = false;
    isCompany = false;

    get isAdmin(): boolean {
        return (this.authService.getUserRole() || '').toLowerCase() === 'admin';
    }
    isApproved = false;

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
        private location: Location,
        private authService: AuthService,
        private certificateService: CertificateRequestService,
        private sanitizer: DomSanitizer
    ) {
        this.form = this.fb.group({
            countryOfExport: [''],
            countryOfProduction: [''],
            competentAuthority: [''],
            departmentOfIssuance: [''],
            commodityName: [''],
            scientificName: [''],
            numberOfPackages: [''],
            netWeight: [''],
            productionDate: [null],
            lotNoOfProducts: [''],
            countryOfOriginRawMaterials: [''],
            processingType: [''],
            productionMode: [''],
            aquacultured: [''],
            wildCaught: [''],
            characterOfProductiveWaterArea: [''],
            aquacultureArea: [''],
            catchArea: [''],
            aquacultureFarmDetails: [''],
            fishingVesselDetails: [''],
            fishingFactoryVesselDetails: [''],
            transportFishingVesselDetails: [''],
            processingPlantDetails: [''],
            coldStorageRawMaterialsDetails: [''],
            coldStorageProductsDetails: [''],
            consignorName: [''],
            consignorAddress: [''],
            consigneeName: [''],
            consigneeAddress: [''],
            nameAddressOfConsignee: [''],
            placeOfDispatchProduction: [''],
            placeOfDestination: [''],
            transportAeroPlane: [''],
            transportShip: [''],
            transportRailwayWagon: [''],
            transportRoadVehicle: [''],
            transportOther: [''],
            identificationDocumentReferences: [''],
            nameOfVessel: [''],
            flightNumber: [''],
            otherTransportMeans: [''],
            containerNumber: [''],
            sealNumber: [''],
            dateOfDeparture: [null],
            placeOfIssue: [''],
            dateOfIssue: [new Date()],
            officialStamp: [''],
            officialSignature: [''],
            dateOfAttachment: [new Date()],
            dfarFpe: [''],
            attachments: this.fb.array([this.createAttachmentRow()])
        });
    }

    ngOnInit(): void {
        this.isCompany = (this.authService.getUserRole() || '').toLowerCase() === 'company';
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
            }
            if (params['requestId']) {
                this.certificateRequestId = Number(params['requestId']);
                this.checkRequestApproval(this.certificateRequestId);
                this.loadVetFormData(this.certificateRequestId);
            }
            if (this.viewOnly) {
                this.form.disable();
            } else {
                this.form.enable();
            }
        });
    }

    private loadVetFormData(requestId: number): void {
        this.certificateService.getVetFormByRequestId(requestId).subscribe({
            next: (data) => {
                if (!data) return;

                const dummyValues = ['Draft', 'ffff', 'FFFF', 'TC 4471', 'TC 4791', 'SX 2008', 'BR 8812', 'Ref.CH/20260217'];
                let certNo = this.refNumber ||
                    (data.referenceNumber?.startsWith('HC-') || data.referenceNumber?.startsWith('*') ? data.referenceNumber : '') ||
                    (data.healthCertNo && !dummyValues.includes(data.healthCertNo.trim()) ? data.healthCertNo : '') ||
                    (data.newHC && !dummyValues.includes(data.newHC.trim()) ? data.newHC : '') ||
                    this.refNumber || '';
                if (!certNo) certNo = this.refNumber || '';
                this.refNumber = certNo;

                const defaultRegNo = data.approvalNo || 'DFAR/FPE/98/62';
                const plantDetails = `${data.processingEstName || data.consignorName || ''}\n${data.processingEstAddress || data.consignorAddress || ''}\n${defaultRegNo}`.trim();
                const isShip = data.transportShip ?? false;

                const firstProduct = Array.isArray(data.products) && data.products.length > 0 ? data.products[0] : null;
                const common = firstProduct?.descCommon || data.descCommon || '';
                const scientific = firstProduct?.descScientific || data.descScientific || '';
                const totalPackages = firstProduct?.numPackages || data.numPackages || '';
                const totalNetWeight = firstProduct?.netWeight || data.netWeight || data.quantity || '';

                this.form.patchValue({
                    countryOfExport: data.countryOrigin || 'SRI LANKA',
                    countryOfProduction: data.countryOrigin || 'SRI LANKA',
                    countryOfOriginRawMaterials: data.countryOrigin || 'SRI LANKA',
                    competentAuthority: 'DEPARTMENT OF FISHERIES AND AQUATIC RESOURCES',
                    departmentOfIssuance: 'FISH INSPECTION AND QUALITY CONTROL DIVISION',
                    commodityName: common,
                    scientificName: scientific,
                    numberOfPackages: totalPackages,
                    netWeight: totalNetWeight,
                    productionDate: data.processingDate ? new Date(data.processingDate) : null,
                    lotNoOfProducts: data.containerId || '',
                    processingType: data.temperatureFrozen ? 'FROZEN' : (data.temperatureChilled ? 'CHILLED' : 'FRESH'),
                    productionMode: data.processingType || 'WHOLE ROUND / FILLETS',
                    aquacultured: data.productTypeAquaculture ? 'true' : 'false',
                    wildCaught: data.productTypeWildCaught ? 'true' : 'false',
                    characterOfProductiveWaterArea: 'Sea water',
                    catchArea: data.regionOriginISO || 'FAO 57 (Indian Ocean)',
                    processingPlantDetails: plantDetails,
                    consignorName: data.consignorName || '',
                    consignorAddress: `${data.consignorAddress || ''} ${data.consignorPostal || ''}`.trim(),
                    consigneeName: data.consigneeName || '',
                    consigneeAddress: `${data.consigneeAddress || ''} ${data.consigneePostal || ''}`.trim(),
                    nameAddressOfConsignee: `${data.consigneeName || ''}\n${data.consigneeAddress || ''}`.trim(),
                    placeOfDispatchProduction: data.placeOfLoading || 'COLOMBO, SRI LANKA',
                    placeOfDestination: data.countryDestinationISO || 'CHINA',
                    transportAeroPlane: !isShip,
                    transportShip: isShip,
                    identificationDocumentReferences: data.docReferences || '',
                    flightNumber: data.transportId || '',
                    containerNumber: data.containerId || '',
                    sealNumber: data.containerId || '',
                    dateOfDeparture: data.dateOfDeparture ? new Date(data.dateOfDeparture) : null,
                    placeOfIssue: 'COLOMBO, SRI LANKA',
                    dateOfIssue: new Date(),
                    dfarFpe: defaultRegNo
                });

                if (Array.isArray(data.products) && data.products.length > 0) {
                    this.attachments.clear();
                    data.products.forEach((p: any) => {
                        const name = p.descCommon ? (p.descScientific ? `${p.descCommon} (${p.descScientific})` : p.descCommon) : (p.descScientific || '');
                        this.attachments.push(
                            this.fb.group({
                                product: [name],
                                netWeight: [Number(p.netWeight) || Number(p.quantity) || 0],
                                numberOfBoxes: [Number(p.numPackages) || 0]
                            })
                        );
                    });
                }

                if (this.viewOnly) {
                    this.form.disable();
                }
            },
            error: () => {}
        });
    }

    get attachments(): FormArray {
        return this.form.get('attachments') as FormArray;
    }

    createAttachmentRow(): FormGroup {
        return this.fb.group({
            product: [''],
            netWeight: [0],
            numberOfBoxes: [0]
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

    calculateTotalNetWeight(): number {
        return this.attachments.controls.map((control) => control.get('netWeight')?.value || 0).reduce((acc, curr) => acc + curr, 0);
    }

    calculateTotalNumberOfBoxes(): number {
        return this.attachments.controls.map((control) => control.get('numberOfBoxes')?.value || 0).reduce((acc, curr) => acc + curr, 0);
    }

    onSubmit(): void {
        if (this.form.valid) {
            const rawValue = this.form.getRawValue();
            const payload = {
                ...rawValue,
                productionDate: toLocalISOString(rawValue.productionDate),
                dateOfDeparture: toLocalISOString(rawValue.dateOfDeparture),
                dateOfIssue: toLocalISOString(rawValue.dateOfIssue),
                dateOfAttachment: toLocalISOString(rawValue.dateOfAttachment)
            };
            console.log('Form submitted:', payload);
            this.messageService.add({ severity: 'success', summary: 'Success', detail: 'China Health Certificate Form Submitted' });
            setTimeout(() => this.goBack(), 1500);
        } else {
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Please fill all required fields' });
        }
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
                    }
                }
            },
            error: () => {}
        });
    }

    print(): void {
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

    goBack(): void {
        const userRole = (this.authService.getUserRole() || '').toLowerCase();
        const targetUrl = userRole === 'admin' ? '/uikit/admin/certificate-requests' : '/uikit/company-request';
        if (typeof window !== 'undefined' && window.self !== window.top && window.top) {
            window.top.location.href = targetUrl;
        } else {
            this.router.navigate([targetUrl]);
        }
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
}
