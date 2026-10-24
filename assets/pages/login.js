import $ from "jquery";

$("#toggle-password-input").on("change", function () {
    if ($(this).prop("checked")) $("#password").attr("type", "text");
    else $("#password").attr("type", "password");
});
