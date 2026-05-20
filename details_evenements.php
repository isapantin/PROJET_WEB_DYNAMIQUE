<?php
session_start();
include('connexion_bdd.php');

if (!isset($_GET['id'])) { header('Location: evenements.php'); exit; }
$id = (int) $_GET['id'];
if ($id <= 0) { header('Location: evenements.php'); exit; }

$req = $pdo->prepare(
    'SELECT e.*, c.nom AS categorie, u.nom AS nom_orga, u.prenom AS prenom_orga,
            (SELECT COUNT(*) FROM inscriptions i WHERE i.id_evenement = e.id AND i.statut = "confirme") AS nb_inscrits
     FROM evenements e
     JOIN categories c ON e.id_categorie = c.id
     JOIN utilisateurs u ON e.id_organisateur = u.id
     WHERE e.id = ?'
);
$req->execute(array($id));
$evenement = $req->fetch();
$req->closeCursor();

if (!$evenement) { header('Location: evenements.php'); exit; }

$taux        = ($evenement['capacite_max'] > 0) ? round(($evenement['nb_inscrits'] / $evenement['capacite_max']) * 100) : 0;
$est_complet = ($taux >= 100);
$cat         = strtolower($evenement['categorie']);

$deja_inscrit = false;
if (isset($_SESSION['login'])) {
    $req_check = $pdo->prepare('SELECT id FROM inscriptions WHERE id_utilisateur = ? AND id_evenement = ? AND statut = "confirme"');
    $req_check->execute(array($_SESSION['id_utilisateur'], $id));
    $deja_inscrit = ($req_check->fetch() !== false);
    $req_check->closeCursor();
}

$message = ''; $type_message = '';

// Traitement POST inscription / désinscription (cf. TP9)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_SESSION['login'])) { header('Location: login.php'); exit; }
    $action = htmlspecialchars($_POST['action']);

    if ($action === 'inscrire' && !$deja_inscrit && !$est_complet) {
        $req_ins = $pdo->prepare('INSERT INTO inscriptions (id_utilisateur, id_evenement, statut) VALUES (?, ?, "confirme")');
        $req_ins->execute(array($_SESSION['id_utilisateur'], $id));
        $req_ins->closeCursor();
        $message = 'Inscription confirmée !'; $type_message = 'succes';
        $deja_inscrit = true; $evenement['nb_inscrits']++;
    }

    if ($action === 'desinscrire' && $deja_inscrit) {
        $req_del = $pdo->prepare('DELETE FROM inscriptions WHERE id_utilisateur = ? AND id_evenement = ?');
        $req_del->execute(array($_SESSION['id_utilisateur'], $id));
        $req_del->closeCursor();
        $message = 'Vous avez été désinscrit.'; $type_message = 'erreur';
        $deja_inscrit = false; $evenement['nb_inscrits']--;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evenement['titre']); ?> - OmnesEvent</title>
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
            <li><a href="evenements.php" class="active">Événements</a></li>
            <?php if (isset($_SESSION['login'])) : ?>
                <?php if ($_SESSION['role'] === 'organisateur') : ?>
                    <li><a href="organisateur/dashboard.php">Mon espace</a></li>
                <?php elseif ($_SESSION['role'] === 'admin') : ?>
                    <li><a href="admin/dashboard.php">Administration</a></li>
                <?php else : ?>
                    <li><a href="participant/mes_billets.php">Mes billets</a></li>
                <?php endif; ?>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            <?php else : ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <section>

            <?php if (!empty($message)) : ?>
                <p class="message-<?php echo $type_message; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="detail-header">
                <div class="categorie <?php echo htmlspecialchars($cat); ?>">
                    <?php echo htmlspecialchars($evenement['categorie']); ?>
                </div>
                <h2><?php echo htmlspecialchars($evenement['titre']); ?></h2>
            </div>

            <div class="detail-infos">
                <p>📅 <strong>Date :</strong> <?php echo date('d/m/Y à H\hi', strtotime($evenement['date_evenement'])); ?></p>
                <p>📍 <strong>Lieu :</strong> <?php echo htmlspecialchars($evenement['lieu']); ?></p>
                <p>👤 <strong>Organisateur :</strong> <?php echo htmlspecialchars($evenement['prenom_orga'] . ' ' . $evenement['nom_orga']); ?></p>
                <p>👥 <strong>Places :</strong> <?php echo $evenement['nb_inscrits']; ?> / <?php echo $evenement['capacite_max']; ?></p>
            </div>

            <div class="jauge-container">
                <p class="jauge-texte">Taux de remplissage : <?php echo $taux; ?>%</p>
                <div class="jauge-barre">
                    <?php $classe = ($taux >= 100) ? 'complet' : (($taux >= 80) ? 'presque-plein' : ''); ?>
                    <div class="jauge-rempli <?php echo $classe; ?>" style="width:<?php echo min($taux, 100); ?>%"></div>
                </div>
            </div>

            <h3>Description</h3>
            <p><?php echo nl2br(htmlspecialchars($evenement['description'])); ?></p>

            <?php if (!isset($_SESSION['login'])) : ?>
                <div class="bloc-connexion-requis">
                    <p>Connectez-vous pour vous inscrire à cet événement.</p>
                    <a href="login.php">Se connecter</a>
                </div>

            <?php elseif ($_SESSION['role'] === 'participant') : ?>
                <?php if ($deja_inscrit) : ?>
                    <p class="message-succes">Vous êtes inscrit à cet événement !</p>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="desinscrire">
                        <p><input type="submit" value="Se désinscrire" class="btn-annuler" onclick="return confirm('Se désinscrire de cet événement ?')"></p>
                    </form>
                <?php elseif ($est_complet) : ?>
                    <p class="message-erreur">Cet événement est complet.</p>
                <?php elseif ($evenement['statut'] === 'annule') : ?>
                    <p class="message-erreur">Cet événement a été annulé.</p>
                <?php else : ?>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="inscrire">
                        <p><input type="submit" value="S'inscrire à cet événement"></p>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <p><a href="evenements.php">← Retour aux événements</a></p>

        </section>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> OmnesEvent - Projet Web Dynamique ING2</p>
    </footer>

    <script src="js/jquery-4.0.0.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>