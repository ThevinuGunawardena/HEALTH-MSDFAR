import { HttpClient } from '@angular/common/http';
import { Injectable, signal } from '@angular/core';
import { TOKEN_KEY, USER_EMAIL_KEY, USER_ID_KEY, USER_ROLE_KEY, USER_NAME_KEY } from '../../shared/constants';
import { environment } from 'src/environments/environment.development';

@Injectable({
    providedIn: 'root'
})
export class AuthService {
    private currentUserDisplayName = signal<string>(localStorage.getItem(USER_NAME_KEY) || '');

    constructor(private http: HttpClient) {}

    createUser(formData: any) {
        formData.role = formData.role || 'User';
        return this.http.post(environment.apiBaseUrl + '/api/identityuser/signup', formData);
    }

    signin(formData: any) {
        return this.http.post(environment.apiBaseUrl + '/api/identityuser/signin', formData);
    }

    ssoLogin(token: string) {
        return this.http.post<any>(environment.apiBaseUrl + '/api/identityuser/sso-login', { token });
    }

    isLoggedIn() {
        return this.getToken() != null ? true : false;
    }

    saveToken(token: string) {
        localStorage.setItem(TOKEN_KEY, token);
    }

    saveUserInfo(email: string, userId: string, role: string, name: string) {
        localStorage.setItem(USER_EMAIL_KEY, email);
        localStorage.setItem(USER_ID_KEY, userId);
        localStorage.setItem(USER_ROLE_KEY, role);
        localStorage.setItem(USER_NAME_KEY, name);
        this.currentUserDisplayName.set(name);
    }

    updateUserName(name: string) {
        localStorage.setItem(USER_NAME_KEY, name);
        this.currentUserDisplayName.set(name);
    }

    getToken() {
        return localStorage.getItem(TOKEN_KEY);
    }

    getUserEmail(): string {
        return localStorage.getItem(USER_EMAIL_KEY) || '';
    }

    getUserId(): string {
        return localStorage.getItem(USER_ID_KEY) || '';
    }

    getUserRole(): string {
        const role = localStorage.getItem(USER_ROLE_KEY);
        if (role) return role;
        
        // Fallback to token parsing if role not in localStorage
        const token = this.getToken();
        if (!token) return '';

        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            return payload.role || '';
        } catch (error) {
            return '';
        }
    }

    getUserName(): string {
        return this.currentUserDisplayName();
    }

    deleteToken() {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_EMAIL_KEY);
        localStorage.removeItem(USER_ID_KEY);
        localStorage.removeItem(USER_ROLE_KEY);
        localStorage.removeItem(USER_NAME_KEY);
        this.currentUserDisplayName.set('');
    }

}
