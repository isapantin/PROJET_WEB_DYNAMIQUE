<?php
session_start();

if (isset($_POST['login'], $_POST['mdp'])) {

    $login = htmlspecialchars($_POST['login']);
    $mdp   = $_POST['mdp'];

    include('connexion_bdd.php');

    // Blocage après 3 tentatives 
    if ($_SESSION['essais'] >= 3) {
        sleep(5);
        $_SESSION['essais'] = 0;
    }

    $reponse = $pdo->prepare('SELECT * FROM utilisateurs WHERE login = ?');
    $reponse->execute(array($login));
    $donnees = $reponse->fetch();
    $reponse->closeCursor();

    if ($donnees && password_verify($mdp, $donnees['mdp'])) {

        if ($donnees['valide'] == 0) {
            $_SESSION['message_erreur'] = 'Votre compte est en attente de validation.';
            header('Location: login.php');
            exit;
        }

        $_SESSION['login']          = $donnees['login'];
        $_SESSION['prenom']         = $donnees['prenom'];
        $_SESSION['nom']            = $donnees['nom'];
        $_SESSION['role']           = $donnees['role'];
        $_SESSION['id_utilisateur'] = $donnees['id'];
        $_SESSION['essais']         = 0;

        if ($donnees['role'] === 'admin')        { header('Location: admin/dashboard.php');        exit; }
        if ($donnees['role'] === 'organisateur') { header('Location: organisateur/dashboard.php'); exit; }
        header('Location: participant/mes_billets.php');
        exit;

    } else {

        if (!isset($_SESSION['essais'])) { $_SESSION['essais'] = 0; }
        $_SESSION['essais']++;

        if ($_SESSION['essais'] >= 3) {
            sleep(5);
            $_SESSION['essais'] = 0;
        }

        $_SESSION['message_erreur'] = 'Login ou mot de passe incorrect.';
        header('Location: login.php');
        exit;
    }

} else {
    header('Location: login.php');
    exit;
}
?>