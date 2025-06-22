import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "searchInput", "tableBody", "filtersSection", "emptyState" ];
    static values = {
        autoFilter: Boolean,
        showStats: Boolean
    };

    connect ()
    {
        console.log( "📋 Matiere list controller connected" );
        this.originalRows = Array.from( this.tableBodyTarget.querySelectorAll( 'tr' ) );
        this.setupSearch();
        this.updateStats();
    }

    setupSearch ()
    {
        if ( !this.hasSearchInputTarget ) return;

        this.searchTimeout = null;

        this.searchInputTarget.addEventListener( 'input', ( event ) =>
        {
            clearTimeout( this.searchTimeout );
            this.searchTimeout = setTimeout( () =>
            {
                this.filterTable( event.target.value );
            }, 300 );
        } );
    }

    search ( event )
    {
        this.filterTable( event.target.value );
    }

    filterTable ( searchTerm )
    {
        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;

        this.originalRows.forEach( row =>
        {
            const nomMatiere = row.querySelector( '.cell-main' )?.textContent.toLowerCase() || '';
            const colorCode = row.querySelector( '.color-code' )?.textContent.toLowerCase() || '';

            const matches = nomMatiere.includes( term ) || colorCode.includes( term );

            if ( matches )
            {
                row.style.display = '';
                visibleCount++;
                // Animation d'apparition
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                setTimeout( () =>
                {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 50 );
            } else
            {
                row.style.display = 'none';
            }
        } );

        this.toggleEmptyState( visibleCount === 0 );
        this.updateStats( visibleCount );
    }

    toggleEmptyState ( show )
    {
        // Créer un message de "pas de résultats" si nécessaire
        let noResultsMsg = this.element.querySelector( '.no-results-message' );

        if ( show && !noResultsMsg )
        {
            noResultsMsg = document.createElement( 'div' );
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.style.cssText = `
                text-align: center;
                padding: 3rem 2rem;
                color: var(--text-muted);
                font-size: 1.1rem;
                animation: fadeInUp 0.3s ease-out;
            `;
            noResultsMsg.innerHTML = `
                <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.7;">🔍</div>
                <h3 style="color: var(--accent-blue); margin-bottom: 1rem; font-size: 1.5rem;">Aucun résultat trouvé</h3>
                <p style="margin: 0;">Essayez de modifier votre recherche ou créez une nouvelle matière.</p>
            `;

            // Insérer après le tableau
            const tableContainer = this.element.querySelector( '.table-responsive' );
            if ( tableContainer )
            {
                tableContainer.parentNode.insertBefore( noResultsMsg, tableContainer.nextSibling );
            }
        } else if ( !show && noResultsMsg )
        {
            noResultsMsg.remove();
        }

        // Gérer la visibilité du tableau
        const tableContainer = this.element.querySelector( '.table-responsive' );
        if ( tableContainer )
        {
            tableContainer.style.display = show ? 'none' : '';
        }
    }

    updateStats ( visibleCount = null )
    {
        const statsCards = this.element.querySelectorAll( '.stat-value' );
        if ( statsCards.length === 0 ) return;

        const totalCount = visibleCount !== null ? visibleCount : this.originalRows.length;
        const uniqueColors = new Set();

        this.originalRows.forEach( row =>
        {
            if ( visibleCount === null || row.style.display !== 'none' )
            {
                const colorCode = row.querySelector( '.color-code' )?.textContent.trim();
                if ( colorCode )
                {
                    uniqueColors.add( colorCode );
                }
            }
        } );

        // Mettre à jour les cartes de statistiques
        if ( statsCards[ 0 ] ) statsCards[ 0 ].textContent = totalCount;
        if ( statsCards[ 1 ] ) statsCards[ 1 ].textContent = uniqueColors.size;
    }

    // Action pour supprimer une matière avec animation
    deleteMatiere ( event )
    {
        const row = event.target.closest( 'tr' );
        if ( !row ) return;

        // Animation de suppression
        row.style.transform = 'translateX(-100%)';
        row.style.opacity = '0';

        setTimeout( () =>
        {
            row.remove();
            this.originalRows = this.originalRows.filter( r => r !== row );
            this.updateStats();
        }, 300 );
    }

    // Action pour rafraîchir la liste
    refresh ()
    {
        console.log( "🔄 Refreshing matiere list" );

        if ( this.hasSearchInputTarget )
        {
            this.searchInputTarget.value = '';
        }

        this.originalRows.forEach( row =>
        {
            row.style.display = '';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        } );

        this.toggleEmptyState( false );
        this.updateStats();
    }

    disconnect ()
    {
        if ( this.searchTimeout )
        {
            clearTimeout( this.searchTimeout );
        }
        console.log( "📋 Matiere list controller disconnected" );
    }
}
