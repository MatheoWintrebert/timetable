import { Controller } from '@hotwired/stimulus';

export default class extends Controller
{
    static targets = [ "grid" ];

    connect ()
    {
        this.setupFeatureCards();
    }

    setupFeatureCards ()
    {
        // Observer pour détecter quand la section entre dans la vue
        this.observer = new IntersectionObserver( ( entries ) =>
        {
            entries.forEach( entry =>
            {
                if ( entry.isIntersecting )
                {
                    this.animateFeatureCards();
                    // Ne déclencher qu'une fois
                    this.observer.unobserve( entry.target );
                }
            } );
        }, {
            threshold: 0.2, // Déclencher quand 20% de la section est visible
            rootMargin: '0px 0px -50px 0px' // Déclencher un peu avant
        } );

        // Observer la grille des features
        if ( this.hasGridTarget )
        {
            this.observer.observe( this.gridTarget );
        }
    }

    animateFeatureCards ()
    {
        console.log( 'Animating feature cards' );

        // Récupérer toutes les cartes dans la grille
        const cards = this.gridTarget.querySelectorAll( '.feature-card' );

        // Animer chaque carte avec un délai
        cards.forEach( ( card, index ) =>
        {
            setTimeout( () =>
            {
                card.classList.add( 'animate-in' );

                // Ajouter un effet de "pulse" subtil
                card.style.transform = 'translateY(0) scale(1.02)';
                setTimeout( () =>
                {
                    card.style.transform = 'translateY(0) scale(1)';
                }, 150 );

            }, index * 200 ); // Délai de 200ms entre chaque carte
        } );
    }

    // Méthode pour réinitialiser les animations (utile pour le développement)
    resetAnimations ()
    {
        const cards = this.gridTarget.querySelectorAll( '.feature-card' );
        cards.forEach( card =>
        {
            card.classList.remove( 'animate-in' );
        } );

        // Relancer l'observation
        if ( this.hasGridTarget )
        {
            this.observer.observe( this.gridTarget );
        }
    }

    disconnect ()
    {
        if ( this.observer )
        {
            this.observer.disconnect();
        }
    }
}