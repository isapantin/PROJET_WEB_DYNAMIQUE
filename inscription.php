<?php

session_start();

require("includes/connexion_bdd.php");

$message = "";

if(isset($_POST['inscription'])) {

    $nom = mysqli_real_escape_string($bdd, $_POST['nom']);
    $prenom = mysqli_real_escape_string($bdd, $_POST['prenom']);
    $email = mysqli_real_escape_string($bdd, $_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilisateurs
    (prenom, nom, email, mot_de_passe, role)

    VALUES

    ('$prenom', '$nom', '$email', '$mot_de_passe_hash', '$role')";

    if(mysqli_query($bdd, $sql)) {

        $message = "Inscription réussie";

    } else {

        $message = "Erreur lors de l'inscription";
    }
}

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<main>

    <section class="formulaire-section">

        <h2>Inscription</h2>

        <form method="POST">

            <input type="text" name="nom" placeholder="Nom" required>

            <input type="text" name="prenom" placeholder="Prénom" required>

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>

            <select name="role">

                <option value="participant">Participant</option>

                <option value="organisateur">Organisateur</option>

            </select>

            <button type="submit" name="inscription">
                S'inscrire
            </button>

        </form>

        <p class="message">
            <?php echo $message; ?>
        </p>

    </section>

</main>

<?php include("includes/footer.php"); ?>