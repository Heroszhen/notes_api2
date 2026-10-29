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
import { readFile } from "./util.js";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

setTimeout(() => {
    $("#flash-messages .alert").remove();
}, 3000);

$(".photo-input").on("change", async function (e) {
    const file = $(this).prop("files")[0];
    if (file) {
        const base64 = await readFile(e.target.files.item(0));
        $(this)
            .parent()
            .find(".preview-photo")
            .html(`<img src='${base64}' alt=''>`);
    }
});
