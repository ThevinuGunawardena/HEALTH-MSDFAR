import { Component, Input, OnInit, OnChanges, SimpleChanges, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import * as QRCode from 'qrcode';
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
    selector: 'app-certificate-qr',
    standalone: true,
    imports: [CommonModule],
    template: `
        <div *ngIf="shouldShow()" class="cert-qr-container" [ngClass]="customClass">
            <!-- Clickable QR Card -->
            <button
                type="button"
                (click)="openModal($event)"
                class="usa-qr-card"
                title="Click to Verify Official Document in DFAR Database"
            >
                <img [src]="qrCodeUrl" alt="Verify Original Document" class="usa-qr-img" [style.width.px]="size" [style.height.px]="size" />
                <span class="usa-qr-lbl">CLICK OR SCAN</span>
                <span class="usa-qr-sub">TO VERIFY</span>
            </button>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- IN-PAGE OFFICIAL VERIFICATION MODAL                             -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div *ngIf="isModalOpen" class="fixed inset-0 z-9999 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs font-sans animate-fade-in">
            <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden text-left max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-5 text-center relative overflow-hidden shrink-0">
                    <button
                        type="button"
                        (click)="closeModal()"
                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm transition-colors"
                        title="Close Verification"
                    >
                        ✕
                    </button>
                    <img src="/demo/images/srilanka-emblem.png" alt="Emblem of Sri Lanka" class="h-12 mx-auto mb-2 drop-shadow" (error)="onImageError($event)" />
                    <div class="text-3xs font-semibold tracking-wider text-emerald-300 uppercase">Democratic Socialist Republic of Sri Lanka</div>
                    <h2 class="text-base sm:text-lg font-bold tracking-wide mt-0.5">DEPARTMENT OF FISHERIES &amp; AQUATIC RESOURCES</h2>
                    <div class="text-3xs text-slate-300 mt-0.5">Document Authenticity &amp; Database Verification System</div>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-5 overflow-y-auto flex-1 space-y-4">
                    <!-- Loading State -->
                    <div *ngIf="isLoading" class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 mb-3 animate-spin">
                            <i class="pi pi-spinner text-xl"></i>
                        </div>
                        <div class="text-sm font-semibold text-slate-700">Checking Official Registry Database...</div>
                        <div class="text-xs text-slate-400 mt-1">Querying certificate records in DFAR database</div>
                    </div>

                    <!-- Verified State -->
                    <ng-container *ngIf="!isLoading && verificationData?.isVerified">
                        <div class="p-4 text-center rounded-xl bg-emerald-50 border border-emerald-200">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 mb-2 shadow-xs">
                                <i class="pi pi-check-circle text-2xl font-bold"></i>
                            </div>
                            <div>
                                <span class="inline-block px-3 py-0.5 rounded-full bg-emerald-600 text-white text-3xs font-bold uppercase tracking-wider mb-1 shadow-xs">
                                    ✓ DATABASE VERIFIED &amp; AUTHENTIC
                                </span>
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">
                                OFFICIAL ORIGINAL CERTIFICATE
                            </h3>
                            <p class="text-xs text-emerald-800 max-w-md mx-auto mt-1 font-medium">
                                Validated against the Department of Fisheries &amp; Aquatic Resources official database registry.
                            </p>
                        </div>

                        <!-- Record Details Grid -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-2xs border-b border-slate-200 pb-1">
                                <span class="font-bold uppercase tracking-wider text-slate-500">Official Database Record Information</span>
                                <span class="font-semibold text-emerald-800 bg-emerald-100 border border-emerald-300 px-1.5 py-0.5 rounded text-3xs uppercase">
                                    Status: {{ verificationData?.status || 'Confirmed' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200/80">
                                    <span class="text-slate-500 font-medium block text-3xs">Certificate Reference No:</span>
                                    <span class="text-xs font-bold text-slate-900 font-mono mt-0.5 block">
                                        {{ verificationData?.certificateReference || displayRef }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200/80">
                                    <span class="text-slate-500 font-medium block text-3xs">Date of Issue:</span>
                                    <span class="text-xs font-bold text-slate-900 mt-0.5 block">
                                        {{ verificationData?.issueDate || displayDate }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200/80 sm:col-span-2">
                                    <span class="text-slate-500 font-medium block text-3xs">Commodity / Product Description:</span>
                                    <span class="text-xs font-bold text-slate-900 mt-0.5 block uppercase">
                                        {{ verificationData?.itemName || item || 'SEE ATTACHED SPECIFICATION' }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200/80">
                                    <span class="text-slate-500 font-medium block text-3xs">Destination Country:</span>
                                    <span class="text-xs font-bold text-slate-900 mt-0.5 block uppercase">
                                        {{ verificationData?.countryOfDestination || destination || 'INTERNATIONAL' }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200/80">
                                    <span class="text-slate-500 font-medium block text-3xs">Authorized Inspector / Officer:</span>
                                    <span class="text-xs font-bold text-slate-900 mt-0.5 block uppercase">
                                        {{ verificationData?.officerName || officer || 'OFFICIAL INSPECTION OFFICER' }}
                                    </span>
                                    <span class="text-3xs text-slate-500 mt-0.5 block" *ngIf="verificationData?.designation || verificationData?.qualification">
                                        {{ verificationData?.designation }} <span *ngIf="verificationData?.qualification">({{ verificationData?.qualification }})</span>
                                    </span>
                                </div>
                            </div>

                            <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950 text-3xs space-y-0.5">
                                <div class="font-bold flex items-center gap-1 text-2xs text-emerald-900">
                                    <i class="pi pi-shield"></i> DFAR Document Security Confirmation
                                </div>
                                <p>
                                    This certificate was authenticated against the official E-Signature &amp; Document Registry of DFAR, Colombo, Sri Lanka.
                                </p>
                            </div>
                        </div>
                    </ng-container>

                    <!-- Unverified State -->
                    <ng-container *ngIf="!isLoading && !verificationData?.isVerified">
                        <div class="p-4 text-center rounded-xl bg-red-50 border border-red-200">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-600 mb-2 shadow-xs">
                                <i class="pi pi-times-circle text-2xl font-bold"></i>
                            </div>
                            <div>
                                <span class="inline-block px-3 py-0.5 rounded-full bg-red-600 text-white text-3xs font-bold uppercase tracking-wider mb-1 shadow-xs">
                                    ✗ UNVERIFIED DOCUMENT
                                </span>
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">
                                DOCUMENT NOT VERIFIED
                            </h3>
                            <p class="text-xs text-red-700 max-w-md mx-auto mt-1 font-medium">
                                No authentic certificate record was found matching this reference in the official DFAR registry database.
                            </p>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg text-amber-900 text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-xs text-amber-950">
                                <i class="pi pi-exclamation-triangle"></i> Verification Details
                            </div>
                            <p>
                                Searched reference: <span class="font-mono font-bold">{{ displayRef }}</span> (ID: {{ certificateRequestId || 'N/A' }}).
                            </p>
                            <p class="text-3xs text-amber-800">
                                If this certificate was just generated or is in draft mode, please submit it first to register it in the official database.
                            </p>
                        </div>
                    </ng-container>
                </div>

                <!-- Modal Footer -->
                <div class="p-3 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2 shrink-0">
                    <span class="text-3xs text-slate-500">DFAR Security © {{ currentYear }}</span>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            (click)="checkDatabase()"
                            class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 text-xs font-semibold flex items-center gap-1 transition-colors"
                        >
                            <i class="pi pi-refresh text-xs"></i> Check Again
                        </button>
                        <button
                            type="button"
                            (click)="openFullPage()"
                            class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold flex items-center gap-1 shadow-xs transition-colors"
                        >
                            <i class="pi pi-external-link text-xs"></i> Open Full Page
                        </button>
                        <button
                            type="button"
                            (click)="closeModal()"
                            class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-semibold transition-colors"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `,
    styles: [`
        .cert-qr-container {
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        .usa-qr-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #ffffff;
            padding: 3px 4px;
            border: 1px solid #000000;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
            cursor: pointer;
            user-select: none;
            outline: none;
        }
        .usa-qr-card:hover {
            transform: scale(1.06);
            box-shadow: 0 4px 10px rgba(4, 120, 87, 0.25);
            border-color: #047857;
        }
        .usa-qr-card:active {
            transform: scale(0.97);
        }
        .usa-qr-img {
            display: block;
            image-rendering: pixelated;
        }
        .usa-qr-lbl {
            font-size: 5.5pt;
            font-weight: 800;
            color: #047857;
            letter-spacing: 0.3px;
            margin-top: 2px;
            line-height: 1;
            white-space: nowrap;
        }
        .usa-qr-sub {
            font-size: 4.8pt;
            font-weight: 700;
            color: #000000;
            letter-spacing: 0.2px;
            line-height: 1.1;
            white-space: nowrap;
        }
        @media print {
            .usa-qr-card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
            .z-9999 {
                display: none !important;
            }
        }
    `]
})
export class CertificateQrComponent implements OnInit, OnChanges {
    @Input() refNumber: string | null = null;
    @Input() certificateRequestId: number | string | null = null;
    @Input() consignor: string | null = null;
    @Input() consignee: string | null = null;
    @Input() item: string | null = null;
    @Input() date: any = null;
    @Input() officer: string | null = null;
    @Input() destination: string | null = null;
    @Input() viewOnly = false;
    @Input() isSubmitted = false;
    @Input() size = 56;
    @Input() customClass = '';

    qrCodeUrl: string | null = null;
    isModalOpen = false;
    isLoading = false;
    verificationData: PublicVerificationResponse | null = null;
    currentYear = new Date().getFullYear();

    constructor(
        private http: HttpClient,
        private cdr: ChangeDetectorRef
    ) {}

    ngOnInit(): void {
        this.generateQrCode();
    }

    ngOnChanges(changes: SimpleChanges): void {
        this.generateQrCode();
    }

    get displayRef(): string {
        return this.refNumber || (this.certificateRequestId ? `CERT-${this.certificateRequestId}` : '');
    }

    get displayDate(): string {
        if (this.date) {
            try {
                return new Date(this.date).toLocaleDateString('en-GB');
            } catch {
                return String(this.date);
            }
        }
        return new Date().toLocaleDateString('en-GB');
    }

    generateQrCode(): void {
        const payload = this.getQrPayload();
        QRCode.toDataURL(payload, {
            width: 220,
            margin: 2,
            errorCorrectionLevel: 'M',
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        })
            .then((url: string) => {
                this.qrCodeUrl = url;
                this.cdr.markForCheck();
            })
            .catch((err: any) => {
                console.error('QR code generation error:', err);
            });
    }

    getVerificationUrl(): string {
        const origin = typeof window !== 'undefined' ? window.location.origin : 'https://localhost:7239';
        const params = new URLSearchParams();
        if (this.displayRef) params.set('ref', this.displayRef);
        if (this.certificateRequestId) params.set('id', String(this.certificateRequestId));
        if (this.item) params.set('item', this.item);
        if (this.displayDate) params.set('date', this.displayDate);
        if (this.officer) params.set('officer', this.officer);
        if (this.destination) params.set('destination', this.destination);

        return `${origin}/verify-document?${params.toString()}`;
    }

    getQrPayload(): string {
        // Direct URL payload ensures iOS Camera, Android Google Lens, and all QR scanners recognize the web link immediately
        return this.getVerificationUrl();
    }

    openModal(event?: MouseEvent): void {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        this.isModalOpen = true;
        this.checkDatabase();
    }

    closeModal(): void {
        this.isModalOpen = false;
        this.cdr.markForCheck();
    }

    checkDatabase(): void {
        this.isLoading = true;
        this.cdr.markForCheck();

        const queryParams = new URLSearchParams();
        if (this.displayRef) queryParams.set('refNumber', this.displayRef);
        if (this.certificateRequestId) queryParams.set('id', String(this.certificateRequestId));

        const url = `${environment.apiBaseUrl}/api/CertificateRequest/public-verify?${queryParams.toString()}`;

        this.http.get<PublicVerificationResponse>(url).subscribe({
            next: (res) => {
                this.verificationData = res;
                this.isLoading = false;
                this.cdr.markForCheck();
            },
            error: () => {
                // If endpoint cannot be reached or returns not found
                this.verificationData = {
                    isVerified: false,
                    status: 'NOT VERIFIED',
                    message: 'No matching record found in official database.'
                };
                this.isLoading = false;
                this.cdr.markForCheck();
            }
        });
    }

    openFullPage(): void {
        const url = this.getVerificationUrl();
        if (typeof window !== 'undefined') {
            window.open(url, '_blank');
        }
    }

    shouldShow(): boolean {
        return !!this.qrCodeUrl;
    }

    onImageError(event: Event): void {
        const target = event.target as HTMLElement;
        if (target) {
            target.style.display = 'none';
        }
    }
}
