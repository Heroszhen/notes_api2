import $ from "jquery";

/**
 *
 * @param {string} videoUrl
 * @param {string} videoType
 * @param {number} height [height = 700]
 * @param {File|null} file
 */
function loadIframe(videoUrl, videoType, height = 700, file = undefined) {
    const iframe = $("#video-player iframe#player")[0];
    if (!iframe) {
        return;
    }

    $("#video-player iframe#player").parent().removeClass("d-none");
    $("#video-player iframe#player").css("height", height);

    iframe.contentWindow.postMessage(
        {
            type: "READY",
            payload: {
                url: videoUrl,
                type: videoType,
                height: height,
                file: file,
            },
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
 */
export const setUrlAndTypeAfterLoadedIframe = (
    videoUrl,
    videoType,
    height = 700
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
    height = 700,
    file = undefined
) => {
    if (videoType && videoUrl) {
        if (videoType === "extern") return;

        window.removeEventListener("message", resizeIframe);
        setTimeout(() => loadIframe(videoUrl, videoType, height, file));
    }
};
