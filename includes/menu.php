<header>

    <h1>OmnesEvent</h1>

    <nav>

        <a href="index.php">Accueil</a>

        <?php if(isset($_SESSION['id'])) { ?>

            <a href="profil.php">Profil</a>

        <?php } ?>

        <?php
        if(isset($_SESSION['role'])
        && $_SESSION['role'] == 'organisateur') {
        ?>

            <a href="creer_evenement.php">
                Créer
            </a>

        <?php } ?>

        <?php if(!isset($_SESSION['id'])) { ?>

            <a href="connexion.php">
                Connexion
            </a>

            <a href="inscription.php">
                Inscription
            </a>

        <?php } else { ?>

            <a href="deconnexion.php">
                Déconnexion
            </a>

        <?php } ?>

    </nav>

</header>