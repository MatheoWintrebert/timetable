// assets/controllers/tabs_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static targets = [ "tab" ];
    static classes = [ "active" ];

    connect ()
    {
        console.log( "Tabs controller connected" );
        this.setActiveTab();
    }

    // Action appelée lors du clic sur un onglet
    selectTab ( event )
    {
        const clickedTab = event.currentTarget;

        // Retirer la classe active de tous les onglets du même groupe
        this.tabTargets.forEach( tab =>
        {
            tab.classList.remove( this.activeClass || 'active' );
        } );

        // Ajouter la classe active à l'onglet cliqué
        clickedTab.classList.add( this.activeClass || 'active' );

        // Sauvegarder l'onglet actif dans sessionStorage si désiré
        const tabId = clickedTab.dataset.tabId;
        if ( tabId )
        {
            sessionStorage.setItem( `activeTab_${ this.element.dataset.tabGroup }`, tabId );
        }
    }

    // Définir l'onglet actif au chargement
    setActiveTab ()
    {
        const currentPath = window.location.pathname;
        const savedTabId = sessionStorage.getItem( `activeTab_${ this.element.dataset.tabGroup }` );

        let activeTab = null;

        // Essayer de trouver l'onglet actif basé sur l'URL
        this.tabTargets.forEach( tab =>
        {
            if ( tab.href && tab.href.includes( currentPath ) )
            {
                activeTab = tab;
            }
        } );

        // Si pas trouvé par URL, utiliser l'onglet sauvegardé
        if ( !activeTab && savedTabId )
        {
            activeTab = this.tabTargets.find( tab => tab.dataset.tabId === savedTabId );
        }

        // Si toujours pas trouvé, utiliser le premier onglet
        if ( !activeTab && this.tabTargets.length > 0 )
        {
            activeTab = this.tabTargets[ 0 ];
        }

        // Appliquer l'état actif
        if ( activeTab )
        {
            this.tabTargets.forEach( tab =>
            {
                tab.classList.remove( this.activeClass || 'active' );
            } );
            activeTab.classList.add( this.activeClass || 'active' );
        }
    }

    // Méthode pour réinitialiser les onglets (utile après navigation Turbo)
    refresh ()
    {
        this.setActiveTab();
    }
}