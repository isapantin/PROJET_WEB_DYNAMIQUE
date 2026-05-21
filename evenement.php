<?php

session_start();

require("includes/connexion_bdd.php");

/* RECUPERATION ID */

if(!isset($_GET['id'])) {

    die("Événement introuvable");
}

$id = $_GET['id'];

/* REQUETE SQL */

$sql = "SELECT * FROM evenements
WHERE id = $id";

$resultat = mysqli_query($bdd, $sql);

$event = mysqli_fetch_assoc($resultat);

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<main>

<section class="detail-evenement">

<img src="uploads/<?php echo $event['image']; ?>">

<div class="contenu-evenement">

<h2>
<?php echo $event['titre']; ?>
</h2>

<p class="categorie-detail">
<?php echo $event['categorie']; ?>
</p>

<p>
<?php echo $event['description']; ?>
</p>

<p>
📍 Lieu :
<?php echo $event['lieu']; ?>
</p>

<p>
📅 Date :
<?php echo $event['date_evenement']; ?>
</p>

<?php

$id_event = $event['id'];

$sql_places = "
SELECT COUNT(*) AS total
FROM reservations
WHERE evenement_id = $id_event
";

$resultat_places = mysqli_query(
    $bdd,
    $sql_places
);

$places = mysqli_fetch_assoc(
    $resultat_places
);

$places_restantes =
$event['capacite']
- $places['total'];

?>

<p>
👥 <?php echo $places_restantes; ?>
places restantes
</p>

<a class="btn-reserver"
href="reservation.php?id=<?php echo $event['id']; ?>">

Réserver une place

</a>

</div>

</section>

</main>

<?php include("includes/footer.php"); ?>