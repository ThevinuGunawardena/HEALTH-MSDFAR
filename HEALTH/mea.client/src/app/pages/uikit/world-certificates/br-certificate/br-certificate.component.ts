import { Component, OnInit } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, FormArray, Validators } from '@angular/forms';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { DatePicker } from 'primeng/datepicker';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '@/pages/service/auth.service';
import { HttpClient } from '@angular/common/http';
import { Checkbox } from 'primeng/checkbox';
import { Select } from 'primeng/select';
import { ConfirmPasswordDialogComponent } from '@/shared/components/confirm-password-dialog/confirm-password-dialog.component';
import { UserService, User } from '@/pages/service/user.service';
import { BrCertificateView, CertificateRequestService, VetFormFieldResponse } from 'src/app/pages/service/certificate-request.service';
import { TooltipModule } from 'primeng/tooltip';
import { TableModule } from 'primeng/table';
import { toLocalISOString } from '@/shared/utils/date-utils';
import { CertificateQrComponent } from '@/shared/components/certificate-qr/certificate-qr.component';

@Component({
    selector: 'app-br-certificate',
    standalone: true,
    imports: [CommonModule, FormsModule, ReactiveFormsModule, ButtonModule, InputTextModule, TextareaModule, DatePicker, ToastModule, Checkbox, TableModule, Select, TooltipModule, ConfirmPasswordDialogComponent, CertificateQrComponent],
    providers: [MessageService],
    templateUrl: './br-certificate.component.html',
    styleUrls: ['./br-certificate.component.css', '../certificate-print.css']
})
export class BrCertificateComponent implements OnInit {
    form: FormGroup;
    refNumber: string = 'BR 8812';
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
        private http: HttpClient,
        private certificateRequestService: CertificateRequestService,
        private userService: UserService,
        private location: Location,
        private authService: AuthService
    ) {
        this.form = this.fb.group({
            countryOfExport: ['SRI LANKA'],
            certificateNo: ['BR 8812'],
            competentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            localCompetentAuthority: ['DEPARTMENT OF FISHERIES & AQUATIC RESOURCES'],
            exporterName: [''],
            exporterAddress: [''],
            importerName: [''],
            importerAddress: [''],
            countryOrigin: ['SRI LANKA'],
            countryOriginISO: ['LK'],
            countryOfDestination: ['BRAZIL'],
            countryDestinationISO: ['BR'],
            placeOfLoading: ['COLOMBO PORT / AIRPORT'],
            transportAeroPlane: [false],
            transportShip: [true],
            transportRailwayWagon: [false],
            transportRoadVehicle: [false],
            transportOther: [false],
            declaredPointOfEntry: ['SANTOS PORT (BRSSZ)'],
            conditionsForTransportStorage: ['FROZEN (-18°C)'],
            identificationOfContainers: [''],
            identificationOfFoodProducts: [''],
            producerDetails: [''],
            hsCode: ['0303.42'],
            intendedPurpose: ['HUMAN CONSUMPTION'],
            products: this.fb.array([this.createProductRow()]),
            totalNetWeight: [0],
            placeAndDate: ['COLOMBO, SRI LANKA'],
            dateOfIssue: [new Date()],
            officialStamp: [''],
            signatoryName: [''],
            qualification: ['AUTHORIZED FISH INSPECTION OFFICER'],
            modeloConformeCircularNo: ['CIRCULAR NO. 442/2026'],
            sanitaryCertification: [''],
            signatoryUserId: [null, Validators.required]
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
                this.refNumber = params['ref'];
                this.form.patchValue({ certificateNo: params['ref'] });
            }
            if (params['requestId']) {
                this.certificateRequestId = Number(params['requestId']);
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

        this.form.get('certificateNo')?.valueChanges.subscribe((val) => {
            if (val) {
                this.refNumber = val;
            }
        });
    }

    loadSampleData(): void {
        this.refNumber = 'BR 8812';
        this.form.patchValue({
            countryOfExport: 'SRI LANKA',
            certificateNo: 'BR 8812',
            competentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            localCompetentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            exporterName: 'GLOBAL SEAFOODS (PVT) LTD',
            exporterAddress: 'NO. 45, HARBOUR ROAD, COLOMBO 15, SRI LANKA',
            importerName: 'BRASIL SEAFOOD IMPORTADORA LTDA',
            importerAddress: 'AVENIDA PAULISTA 1000, SAO PAULO, SP, BRAZIL',
            countryOrigin: 'SRI LANKA',
            countryOriginISO: 'LK',
            countryOfDestination: 'BRAZIL',
            countryDestinationISO: 'BR',
            placeOfLoading: 'COLOMBO PORT / AIRPORT',
            transportAeroPlane: false,
            transportShip: true,
            transportRailwayWagon: false,
            transportRoadVehicle: false,
            transportOther: false,
            declaredPointOfEntry: 'SANTOS PORT (BRSSZ)',
            conditionsForTransportStorage: 'FROZEN (-18°C)',
            identificationOfContainers: 'BR-CONT-4411',
            identificationOfFoodProducts: 'Frozen Yellowfin Tuna Loins & Swordfish Steaks',
            producerDetails: 'GLOBAL SEAFOODS (PVT) LTD PROCESSING FACILITY, MUTWAL, COLOMBO 15\nAPPROVAL NO: DFAR/FQC/PP/042',
            hsCode: '0303.42',
            intendedPurpose: 'HUMAN CONSUMPTION',
            placeAndDate: 'COLOMBO, SRI LANKA',
            dateOfIssue: new Date(),
            qualification: 'AUTHORIZED FISH INSPECTION OFFICER',
            modeloConformeCircularNo: 'CIRCULAR NO. 442/2026',
            sanitaryCertification: ''
        });

        const products = this.form.get('products') as FormArray;
        products.clear();
        products.push(
            this.fb.group({
                nameOfTheProduct: ['Frozen Yellowfin Tuna Loins'],
                scientificName: ['Thunnus albacares'],
                typeOfPackaging: ['Vacuum Pack Cartons'],
                numberOfPackages: [60],
                netWeight: [1200]
            })
        );
        products.push(
            this.fb.group({
                nameOfTheProduct: ['Frozen Swordfish Steaks'],
                scientificName: ['Xiphias gladius'],
                typeOfPackaging: ['Vacuum Pack Cartons'],
                numberOfPackages: [50],
                netWeight: [1000]
            })
        );

        this.form.patchValue({
            totalNetWeight: 2200
        });

        this.messageService.add({
            severity: 'info',
            summary: 'Sample Loaded',
            detail: 'Loaded standard Brazil certificate sample (BR 8812).'
        });
    }

    private loadSavedCertificateData(requestId: number) {
        this.certificateRequestService.getBrCertificateByRequestId(requestId).subscribe({
            next: (data: BrCertificateView) => {
                if (!data) {
                    this.loadVetFormData(requestId);
                    return;
                }
                const certNo = data.certificateNo || this.refNumber || 'BR 8812';
                this.refNumber = certNo;
                this.form.patchValue({
                    countryOfExport: data.countryOfExport || 'SRI LANKA',
                    certificateNo: certNo,
                    competentAuthority: data.competentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    localCompetentAuthority: data.localCompetentAuthority || 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
                    exporterName: data.exporterName,
                    exporterAddress: data.exporterAddress,
                    importerName: data.importerName,
                    importerAddress: data.importerAddress,
                    countryOrigin: data.countryOrigin || 'SRI LANKA',
                    countryOriginISO: data.countryOriginISO || 'LK',
                    countryOfDestination: data.countryOfDestination || 'BRAZIL',
                    countryDestinationISO: data.countryDestinationISO || 'BR',
                    placeOfLoading: data.placeOfLoading || 'COLOMBO PORT / AIRPORT',
                    transportAeroPlane: data.transportAeroPlane,
                    transportShip: data.transportShip,
                    transportRailwayWagon: data.transportRailwayWagon,
                    transportRoadVehicle: data.transportRoadVehicle,
                    transportOther: data.transportOther,
                    declaredPointOfEntry: data.declaredPointOfEntry || 'SANTOS PORT (BRSSZ)',
                    conditionsForTransportStorage: data.conditionsForTransportStorage || 'FROZEN (-18°C)',
                    identificationOfContainers: data.identificationOfContainers,
                    identificationOfFoodProducts: data.identificationOfFoodProducts,
                    producerDetails: data.producerDetails,
                    hsCode: data.hsCode || '0303.42',
                    intendedPurpose: data.intendedPurpose || 'HUMAN CONSUMPTION',
                    totalNetWeight: data.totalNetWeight,
                    placeAndDate: data.placeAndDate || 'COLOMBO, SRI LANKA',
                    dateOfIssue: data.dateOfIssue ? new Date(data.dateOfIssue) : new Date(),
                    officialStamp: data.officialStamp,
                    signatoryUserId: data.signatoryUserId,
                    signatoryName: data.signatoryName,
                    qualification: data.qualification || 'AUTHORIZED FISH INSPECTION OFFICER',
                    modeloConformeCircularNo: data.modeloConformeCircularNo || 'CIRCULAR NO. 442/2026',
                    sanitaryCertification: data.sanitaryCertification
                });
                if (data.products && data.products.length > 0) {
                    const arr = this.form.get('products') as FormArray;
                    arr.clear();
                    data.products.forEach((p) => {
                        arr.push(this.fb.group({ nameOfTheProduct: [p.nameOfTheProduct], scientificName: [p.scientificName], typeOfPackaging: [p.typeOfPackaging], numberOfPackages: [p.numberOfPackages], netWeight: [p.netWeight] }));
                    });
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
                console.log('Vet form data loaded:', data);
                this.autofillForm(data);
                if (this.viewOnly) {
                    this.form.disable();
                } else {
                    this.form.enable();
                }
            },
            error: (err) => {
                console.error('Failed to load vet form data for autofill', err);
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
                      packagingType: data.packagingType,
                      numPackages: data.numPackages,
                      netWeight: data.netWeight
                  }
              ];

        const products = this.form.get('products') as FormArray;
        products.clear();

        vetProducts.forEach((product) => {
            products.push(
                this.fb.group({
                    nameOfTheProduct: [product?.descCommon || ''],
                    scientificName: [product?.descScientific || ''],
                    typeOfPackaging: [product?.packagingType || ''],
                    numberOfPackages: [Number(product?.numPackages || 0)],
                    netWeight: [Number(product?.netWeight || 0)]
                })
            );
        });

        const totalNetWeight = products.controls.map((control) => Number(control.get('netWeight')?.value || 0)).reduce((sum, value) => sum + value, 0);
        const firstProduct = vetProducts[0];
        const certNo = this.refNumber || this.form.get('certificateNo')?.value || 'BR 8812';
        this.refNumber = certNo;

        this.form.patchValue({
            certificateNo: certNo,
            countryOfExport: 'SRI LANKA',
            competentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            localCompetentAuthority: 'DEPARTMENT OF FISHERIES & AQUATIC RESOURCES',
            exporterName: data.consignorName,
            exporterAddress: data.consignorAddress,
            importerName: data.consigneeName,
            importerAddress: data.consigneeAddress,
            countryOrigin: data.countryOrigin || 'SRI LANKA',
            countryOriginISO: data.countryOriginISO || 'LK',
            countryOfDestination: 'BRAZIL',
            countryDestinationISO: data.countryDestinationISO || 'BR',
            placeOfLoading: data.placeOfLoading || 'COLOMBO PORT / AIRPORT',
            transportAeroPlane: data.transportAeroPlane,
            transportShip: data.transportShip || true,
            transportRailwayWagon: data.transportRailwayWagon,
            transportRoadVehicle: data.transportRoadVehicle,
            transportOther: data.transportOther,
            declaredPointOfEntry: data.entryBIP || 'SANTOS PORT (BRSSZ)',
            conditionsForTransportStorage: 'FROZEN (-18°C)',
            hsCode: data.hsCode || '0303.42',
            intendedPurpose: 'HUMAN CONSUMPTION',
            totalNetWeight,
            identificationOfFoodProducts: firstProduct?.descCommon || data.descCommon,
            identificationOfContainers: [data.transportId, data.containerId].filter((x) => !!x).join(', '),
            producerDetails: [data.processingEstName, data.processingEstAddress, data.approvalNo ? `Approval No: ${data.approvalNo}` : null].filter((x) => !!x).join('\n'),
            placeAndDate: 'COLOMBO, SRI LANKA',
            dateOfIssue: new Date(),
            modeloConformeCircularNo: 'CIRCULAR NO. 442/2026',
            qualification: 'AUTHORIZED FISH INSPECTION OFFICER'
        });
    }

    createProductRow(): FormGroup {
        return this.fb.group({
            nameOfTheProduct: [''],
            scientificName: [''],
            typeOfPackaging: [''],
            numberOfPackages: [''],
            netWeight: [0]
        });
    }

    get products(): FormArray {
        return this.form.get('products') as FormArray;
    }

    get productRows() {
        return (this.form.get('products') as FormArray).getRawValue() ?? [];
    }

    addProduct() {
        this.products.push(this.createProductRow());
    }

    removeProduct(index: number) {
        if (this.products.length > 1) {
            this.products.removeAt(index);
        }
    }

    calculateTotalNetWeight(): number {
        return this.products.controls.map((control) => control.get('netWeight')?.value || 0).reduce((acc, curr) => acc + curr, 0);
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

        if (this.form.valid) {
            this.isSaving = true;
            const rawValue = this.form.getRawValue();

            const payload: any = {
                certificateRequestId: this.certificateRequestId,
                refNumber: rawValue.certificateNo || this.refNumber || 'BR 8812',
                countryOfExport: rawValue.countryOfExport || 'SRI LANKA',
                certificateNo: rawValue.certificateNo || this.refNumber || 'BR 8812',
                competentAuthority: rawValue.competentAuthority || '',
                localCompetentAuthority: rawValue.localCompetentAuthority || '',
                exporterName: rawValue.exporterName || '',
                exporterAddress: rawValue.exporterAddress || '',
                importerName: rawValue.importerName || '',
                importerAddress: rawValue.importerAddress || '',
                countryOrigin: rawValue.countryOrigin || '',
                countryOriginISO: rawValue.countryOriginISO || '',
                countryOfDestination: rawValue.countryOfDestination || '',
                countryDestinationISO: rawValue.countryDestinationISO || '',
                placeOfLoading: rawValue.placeOfLoading || '',
                transportAeroPlane: rawValue.transportAeroPlane || false,
                transportShip: rawValue.transportShip || false,
                transportRailwayWagon: rawValue.transportRailwayWagon || false,
                transportRoadVehicle: rawValue.transportRoadVehicle || false,
                transportOther: rawValue.transportOther || false,
                declaredPointOfEntry: rawValue.declaredPointOfEntry || '',
                conditionsForTransportStorage: rawValue.conditionsForTransportStorage || '',
                identificationOfContainers: rawValue.identificationOfContainers || '',
                identificationOfFoodProducts: rawValue.identificationOfFoodProducts || '',
                producerDetails: rawValue.producerDetails || '',
                hsCode: rawValue.hsCode || '',
                intendedPurpose: rawValue.intendedPurpose || '',
                totalNetWeight: Number(rawValue.totalNetWeight || 0),
                placeAndDate: rawValue.placeAndDate || '',
                dateOfIssue: toLocalISOString(rawValue.dateOfIssue) || toLocalISOString(new Date()),
                officialStamp: rawValue.officialStamp || '',
                signatoryUserId: rawValue.signatoryUserId || null,
                signatoryName: rawValue.signatoryName || '',
                qualification: rawValue.qualification || '',
                modeloConformeCircularNo: rawValue.modeloConformeCircularNo || '',
                sanitaryCertification: rawValue.sanitaryCertification || '',
                products: (rawValue.products || []).map((p: any) => ({
                    nameOfTheProduct: p.nameOfTheProduct || '',
                    scientificName: p.scientificName || '',
                    typeOfPackaging: p.typeOfPackaging || '',
                    numberOfPackages: Number(p.numberOfPackages || 0),
                    netWeight: Number(p.netWeight || 0)
                }))
            };

            this.certificateRequestService.submitBrCertificate(payload).subscribe({
                next: () => {
                    this.isSaving = false;
                    this.messageService.add({ severity: 'success', summary: 'Success', detail: 'Brazil Certificate Form Submitted' });
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
                error: (err) => {
                    this.isSaving = false;
                    console.error('Submission failed', err);
                    this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Submission failed. Please try again.' });
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
                qualification: user.qualification || 'AUTHORIZED FISH INSPECTION OFFICER'
            });
        } else {
            this.form.patchValue({
                signatoryName: '',
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
            this.form.patchValue({
                signatoryName: '',
                qualification: ''
            });
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

    print() {
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
}

