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

$(".delete-actress-photo").on("click", async function (e) {
    e.preventDefault();
    const response = await fetch($(this).attr("href"), {
        method: "DELETE",
        headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (response.ok) {
        $(this).closest(".wrap-photo").remove();
    }
});
