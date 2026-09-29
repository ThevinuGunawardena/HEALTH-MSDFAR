import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { ActivatedRoute, Router } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { TabsModule } from 'primeng/tabs';
import { AuthService } from '@/pages/service/auth.service';

interface CertificatePreviewTab {
    value: 'country' | 'vet';
    title: string;
    src: SafeResourceUrl | null;
    emptyMessage: string;
}

const COUNTRY_CERTIFICATE_MAP: Record<string, string> = {
    Australia: '/uikit/world-certificates/au-certificate',
    Brazil: '/uikit/world-certificates/br-certificate',
    China: '/uikit/world-certificates/ch-certificate',
    Armenia: '/uikit/world-certificates/am-certificate',
    'Hong Kong': '/uikit/world-certificates/hk-certificate',
    India: '/uikit/world-certificates/in-certificate',
    Indonesia: '/uikit/world-certificates/id-certificate',
    Malaysia: '/uikit/world-certificates/my-certificate',
    Kuwait: '/uikit/world-certificates/kw-certificate',
    Taiwan: '/uikit/world-certificates/tw-certificate',
    Ukraine: '/uikit/world-certificates/ua-certificate',
    Russia: '/uikit/world-certificates/ru-certificate',
    Kazakhstan: '/uikit/world-certificates/kz-certificate',
    Japan: '/uikit/world-certificates/jp-certificate',
    'New Zealand': '/uikit/world-certificates/nz-certificate',
    USA: '/uikit/world-certificates/usa-certificate',
    'United States of America': '/uikit/world-certificates/usa-certificate',
    'United States': '/uikit/world-certificates/usa-certificate',
    UK: '/uikit/world-certificates/uk-certificate',
    'United Kingdom': '/uikit/world-certificates/uk-certificate',
    'Great Britain': '/uikit/world-certificates/uk-certificate',
    Israel: '/uikit/world-certificates/il-certificate',
    Maldives: '/uikit/world-certificates/mv-certificate',
    Canada: '/uikit/world-certificates/ca-certificate',
    'Saudi Arabia': '/uikit/world-certificates/sa-certificate',
    'South Africa': '/uikit/world-certificates/za-certificate',
    'European Union': '/uikit/certificate',
    EU: '/uikit/certificate'
};

function getCertificatePath(countryName?: string | null): string | null {
    if (!countryName) return null;
    const normalized = countryName.trim().toLowerCase();
    
    if (normalized === 'european union' || normalized === 'eu') return '/uikit/certificate';
    if (normalized === 'australia') return '/uikit/world-certificates/au-certificate';
    if (normalized === 'usa' || normalized === 'united states' || normalized === 'united states of america') return '/uikit/world-certificates/usa-certificate';
    if (normalized === 'uk' || normalized === 'united kingdom' || normalized === 'great britain') return '/uikit/world-certificates/uk-certificate';
    if (normalized === 'brazil') return '/uikit/world-certificates/br-certificate';
    if (normalized === 'china') return '/uikit/world-certificates/ch-certificate';
    if (normalized === 'armenia') return '/uikit/world-certificates/am-certificate';
    if (normalized === 'hong kong') return '/uikit/world-certificates/hk-certificate';
    if (normalized === 'india') return '/uikit/world-certificates/in-certificate';
    if (normalized === 'indonesia') return '/uikit/world-certificates/id-certificate';
    if (normalized === 'malaysia') return '/uikit/world-certificates/my-certificate';
    if (normalized === 'kuwait') return '/uikit/world-certificates/kw-certificate';
    if (normalized === 'taiwan') return '/uikit/world-certificates/tw-certificate';
    if (normalized === 'ukraine') return '/uikit/world-certificates/ua-certificate';
    if (normalized === 'russia') return '/uikit/world-certificates/ru-certificate';
    if (normalized === 'kazakhstan' || normalized === 'republic of kazakhstan') return '/uikit/world-certificates/kz-certificate';
    if (normalized === 'japan') return '/uikit/world-certificates/jp-certificate';
    if (normalized === 'new zealand') return '/uikit/world-certificates/nz-certificate';
    if (normalized === 'israel') return '/uikit/world-certificates/il-certificate';
    if (normalized === 'maldives') return '/uikit/world-certificates/mv-certificate';
    if (normalized === 'canada') return '/uikit/world-certificates/ca-certificate';
    if (normalized === 'saudi arabia') return '/uikit/world-certificates/sa-certificate';
    if (normalized === 'south africa') return '/uikit/world-certificates/za-certificate';

    const matchKey = Object.keys(COUNTRY_CERTIFICATE_MAP).find(
        (k) => k.toLowerCase().trim() === normalized
    );
    return matchKey ? COUNTRY_CERTIFICATE_MAP[matchKey] : null;
}

@Component({
    selector: 'app-certificate-requests-view',
    standalone: true,
    imports: [CommonModule, ButtonModule, TabsModule],
    templateUrl: './certificate-requests-view.component.html',
    styleUrls: ['./certificate-requests-view.component.css']
})
export class CertificateRequestsViewComponent implements OnInit {
    requestId: number | null = null;
    requestRef = 'N/A';
    requestType = 'N/A';
    requestCountry: string | null = null;
    activeTab: 'country' | 'vet' = 'vet';
    countryPreviewTab: CertificatePreviewTab | null = null;
    vetPreviewTab: CertificatePreviewTab | null = null;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private sanitizer: DomSanitizer,
        private authService: AuthService
    ) {}

    ngOnInit(): void {
        this.route.queryParams.subscribe((params) => {
            this.requestId = params['requestId'] ? Number(params['requestId']) : null;
            this.requestRef = params['ref'] || 'N/A';
            this.requestType = params['type'] || 'N/A';
            this.requestCountry = params['country'] || null;

            this.countryPreviewTab = this.buildCountryPreviewTab();
            this.vetPreviewTab = this.buildVetPreviewTab();
            this.activeTab = this.countryPreviewTab?.src ? 'country' : 'vet';
        });
    }

    goBack(): void {
        const role = this.authService.getUserRole().toLowerCase();
        if (role === 'company') {
            this.router.navigate(['/company-request-history']);
        } else {
            this.router.navigate(['/uikit/admin/certificate-requests']);
        }
    }

    onEditCurrentForm(): void {
        if (!this.requestId) return;

        if (this.activeTab === 'country' && this.requestCountry) {
            const certPath = getCertificatePath(this.requestCountry);
            if (certPath) {
                this.router.navigate([certPath], {
                    queryParams: {
                        requestId: this.requestId,
                        ref: this.requestRef,
                        adminEdit: 'true'
                    }
                });
                return;
            }
        }

        // Edit Application / Vet Certificate
        this.router.navigate(['/uikit/certificate'], {
            queryParams: {
                requestId: this.requestId,
                ref: this.requestRef,
                type: this.requestType,
                adminEdit: 'true'
            }
        });
    }

    private buildCountryPreviewTab(): CertificatePreviewTab {
        return {
            value: 'country',
            title: 'Country Certificate',
            src: this.getCountryCertificateUrl(),
            emptyMessage: this.requestCountry ? `The certificate form for ${this.requestCountry} is not available yet.` : 'No country certificate is linked to this request.'
        };
    }

    private buildVetPreviewTab(): CertificatePreviewTab {
        return {
            value: 'vet',
            title: 'Vet Certificate',
            src: this.getVetCertificateUrl(),
            emptyMessage: 'No vet certificate is linked to this request.'
        };
    }

    private getCountryCertificateUrl(): SafeResourceUrl | null {
        if (!this.requestCountry || !this.requestId) {
            return null;
        }

        const certificatePath = getCertificatePath(this.requestCountry);
        if (!certificatePath) {
            return null;
        }

        return this.createSafePreviewUrl(certificatePath, {
            requestId: String(this.requestId),
            ref: this.requestRef,
            type: this.requestType,
            country: this.requestCountry || ''
        });
    }

    private getVetCertificateUrl(): SafeResourceUrl | null {
        if (!this.requestId) {
            return null;
        }

        return this.createSafePreviewUrl('/uikit/certificate', {
            requestId: String(this.requestId),
            ref: this.requestRef,
            type: this.requestType
        });
    }

    private createSafePreviewUrl(path: string, extraQueryParams: Record<string, string> = {}): SafeResourceUrl {
        const queryParams = new URLSearchParams({
            requestId: String(this.requestId ?? ''),
            viewOnly: 'true',
            embedded: 'true',
            ...extraQueryParams
        });

        return this.sanitizer.bypassSecurityTrustResourceUrl(`${path}?${queryParams.toString()}`);
    }
}