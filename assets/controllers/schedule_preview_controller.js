import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "container" ];

    connect ()
    {
        console.log( "📅 Schedule preview controller connected" );
        this.isAnimating = false;

        // Attendre que le DOM soit complètement chargé
        this.element.addEventListener( 'turbo:load', () =>
        {
            this.initializeAnimation();
        } );

        // Initialiser immédiatement aussi
        setTimeout( () =>
        {
            this.initializeAnimation();
        }, 500 );
    }

    initializeAnimation ()
    {
        console.log( "🔍 Initializing schedule animation..." );

        // Chercher les créneaux avec plusieurs sélecteurs possibles
        const timeSlots = this.findTimeSlots();

        if ( timeSlots.length === 0 )
        {
            console.log( "⚠️ No time slots found. Retrying in 1 second..." );
            setTimeout( () =>
            {
                this.initializeAnimation();
            }, 1000 );
            return;
        }

        console.log( `✅ Found ${ timeSlots.length } time slots` );
        this.startAnimation();
    }

    findTimeSlots ()
    {
        // Essayer différents sélecteurs pour trouver les créneaux
        const selectors = [
            '.time-slot',
            '.time-slot-compact',
            '[class*="time-slot"]',
            '[class*="slot"]'
        ];

        let timeSlots = [];

        for ( const selector of selectors )
        {
            if ( this.hasContainerTarget )
            {
                timeSlots = this.containerTarget.querySelectorAll( selector );
            } else
            {
                timeSlots = this.element.querySelectorAll( selector );
            }

            if ( timeSlots.length > 0 )
            {
                console.log( `🎯 Found ${ timeSlots.length } slots with selector: ${ selector }` );
                break;
            }
        }

        // Si toujours rien, chercher dans tout le document
        if ( timeSlots.length === 0 )
        {
            timeSlots = document.querySelectorAll( '.time-slot-compact, .time-slot, [class*="slot"]' );
            console.log( `🔍 Global search found ${ timeSlots.length } slots` );
        }

        return timeSlots;
    }

    startAnimation ()
    {
        if ( this.isAnimating ) return;

        const timeSlots = this.findTimeSlots();

        if ( timeSlots.length === 0 )
        {
            console.log( "❌ No time slots available for animation" );
            return;
        }

        this.isAnimating = true;
        console.log( `🎬 Starting animation for ${ timeSlots.length } time slots` );

        // Animation séquentielle des créneaux
        timeSlots.forEach( ( slot, index ) =>
        {
            setTimeout( () =>
            {
                this.animateSlot( slot );
            }, index * 200 ); // 200ms entre chaque créneau
        } );

        // Marquer la fin de l'animation
        setTimeout( () =>
        {
            this.isAnimating = false;
            console.log( "✅ Animation cycle completed" );
        }, timeSlots.length * 200 + 1500 );

        // Programmer la prochaine animation (toutes les 8 secondes)
        this.scheduleNextAnimation();
    }

    animateSlot ( slot )
    {
        // Sauvegarder les styles originaux
        const originalTransform = slot.style.transform;
        const originalBackground = slot.style.backgroundColor;
        const originalBoxShadow = slot.style.boxShadow;

        // Appliquer l'animation highlight
        slot.classList.add( 'highlight' );

        // Effets visuels personnalisés
        slot.style.transform = 'translateX(5px) scale(1.02)';
        slot.style.backgroundColor = 'rgba(8, 164, 189, 0.2)';
        slot.style.boxShadow = '0 4px 15px rgba(8, 164, 189, 0.3)';
        slot.style.borderLeftWidth = '5px';

        console.log( `✨ Animating slot: ${ slot.textContent?.substring( 0, 20 ) }...` );

        // Retirer l'animation après 1.2 secondes
        setTimeout( () =>
        {
            slot.classList.remove( 'highlight' );
            slot.style.transform = originalTransform;
            slot.style.backgroundColor = originalBackground;
            slot.style.boxShadow = originalBoxShadow;
            slot.style.borderLeftWidth = '3px';
        }, 1200 );
    }

    scheduleNextAnimation ()
    {
        if ( this.animationTimeout )
        {
            clearTimeout( this.animationTimeout );
        }

        this.animationTimeout = setTimeout( () =>
        {
            console.log( "🔄 Starting next animation cycle" );
            this.startAnimation();
        }, 8000 ); // 8 secondes
    }

    // Méthode pour redémarrer manuellement l'animation
    restart ()
    {
        console.log( "🔄 Manually restarting schedule animation" );
        if ( this.animationTimeout )
        {
            clearTimeout( this.animationTimeout );
        }
        this.isAnimating = false;
        setTimeout( () =>
        {
            this.startAnimation();
        }, 100 );
    }

    // Méthode pour débugger
    debug ()
    {
        console.log( "🐛 Debug information:" );
        console.log( "- Element:", this.element );
        console.log( "- Has container target:", this.hasContainerTarget );
        if ( this.hasContainerTarget )
        {
            console.log( "- Container:", this.containerTarget );
        }

        const timeSlots = this.findTimeSlots();
        console.log( "- Time slots found:", timeSlots.length );
        timeSlots.forEach( ( slot, index ) =>
        {
            console.log( `  ${ index + 1 }. ${ slot.className } - ${ slot.textContent?.substring( 0, 30 ) }...` );
        } );
    }

    disconnect ()
    {
        if ( this.animationTimeout )
        {
            clearTimeout( this.animationTimeout );
        }
        console.log( "📅 Schedule preview controller disconnected" );
    }
}