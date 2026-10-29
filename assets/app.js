import "./stimulus_bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "bootstrap-icons/font/bootstrap-icons.min.css";
import "./styles/app.scss";

import $ from "jquery";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

setTimeout(() => {
    $(".alert").remove();
}, 3000);
