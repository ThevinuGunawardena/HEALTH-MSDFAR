import { Component, OnInit } from '@angular/core';
import { Router, RouterModule } from '@angular/router';

@Component({
    selector: 'app-root',
    standalone: true,
    imports: [RouterModule],
    template: `<router-outlet></router-outlet>`
})
export class AppComponent implements OnInit {
    constructor(private router: Router) {}

    ngOnInit(): void {
        if (typeof window !== 'undefined') {
            const hash = window.location.hash || '';
            const search = window.location.search || '';
            const href = window.location.href || '';

            // Handle SSO token present in hash (e.g. /#/auth/sso?token=... or #token=...)
            if (hash.includes('sso') || hash.includes('token=')) {
                const cleanHash = hash.replace(/^#\/?/, '/');
                this.router.navigateByUrl(cleanHash);
                return;
            }

            if (hash.includes('verify-document')) {
                const cleanHash = hash.replace(/^#\/?/, '/');
                this.router.navigateByUrl(cleanHash);
                return;
            }
        }
    }
}
