<?php

session_start();

require("includes/connexion_bdd.php");

$message = "";

if(isset($_POST['connexion'])) {

    $email = mysqli_real_escape_string($bdd, $_POST['email']);

    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs
    WHERE email = '$email'";

    $resultat = mysqli_query($bdd, $sql);

    if(mysqli_num_rows($resultat) > 0) {

        $utilisateur = mysqli_fetch_assoc($resultat);

        if(password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {

            $_SESSION['id'] = $utilisateur['id'];

            $_SESSION['nom'] = $utilisateur['nom'];

            $_SESSION['prenom'] = $utilisateur['prenom'];

            $_SESSION['role'] = $utilisateur['role'];

            header("Location: index.php");

            exit();

        } else {

            $message = "Mot de passe incorrect";
        }

    } else {

        $message = "Utilisateur introuvable";
    }
}

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<main>

    <section class="formulaire-section">

        <h2>Connexion</h2>

        <form method="POST">

            <input type="email"
                   name="email"
                   placeholder="Email"
                   required>

            <input type="password"
                   name="mot_de_passe"
                   placeholder="Mot de passe"
                   required>

            <button type="submit" name="connexion">
                Se connecter
            </button>

        </form>

        <p class="message">
            <?php echo $message; ?>
        </p>

    </section>

</main>

<?php include("includes/footer.php"); ?>