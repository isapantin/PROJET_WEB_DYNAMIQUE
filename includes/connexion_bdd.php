<?php

$bdd = mysqli_connect(
    "localhost",
    "root",
    "",
    "omnesevent"
);

if(!$bdd) {
    die("Erreur de connexion à la base de données");
}

?>