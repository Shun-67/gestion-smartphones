<?php
$currentPage = 'liste.php';

require_once 'bd_connexion.php';
require_once 'init_session.php';

if (!isset($_SESSION['id'])) {
    header("Location: login_form.php");
    exit;
}

$id = $_SESSION['id'];
$marque_selected = $_GET['marque'] ?? 'all';
$tri = $_GET['tri'] ?? 'alpha';
$recherche = $_GET['recherche'] ?? '';

// Rôle de l'utilisateur
$stmt = mysqli_prepare($cnx, 'SELECT role FROM utilisateurs WHERE id_utilisateur = ?');
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user_role = mysqli_fetch_assoc($result) ?? ['role' => 'simple'];
$role = $user_role['role'];
$_SESSION['role'] = $role;

echo $id;
echo $role;

// Marques
$marque_query = mysqli_query($cnx, 'SELECT * FROM marques');
$marques = mysqli_fetch_all($marque_query, MYSQLI_ASSOC);

// Smartphones
$sql = "SELECT s.*, m.nom_marque 
        FROM smartphones s
        JOIN marques m ON s.id_marque = m.id_marque";

$wheres = [];
$params = [];
$types = '';

if (!empty($recherche)) {
    $wheres[] = 's.nom LIKE ?';
    $params[] = '%' . trim($recherche) . '%';
    $types .= 's';
}

if ($marque_selected !== 'all') {
    $wheres[] = 'm.nom_marque = ?';
    $params[] = $marque_selected;
    $types .= 's';
}

if (!empty($wheres)) {
    $sql .= ' WHERE ' . implode(' AND ', $wheres);
}

if ($tri === 'prix') {
    $sql .= ' ORDER BY s.prix ASC';
} else {
    $sql .= ' ORDER BY m.nom_marque ASC, s.nom ASC';
}

$stmt = mysqli_prepare($cnx, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$phones = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Regrouper les téléphones par marque
$groupes_phones = [];

$marque_map = [];
foreach ($marques as $m) {
    $marque_map[$m['id_marque']] = $m['nom_marque'];
}

foreach ($phones as $p) {
    $nom_marque = $marque_map[$p['id_marque']] ?? 'Inconnue';
    $groupes_phones[$nom_marque][] = $p;
}

?>
