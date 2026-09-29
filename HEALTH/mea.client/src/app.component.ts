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
        if (typeof window !== 'undefined' && window.location.hash && window.location.hash.includes('verify-document')) {
            const cleanHash = window.location.hash.replace(/^#\/?/, '/');
            this.router.navigateByUrl(cleanHash);
        }
    }
}
