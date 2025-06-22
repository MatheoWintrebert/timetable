import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "row" ];

    connect ()
    {
        this.setupRowHover();
        this.setupRowSelection();
    }

    setupRowHover ()
    {
        this.rowTargets.forEach( row =>
        {
            row.addEventListener( 'mouseenter', () =>
            {
                row.style.transform = 'translateX(5px)';
            } );

            row.addEventListener( 'mouseleave', () =>
            {
                row.style.transform = 'translateX(0)';
            } );
        } );
    }

    setupRowSelection ()
    {
        this.rowTargets.forEach( row =>
        {
            row.addEventListener( 'click', ( event ) =>
            {
                if ( !event.target.closest( 'a, button' ) )
                {
                    row.classList.toggle( 'selected' );
                }
            } );
        } );
    }
}