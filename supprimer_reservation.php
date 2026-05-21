<?php

session_start();

require("includes/connexion_bdd.php");

/* VERIFICATION CONNEXION */

if(!isset($_SESSION['id'])) {

    die("Vous devez être connecté");
}

/* VERIFICATION ID */

if(!isset($_GET['id'])) {

    die("Réservation introuvable");
}

$id_reservation = $_GET['id'];

$id_user = $_SESSION['id'];

/* VERIFIER QUE LA RESERVATION
APPARTIENT A L'UTILISATEUR */

$sql = "
SELECT *
FROM reservations

WHERE id = $id_reservation

AND utilisateur_id = $id_user
";

$resultat = mysqli_query(
    $bdd,
    $sql
);

if(mysqli_num_rows($resultat) == 0) {

    die("Accès refusé");
}

/* SUPPRESSION */

mysqli_query(
    $bdd,
    "DELETE FROM reservations
    WHERE id = $id_reservation"
);

/* REDIRECTION */

header("Location: profil.php");

exit();

?>