import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    connect ()
    {
        this.element.addEventListener( 'mouseenter', this.onHover.bind( this ) );
        this.element.addEventListener( 'mouseleave', this.onLeave.bind( this ) );
    }

    onHover ()
    {
        this.element.style.transform = 'translateY(-2px)';
        this.element.style.boxShadow = '0 8px 25px rgba(8, 164, 189, 0.3)';
    }

    onLeave ()
    {
        this.element.style.transform = 'translateY(0)';
        this.element.style.boxShadow = '';
    }
}