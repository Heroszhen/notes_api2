import $ from "jquery";
import { alertMessage } from "../../util.js";
import {
    setUrlAndTypeAfterLoadedIframe,
    setUrlAndType,
    defaultVideoHeight,
} from "../video_player.js";

$(function () {
    setUrlAndTypeAfterLoadedIframe(
        $("#video_videoUrl").val(),
        $("#video_videoType").val(),
        defaultVideoHeight
    );
});

// $("#video_videoFile").on("change", function () {
//     $("#video_videoUrl").val(this.files[0]?.name ?? "");
// });

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
    } catch {
        alertMessage("Erreur", "danger");
    }
});

$("#delete-video-to-next").on("click", function (e) {
    e.preventDefault();

    if (!window.confirm("Veux-tu vraiment supprimer cette vidéo ?")) {
        return;
    }

    location.replace($(this).prop("href"));
});

$("#video_videoUrl").on("change", function () {
    setUrlAndType(
        $("#video_videoUrl").val(),
        $("#video_videoType").val(),
        defaultVideoHeight
    );
});
$("#video_videoType").on("change", function () {
    setUrlAndType(
        $("#video_videoUrl").val(),
        $("#video_videoType").val(),
        defaultVideoHeight
    );
});

$("#video_videoFile").on("change", function () {
    setUrlAndType("url", "mp4", defaultVideoHeight, $(this).prop("files")[0]);
});

$(".activate-video").on("change", async function () {
    try {
        const response = await fetch($(this).data("url"), {
            method: "PATCH",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ visible: $(this).prop("checked") }),
        });

        let message, type;
        if (response.ok) {
            message = "Enregistrée";
            type = "success";
        } else {
            message = "Erreur";
            type = "danger";
        }
        alertMessage(message, type);
    } catch {
        alertMessage("Erreur", "danger");
    }
});

$("#reload-video-player").on("click", () => {
    const file = $("#video_videoFile").prop("files")?.[0];

    setUrlAndType(
        file ? "url" : $("#video_videoUrl").val(),
        file ? "mp4" : $("#video_videoType").val(),
        defaultVideoHeight,
        file
    );
});
