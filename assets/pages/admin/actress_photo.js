import $ from "jquery";

$("#actress_photo_photos").on("change", function () {
    $("#wrap-photos").empty();
    Array.from($(this).prop("files")).forEach((file) => {
        $("#wrap-photos").append(`
            <div class='col-6 col-md-4 col-lg-3 mb-3'>
                <img src=${URL.createObjectURL(file)} alt=''>
            </div>
        `);
    });
});
