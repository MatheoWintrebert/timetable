import { Controller } from "@hotwired/stimulus";

export default class extends Controller
{
    spin ()
    {
        this.element.style.transform = 'rotate(360deg) scale(1.1)';
    }

    reset ()
    {
        this.element.style.transform = 'rotate(0deg) scale(1)';
    }
}
