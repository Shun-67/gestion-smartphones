<?php
<<<<<<< HEAD
require_once 'bd_connexion.php';
=======
require_once 'bd_connection.php';
>>>>>>> 169d52d44ea26051093614c7590517f57b7fa78c
$id = $_GET['id'] ?? null;
if ($id) {
    $sql = "DELETE FROM smartphones WHERE id = ?";
    $stmt = mysqli_prepare($cnx, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $sql1 = "DELETE FROM smartphone_couleurs WHERE id = ?";
    $stmt = mysqli_prepare($cnx, $sql1);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
}
header('Location: liste.php');
exit;
