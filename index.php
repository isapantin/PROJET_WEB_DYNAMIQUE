<?php

session_start();

require("includes/connexion_bdd.php");

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<?php

if(isset($_SESSION['prenom'])) {

    echo "<p class='bonjour'>Bonjour "
    . $_SESSION['prenom'] .
    "</p>";
}

?>

<main>

    <section class="hero">

        <h2>Bienvenue sur OmnesEvent</h2>

        <p>
            Plateforme de gestion des événements étudiants d’Omnes.
        </p>

    </section>

</main>

<?php include("includes/footer.php"); ?>