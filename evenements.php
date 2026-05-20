<?php
session_start();
include('connexion_bdd.php');

// Filtres GET 
$recherche    = '';
$id_categorie = 0;
if (isset($_GET['recherche']))  { $recherche    = htmlspecialchars(trim($_GET['recherche'])); }
if (isset($_GET['categorie']))  { $id_categorie = (int) $_GET['categorie']; }

$sql    = 'SELECT e.*, c.nom AS categorie, u.nom AS nom_orga, u.prenom AS prenom_orga,
                  (SELECT COUNT(*) FROM inscriptions i WHERE i.id_evenement = e.id AND i.statut = "confirme") AS nb_inscrits
           FROM evenements e
           JOIN categories c ON e.id_categorie = c.id
           JOIN utilisateurs u ON e.id_organisateur = u.id
           WHERE e.statut = "actif"';
$params = array();

if (!empty($recherche)) {
    $sql     .= ' AND (e.titre LIKE ? OR e.lieu LIKE ? OR u.nom LIKE ?)';
    $terme    = '%' . $recherche . '%';
    $params[] = $terme; $params[] = $terme; $params[] = $terme;
}
if ($id_categorie > 0) {
    $sql     .= ' AND e.id_categorie = ?';
    $params[] = $id_categorie;
}
$sql .= ' ORDER BY e.date_evenement ASC';

$req = $pdo->prepare($sql);
$req->execute($params);

$reponse_cats = $pdo->query('SELECT * FROM categories ORDER BY nom');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - OmnesEvent</title>
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
            <h2>Tous les événements</h2>

            <!-- Formulaire GET -->
            <form method="get" action="evenements.php">
                <div class="barre-recherche">
                    <input type="text" name="recherche" id="champ-recherche"
                           placeholder="Rechercher un événement..."
                           value="<?php echo htmlspecialchars($recherche); ?>">
                    <select name="categorie">
                        <option value="0">Toutes les catégories</option>
                        <?php while ($cat = $reponse_cats->fetch()) : ?>
                            <option value="<?php echo (int)$cat['id']; ?>"
                                <?php echo ($id_categorie === (int)$cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nom']); ?>
                            </option>
                        <?php endwhile; ?>
                        <?php $reponse_cats->closeCursor(); ?>
                    </select>
                    <input type="submit" value="Rechercher">
                    <?php if (!empty($recherche) || $id_categorie > 0) : ?>
                        <a href="evenements.php">Réinitialiser</a>
                    <?php endif; ?>
                </div>
            </form>

            <?php if ($req->rowCount() === 0) : ?>
                <p>Aucun événement trouvé. <a href="evenements.php">Voir tous les événements</a></p>
            <?php else : ?>
                <p><?php echo $req->rowCount(); ?> événement(s) trouvé(s)</p>
                <div class="grille-evenements">
                <?php while ($evenement = $req->fetch()) : ?>
                    <?php
                    $taux = ($evenement['capacite_max'] > 0)
                        ? round(($evenement['nb_inscrits'] / $evenement['capacite_max']) * 100) : 0;
                    $classe = ($taux >= 100) ? 'complet' : (($taux >= 80) ? 'presque-plein' : '');
                    $cat = strtolower($evenement['categorie']);
                    ?>
                    <div class="carte-evenement" data-categorie="<?php echo htmlspecialchars($cat); ?>">
                        <div class="categorie <?php echo htmlspecialchars($cat); ?>">
                            <?php echo htmlspecialchars($evenement['categorie']); ?>
                        </div>
                        <div class="info">
                            <h3><?php echo htmlspecialchars($evenement['titre']); ?></h3>
                            <p class="meta">📅 <?php echo date('d/m/Y à H\hi', strtotime($evenement['date_evenement'])); ?></p>
                            <p class="meta">📍 <?php echo htmlspecialchars($evenement['lieu']); ?></p>
                            <p class="meta-orga">Par <?php echo htmlspecialchars($evenement['prenom_orga'] . ' ' . $evenement['nom_orga']); ?></p>
                            <div class="jauge-container">
                                <p class="jauge-texte"><?php echo $evenement['nb_inscrits']; ?> / <?php echo $evenement['capacite_max']; ?> inscrits</p>
                                <div class="jauge-barre">
                                    <div class="jauge-rempli <?php echo $classe; ?>" style="width:<?php echo min($taux, 100); ?>%"></div>
                                </div>
                            </div>
                            <?php if ($taux >= 100) : ?>
                                <span class="complet-label">Complet</span>
                            <?php else : ?>
                                <a href="detail_evenement.php?id=<?php echo (int)$evenement['id']; ?>">Voir et s'inscrire →</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php $req->closeCursor(); ?>
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