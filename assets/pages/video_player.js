import $ from "jquery";

/**
 *
 * @param {string} videoUrl
 * @param {string} videoType
 * @param {number} height [height = 700]
 */
function loadIframe(videoUrl, videoType, height = 700) {
    const iframe = $("#video-player iframe#player")[0];
    if (!iframe) {
        return;
    }

    $("#video-player iframe#player").parent().removeClass("d-none");
    $("#video-player iframe#player").css("height", height);
    console.log(videoType, videoUrl);
    iframe.contentWindow.postMessage(
        {
            type: "READY",
            payload: {
                url: videoUrl,
                type: videoType,
                height: height,
            },
        },
        $("#video-player #front-service-url").val()
    );

    resizeIframe(iframe);
}

function resizeIframe(iframe) {
    window.addEventListener("message", (event) => {
        if (event.origin !== $("#video-player #front-service-url").val())
            return;

        if (event.source !== iframe.contentWindow) return;

        const data = event.data;
        if (data?.type === "RESIZE") {
            iframe.style.height = data.payload.videoHeight + "px";
        }
    });
}

/**
 *
 * @param {string|undefined} videoUrl
 * @param {string|undefined} videoType
 */
export const setUrlAndType = (videoUrl, videoType, height = 700) => {
    if (videoType && videoUrl) {
        if (videoType === "extern") return;

        $("#video-player iframe#player").on("load", () => {
            setTimeout(() => loadIframe(videoUrl, videoType, height));
        });
    }
};
