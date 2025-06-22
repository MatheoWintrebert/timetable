import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    connect ()
    {
        this.element.addEventListener( 'mouseenter', this.onMouseEnter.bind( this ) );
        this.element.addEventListener( 'mouseleave', this.onMouseLeave.bind( this ) );
    }

    onMouseEnter ()
    {
        this.element.style.transform = 'translateY(-10px) scale(1.02)';
    }

    onMouseLeave ()
    {
        this.element.style.transform = 'translateY(0) scale(1)';
    }
}