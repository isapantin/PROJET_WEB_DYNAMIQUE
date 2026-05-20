<?php
session_start();

if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'admin')        { header('Location: admin/dashboard.php');        exit; }
    if ($_SESSION['role'] === 'organisateur') { header('Location: organisateur/dashboard.php'); exit; }
    header('Location: participant/mes_billets.php'); exit;
}

if (!isset($_SESSION['essais'])) { $_SESSION['essais'] = 0; }

$message_erreur = '';
if (isset($_SESSION['message_erreur'])) {
    $message_erreur = $_SESSION['message_erreur'];
    unset($_SESSION['message_erreur']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - OmnesEvent</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <div>
            <h1>OmnesEvent</h1>
            <p>La plateforme des événements d'Omnes Éducation</p>
        </div>
    </header>

    <nav>
        <button id="burger">☰</button>
        <ul id="nav-liste">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="evenements.php">Événements</a></li>
            <li><a href="login.php" class="active">Connexion</a></li>
            <li><a href="inscription.php">Inscription</a></li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Connexion</h2>

            <?php if (!empty($message_erreur)) : ?>
                <p class="message-erreur"><?php echo $message_erreur; ?></p>
            <?php endif; ?>

            <?php if ($_SESSION['essais'] > 0) : ?>
                <p>Tentatives incorrectes : <?php echo $_SESSION['essais']; ?>/3</p>
            <?php endif; ?>

            <form method="post" action="verif_login.php">
                <p>
                    <label for="login">Login :</label>
                    <input type="text" name="login" id="login">
                </p>
                <p>
                    <label for="mdp">Mot de passe :</label>
                    <input type="password" name="mdp" id="mdp">
                </p>
                <p>
                    <input type="submit" value="Se connecter">
                </p>
            </form>

            <p><a href="inscription.php">Pas encore inscrit ? Créer un compte</a></p>

        </section>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> OmnesEvent - Projet Web Dynamique ING2</p>
    </footer>

    <script src="js/jquery-4.0.0.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>