<?php

session_start();

require("includes/connexion_bdd.php");

/* RECUPERATION EVENEMENTS */

$sql = "SELECT * FROM evenements
ORDER BY date_evenement ASC";

$resultat = mysqli_query($bdd, $sql);

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

<!-- EVENEMENTS -->

<section class="evenements">

<h2 class="titre-section">
Événements à venir
</h2>

<div class="grille-evenements">

<?php while($event = mysqli_fetch_assoc($resultat)) { ?>

<div class="carte-evenement">

<img src="uploads/<?php echo $event['image']; ?>">

<h3>
<?php echo $event['titre']; ?>
</h3>

<p>
<?php echo $event['description']; ?>
</p>

<p>
📍 <?php echo $event['lieu']; ?>
</p>

<p>
📅 <?php echo $event['date_evenement']; ?>
</p>

<p>
👥 <?php echo $event['capacite']; ?> places
</p>

<p class="categorie">
<?php echo $event['categorie']; ?>
</p>

<a href="evenement.php?id=<?php echo $event['id']; ?>">
Voir plus
</a>

</div>

<?php } ?>

</div>

</section>

</main>

<?php include("includes/footer.php"); ?>