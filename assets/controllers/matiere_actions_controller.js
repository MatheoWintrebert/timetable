import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "deleteButton" ];
    static values = {
        matiereId: Number,
        matiereName: String
    };

    connect ()
    {
        console.log( `⚙️ Matiere actions controller connected for matiere ${ this.matiereNameValue }` );
        this.setupConfirmations();
    }

    setupConfirmations ()
    {
        if ( this.hasDeleteButtonTarget )
        {
            this.deleteButtonTarget.addEventListener( 'click', ( event ) =>
            {
                this.confirmDelete( event );
            } );
        }
    }

    confirmDelete ( event )
    {
        event.preventDefault();

        const message = `Êtes-vous absolument sûr de vouloir supprimer la matière "${ this.matiereNameValue }" ?\n\nCette action est irréversible et affectera tous les emplois du temps qui utilisent cette matière.`;

        // Utiliser une confirmation personnalisée avec style
        this.showCustomConfirm( message, () =>
        {
            this.performDelete();
        } );
    }

    showCustomConfirm ( message, callback )
    {
        // Créer une modal de confirmation personnalisée
        const modal = document.createElement( 'div' );
        modal.className = 'custom-confirm-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        `;

        modal.innerHTML = `
            <div class="confirm-content" style="
                background: var(--form-dark);
                border: 2px solid var(--danger-red);
                border-radius: 12px;
                padding: 2rem;
                max-width: 500px;
                margin: 1rem;
                text-align: center;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                animation: slideInUp 0.3s ease-out;
            ">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                <h3 style="color: var(--danger-red); margin-bottom: 1rem; font-size: 1.3rem;">Confirmer la suppression</h3>
                <p style="color: var(--text-light); margin-bottom: 2rem; line-height: 1.5; font-size: 1rem;">${ message }</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="confirm-cancel" style="
                        padding: 0.75rem 1.5rem;
                        border: 2px solid var(--input-border);
                        background: transparent;
                        color: var(--text-light);
                        border-radius: 6px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        font-weight: 500;
                    ">Annuler</button>
                    <button class="confirm-delete" style="
                        padding: 0.75rem 1.5rem;
                        border: 2px solid var(--danger-red);
                        background: var(--danger-red);
                        color: white;
                        border-radius: 6px;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        font-weight: 600;
                    ">Supprimer définitivement</button>
                </div>
            </div>
        `;

        document.body.appendChild( modal );

        // Gestion des clics
        modal.querySelector( '.confirm-cancel' ).addEventListener( 'click', () =>
        {
            this.closeModal( modal );
        } );

        modal.querySelector( '.confirm-delete' ).addEventListener( 'click', () =>
        {
            this.closeModal( modal );
            callback();
        } );

        // Fermer avec Escape
        document.addEventListener( 'keydown', ( event ) =>
        {
            if ( event.key === 'Escape' )
            {
                this.closeModal( modal );
            }
        }, { once: true } );

        // Fermer en cliquant à côté
        modal.addEventListener( 'click', ( event ) =>
        {
            if ( event.target === modal )
            {
                this.closeModal( modal );
            }
        } );
    }

    closeModal ( modal )
    {
        modal.style.opacity = '0';
        setTimeout( () =>
        {
            if ( modal.parentNode )
            {
                modal.parentNode.removeChild( modal );
            }
        }, 300 );
    }

    performDelete ()
    {
        console.log( `🗑️ Deleting matiere ${ this.matiereNameValue }` );

        // Changer le texte du bouton
        if ( this.hasDeleteButtonTarget )
        {
            this.deleteButtonTarget.innerHTML = `
                <div class="loading-spinner-small"></div>
                <span>Suppression...</span>
            `;
            this.deleteButtonTarget.disabled = true;
        }

        // Soumettre le formulaire de suppression
        const form = this.deleteButtonTarget.closest( 'form' );
        if ( form )
        {
            form.submit();
        }
    }

    disconnect ()
    {
        console.log( `⚙️ Matiere actions controller disconnected for matiere ${ this.matiereNameValue }` );
    }
}