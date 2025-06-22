import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    static values = { message: String };

    ask ( event )
    {
        const message = this.messageValue || 'Êtes-vous sûr ?';
        if ( !confirm( message ) )
        {
            event.preventDefault();
            event.stopPropagation();
        }
    }
}