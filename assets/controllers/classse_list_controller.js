import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "searchInput", "filterSelect", "tableBody", "emptyState", "statsCards" ];
    static values = {
        autoFilter: Boolean,
        showStats: Boolean
    };

    connect ()
    {
        console.log( "📋 Classe list controller connected" );
        this.originalRows = Array.from( this.tableBodyTarget.querySelectorAll( 'tr' ) );
        this.setupSearch();
        this.setupFilters();
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

    setupFilters ()
    {
        if ( !this.hasFilterSelectTarget ) return;

        this.filterSelectTarget.addEventListener( 'change', ( event ) =>
        {
            this.filterByLevel( event.target.value );
        } );
    }

    filterTable ( searchTerm )
    {
        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;

        this.originalRows.forEach( row =>
        {
            const nomClasse = row.querySelector( '.cell-main' )?.textContent.toLowerCase() || '';
            const niveau = row.querySelector( '.badge-custom' )?.textContent.toLowerCase() || '';

            const matches = nomClasse.includes( term ) || niveau.includes( term );

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

    filterByLevel ( level )
    {
        let visibleCount = 0;

        this.originalRows.forEach( row =>
        {
            const rowLevel = row.querySelector( '.badge-custom' )?.textContent.trim() || '';

            const matches = level === '' || rowLevel.includes( level );

            if ( matches )
            {
                row.style.display = '';
                visibleCount++;
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
        if ( this.hasEmptyStateTarget )
        {
            this.emptyStateTarget.style.display = show ? 'block' : 'none';
        }

        if ( this.hasTableBodyTarget )
        {
            this.tableBodyTarget.style.display = show ? 'none' : '';
        }
    }

    updateStats ( visibleCount = null )
    {
        if ( !this.hasStatsCardsTarget ) return;

        const totalCount = visibleCount !== null ? visibleCount : this.originalRows.length;
        const niveauxUniques = new Set();

        this.originalRows.forEach( row =>
        {
            if ( visibleCount === null || row.style.display !== 'none' )
            {
                const niveau = row.querySelector( '.badge-custom' )?.textContent.trim();
                if ( niveau )
                {
                    niveauxUniques.add( niveau );
                }
            }
        } );

        // Mettre à jour les cartes de statistiques
        const statValues = this.statsCardsTarget.querySelectorAll( '.stat-value' );
        if ( statValues[ 0 ] ) statValues[ 0 ].textContent = totalCount;
        if ( statValues[ 1 ] ) statValues[ 1 ].textContent = niveauxUniques.size;
    }

    // Action pour supprimer une classe avec animation
    deleteClasse ( event )
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
        console.log( "🔄 Refreshing classe list" );

        // Réinitialiser les filtres
        if ( this.hasSearchInputTarget )
        {
            this.searchInputTarget.value = '';
        }

        if ( this.hasFilterSelectTarget )
        {
            this.filterSelectTarget.value = '';
        }

        // Rendre toutes les lignes visibles
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
        console.log( "📋 Classe list controller disconnected" );
    }
}