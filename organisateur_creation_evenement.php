<?php
session_start();
if (!isset($_SESSION['login']) || !in_array($_SESSION['role'], array('organisateur', 'admin'))) {
    header('Location: ../login.php'); exit;
}

$erreur = ''; $succes = '';

include('../connexion_bdd.php');
$reponse_cats = $pdo->query('SELECT * FROM categories ORDER BY nom');
$categories   = $reponse_cats->fetchAll();
$reponse_cats->closeCursor();

if (isset($_POST['titre'], $_POST['description'], $_POST['date_evenement'], $_POST['lieu'], $_POST['capacite_max'], $_POST['id_categorie'])) {

    $titre          = htmlspecialchars(trim($_POST['titre']));
    $description    = htmlspecialchars(trim($_POST['description']));
    $date_evenement = htmlspecialchars(trim($_POST['date_evenement']));
    $lieu           = htmlspecialchars(trim($_POST['lieu']));
    $capacite_max   = (int) $_POST['capacite_max'];
    $id_categorie   = (int) $_POST['id_categorie'];

    if (empty($titre) || empty($description) || empty($date_evenement) || empty($lieu)) {
        $erreur = 'Tous les champs obligatoires doivent être remplis.';
    } elseif ($capacite_max <= 0) {
        $erreur = 'La capacité doit être supérieure à 0.';
    } elseif ($id_categorie <= 0) {
        $erreur = 'Veuillez choisir une catégorie.';
    } elseif (strtotime($date_evenement) <= time()) {
        $erreur = 'La date doit être dans le futur.';
    } else {
        $nom_fichier = null;
        if (isset($_FILES['affiche']) && $_FILES['affiche']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['affiche']['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, array('jpg', 'jpeg', 'png', 'gif', 'webp'))) {
                $erreur = 'Format image non autorisé.';
            } elseif ($_FILES['affiche']['size'] > 2 * 1024 * 1024) {
                $erreur = 'Image trop lourde (max 2 Mo).';
            } else {
                $nom_fichier = uniqid('affiche_') . '.' . $extension;
                if (!move_uploaded_file($_FILES['affiche']['tmp_name'], '../images/affiches/' . $nom_fichier)) {
                    $erreur = "Erreur lors de l'upload.";
                    $nom_fichier = null;
                }
            }
        }

        if (empty($erreur)) {
            $req = $pdo->prepare('INSERT INTO evenements (titre, description, date_evenement, lieu, affiche, capacite_max, id_organisateur, id_categorie) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $req->execute(array($titre, $description, $date_evenement, $lieu, $nom_fichier, $capacite_max, (int)$_SESSION['id_utilisateur'], $id_categorie));
            $req->closeCursor();
            $succes = 'Événement créé avec succès !';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement - OmnesEvent</title>
    <link rel="stylesheet" href="../css/style.css">
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
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="../evenements.php">Événements</a></li>
            <li><a href="../organisateur/dashboard.php">Mon espace</a></li>
            <li><a href="../organisateur/creer_evenement.php" class="active">Créer un événement</a></li>
            <li><a href="../deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Créer un événement</h2>

            <?php if (!empty($succes)) : ?>
                <p class="message-succes"><?php echo $succes; ?></p>
                <p><a href="dashboard.php">← Retour au tableau de bord</a></p>
            <?php else : ?>

                <?php if (!empty($erreur)) : ?>
                    <p class="message-erreur"><?php echo $erreur; ?></p>
                <?php endif; ?>

                <form method="post" action="creer_evenement.php" enctype="multipart/form-data">
                    <p>
                        <label for="titre">Titre * :</label>
                        <input type="text" name="titre" id="titre"
                               value="<?php echo isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : ''; ?>">
                    </p>
                    <p>
                        <label for="description">Description * :</label>
                        <textarea name="description" id="description" rows="4"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </p>
                    <p>
                        <label for="date_evenement">Date et heure * :</label>
                        <input type="datetime-local" name="date_evenement" id="date_evenement"
                               value="<?php echo isset($_POST['date_evenement']) ? htmlspecialchars($_POST['date_evenement']) : ''; ?>">
                    </p>
                    <p>
                        <label for="lieu">Lieu * :</label>
                        <input type="text" name="lieu" id="lieu"
                               value="<?php echo isset($_POST['lieu']) ? htmlspecialchars($_POST['lieu']) : ''; ?>">
                    </p>
                    <p>
                        <label for="capacite_max">Capacité maximale * :</label>
                        <input type="number" name="capacite_max" id="capacite_max" min="1"
                               value="<?php echo isset($_POST['capacite_max']) ? (int)$_POST['capacite_max'] : ''; ?>">
                    </p>
                    <p>
                        <label for="id_categorie">Catégorie * :</label>
                        <select name="id_categorie" id="id_categorie">
                            <option value="">-- Choisir --</option>
                            <?php foreach ($categories as $cat) : ?>
                                <option value="<?php echo (int)$cat['id']; ?>"
                                    <?php echo (isset($_POST['id_categorie']) && (int)$_POST['id_categorie'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="affiche">Affiche (optionnelle, max 2 Mo) :</label>
                        <input type="file" name="affiche" id="affiche" accept="image/*">
                    </p>
                    <p>
                        <input type="submit" value="Publier l'événement">
                    </p>
                </form>

                <p><a href="dashboard.php">← Retour au tableau de bord</a></p>

            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> OmnesEvent - Projet Web Dynamique ING2</p>
    </footer>

    <script src="../js/jquery-4.0.0.min.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>