import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "container" ];

    connect ()
    {
        this.startAnimation();
    }

    startAnimation ()
    {
        const timeSlots = this.containerTarget.querySelectorAll( '.time-slot' );

        timeSlots.forEach( ( slot, index ) =>
        {
            setTimeout( () =>
            {
                slot.classList.add( 'highlight' );
                setTimeout( () =>
                {
                    slot.classList.remove( 'highlight' );
                }, 1000 );
            }, index * 500 );
        } );

        // Répéter l'animation toutes les 5 secondes
        setInterval( () =>
        {
            this.startAnimation();
        }, 5000 );
    }
}
