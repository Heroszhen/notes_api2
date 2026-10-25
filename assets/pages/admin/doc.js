import $ from "jquery";

$("a.delete-doc").on("click", function (e) {
    e.preventDefault();

    if (confirm("Voulez-vous vraiment supprimer ce document ?")) {
        document.location.assign($(this).attr("href"));
    }
});

$("#document_search_keywords").on("input", function (e) {
    if (!(e.originalEvent instanceof InputEvent)) {
        $("form[name='document_search']").trigger("submit");
    }
});
