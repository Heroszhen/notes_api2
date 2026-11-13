import $ from "jquery";

export const defaultVideoHeight = 700;

/**
 *
 * @param {string} videoUrl
 * @param {string} videoType
 * @param {number} height [height = 700]
 * @param {File|undefined} file
 */
function loadIframe(videoUrl, videoType, height = defaultVideoHeight, file) {
    const iframe = $("#video-player iframe#player")[0];
    if (!iframe) {
        return;
    }

    $("#video-player iframe#player").parent().removeClass("d-none");
    $("#video-player iframe#player").css("height", height);

    const payload = {
        url: videoUrl,
        type: videoType,
        height: height,
        maxWidth: $("#wrap-iframe").width(),
        file: file,
    };
    window.videoPayload = payload;

    iframe.contentWindow.postMessage(
        {
            type: "READY",
            payload: payload,
        },
        $("#video-player #front-service-url").val()
    );

    window.addEventListener("message", resizeIframe);
}

function resizeIframe(event) {
    const iframe = $("#video-player iframe#player")[0];
    if (event.origin !== $("#video-player #front-service-url").val()) return;

    if (event.source !== iframe.contentWindow) return;

    const data = event.data;
    if (data?.type === "RESIZE") {
        iframe.style.height = data.payload.videoHeight + "px";
    }
}

/**
 *
 * @param {string|undefined} videoUrl
 * @param {string|undefined} videoType
 * @param {number} height
 * @returns
 */
export const setUrlAndTypeAfterLoadedIframe = (
    videoUrl,
    videoType,
    height = defaultVideoHeight
) => {
    if (videoType && videoUrl) {
        if (videoType === "extern") return;
        $("#video-player iframe#player").on("load", () => {
            setTimeout(() => setUrlAndType(videoUrl, videoType, height));
        });
    }
};

/**
 *
 * @param {string} videoUrl
 * @param {string} videoType
 * @param {number} height
 * @param {File|undefined} file
 * @returns
 */
export const setUrlAndType = (
    videoUrl,
    videoType,
    height = defaultVideoHeight,
    file
) => {
    if (videoType && videoUrl) {
        if (videoType === "extern") return;

        window.removeEventListener("message", resizeIframe);
        setTimeout(() => loadIframe(videoUrl, videoType, height, file));
    }
};

$(".toggle-video-player-fullscreen").on("click", function () {
    if (!window.videoPayload) {
        return;
    }

    $("#video-player").toggleClass("fullscreen");

    if ($("#video-player").hasClass("fullscreen")) {
        $("#video-player .wrap-btns").removeClass("d-none");
    } else {
        $("#video-player .wrap-btns").addClass("d-none");
    }

    setUrlAndType(
        window.videoPayload.url,
        window.videoPayload.type,
        $("#video-player").hasClass("fullscreen")
            ? $(window).height() - 45
            : defaultVideoHeight,
        window.videoPayload.file
    );
});

$("#video-player .reload-video-player").on("click", () => {
    if (!window.videoPayload) {
        return;
    }
    const file = window.videoPayload.file;

    setUrlAndType(
        window.videoPayload.url,
        window.videoPayload.type,
        $(window).height() - 45,
        file
    );
});
