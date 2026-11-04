import $ from "jquery";
import { alertMessage } from "../../util.js";
import { setUrlAndType } from "../video_player.js";

$(function () {
    setUrlAndType($("#video_videoUrl").val(), $("#video_videoType").val());
});

$("#video_videoFile").on("change", function () {
    $("#video_videoUrl").val(this.files[0]?.name ?? "");
});

$(".delete-video").on("click", async function (e) {
    e.preventDefault();

    if (!window.confirm("Veux-tu vraiment supprimer cette vidéo ?")) {
        return;
    }

    try {
        const response = await fetch($(this).attr("href"), {
            method: "DELETE",
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });

        let message, type;
        if (response.ok) {
            message = "Supprimée";
            type = "success";
            $(this).closest("tr").remove();
        } else {
            message = "Erreur";
            type = "danger";
        }
        alertMessage(message, type);
    } catch {}
});

$("#video_videoUrl").on("change", function () {
    setUrlAndType($("#video_videoUrl").val(), $("#video_videoType").val());
});
$("#video_videoType").on("change", function () {
    setUrlAndType($("#video_videoUrl").val(), $("#video_videoType").val());
});
