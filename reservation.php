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