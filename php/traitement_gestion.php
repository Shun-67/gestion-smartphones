<?php
require_once 'bd_connexion.php';
require_once 'init_session.php';

$currentPage = 'parametres.php';
$role = $_SESSION['role'];

// Vérifier le rôle admin
if ($role !== 'admin') {
    header('Location: liste.php');
    exit;
}

// Gestion ajout
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_marque'])) {
    $nom_marque = trim($_POST['nom_marque']);
    if ($nom_marque === '') {
        $errors[] = "Le nom de la marque est requis.";
    } else {
        // Vérifier doublon
        $stmt = mysqli_prepare($cnx, "SELECT id_marque FROM marques WHERE nom_marque = ?");
        mysqli_stmt_bind_param($stmt, "s", $nom_marque);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($exists) {
            $errors[] = "Cette marque existe déjà.";
        } else {
            $stmt = mysqli_prepare($cnx, "INSERT INTO marques (nom_marque) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $nom_marque);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: gerer_marques.php');
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout.";
                mysqli_stmt_close($stmt);
            }
        }
    }
}


// Gestion suppression (GET param)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($cnx, "DELETE FROM marques WHERE id_marque = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: gerer_marques.php');
    exit;
}


// Gestion modification (simple)
// On affiche un formulaire si ?edit=ID est dans l’URL
$editMarque = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = mysqli_prepare($cnx, "SELECT * FROM marques WHERE id_marque = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editMarque = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['edit_nom_marque'])) {
    $id = intval($_POST['edit_id']);
    $nom_marque = trim($_POST['edit_nom_marque']);
    if ($nom_marque !== '') {
        $stmt = mysqli_prepare($cnx, "UPDATE marques SET nom_marque = ? WHERE id_marque = ?");
        mysqli_stmt_bind_param($stmt, "si", $nom_marque, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header('Location: gerer_marques.php');
        exit;
    } else {
        $errors[] = "Le nom de la marque est requis.";
    }
}


// Récupérer la liste des marques
$result = mysqli_query($cnx, "SELECT * FROM marques ORDER BY nom_marque ASC");
$marques = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_free_result($result);
?>