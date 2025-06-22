import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "nomInput", "colorInput", "colorPreview", "colorPalette", "submitButton" ];

    connect ()
    {
        console.log( "📚 Matiere form controller connected" );
        this.setupValidation();
        this.updateColorPreview();
        this.setupAutoSave();
    }

    setupValidation ()
    {
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
    }

    validateNom ( input, showErrors = false )
    {
        const value = input.value.trim();
        const isValid = value.length >= 2 && value.length <= 100;

        // Vérifier le format (lettres, chiffres, espaces, tirets, apostrophes)
        const formatValid = /^[a-zA-Z0-9\s\-'À-ÿ]+$/.test( value );

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
                this.showError( input, 'Le nom doit contenir entre 2 et 100 caractères (lettres, chiffres, espaces, tirets et apostrophes uniquement)' );
            }
        }

        this.updateSubmitButton();
        return isValid && formatValid;
    }

    updateColorPreview ()
    {
        if ( !this.hasColorInputTarget || !this.hasColorPreviewTarget ) return;

        const color = this.colorInputTarget.value;
        const swatch = this.colorPreviewTarget.querySelector( '.color-swatch' );
        if ( swatch )
        {
            swatch.style.backgroundColor = color;
        }

        // Mettre à jour la sélection dans la palette
        this.updatePaletteSelection( color );

        // Sauvegarder dans le brouillon
        this.saveDraft();
    }

    updatePaletteSelection ( selectedColor )
    {
        if ( !this.hasColorPaletteTarget ) return;

        const paletteColors = this.colorPaletteTarget.querySelectorAll( '.palette-color' );
        paletteColors.forEach( colorBtn =>
        {
            const btnColor = colorBtn.dataset.color;
            if ( btnColor === selectedColor )
            {
                colorBtn.classList.add( 'selected' );
            } else
            {
                colorBtn.classList.remove( 'selected' );
            }
        } );
    }

    selectColor ( event )
    {
        event.preventDefault();
        const color = event.currentTarget.dataset.color;
        if ( this.hasColorInputTarget )
        {
            this.colorInputTarget.value = color;
            this.updateColorPreview();

            // Déclencher l'événement change pour la validation
            this.colorInputTarget.dispatchEvent( new Event( 'change', { bubbles: true } ) );
        }
    }

    setupAutoSave ()
    {
        // Sauvegarde automatique du brouillon dans sessionStorage
        const inputs = [ this.nomInputTarget, this.colorInputTarget ].filter( Boolean );

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
            couleur: this.hasColorInputTarget ? this.colorInputTarget.value : '#3498db'
        };

        sessionStorage.setItem( 'matiere_draft', JSON.stringify( draft ) );
    }

    restoreDraft ()
    {
        const draftData = sessionStorage.getItem( 'matiere_draft' );
        if ( !draftData ) return;

        try
        {
            const draft = JSON.parse( draftData );

            if ( this.hasNomInputTarget && draft.nom )
            {
                this.nomInputTarget.value = draft.nom;
            }

            if ( this.hasColorInputTarget && draft.couleur )
            {
                this.colorInputTarget.value = draft.couleur;
                this.updateColorPreview();
            }
        } catch ( error )
        {
            console.error( 'Erreur lors de la restauration du brouillon:', error );
        }
    }

    clearDraft ()
    {
        sessionStorage.removeItem( 'matiere_draft' );
    }

    showError ( input, message )
    {
        this.clearError( input );

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
            animation: slideInDown 0.3s ease-out;
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

        if ( nomValid )
        {
            this.submitButtonTarget.disabled = false;
            this.submitButtonTarget.classList.remove( 'btn-disabled' );
        } else
        {
            this.submitButtonTarget.disabled = true;
            this.submitButtonTarget.classList.add( 'btn-disabled' );
        }
    }

    submitForm ( event )
    {
        console.log( "📤 Submitting matiere form" );

        let isValid = true;

        if ( this.hasNomInputTarget )
        {
            isValid = this.validateNom( this.nomInputTarget, true ) && isValid;
        }

        if ( !isValid )
        {
            event.preventDefault();
            console.log( "❌ Form validation failed" );

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

        console.log( "✅ Matiere form submitted successfully" );
    }

    disconnect ()
    {
        console.log( "📚 Matiere form controller disconnected" );
    }
}