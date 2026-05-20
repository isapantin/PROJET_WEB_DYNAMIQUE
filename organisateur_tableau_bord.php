<?php
session_start();
if (!isset($_SESSION['login']) || !in_array($_SESSION['role'], array('organisateur', 'admin'))) {
    header('Location: ../login.php'); exit;
}
include('../connexion_bdd.php');

$req = $pdo->prepare(
    'SELECT e.*, c.nom AS categorie,
            (SELECT COUNT(*) FROM inscriptions i WHERE i.id_evenement = e.id AND i.statut = "confirme") AS nb_inscrits
     FROM evenements e
     JOIN categories c ON e.id_categorie = c.id
     WHERE e.id_organisateur = ?
     ORDER BY e.date_evenement DESC'
);
$req->execute(array((int)$_SESSION['id_utilisateur']));
$evenements = $req->fetchAll();
$req->closeCursor();

$total_inscrits = 0;
foreach ($evenements as $evt) { $total_inscrits += $evt['nb_inscrits']; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - OmnesEvent</title>
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
            <li><a href="../organisateur/dashboard.php" class="active">Mon espace</a></li>
            <li><a href="../organisateur/creer_evenement.php">Créer un événement</a></li>
            <li><a href="../deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main>

        <div class="banniere">
            <h2>Mon espace organisateur</h2>
            <p>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> !</p>
        </div>

        <section>
            <div class="stats">
                <div class="stat-bloc">
                    <div class="chiffre"><?php echo count($evenements); ?></div>
                    <div class="label">Événement(s) créé(s)</div>
                </div>
                <div class="stat-bloc">
                    <div class="chiffre"><?php echo $total_inscrits; ?></div>
                    <div class="label">Inscription(s) totale(s)</div>
                </div>
            </div>
        </section>

        <section>
            <h2>Mes événements</h2>
            <p><a href="creer_evenement.php">+ Créer un nouvel événement</a></p>

            <?php if (empty($evenements)) : ?>
                <p>Vous n'avez pas encore créé d'événement.</p>
            <?php else : ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Inscrits</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evenements as $evt) : ?>
                            <?php $taux = ($evt['capacite_max'] > 0) ? round(($evt['nb_inscrits'] / $evt['capacite_max']) * 100) : 0; ?>
                            <tr>
                                <td data-label="Titre"><?php echo htmlspecialchars($evt['titre']); ?></td>
                                <td data-label="Date"><?php echo date('d/m/Y', strtotime($evt['date_evenement'])); ?></td>
                                <td data-label="Inscrits"><?php echo $evt['nb_inscrits']; ?> / <?php echo $evt['capacite_max']; ?></td>
                                <td data-label="Statut">
                                    <?php if ($evt['statut'] === 'annule') { echo 'Annulé'; }
                                    elseif ($evt['date_evenement'] < date('Y-m-d H:i:s')) { echo 'Terminé'; }
                                    else { echo 'Actif'; } ?>
                                </td>
                                <td data-label="Actions" class="actions">
                                    <a href="liste_inscrits.php?id=<?php echo (int)$evt['id']; ?>">Inscrits</a>
                                    <a href="modifier_evenement.php?id=<?php echo (int)$evt['id']; ?>">Modifier</a>
                                    <?php if ($evt['statut'] === 'actif') : ?>
                                        <a href="annuler_evenement.php?id=<?php echo (int)$evt['id']; ?>"
                                           class="btn-annuler" data-message="Annuler cet événement ?">Annuler</a>
                                    <?php endif; ?>
                                </td>
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

    <script src="../js/jquery-4.0.0.min.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>