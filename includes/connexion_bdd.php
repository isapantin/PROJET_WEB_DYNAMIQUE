<?php

$bdd = mysqli_connect(
    "localhost",
    "root",
    "",
    "omnesevent"
);

if(!$bdd) {

    die("Erreur connexion : "
    . mysqli_connect_error());
}

?>