<?php

session_start();

require("includes/connexion_bdd.php");

if(!isset($_SESSION['id'])) {

    die("Vous devez être connecté");
}

$id_user = $_SESSION['id'];

/* MES RESERVATIONS */

$sql_reservations = "
SELECT evenements.*, reservations.id AS reservation_id
FROM reservations

INNER JOIN evenements
ON reservations.evenement_id = evenements.id

WHERE reservations.utilisateur_id = $id_user
";

$resultat_reservations = mysqli_query(
    $bdd,
    $sql_reservations
);

/* MES EVENEMENTS */

$sql_evenements = "
SELECT *
FROM evenements

WHERE organisateur_id = $id_user
";

$resultat_evenements = mysqli_query(
    $bdd,
    $sql_evenements
);

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<main>

<section class="profil-section">

<h2>
Mon Profil
</h2>

<div class="infos-profil">

<p>
<strong>Nom :</strong>
<?php echo $_SESSION['nom']; ?>
</p>

<p>
<strong>Prénom :</strong>
<?php echo $_SESSION['prenom']; ?>
</p>

<p>
<strong>Rôle :</strong>
<?php echo $_SESSION['role']; ?>
</p>

</div>

<!-- RESERVATIONS -->

<h3 class="titre-profil">
Mes réservations
</h3>

<div class="grille-evenements">

<?php while($event =
mysqli_fetch_assoc($resultat_reservations)) { ?>

<div class="carte-evenement">

<img src="uploads/<?php echo $event['image']; ?>">

<h3>
<?php echo $event['titre']; ?>
</h3>

<p>
📅 <?php echo $event['date_evenement']; ?>
</p>

<p>
📍 <?php echo $event['lieu']; ?>
</p>

<div class="zone-qr">

<img class="qr-code"
src="qrcodes/qr_<?php echo $event['reservation_id']; ?>.png">

</div>

<a class="btn-supprimer"

href="supprimer_reservation.php?id=<?php echo $event['reservation_id']; ?>"

onclick="return confirm(
'Annuler cette réservation ?'
)">

Annuler la réservation

</a>

</div>

<?php } ?>

</div>

<!-- EVENEMENTS CREES -->

<?php
if($_SESSION['role'] == 'organisateur') {
?>

<h3 class="titre-profil">
Mes événements créés
</h3>

<div class="grille-evenements">

<?php while($event =
mysqli_fetch_assoc($resultat_evenements)) { ?>

<div class="carte-evenement">

<img src="uploads/<?php echo $event['image']; ?>">

<h3>
<?php echo $event['titre']; ?>
</h3>

<p>
📅 <?php echo $event['date_evenement']; ?>
</p>

<p>
📍 <?php echo $event['lieu']; ?>
</p>

<a class="btn-supprimer"

href="supprimer_evenement.php?id=<?php echo $event['id']; ?>"

onclick="return confirm(
'Supprimer cet événement ?'
)">

Supprimer

</a>

</div>

<?php } ?>

</div>

<?php } ?>

</section>

</main>

<?php include("includes/footer.php"); ?>