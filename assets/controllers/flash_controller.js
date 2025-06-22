import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static values = {
        autoHide: Boolean,
        delay: Number
    };

    connect ()
    {
        if ( this.autoHideValue )
        {
            this.scheduleHide();
        }
        this.element.classList.add( 'show' );
    }

    scheduleHide ()
    {
        this.timeout = setTimeout( () =>
        {
            this.hide();
        }, this.delayValue || 5000 );
    }

    hide ()
    {
        this.element.classList.add( 'fade-out' );
        setTimeout( () =>
        {
            this.remove();
        }, 300 );
    }

    remove ()
    {
        if ( this.timeout )
        {
            clearTimeout( this.timeout );
        }
        this.element.remove();
    }

    disconnect ()
    {
        if ( this.timeout )
        {
            clearTimeout( this.timeout );
        }
    }
}