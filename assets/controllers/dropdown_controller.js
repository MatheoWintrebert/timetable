import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "menu" ];

    connect ()
    {
        this.isOpen = false;
        this.setupClickOutside();
    }

    toggle ( event )
    {
        event.preventDefault();

        if ( this.isOpen )
        {
            this.close();
        } else
        {
            this.open();
        }
    }

    open ()
    {
        this.menuTarget.classList.add( 'show' );
        this.element.querySelector( '.dropdown-toggle' ).setAttribute( 'aria-expanded', 'true' );
        this.isOpen = true;
    }

    close ()
    {
        this.menuTarget.classList.remove( 'show' );
        this.element.querySelector( '.dropdown-toggle' ).setAttribute( 'aria-expanded', 'false' );
        this.isOpen = false;
    }

    setupClickOutside ()
    {
        document.addEventListener( 'click', ( event ) =>
        {
            if ( !this.element.contains( event.target ) && this.isOpen )
            {
                this.close();
            }
        } );
    }
}
