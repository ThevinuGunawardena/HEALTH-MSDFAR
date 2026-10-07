export interface CertificateTemplateOption {
    id: string;
    label: string;
    description: string;
    path: string;
    badge: string;
    icon: string;
}

export const MULTI_TEMPLATE_COUNTRIES: Record<string, CertificateTemplateOption[]> = {
    malaysia: [
        {
            id: 'my-standard',
            label: 'Malaysia Health Certificate',
            description: 'Standard official health certificate for export of fish and fishery products to Malaysia.',
            path: '/uikit/world-certificates/my-certificate',
            badge: 'Health',
            icon: 'pi pi-file'
        },
        {
            id: 'my-quality',
            label: 'Malaysia Quality Certificate',
            description: 'Certificate of quality, chemical and microbiological inspection for Malaysian customs.',
            path: '/uikit/world-certificates/my-quality-certificate',
            badge: 'Quality',
            icon: 'pi pi-shield'
        }
    ],
    taiwan: [
        {
            id: 'tw-standard',
            label: 'Taiwan Health Certificate',
            description: 'Standard sanitary certificate for fishery and aquatic exports to Taiwan.',
            path: '/uikit/world-certificates/tw-certificate',
            badge: 'Health',
            icon: 'pi pi-file'
        },
        {
            id: 'tw-quality',
            label: 'Taiwan Quality Certificate',
            description: 'Certificate of quality verification, test analysis and specification for Taiwan.',
            path: '/uikit/world-certificates/tw-quality-certificate',
            badge: 'Quality',
            icon: 'pi pi-shield'
        }
    ],
    china: [
        {
            id: 'ch-standard',
            label: 'China Aquatic Products Certificate',
            description: 'Veterinary health certificate for edible aquatic animals and processed fishery goods.',
            path: '/uikit/world-certificates/ch-certificate',
            badge: 'Aquatic',
            icon: 'pi pi-file'
        },
        {
            id: 'ch-health',
            label: 'China Health Certificate',
            description: 'Official sanitary and health inspection certificate conforming to China GACC guidelines.',
            path: '/uikit/world-certificates/ch-health-certificate',
            badge: 'Sanitary',
            icon: 'pi pi-verified'
        }
    ],
    ukraine: [
        {
            id: 'ua-standard',
            label: 'Ukraine Health Certificate',
            description: 'International veterinary health certificate for export of fishery products to Ukraine.',
            path: '/uikit/world-certificates/ua-certificate',
            badge: 'Standard',
            icon: 'pi pi-file'
        },
        {
            id: 'ua-attachment',
            label: 'Ukraine Attachment Certificate',
            description: 'Consignment specification and batch annex certificate for Ukrainian border control.',
            path: '/uikit/world-certificates/ua-attachment-certificate',
            badge: 'Attachment',
            icon: 'pi pi-paperclip'
        }
    ],
    russia: [
        {
            id: 'ru-standard',
            label: 'Russia Veterinary Certificate',
            description: 'Veterinary certificate for fish, crustaceans and molluscs to the Russian Federation.',
            path: '/uikit/world-certificates/ru-certificate',
            badge: 'Standard',
            icon: 'pi pi-file'
        },
        {
            id: 'ru-attachment',
            label: 'Russia Attachment Certificate',
            description: 'Annex form with detailed product descriptions for Russian consignments.',
            path: '/uikit/world-certificates/ru-attachment-certificate',
            badge: 'Attachment',
            icon: 'pi pi-paperclip'
        }
    ],
    kazakhstan: [
        {
            id: 'kz-standard',
            label: 'Kazakhstan Veterinary Certificate',
            description: 'Veterinary certificate for edible fishery products exported to the Republic of Kazakhstan.',
            path: '/uikit/world-certificates/kz-certificate',
            badge: 'Standard',
            icon: 'pi pi-file'
        },
        {
            id: 'kz-attachment',
            label: 'Kazakhstan Attachment Certificate',
            description: 'Annex schedule of consignments and container specifications for Kazakhstan.',
            path: '/uikit/world-certificates/kz-attachment-certificate',
            badge: 'Attachment',
            icon: 'pi pi-paperclip'
        }
    ]
};

export const SINGLE_TEMPLATE_COUNTRIES: Record<string, { label: string; path: string }> = {
    australia: { label: 'Australia Health Certificate', path: '/uikit/world-certificates/au-certificate' },
    armenia: { label: 'Armenia Health Certificate', path: '/uikit/world-certificates/am-certificate' },
    brazil: { label: 'Brazil Health Certificate', path: '/uikit/world-certificates/br-certificate' },
    canada: { label: 'Canada Health Certificate', path: '/uikit/world-certificates/ca-certificate' },
    'hong kong': { label: 'Hong Kong Health Certificate', path: '/uikit/world-certificates/hk-certificate' },
    hongkong: { label: 'Hong Kong Health Certificate', path: '/uikit/world-certificates/hk-certificate' },
    india: { label: 'India Health Certificate', path: '/uikit/world-certificates/in-certificate' },
    indonesia: { label: 'Indonesia Health Certificate', path: '/uikit/world-certificates/id-certificate' },
    israel: { label: 'Israel Health Certificate', path: '/uikit/world-certificates/il-certificate' },
    japan: { label: 'Japan Health Certificate', path: '/uikit/world-certificates/jp-certificate' },
    kuwait: { label: 'Kuwait Health Certificate', path: '/uikit/world-certificates/kw-certificate' },
    maldives: { label: 'Maldives Health Certificate', path: '/uikit/world-certificates/mv-certificate' },
    'new zealand': { label: 'New Zealand Health Certificate', path: '/uikit/world-certificates/nz-certificate' },
    newzealand: { label: 'New Zealand Health Certificate', path: '/uikit/world-certificates/nz-certificate' },
    'saudi arabia': { label: 'Saudi Arabia Health Certificate', path: '/uikit/world-certificates/sa-certificate' },
    saudi: { label: 'Saudi Arabia Health Certificate', path: '/uikit/world-certificates/sa-certificate' },
    'south africa': { label: 'South Africa Health Certificate', path: '/uikit/world-certificates/za-certificate' },
    southafrica: { label: 'South Africa Health Certificate', path: '/uikit/world-certificates/za-certificate' },
    uk: { label: 'United Kingdom Health Certificate', path: '/uikit/world-certificates/uk-certificate' },
    'united kingdom': { label: 'United Kingdom Health Certificate', path: '/uikit/world-certificates/uk-certificate' },
    'great britain': { label: 'United Kingdom Health Certificate', path: '/uikit/world-certificates/uk-certificate' },
    usa: { label: 'USA Health Certificate', path: '/uikit/world-certificates/usa-certificate' },
    'united states': { label: 'USA Health Certificate', path: '/uikit/world-certificates/usa-certificate' },
    'united states of america': { label: 'USA Health Certificate', path: '/uikit/world-certificates/usa-certificate' }
};

/**
 * Normalizes country strings for consistent matching.
 */
export function normalizeCountryName(countryName?: string | null): string {
    return (countryName || '').trim().toLowerCase();
}

/**
 * Returns all available certificate template options for a given country and request type.
 */
export function getCertificateTemplates(countryName?: string | null, type?: string | number | null): CertificateTemplateOption[] {
    const isEU = type === 0 || String(type).toUpperCase() === 'EU';
    if (isEU) {
        return [
            {
                id: 'eu-vet',
                label: 'European Union Veterinary Certificate',
                description: 'Official animal health and food safety certificate for entry into the EU.',
                path: '/uikit/certificate',
                badge: 'EU Form',
                icon: 'pi pi-file'
            }
        ];
    }

    const normalized = normalizeCountryName(countryName);
    if (!normalized) {
        return [
            {
                id: 'generic-vet',
                label: 'General Veterinary Certificate',
                description: 'Standard health certificate application form.',
                path: '/uikit/certificate',
                badge: 'Standard',
                icon: 'pi pi-file'
            }
        ];
    }

    // Check multi-template countries
    if (MULTI_TEMPLATE_COUNTRIES[normalized]) {
        return MULTI_TEMPLATE_COUNTRIES[normalized];
    }

    // Check single-template countries
    if (SINGLE_TEMPLATE_COUNTRIES[normalized]) {
        const item = SINGLE_TEMPLATE_COUNTRIES[normalized];
        return [
            {
                id: normalized,
                label: item.label,
                description: `Official export certificate template for ${countryName}.`,
                path: item.path,
                badge: 'Single Form',
                icon: 'pi pi-file'
            }
        ];
    }

    // Fallback default
    return [
        {
            id: 'fallback-vet',
            label: `${countryName} Certificate`,
            description: `Standard export certificate application form for ${countryName}.`,
            path: '/uikit/certificate',
            badge: 'Standard',
            icon: 'pi pi-file'
        }
    ];
}

/**
 * Checks whether a given country has multiple certificate template options.
 */
export function hasMultipleTemplates(countryName?: string | null, type?: string | number | null): boolean {
    const isEU = type === 0 || String(type).toUpperCase() === 'EU';
    if (isEU) return false;
    const normalized = normalizeCountryName(countryName);
    return Boolean(MULTI_TEMPLATE_COUNTRIES[normalized] && MULTI_TEMPLATE_COUNTRIES[normalized].length > 1);
}

/**
 * Backward-compatible single path resolver.
 */
export function getCertificatePath(countryName?: string | null): string | null {
    if (!countryName) return null;
    const templates = getCertificateTemplates(countryName, 'NonEU');
    return templates.length > 0 ? templates[0].path : null;
}
