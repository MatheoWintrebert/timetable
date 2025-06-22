import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "badge", "title", "subtitle", "actions", "visual" ];

    connect ()
    {
        this.animateElements();
    }

    animateElements ()
    {
        const elements = [
            this.badgeTarget,
            this.titleTarget,
            this.subtitleTarget,
            this.actionsTarget,
            this.visualTarget
        ];

        elements.forEach( ( element, index ) =>
        {
            if ( element )
            {
                setTimeout( () =>
                {
                    element.classList.add( 'animate-fade-in-up' );
                }, index * 200 );
            }
        } );
    }
}