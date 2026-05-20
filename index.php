<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - OmnesEvent</title>
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
            <li><a href="index.php" class="active">Accueil</a></li>
            <li><a href="evenements.php">Événements</a></li>
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

        <div class="banniere">
            <h2>Bienvenue sur OmnesEvent</h2>
            <p>Retrouvez tous les événements de votre école en un seul endroit : soirées, sport, culture, conférences...</p>
            <?php if (isset($_SESSION['login'])) : ?>
                <p>Bonjour <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> !</p>
            <?php endif; ?>
        </div>

        <section>
            <h2>Prochains événements</h2>

            <?php
            include('connexion_bdd.php');

            $reponse = $pdo->query(
                'SELECT e.*, c.nom AS categorie, u.nom AS nom_orga, u.prenom AS prenom_orga,
                        (SELECT COUNT(*) FROM inscriptions i WHERE i.id_evenement = e.id AND i.statut = "confirme") AS nb_inscrits
                 FROM evenements e
                 JOIN categories c ON e.id_categorie = c.id
                 JOIN utilisateurs u ON e.id_organisateur = u.id
                 WHERE e.date_evenement >= NOW() AND e.statut = "actif"
                 ORDER BY e.date_evenement ASC
                 LIMIT 6'
            );
            ?>

            <div class="grille-evenements">
            <?php while ($evenement = $reponse->fetch()) : ?>
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
                        <div class="jauge-container">
                            <p class="jauge-texte"><?php echo $evenement['nb_inscrits']; ?> / <?php echo $evenement['capacite_max']; ?> inscrits</p>
                            <div class="jauge-barre">
                                <div class="jauge-rempli <?php echo $classe; ?>" style="width:<?php echo min($taux, 100); ?>%"></div>
                            </div>
                        </div>
                        <?php if ($taux >= 100) : ?>
                            <span class="complet-label">Complet</span>
                        <?php else : ?>
                            <a href="detail_evenement.php?id=<?php echo (int)$evenement['id']; ?>">Voir l'événement →</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php $reponse->closeCursor(); ?>
            </div>

            <p><a href="evenements.php">Voir tous les événements →</a></p>
        </section>

    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> OmnesEvent - Projet Web Dynamique ING2</p>
    </footer>

    <script src="js/jquery-4.0.0.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>