<?php

$bdd = mysqli_connect(
    "fdb1031.your-hosting.net",
    "4760659_omneseventasso",
    "Isa123465.",
    "4760659_omneseventasso",
    3306
);

if(!$bdd) {

    die(mysqli_connect_error());
}

?>