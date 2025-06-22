import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    connect ()
    {
        console.log( "Application controller connected" );
        this.setupGlobalKeyboardShortcuts();
    }

    setupGlobalKeyboardShortcuts ()
    {
        document.addEventListener( "keydown", ( event ) =>
        {
            // Ctrl/Cmd + K pour la recherche globale
            if ( ( event.ctrlKey || event.metaKey ) && event.key === "k" )
            {
                event.preventDefault();
                this.focusSearch();
            }
        } );
    }

    focusSearch ()
    {
        const searchInput = document.querySelector( '[data-search-target="input"]' );
        if ( searchInput )
        {
            searchInput.focus();
        }
    }
}