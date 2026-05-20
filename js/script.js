$(document).ready(function () {

    // Menu burger mobile 
    $('#btn-burger').click(function () {
        $('#nav-mobile').toggleClass('hidden').toggleClass('flex');
    });

    // Recherche en temps réel sur la page événements
    $('#champ-recherche').on('keyup', function () {
        var terme = $(this).val().toLowerCase();
        $('.carte-evenement').each(function () {
            var texte = $(this).text().toLowerCase();
            if (texte.indexOf(terme) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Filtrage par catégorie 
    $('#filtre-categorie').on('change', function () {
        var categorie = $(this).val().toLowerCase();
        if (categorie === 'tout') {
            $('.carte-evenement').show();
        } else {
            $('.carte-evenement').each(function () {
                if ($(this).data('categorie') === categorie) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Confirmation avant suppression
    $('.btn-supprimer, .btn-annuler').on('click', function (e) {
        var message = $(this).data('message') || 'Êtes-vous sûr ?';
        if (!confirm(message)) {
            e.preventDefault();
        }
    });

    // Affichage du nom du fichier uploadé
    $('#affiche').on('change', function () {
        var nomFichier = $(this).val().split('\\').pop();
        $('#nom-fichier').text(nomFichier || 'Aucun fichier choisi');
    });

    // Compte à rebours
    $('.compte-a-rebours').each(function () {
        var dateEvenement = new Date($(this).data('date'));
        var element = $(this);

        function mettreAJour() {
            var diff = dateEvenement - new Date();
            if (diff <= 0) { element.text('En cours ou terminé'); return; }
            var jours   = Math.floor(diff / (1000 * 60 * 60 * 24));
            var heures  = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            element.text(jours + 'j ' + heures + 'h ' + minutes + 'min');
        }

        mettreAJour();
        setInterval(mettreAJour, 60000);
    });

    // Messages flash - disparition auto après 4s
    setTimeout(function () {
        $('.message-succes, .message-erreur').fadeOut(500);
    }, 4000);

});