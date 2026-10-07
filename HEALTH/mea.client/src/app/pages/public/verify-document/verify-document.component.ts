import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { ButtonModule } from 'primeng/button';
import { environment } from 'src/environments/environment';

export interface PublicVerificationResponse {
    isVerified: boolean;
    status: string;
    certificateReference?: string;
    requestId?: number;
    issueDate?: string;
    consignorName?: string;
    consignorAddress?: string;
    consigneeName?: string;
    consigneeAddress?: string;
    itemName?: string;
    countryOfDestination?: string;
    officerName?: string;
    designation?: string;
    qualification?: string;
    competentAuthority?: string;
    createdAt?: string;
    message?: string;
}

@Component({
    selector: 'app-verify-document',
    standalone: true,
    imports: [CommonModule, RouterModule, ButtonModule],
    template: `
        <div class="verify-page min-h-screen bg-slate-100 flex flex-col items-center justify-center p-3 sm:p-6 font-sans">
            <!-- Container Card -->
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden my-4">
                <!-- Top Official Banner -->
                <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 text-center relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                    <img src="/demo/images/srilanka-emblem.png" alt="Emblem of Sri Lanka" class="h-16 mx-auto mb-3 drop-shadow" (error)="onImageError($event)" />
                    <div class="text-xs font-semibold tracking-wider text-emerald-300 uppercase">Democratic Socialist Republic of Sri Lanka</div>
                    <h1 class="text-lg sm:text-xl font-bold tracking-wide mt-1">DEPARTMENT OF FISHERIES &amp; AQUATIC RESOURCES</h1>
                    <div class="text-xs text-slate-300 mt-1">Document Authenticity &amp; Database Verification System</div>
                </div>

                <!-- Loading State -->
                <div class="p-12 text-center" *ngIf="isLoading">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 mb-3 animate-spin">
                        <i class="pi pi-spinner text-2xl"></i>
                    </div>
                    <div class="text-sm font-semibold text-slate-700">Verifying document against official DFAR registry database...</div>
                    <div class="text-xs text-slate-400 mt-1">Checking record status, certificate reference, and digital registration</div>
                </div>

                <!-- Verified State -->
                <ng-container *ngIf="!isLoading && verificationData?.isVerified">
                    <div class="p-6 text-center border-b border-slate-100 bg-emerald-50/70">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-3 shadow-xs">
                            <i class="pi pi-check-circle text-3xl font-bold"></i>
                        </div>
                        <div>
                            <span class="inline-block px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider mb-2 shadow-xs">
                                ✓ DATABASE VERIFIED &amp; AUTHENTIC
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                            CERTIFICATE VERIFIED (ORIGINAL DOCUMENT)
                        </h2>
                        <p class="text-xs sm:text-sm text-emerald-800 max-w-lg mx-auto mt-2 leading-relaxed font-medium">
                            This document has been verified against the official Department of Fisheries &amp; Aquatic Resources database as an authentic, confirmed original health certificate.
                        </p>
                    </div>

                    <!-- Certificate Details Grid -->
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-xs border-b border-slate-200 pb-1.5">
                            <span class="font-bold uppercase tracking-wider text-slate-500">Official Database Record Details</span>
                            <span class="font-semibold text-emerald-800 bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded text-3xs uppercase">
                                Status: {{ verificationData?.status || 'Confirmed' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80">
                                <span class="text-slate-500 font-medium block">Certificate Reference No:</span>
                                <span class="text-sm font-bold text-slate-900 font-mono mt-0.5 block">
                                    {{ verificationData?.certificateReference || urlRef || 'OFFICIAL-RECORD' }}
                                </span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80">
                                <span class="text-slate-500 font-medium block">Date of Issue:</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block">
                                    {{ verificationData?.issueDate || urlDate || currentDate }}
                                </span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80 sm:col-span-2">
                                <span class="text-slate-500 font-medium block">Product / Item Description:</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block uppercase">
                                    {{ verificationData?.itemName || urlItem || 'SEE ATTACHED SPECIFICATION' }}
                                </span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80">
                                <span class="text-slate-500 font-medium block">Destination Country:</span>
                                <span class="text-xs font-bold text-slate-900 mt-0.5 block uppercase">
                                    {{ verificationData?.countryOfDestination || urlDestination || 'INTERNATIONAL DESTINATION' }}
                                </span>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80">
                                <span class="text-slate-500 font-medium block">Authorized Signatory Inspector:</span>
                                <span class="text-xs font-bold text-slate-900 mt-0.5 block uppercase">
                                    {{ verificationData?.officerName || urlOfficer || 'OFFICIAL INSPECTION OFFICER' }}
                                </span>
                                <span class="text-3xs text-slate-500 mt-0.5 block" *ngIf="verificationData?.designation || verificationData?.qualification">
                                    {{ verificationData?.designation }} <span *ngIf="verificationData?.qualification">({{ verificationData?.qualification }})</span>
                                </span>
                            </div>
                        </div>

                        <!-- Security & Legal Notice -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950 text-2xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-xs text-emerald-900">
                                <i class="pi pi-shield"></i> Official DFAR Security Verification
                            </div>
                            <p>
                                This digital certificate record was validated against the official Document Management and E-Signature Management System of the Department of Fisheries &amp; Aquatic Resources (DFAR), Colombo, Sri Lanka.
                            </p>
                        </div>
                    </div>
                </ng-container>

                <!-- Unverified State -->
                <ng-container *ngIf="!isLoading && !verificationData?.isVerified">
                    <div class="p-6 text-center border-b border-slate-100 bg-red-50/70">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-3 shadow-xs">
                            <i class="pi pi-times-circle text-3xl font-bold"></i>
                        </div>
                        <div>
                            <span class="inline-block px-3 py-1 rounded-full bg-red-600 text-white text-xs font-bold uppercase tracking-wider mb-2 shadow-xs">
                                ✗ UNVERIFIED DOCUMENT
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                            DOCUMENT NOT VERIFIED
                        </h2>
                        <p class="text-xs sm:text-sm text-red-700 max-w-lg mx-auto mt-2 leading-relaxed font-medium">
                            No authentic certificate record was found in the official DFAR registry matching this reference. This document could not be validated against official records.
                        </p>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg text-amber-900 space-y-2">
                            <div class="font-bold flex items-center gap-2 text-sm text-amber-950">
                                <i class="pi pi-exclamation-triangle"></i> Verification Details
                            </div>
                            <p>
                                The certificate with reference <span class="font-mono font-bold">{{ urlRef || 'Not Provided' }}</span> (ID: {{ urlId || 'N/A' }}) was queried in the official DFAR database, but no confirmed matching record was identified.
                            </p>
                            <p class="text-3xs text-amber-800">
                                If this document was recently created or is still in draft state, please ensure that it has been formally submitted and approved by the Competent Authority.
                            </p>
                        </div>
                    </div>
                </ng-container>

                <!-- Footer Actions -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 print:hidden">
                    <span class="text-3xs text-slate-500">DFAR Security Seal © Department of Fisheries &amp; Aquatic Resources</span>
                    <div class="flex items-center gap-2">
                        <p-button label="Verify Again" icon="pi pi-refresh" size="small" [outlined]="true" (onClick)="verify()"></p-button>
                        <p-button *ngIf="verificationData?.isVerified" label="Print Certificate Record" icon="pi pi-print" size="small" [outlined]="true" (onClick)="print()"></p-button>
                        <p-button label="Back to DFAR" icon="pi pi-arrow-left" size="small" routerLink="/"></p-button>
                    </div>
                </div>
            </div>
        </div>
    `,
    styles: [
        `
            @media print {
                .verify-page {
                    background: white !important;
                    padding: 0 !important;
                }
                p-button,
                button,
                .print\\:hidden {
                    display: none !important;
                }
            }
        `
    ]
})
export class VerifyDocumentComponent implements OnInit {
    isLoading = true;
    verificationData: PublicVerificationResponse | null = null;

    urlRef = '';
    urlId = '';
    urlConsignor = '';
    urlConsignee = '';
    urlItem = '';
    urlDate = '';
    urlOfficer = '';
    urlDestination = '';
    currentDate = new Date().toLocaleDateString('en-GB');

    constructor(
        private route: ActivatedRoute,
        private http: HttpClient,
        private cdr: ChangeDetectorRef
    ) {}

    ngOnInit(): void {
        this.route.queryParams.subscribe((params) => {
            this.urlRef = params['ref'] || '';
            this.urlId = params['id'] || '';
            this.urlConsignor = params['consignor'] || '';
            this.urlConsignee = params['consignee'] || '';
            this.urlItem = params['item'] || '';
            this.urlDate = params['date'] || '';
            this.urlOfficer = params['officer'] || '';
            this.urlDestination = params['destination'] || '';

            this.verify();
        });
    }

    verify(): void {
        this.isLoading = true;
        this.cdr.markForCheck();

        const queryParams = new URLSearchParams();
        if (this.urlRef) queryParams.set('refNumber', this.urlRef);
        if (this.urlId) queryParams.set('id', this.urlId);

        const url = `${environment.apiBaseUrl}/api/CertificateRequest/public-verify?${queryParams.toString()}`;

        this.http.get<PublicVerificationResponse>(url).subscribe({
            next: (res) => {
                this.verificationData = res;
                this.isLoading = false;
                this.cdr.markForCheck();
            },
            error: (err) => {
                console.warn('Backend verification query error:', err);
                this.verificationData = {
                    isVerified: false,
                    status: 'NOT VERIFIED',
                    message: 'No authentic certificate record was found in the official DFAR registry matching this reference.'
                };
                this.isLoading = false;
                this.cdr.markForCheck();
            }
        });
    }

    print(): void {
        if (typeof window !== 'undefined') {
            window.print();
        }
    }

    onImageError(event: Event): void {
        const target = event.target as HTMLElement;
        if (target) {
            target.style.display = 'none';
        }
    }
}
