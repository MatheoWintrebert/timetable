import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "item" ];

    connect ()
    {
        this.observer = new IntersectionObserver( ( entries ) =>
        {
            entries.forEach( entry =>
            {
                if ( entry.isIntersecting )
                {
                    this.animateCounters();
                    this.observer.unobserve( this.element );
                }
            } );
        } );

        this.observer.observe( this.element );
    }

    animateCounters ()
    {
        this.itemTargets.forEach( ( item, index ) =>
        {
            setTimeout( () =>
            {
                item.style.transform = 'scale(1.1)';
                setTimeout( () =>
                {
                    item.style.transform = 'scale(1)';
                }, 300 );
            }, index * 200 );
        } );
    }

    disconnect ()
    {
        if ( this.observer )
        {
            this.observer.disconnect();
        }
    }
}
