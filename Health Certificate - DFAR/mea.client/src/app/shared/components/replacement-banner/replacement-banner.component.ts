import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-replacement-banner',
    standalone: true,
    imports: [CommonModule],
    template: `
        <div *ngIf="refNumber" class="replacement-banner-container" [ngClass]="customClass">
            <div class="replacement-banner-box">
                <div class="banner-title">THIS HEALTH CERTIFICATE CANCELS & REPLACES THE HEALTH CERTIFICATE NO:</div>
                <div class="banner-details">
                    <span class="banner-ref">{{ refNumber }}</span>
                    <span class="banner-dated">DATED :</span>
                    <span class="banner-date">{{ formattedDate }}</span>
                </div>
            </div>
        </div>
    `,
    styles: [`
        .replacement-banner-container {
            width: 100%;
            margin-bottom: 6px;
            display: flex;
            justify-content: flex-start;
        }

        .replacement-banner-box {
            border: 1.5px solid #000000;
            background-color: #ffffff;
            color: #000000;
            padding: 4px 10px;
            text-align: left;
            box-sizing: border-box;
            display: inline-block;
            max-width: 80%;
        }

        .banner-title {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 700;
            font-size: 10.5px;
            line-height: 1.25;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            color: #000000;
            text-align: left;
            margin-bottom: 2px;
            white-space: nowrap;
        }

        .banner-details {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 700;
            font-size: 11px;
            line-height: 1.25;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            color: #000000;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .banner-ref {
            font-weight: 700;
        }

        .banner-dated {
            font-weight: 700;
            margin-left: 4px;
        }

        .banner-date {
            font-weight: 700;
        }

        @media print {
            .replacement-banner-container {
                display: flex !important;
                justify-content: flex-start !important;
                margin-bottom: 4px !important;
                page-break-inside: avoid;
            }

            .replacement-banner-box {
                border: 1.5px solid #000000 !important;
                color: #000000 !important;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 3px 8px !important;
                text-align: left !important;
                display: inline-block !important;
            }

            .banner-title, .banner-details, .banner-ref, .banner-dated, .banner-date {
                color: #000000 !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
                font-weight: 700 !important;
            }
        }
    `]
})
export class ReplacementBannerComponent {
    @Input() refNumber: string | null | undefined = null;
    @Input() date: string | Date | null | undefined = null;
    @Input() customClass: string = '';

    get formattedDate(): string {
        if (!this.date) return '';
        try {
            const d = new Date(this.date);
            if (isNaN(d.getTime())) {
                return String(this.date);
            }
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        } catch {
            return String(this.date);
        }
    }
}
