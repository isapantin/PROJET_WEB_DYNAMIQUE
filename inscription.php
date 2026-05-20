<?php
session_start();
if (isset($_SESSION['login'])) { header('Location: index.php'); exit; }

$erreur  = '';
$succes  = '';

if (isset($_POST['login'], $_POST['mdp'], $_POST['mdp2'], $_POST['nom'], $_POST['prenom'], $_POST['role'])) {

    $login  = htmlspecialchars(trim($_POST['login']));
    $nom    = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $mdp    = $_POST['mdp'];
    $mdp2   = $_POST['mdp2'];
    $role   = htmlspecialchars($_POST['role']);

    if (empty($login) || empty($nom) || empty($prenom) || empty($mdp)) {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif ($mdp !== $mdp2) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($mdp) < 6) {
        $erreur = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif (!in_array($role, array('participant', 'organisateur'))) {
        $erreur = 'Rôle non valide.';
    } else {
        include('connexion_bdd.php');

        // Vérifier que le login n'existe pas déjà
        $req_check = $pdo->prepare('SELECT id FROM utilisateurs WHERE login = ?');
        $req_check->execute(array($login));
        $existe = $req_check->fetch();
        $req_check->closeCursor();

        if ($existe) {
            $erreur = 'Ce login est déjà utilisé.';
        } else {
            $mdp_hache = password_hash($mdp, PASSWORD_DEFAULT);
            $valide    = ($role === 'participant') ? 1 : 0;

            $reponse = $pdo->prepare('INSERT INTO utilisateurs (login, mdp, nom, prenom, role, valide) VALUES (?, ?, ?, ?, ?, ?)');
            $reponse->execute(array($login, $mdp_hache, $nom, $prenom, $role, $valide));
            $reponse->closeCursor();

            $succes = ($role === 'organisateur')
                ? 'Compte créé ! En attente de validation par un administrateur.'
                : 'Compte créé avec succès ! Vous pouvez vous connecter.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - OmnesEvent</title>
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
            <li><a href="login.php">Connexion</a></li>
            <li><a href="inscription.php" class="active">Inscription</a></li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Inscription</h2>

            <?php if (!empty($succes)) : ?>
                <p class="message-succes"><?php echo $succes; ?></p>
                <p><a href="login.php">← Se connecter</a></p>
            <?php else : ?>

                <?php if (!empty($erreur)) : ?>
                    <p class="message-erreur"><?php echo $erreur; ?></p>
                <?php endif; ?>

                <form method="post" action="inscription.php">
                    <p>
                        <label for="prenom">Prénom :</label>
                        <input type="text" name="prenom" id="prenom"
                               value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
                    </p>
                    <p>
                        <label for="nom">Nom :</label>
                        <input type="text" name="nom" id="nom"
                               value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                    </p>
                    <p>
                        <label for="login">Login :</label>
                        <input type="text" name="login" id="login"
                               value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
                    </p>
                    <p>
                        <label for="mdp">Mot de passe :</label>
                        <input type="password" name="mdp" id="mdp">
                    </p>
                    <p>
                        <label for="mdp2">Confirmer le mot de passe :</label>
                        <input type="password" name="mdp2" id="mdp2">
                    </p>
                    <p>
                        <label for="role">Je suis :</label>
                        <select name="role" id="role">
                            <option value="participant">Étudiant / Participant</option>
                            <option value="organisateur">Organisateur d'association</option>
                        </select>
                    </p>
                    <p>
                        <input type="submit" value="S'inscrire">
                    </p>
                </form>

                <p><a href="login.php">Déjà un compte ? Se connecter</a></p>

            <?php endif; ?>

        </section>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> OmnesEvent - Projet Web Dynamique ING2</p>
    </footer>

    <script src="js/jquery-4.0.0.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>