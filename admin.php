<?php

session_start();

require("includes/connexion_bdd.php");

/* SECURITE ADMIN */

if(!isset($_SESSION['role'])
|| $_SESSION['role'] != 'admin') {

    die("Accès interdit");
}

/* SUPPRESSION UTILISATEUR */

if(isset($_GET['supprimer_user'])) {

    $id = $_GET['supprimer_user'];

    mysqli_query(
        $bdd,
        "DELETE FROM utilisateurs WHERE id = $id"
    );
}

/* SUPPRESSION EVENEMENT */

if(isset($_GET['supprimer_event'])) {

    $id = $_GET['supprimer_event'];

    mysqli_query(
        $bdd,
        "DELETE FROM evenements WHERE id = $id"
    );
}

/* RECUPERATION DONNEES */

$users = mysqli_query(
    $bdd,
    "SELECT * FROM utilisateurs"
);

$events = mysqli_query(
    $bdd,
    "SELECT * FROM evenements"
);

?>

<?php include("includes/header.php"); ?>
<?php include("includes/menu.php"); ?>

<main>

<section class="admin-section">

<h2>
Administration
</h2>

<!-- UTILISATEURS -->

<h3 class="titre-admin">
Utilisateurs
</h3>

<table>

<tr>
<th>ID</th>
<th>Nom</th>
<th>Prénom</th>
<th>Rôle</th>
<th>Action</th>
</tr>

<?php while($user = mysqli_fetch_assoc($users)) { ?>

<tr>

<td>
<?php echo $user['id']; ?>
</td>

<td>
<?php echo $user['nom']; ?>
</td>

<td>
<?php echo $user['prenom']; ?>
</td>

<td>
<?php echo $user['role']; ?>
</td>

<td>

<?php if($user['id'] != $_SESSION['id']) { ?>

<a class="btn-delete"
href="admin.php?supprimer_user=<?php echo $user['id']; ?>">

Supprimer

</a>

<?php } else { ?>

Admin actuel

<?php } ?>

</td>

<?php } ?>

</table>

<!-- EVENEMENTS -->

<h3 class="titre-admin">
Événements
</h3>

<table>

<tr>
<th>ID</th>
<th>Titre</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php while($event = mysqli_fetch_assoc($events)) { ?>

<tr>

<td>
<?php echo $event['id']; ?>
</td>

<td>
<?php echo $event['titre']; ?>
</td>

<td>
<?php echo $event['date_evenement']; ?>
</td>

<td>

<a class="btn-delete"
href="admin.php?supprimer_event=<?php echo $event['id']; ?>">

Supprimer

</a>

</td>

</tr>

<?php } ?>

</table>

</section>

</main>

<?php include("includes/footer.php"); ?>