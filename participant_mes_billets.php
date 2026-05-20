<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] === 'organisateur') {
    header('Location: ../login.php'); exit;
}
include('../connexion_bdd.php');

$id_utilisateur = (int) $_SESSION['id_utilisateur'];

$req_avenir = $pdo->prepare(
    'SELECT e.*, c.nom AS categorie, i.date_inscription
     FROM inscriptions i
     JOIN evenements e ON i.id_evenement = e.id
     JOIN categories c ON e.id_categorie = c.id
     WHERE i.id_utilisateur = ? AND i.statut = "confirme" AND e.date_evenement >= NOW()
     ORDER BY e.date_evenement ASC'
);
$req_avenir->execute(array($id_utilisateur));

$req_passe = $pdo->prepare(
    'SELECT e.*, c.nom AS categorie, i.present
     FROM inscriptions i
     JOIN evenements e ON i.id_evenement = e.id
     JOIN categories c ON e.id_categorie = c.id
     WHERE i.id_utilisateur = ? AND i.statut = "confirme" AND e.date_evenement < NOW()
     ORDER BY e.date_evenement DESC'
);
$req_passe->execute(array($id_utilisateur));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes billets - OmnesEvent</title>
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
            <li><a href="../index.php">Accueil</a></li>
            <li><a href="../evenements.php">Événements</a></li>
            <li><a href="../participant/mes_billets.php" class="active">Mes billets</a></li>
            <li><a href="../deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main>

        <div class="banniere">
            <h2>Mes billets</h2>
            <p>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> !</p>
        </div>

        <section>
            <h2>Événements à venir</h2>
            <?php $billets = $req_avenir->fetchAll(); $req_avenir->closeCursor(); ?>
            <?php if (empty($billets)) : ?>
                <p>Vous n'êtes inscrit à aucun événement à venir. <a href="../evenements.php">Voir les événements</a></p>
            <?php else : ?>
                <div class="grille-evenements">
                <?php foreach ($billets as $billet) : ?>
                    <div class="carte-evenement">
                        <div class="categorie <?php echo htmlspecialchars(strtolower($billet['categorie'])); ?>">
                            <?php echo htmlspecialchars($billet['categorie']); ?>
                        </div>
                        <div class="info">
                            <h3><?php echo htmlspecialchars($billet['titre']); ?></h3>
                            <p class="meta">📅 <?php echo date('d/m/Y à H\hi', strtotime($billet['date_evenement'])); ?></p>
                            <p class="meta">📍 <?php echo htmlspecialchars($billet['lieu']); ?></p>
                            <p class="meta">Inscrit le <?php echo date('d/m/Y', strtotime($billet['date_inscription'])); ?></p>
                            <p class="meta">⏱️ Dans : <span class="compte-a-rebours" data-date="<?php echo htmlspecialchars($billet['date_evenement']); ?>">...</span></p>
                            <a href="../detail_evenement.php?id=<?php echo (int)$billet['id']; ?>">Voir l'événement →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section>
            <h2>Historique</h2>
            <?php $historique = $req_passe->fetchAll(); $req_passe->closeCursor(); ?>
            <?php if (empty($historique)) : ?>
                <p>Aucun événement passé.</p>
            <?php else : ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Présence</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($historique as $evt) : ?>
                            <tr>
                                <td data-label="Événement"><?php echo htmlspecialchars($evt['titre']); ?></td>
                                <td data-label="Date"><?php echo date('d/m/Y', strtotime($evt['date_evenement'])); ?></td>
                                <td data-label="Lieu"><?php echo htmlspecialchars($evt['lieu']); ?></td>
                                <td data-label="Présence"><?php echo $evt['present'] ? '✅ Présent' : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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