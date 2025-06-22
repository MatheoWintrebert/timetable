import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "overlay" ];

    connect ()
    {
        this.setupTurboEvents();
    }

    setupTurboEvents ()
    {
        document.addEventListener( "turbo:visit", () =>
        {
            this.show();
        } );

        document.addEventListener( "turbo:load", () =>
        {
            this.hide();
        } );

        document.addEventListener( "turbo:before-fetch-request", () =>
        {
            this.show();
        } );

        document.addEventListener( "turbo:before-fetch-response", () =>
        {
            this.hide();
        } );
    }

    show ()
    {
        this.overlayTarget.classList.remove( 'd-none' );
        setTimeout( () =>
        {
            this.overlayTarget.style.opacity = '1';
        }, 10 );
    }

    hide ()
    {
        this.overlayTarget.style.opacity = '0';
        setTimeout( () =>
        {
            this.overlayTarget.classList.add( 'd-none' );
        }, 300 );
    }
}