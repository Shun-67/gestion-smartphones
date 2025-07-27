<?php
// Inclure le fichier de connexion à la base de données
require_once 'bd_connexion.php';

// Récupérer l'ID du smartphone à supprimer depuis l'URL (GET)
$id = $_GET['id'] ?? null;

// Vérifier que l'ID est présent
if ($id) {
    // Première suppression : dans la table principale `smartphones`
    $sql = "DELETE FROM smartphones WHERE id = ?";
    $stmt = mysqli_prepare($cnx, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    // Noter : mysqli_stmt_get_result() n'est pas nécessaire pour DELETE
    mysqli_stmt_close($stmt);

    // Deuxième suppression : dans la table de liaison `smartphone_couleurs`
    // Pour éviter les enregistrements orphelins
    $sql1 = "DELETE FROM smartphone_couleurs WHERE id = ?";
    $stmt = mysqli_prepare($cnx, $sql1);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    // Idem : pas besoin de récupérer un résultat pour DELETE
    mysqli_stmt_close($stmt);
}

// Rediriger vers la page de liste après suppression (même si $id est null)
header('Location: liste.php');
exit;
?>