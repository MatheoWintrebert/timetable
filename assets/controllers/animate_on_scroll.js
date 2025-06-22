import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static values = {
        threshold: Number,
        once: Boolean
    };

    connect ()
    {
        this.thresholdValue = this.thresholdValue || 0.1;
        this.onceValue = this.onceValue !== false;

        this.observer = new IntersectionObserver( ( entries ) =>
        {
            entries.forEach( entry =>
            {
                if ( entry.isIntersecting )
                {
                    this.animate();
                    if ( this.onceValue )
                    {
                        this.observer.unobserve( this.element );
                    }
                }
            } );
        }, {
            threshold: this.thresholdValue
        } );

        this.observer.observe( this.element );
    }

    animate ()
    {
        this.element.classList.add( 'animate-in' );
    }

    disconnect ()
    {
        if ( this.observer )
        {
            this.observer.disconnect();
        }
    }
}