import { CommonModule } from '@angular/common';
import { AfterViewInit, Component, ElementRef, Input, OnDestroy, OnInit } from '@angular/core';

@Component({
    selector: 'app-lazy-image-widget',
    standalone: true,
    imports: [CommonModule],
    template: `
        <img
            [src]="src"
            [alt]="alt"
            [class]="className"
            [ngClass]="{
                'opacity-0': !isLoaded,
                'transition-opacity duration-500 ease-out': true
            }"
            (load)="handleLoad()"
            (error)="handleError()"
            #image
        />
    `
})
export class LazyImageWidget implements OnInit, AfterViewInit, OnDestroy {
    @Input() src: string = '';
    @Input() alt: string = '';
    @Input() className: string = '';

    isLoaded = false;
    imageElement: HTMLImageElement | null = null;

    constructor(private el: ElementRef) {}

    ngOnInit() {}

    ngAfterViewInit() {
        this.imageElement = this.el.nativeElement.querySelector('img');
        if (this.imageElement && this.imageElement.complete && this.imageElement.naturalWidth > 0) {
            this.isLoaded = true;
        }
    }

    ngOnDestroy() {
        this.imageElement = null;
    }

    handleLoad() {
        this.isLoaded = true;
    }

    handleError() {
        this.isLoaded = true;
    }
}
