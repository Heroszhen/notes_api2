import $ from "jquery";
import { alertMessage } from "../../util.js";

$(".delete-category").on("click", async function (e) {
    e.preventDefault();

    if (!window.confirm("Veux-tu vraiment supprimer cette catégorie ?")) {
        return;
    }

    const response = await fetch($(this).attr("href"), {
        method: "DELETE",
        headers: { "X-Requested-With": "XMLHttpRequest" },
    });

    let message, type;
    if (response.ok) {
        message = "Supprimée";
        type = "success";
        $(this).closest(".category").remove();
    } else {
        message = "Erreur";
        type = "danger";
    }
    alertMessage(message, type);
});
