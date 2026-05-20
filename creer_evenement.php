<?php

session_start();

require("includes/connexion_bdd.php");

if(!isset($_SESSION['role'])
|| $_SESSION['role'] != 'organisateur') {

    die("Accès refusé");
}

$message = "";

if(isset($_POST['creer'])) {

    $titre = mysqli_real_escape_string($bdd, $_POST['titre']);

    $description = mysqli_real_escape_string(
        $bdd,
        $_POST['description']
    );

    $date = $_POST['date_evenement'];

    $lieu = mysqli_real_escape_string($bdd, $_POST['lieu']);

    $categorie = $_POST['categorie'];

    $capacite = $_POST['capacite'];

    $organisateur_id = $_SESSION['id'];

    /* IMAGE */

    $image = $_FILES['image']['name'];

    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file(
        $tmp,
        "uploads/" . $image
    );

    /* INSERT SQL */

    $sql = "INSERT INTO evenements
    (titre, description, date_evenement,
    lieu, categorie, capacite,
    image, organisateur_id)

    VALUES

    ('$titre', '$description',
    '$date', '$lieu',
    '$categorie', '$capacite',
    '$image', '$organisateur_id')";

    if(mysqli_query($bdd, $sql)) {

        $message = "Événement créé avec succès";

    } else {

        $message = "Erreur";
    }
}

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<main>

<section class="formulaire-section">

<h2>Créer un événement</h2>

<form method="POST"
      enctype="multipart/form-data">

<input type="text"
       name="titre"
       placeholder="Titre"
       required>

<textarea name="description"
          placeholder="Description"
          required></textarea>

<input type="date"
       name="date_evenement"
       required>

<input type="text"
       name="lieu"
       placeholder="Lieu"
       required>

<select name="categorie">

<option value="Soirée">Soirée</option>

<option value="Sport">Sport</option>

<option value="Culture">Culture</option>

<option value="Conférence">
    Conférence
</option>

</select>

<input type="number"
       name="capacite"
       placeholder="Capacité"
       required>

<input type="file"
       name="image"
       required>

<button type="submit"
        name="creer">

Créer l'événement

</button>

</form>

<p class="message">
    <?php echo $message; ?>
</p>

</section>

</main>

<?php include("includes/footer.php"); ?>