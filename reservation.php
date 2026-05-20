<?php

session_start();

require("includes/connexion_bdd.php");

/* VERIFICATION CONNEXION */

if(!isset($_SESSION['id'])) {

    die("Vous devez être connecté");
}

/* RECUPERATION ID EVENEMENT */

if(!isset($_GET['id'])) {

    die("Événement introuvable");
}

$id_event = $_GET['id'];

$id_user = $_SESSION['id'];

/* RECUPERATION EVENEMENT */

$sql_event = "SELECT * FROM evenements
WHERE id = $id_event";

$resultat_event = mysqli_query($bdd, $sql_event);

$event = mysqli_fetch_assoc($resultat_event);

/* VERIFIER SI DEJA RESERVE */

$sql_verif = "SELECT * FROM reservations
WHERE utilisateur_id = $id_user
AND evenement_id = $id_event";

$resultat_verif = mysqli_query($bdd, $sql_verif);

/* COMPTER RESERVATIONS */

$sql_count = "SELECT COUNT(*) AS total
FROM reservations
WHERE evenement_id = $id_event";

$resultat_count = mysqli_query($bdd, $sql_count);

$data_count = mysqli_fetch_assoc($resultat_count);

$total_reservations = $data_count['total'];

/* MESSAGE */

$message = "";

/* RESERVATION */

if(mysqli_num_rows($resultat_verif) > 0) {

    $message = "Vous avez déjà réservé";

} else {

    if($total_reservations >= $event['capacite']) {

        $message = "Événement complet";

    } else {

        $sql_insert = "INSERT INTO reservations
        (utilisateur_id, evenement_id)

        VALUES

        ('$id_user', '$id_event')";

        if(mysqli_query($bdd, $sql_insert)) {

            $message = "Réservation réussie";

        } else {

            $message = "Erreur lors de la réservation";
        }
    }
}

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<main>

<section class="formulaire-section">

<h2>
Réservation
</h2>

<p class="message">
<?php echo $message; ?>
</p>

<a class="btn-reserver"
href="index.php">

Retour accueil

</a>

</section>

</main>

<?php include("includes/footer.php"); ?>