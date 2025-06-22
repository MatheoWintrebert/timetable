import "./bootstrap.js";
import { start } from "@hotwired/turbo";

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
start();
import "./styles/app.css";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");
document.addEventListener("turbo:load", () => {
  console.log("Turbo is working!");
});
