import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "input", "results" ];
    static values = { url: String, debounce: Number };

    connect ()
    {
        this.debounceValue = this.debounceValue || 300;
        this.timeout = null;
    }

    search ()
    {
        clearTimeout( this.timeout );

        this.timeout = setTimeout( () =>
        {
            this.performSearch();
        }, this.debounceValue );
    }

    async performSearch ()
    {
        const query = this.inputTarget.value.trim();

        if ( query.length < 2 )
        {
            this.clearResults();
            return;
        }

        try
        {
            const response = await fetch( `${ this.urlValue }?q=${ encodeURIComponent( query ) }`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            } );

            if ( response.ok )
            {
                const data = await response.json();
                this.displayResults( data );
            }
        } catch ( error )
        {
            console.error( 'Search error:', error );
        }
    }

    displayResults ( data )
    {
        if ( this.hasResultsTarget )
        {
            this.resultsTarget.innerHTML = data.html || '';
        }
    }

    clearResults ()
    {
        if ( this.hasResultsTarget )
        {
            this.resultsTarget.innerHTML = '';
        }
    }

    disconnect ()
    {
        if ( this.timeout )
        {
            clearTimeout( this.timeout );
        }
    }
}