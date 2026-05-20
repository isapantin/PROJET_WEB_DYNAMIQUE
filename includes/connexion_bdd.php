<?php

mysqli_report(MYSQLI_REPORT_OFF);

$bdd = mysqli_init();

mysqli_real_connect(
    $bdd,
    "fdb1031.your-hosting.net",
    "4760659_omneseventasso",
    "Isa123465.",
    "4760659_omneseventasso",
    3306
);

if(mysqli_connect_errno()) {

    die("Erreur connexion : "
    . mysqli_connect_error());
}

?>