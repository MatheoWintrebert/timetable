import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "input" ];

    connect ()
    {
        this.setupValidation();
    }

    setupValidation ()
    {
        this.inputTargets.forEach( input =>
        {
            input.addEventListener( 'blur', () => this.validateInput( input ) );
            input.addEventListener( 'input', () => this.clearValidation( input ) );
        } );
    }

    validateInput ( input )
    {
        if ( input.checkValidity() )
        {
            input.classList.remove( 'is-invalid' );
            input.classList.add( 'is-valid' );
        } else
        {
            input.classList.remove( 'is-valid' );
            input.classList.add( 'is-invalid' );
        }
    }

    clearValidation ( input )
    {
        input.classList.remove( 'is-invalid', 'is-valid' );
    }

    validateForm ( event )
    {
        let isValid = true;

        this.inputTargets.forEach( input =>
        {
            this.validateInput( input );
            if ( !input.checkValidity() )
            {
                isValid = false;
            }
        } );

        if ( !isValid )
        {
            event.preventDefault();
            // Focus sur le premier champ invalide
            const firstInvalid = this.element.querySelector( '.is-invalid' );
            if ( firstInvalid )
            {
                firstInvalid.focus();
            }
        }
    }
}
