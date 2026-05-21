<?php

session_start();

require("includes/connexion_bdd.php");

/* VERIFICATION CONNEXION */

if(!isset($_SESSION['id'])) {

    die("Vous devez être connecté");
}

/* VERIFICATION ID EVENEMENT */

if(!isset($_GET['id'])) {

    die("Événement introuvable");
}

$id_event = $_GET['id'];

$id_user = $_SESSION['id'];

/* VERIFIER QUE L'EVENEMENT
APPARTIENT A L'ORGANISATEUR */

$sql = "SELECT * FROM evenements

WHERE id = $id_event

AND organisateur_id = $id_user";

$resultat = mysqli_query($bdd, $sql);

/* SI L'EVENEMENT N'EXISTE PAS */

if(mysqli_num_rows($resultat) == 0) {

    die("Accès refusé");
}

/* SUPPRESSION DES RESERVATIONS */

mysqli_query(
    $bdd,
    "DELETE FROM reservations
    WHERE evenement_id = $id_event"
);

/* SUPPRESSION EVENEMENT */

mysqli_query(
    $bdd,
    "DELETE FROM evenements
    WHERE id = $id_event"
);

/* REDIRECTION */

header("Location: profil.php");

exit();

?>