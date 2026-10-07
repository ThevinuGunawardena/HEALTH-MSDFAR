import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';
import { AuthService } from './auth.service';
import { Observable, shareReplay, catchError, of, tap } from 'rxjs';

export interface User {
    id: string;
    name: string;
    email: string;
    phone: string;
    roleId?: string;
    roleName?: string;
    companyId?: number;
    companyName?: string;
    qualification?: string;
    isActive: boolean;
}

export interface UserCreatePayload {
    fullName: string;
    email: string;
    phone: string;
    roleId: string;
    companyId?: number | null;
    qualification?: string;
    password?: string;
}

export interface UserUpdatePayload {
    fullName: string;
    email: string;
    phone: string;
    roleId: string;
    companyId?: number | null;
    qualification?: string;
    password?: string;
}

export interface UserLookupOption {
    id: string | number;
    name: string;
}

@Injectable({
    providedIn: 'root'
})
export class UserService {
    private allUsers$?: Observable<User[]>;

    constructor(
        private http: HttpClient,
        private authService: AuthService
    ) {}

    private getAuthHeaders(): HttpHeaders {
        return new HttpHeaders({ Authorization: 'Bearer ' + this.authService.getToken() });
    }

    clearUserCache(): void {
        this.allUsers$ = undefined;
    }

    getUserProfile() {
        const reqHeader = this.getAuthHeaders();
        return this.http.get(environment.apiBaseUrl + '/api/UserProfile', { headers: reqHeader });
    }

    updateUserProfile(payload: { fullName?: string; phone?: string; email?: string; qualification?: string }) {
        const reqHeader = this.getAuthHeaders();
        return this.http.put(environment.apiBaseUrl + '/api/UserProfile', payload, { headers: reqHeader });
    }

    updatePassword(payload: { currentPassword?: string; newPassword?: string }) {
        const reqHeader = this.getAuthHeaders();
        return this.http.put(environment.apiBaseUrl + '/api/UpdatePassword', payload, { headers: reqHeader });
    }

    getAllUsers(forceRefresh = false): Observable<User[]> {
        if (forceRefresh) {
            this.clearUserCache();
        }

        if (!this.allUsers$) {
            const reqHeader = this.getAuthHeaders();
            this.allUsers$ = this.http.get<User[]>(`${environment.apiBaseUrl}/api/user/users`, { headers: reqHeader }).pipe(
                catchError(() => of([] as User[])),
                shareReplay({ bufferSize: 1, refCount: true })
            );
        }
        return this.allUsers$;
    }

    getRoles(): Observable<UserLookupOption[]> {
        const reqHeader = this.getAuthHeaders();
        return this.http.get<UserLookupOption[]>(`${environment.apiBaseUrl}/api/user/roles`, { headers: reqHeader });
    }

    getCompanies(): Observable<UserLookupOption[]> {
        const reqHeader = this.getAuthHeaders();
        return this.http.get<UserLookupOption[]>(`${environment.apiBaseUrl}/api/company/companies`, { headers: reqHeader });
    }

    insertUser(payload: UserCreatePayload): Observable<User> {
        const reqHeader = this.getAuthHeaders();
        return this.http.post<User>(`${environment.apiBaseUrl}/api/user/saveuser`, payload, { headers: reqHeader }).pipe(
            tap(() => this.clearUserCache())
        );
    }

    updateUser(id: string, payload: UserUpdatePayload): Observable<User> {
        const reqHeader = this.getAuthHeaders();
        return this.http.put<User>(`${environment.apiBaseUrl}/api/user/updateuser/${id}`, payload, { headers: reqHeader }).pipe(
            tap(() => this.clearUserCache())
        );
    }

    deleteUser(id: string): Observable<any> {
        const reqHeader = this.getAuthHeaders();
        return this.http.delete(`${environment.apiBaseUrl}/api/user/deleteuser/${id}`, { headers: reqHeader }).pipe(
            tap(() => this.clearUserCache())
        );
    }
}
