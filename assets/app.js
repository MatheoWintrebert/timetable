// Import du CSS principal
import './styles/app.css';

// Import de Turbo pour la navigation fluide
import '@hotwired/turbo';

// Import de Stimulus
import './bootstrap.js';

console.log( '✅ EDT Manager loaded with AssetMapper!' );

// Configuration Turbo
document.addEventListener( "turbo:load", () =>
{
  console.log( "🚀 Turbo: Page loaded" );

  // Auto-focus sur le premier input visible
  const firstInput = document.querySelector( 'input:not([type="hidden"]):not([autofocus]), select:not([autofocus]), textarea:not([autofocus])' );
  if ( firstInput && !document.querySelector( '[autofocus]' ) )
  {
    firstInput.focus();
  }
} );

// Utilitaires globaux
window.EDT = {
  confirmDelete: ( message = 'Êtes-vous sûr de vouloir supprimer cet élément ?' ) =>
  {
    return confirm( message );
  },

  notify: ( message, type = 'info' ) =>
  {
    const flashContainer = document.getElementById( 'flash' );
    if ( flashContainer )
    {
      const alert = document.createElement( 'div' );
      alert.className = `alert alert-${ type } flash-message`;
      alert.innerHTML = `
        <div class="alert-icon">
          ${ type === 'success' ? '🎉' : type === 'error' ? '⚠️' : type === 'warning' ? '🔔' : 'ℹ️' }
        </div>
        <div class="alert-content">
          <div class="alert-message">${ message }</div>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">
          <span aria-hidden="true">×</span>
        </button>
      `;
      flashContainer.appendChild( alert );

      // Auto-hide après 5 secondes
      setTimeout( () =>
      {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        setTimeout( () => alert.remove(), 300 );
      }, 5000 );
    }
  }
};