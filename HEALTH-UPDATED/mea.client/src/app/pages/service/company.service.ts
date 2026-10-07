import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';

export interface CompanyCreatePayload {
    companyName: string;
    companyEmail: string;
    companyPhone: string;
    companyAddress: string;
    registrationNo: string;
    productCertificate?: number | null;
    status: number;
    listedCountry: number;
}

export interface ListedCountryDto {
    id: number;
    name: string;
}

export interface LookupOptionDto {
    id: number;
    name: string;
}

@Injectable({
    providedIn: 'root'
})
export class CompanyService {
    constructor(private http: HttpClient) {}

    getCompanies() {
        return this.http.get(`${environment.apiBaseUrl}/api/company/companies`);
    }

    getListedCountries() {
        return this.http.get<ListedCountryDto[]>(`${environment.apiBaseUrl}/api/company/listed-countries`);
    }

    getProductCertificates() {
        return this.http.get<LookupOptionDto[]>(`${environment.apiBaseUrl}/api/company/product-certificates`);
    }

    getCompanyStatuses() {
        return this.http.get<LookupOptionDto[]>(`${environment.apiBaseUrl}/api/company/company-statuses`);
    }

    createCompany(payload: CompanyCreatePayload) {
        return this.http.post(`${environment.apiBaseUrl}/api/company/savecompanies`, payload);
    }

    editCompany(id: number, payload: CompanyCreatePayload) {
        return this.http.put(`${environment.apiBaseUrl}/api/company/updatecompanies/${id}`, payload);
    }

    deleteCompany(id: number) {
        return this.http.delete(`${environment.apiBaseUrl}/api/company/deletecompanies/${id}`);
    }
}
