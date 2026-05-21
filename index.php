<?php

session_start();

require("includes/connexion_bdd.php");

/* RECHERCHE */

$recherche = "";

$categorie = "";

/* REQUETE SQL */

$sql = "SELECT * FROM evenements
WHERE date_evenement >= CURDATE()";

/* RECHERCHE TEXTE */

if(isset($_GET['recherche'])
&& !empty($_GET['recherche'])) {

    $recherche = mysqli_real_escape_string(
        $bdd,
        $_GET['recherche']
    );

    $sql .= " AND (

        titre LIKE '%$recherche%'

        OR description LIKE '%$recherche%'

        OR lieu LIKE '%$recherche%'

        OR categorie LIKE '%$recherche%'

    )";
}

/* FILTRE CATEGORIE */

if(isset($_GET['categorie'])
&& !empty($_GET['categorie'])) {

    $categorie = mysqli_real_escape_string(
        $bdd,
        $_GET['categorie']
    );

    $sql .= " AND categorie = '$categorie'";
}

/* TRI PAR DATE */

$sql .= " ORDER BY date_evenement ASC";

/* EXECUTION */

$resultat = mysqli_query($bdd, $sql);

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<?php

if(isset($_SESSION['prenom'])) {

    echo "<p class='bonjour'>Bonjour "
    . $_SESSION['prenom'] .
    "</p>";
}

?>

<main>

<section class="hero">

<h2>Bienvenue sur OmnesEvent</h2>

<p>
Plateforme de gestion des événements étudiants d’Omnes.
</p>

</section>

<section class="recherche-section">

<form method="GET" class="form-recherche">

<input type="text"
       name="recherche"
       placeholder="Rechercher un événement"
       value="<?php echo $recherche; ?>">

<select name="categorie">

<option value="">
Toutes les catégories
</option>

<option value="Soirée"
<?php if($categorie == "Soirée") echo "selected"; ?>>
Soirée
</option>

<option value="Sport"
<?php if($categorie == "Sport") echo "selected"; ?>>
Sport
</option>

<option value="Culture"
<?php if($categorie == "Culture") echo "selected"; ?>>
Culture
</option>

<option value="Conférence"
<?php if($categorie == "Conférence") echo "selected"; ?>>
Conférence
</option>

</select>

<button type="submit">
Rechercher
</button>

</form>

</section>

<!-- EVENEMENTS -->

<section class="evenements">

<h2 class="titre-section">
Événements à venir
</h2>

<div class="grille-evenements">

<?php while($event = mysqli_fetch_assoc($resultat)) { ?>

<div class="carte-evenement">

<img src="uploads/<?php echo $event['image']; ?>">

<h3>
<?php echo $event['titre']; ?>
</h3>

<p>
<?php echo $event['description']; ?>
</p>

<p>
📍 <?php echo $event['lieu']; ?>
</p>

<p>
📅 <?php echo $event['date_evenement']; ?>
</p>

<p>
👥 <?php echo $event['capacite']; ?> places
</p>

<p class="categorie">
<?php echo $event['categorie']; ?>
</p>

<a href="evenement.php?id=<?php echo $event['id']; ?>">
Voir plus
</a>

</div>

<?php } ?>

</div>

</section>

</main>

<?php include("includes/footer.php"); ?>