import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "nomInput", "niveauSelect", "submitButton" ];
    static values = {
        autoValidate: Boolean,
        showProgress: Boolean
    };

    connect ()
    {
        console.log( "📝 Classe form controller connected" );
        this.setupValidation();
        this.setupAutoSave();
    }

    setupValidation ()
    {
        // Validation en temps réel du nom de classe
        if ( this.hasNomInputTarget )
        {
            this.nomInputTarget.addEventListener( 'input', ( event ) =>
            {
                this.validateNom( event.target );
            } );

            this.nomInputTarget.addEventListener( 'blur', ( event ) =>
            {
                this.validateNom( event.target, true );
            } );
        }

        // Validation du niveau
        if ( this.hasNiveauSelectTarget )
        {
            this.niveauSelectTarget.addEventListener( 'change', ( event ) =>
            {
                this.validateNiveau( event.target );
            } );
        }
    }

    validateNom ( input, showErrors = false )
    {
        const value = input.value.trim();
        const isValid = value.length >= 2 && value.length <= 50;

        // Vérifier le format (lettres, chiffres, espaces, tirets)
        const formatValid = /^[a-zA-Z0-9\s\-À-ÿ]+$/.test( value );

        if ( isValid && formatValid )
        {
            input.classList.remove( 'is-invalid' );
            input.classList.add( 'is-valid' );
            this.clearError( input );
        } else
        {
            input.classList.remove( 'is-valid' );
            if ( showErrors )
            {
                input.classList.add( 'is-invalid' );
                this.showError( input, 'Le nom doit contenir entre 2 et 50 caractères (lettres, chiffres, espaces et tirets uniquement)' );
            }
        }

        this.updateSubmitButton();
        return isValid && formatValid;
    }

    validateNiveau ( select )
    {
        const isValid = select.value !== '';

        if ( isValid )
        {
            select.classList.remove( 'is-invalid' );
            select.classList.add( 'is-valid' );
            this.clearError( select );
        } else
        {
            select.classList.remove( 'is-valid' );
            select.classList.add( 'is-invalid' );
            this.showError( select, 'Veuillez sélectionner un niveau' );
        }

        this.updateSubmitButton();
        return isValid;
    }

    showError ( input, message )
    {
        // Supprimer l'ancien message d'erreur
        this.clearError( input );

        // Créer le nouveau message d'erreur
        const errorDiv = document.createElement( 'div' );
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            color: var(--danger-red);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 4px;
            border-left: 3px solid var(--danger-red);
        `;
        errorDiv.textContent = message;

        input.parentNode.appendChild( errorDiv );
    }

    clearError ( input )
    {
        const errorMessage = input.parentNode.querySelector( '.error-message' );
        if ( errorMessage )
        {
            errorMessage.remove();
        }
    }

    updateSubmitButton ()
    {
        if ( !this.hasSubmitButtonTarget ) return;

        const nomValid = this.hasNomInputTarget ? this.nomInputTarget.classList.contains( 'is-valid' ) : true;
        const niveauValid = this.hasNiveauSelectTarget ? this.niveauSelectTarget.classList.contains( 'is-valid' ) : true;

        if ( nomValid && niveauValid )
        {
            this.submitButtonTarget.disabled = false;
            this.submitButtonTarget.classList.remove( 'btn-disabled' );
        } else
        {
            this.submitButtonTarget.disabled = true;
            this.submitButtonTarget.classList.add( 'btn-disabled' );
        }
    }

    setupAutoSave ()
    {
        // Sauvegarde automatique du brouillon dans sessionStorage
        const inputs = [ this.nomInputTarget, this.niveauSelectTarget ].filter( Boolean );

        inputs.forEach( input =>
        {
            input.addEventListener( 'input', () =>
            {
                this.saveDraft();
            } );
        } );

        // Restaurer le brouillon au chargement
        this.restoreDraft();
    }

    saveDraft ()
    {
        const draft = {
            nom: this.hasNomInputTarget ? this.nomInputTarget.value : '',
            niveau: this.hasNiveauSelectTarget ? this.niveauSelectTarget.value : ''
        };

        sessionStorage.setItem( 'classe_draft', JSON.stringify( draft ) );
    }

    restoreDraft ()
    {
        const draftData = sessionStorage.getItem( 'classe_draft' );
        if ( !draftData ) return;

        try
        {
            const draft = JSON.parse( draftData );

            if ( this.hasNomInputTarget && draft.nom )
            {
                this.nomInputTarget.value = draft.nom;
            }

            if ( this.hasNiveauSelectTarget && draft.niveau )
            {
                this.niveauSelectTarget.value = draft.niveau;
            }
        } catch ( error )
        {
            console.error( 'Erreur lors de la restauration du brouillon:', error );
        }
    }

    clearDraft ()
    {
        sessionStorage.removeItem( 'classe_draft' );
    }

    // Action appelée lors de la soumission
    submitForm ( event )
    {
        console.log( "📤 Submitting classe form" );

        // Validation finale
        let isValid = true;

        if ( this.hasNomInputTarget )
        {
            isValid = this.validateNom( this.nomInputTarget, true ) && isValid;
        }

        if ( this.hasNiveauSelectTarget )
        {
            isValid = this.validateNiveau( this.niveauSelectTarget ) && isValid;
        }

        if ( !isValid )
        {
            event.preventDefault();
            console.log( "❌ Form validation failed" );

            // Faire défiler vers la première erreur
            const firstError = this.element.querySelector( '.is-invalid' );
            if ( firstError )
            {
                firstError.scrollIntoView( { behavior: 'smooth', block: 'center' } );
                firstError.focus();
            }

            return false;
        }

        // Afficher un indicateur de chargement
        if ( this.hasSubmitButtonTarget )
        {
            this.submitButtonTarget.innerHTML = `
                <div class="loading-spinner-small"></div>
                <span>Création en cours...</span>
            `;
            this.submitButtonTarget.disabled = true;
        }

        // Nettoyer le brouillon après soumission réussie
        setTimeout( () =>
        {
            this.clearDraft();
        }, 1000 );

        console.log( "✅ Form submitted successfully" );
    }

    disconnect ()
    {
        console.log( "📝 Classe form controller disconnected" );
    }
}