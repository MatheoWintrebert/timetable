import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "menu" ];

    connect ()
    {
        this.setupScrollBehavior();
    }

    toggle ()
    {
        const isExpanded = this.menuTarget.classList.contains( 'show' );
        if ( isExpanded )
        {
            this.menuTarget.classList.remove( 'show' );
        } else
        {
            this.menuTarget.classList.add( 'show' );
        }
    }

    setupScrollBehavior ()
    {
        let lastScrollTop = 0;
        const navbar = this.element;

        window.addEventListener( 'scroll', () =>
        {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if ( scrollTop > lastScrollTop && scrollTop > 100 )
            {
                // Scrolling down - hide navbar
                navbar.style.transform = 'translateY(-100%)';
            } else
            {
                // Scrolling up - show navbar
                navbar.style.transform = 'translateY(0)';
            }

            lastScrollTop = scrollTop;
        } );
    }
}
