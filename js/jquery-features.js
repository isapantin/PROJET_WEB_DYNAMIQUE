// jQuery

$(document).ready(function () {

    // Menu burger (mobile)
    $('#burger').click(function () {
        $('#nav-liste').slideToggle(200);
    });

    // Dropdown Bibliothèque
    $('#dropdown-btn').click(function (e) {
        e.preventDefault();
        $('#dropdown-menu').slideToggle(200);
    });

    // Fermer le dropdown si on clique ailleurs
    $(document).click(function (e) {
        if (!$(e.target).closest('#dropdown-parent').length) {
            $('#dropdown-menu').slideUp(200);
        }
    });

});
