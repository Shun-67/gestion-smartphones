<?php
require_once 'db_connection.php';
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
