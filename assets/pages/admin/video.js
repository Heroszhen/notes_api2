import $ from "jquery";

$("#video_videoFile").on("change", function () {
    $("#video_videoUrl").val(this.files[0]?.name ?? "");
});
