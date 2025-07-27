<?php
// Définir la page active pour la barre latérale (pour mise en surbrillance)
$currentPage = 'liste.php';

// Inclure les fichiers nécessaires
require_once 'bd_connexion.php'; // Connexion à la base de données MySQL
require_once 'init_session.php'; // Démarrage de la session

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    // Si non, rediriger vers la page de connexion
    header("Location: login_form.php");
    exit;
}

// Récupérer l'ID de l'utilisateur depuis la session
$id = $_SESSION['id'];

// Récupérer le filtre de marque depuis l'URL (ex: ?marque=Samsung)
// Par défaut : 'all' → affiche toutes les marques
$marque_selected = $_GET['marque'] ?? 'all';

// Récupérer le type de tri (alphabétique ou par prix)
// Par défaut : 'alpha'
$tri = $_GET['tri'] ?? 'alpha';

// Récupérer le terme de recherche (si présent)
// Par défaut : chaîne vide
$recherche = $_GET['recherche'] ?? '';

// Récupérer le rôle de l'utilisateur (admin ou simple) depuis la base
$stmt = mysqli_prepare($cnx, 'SELECT role FROM utilisateurs WHERE id_utilisateur = ?');
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Récupérer le rôle, ou définir 'simple' par défaut
$user_role = mysqli_fetch_assoc($result) ?? ['role' => 'simple'];
$role = $user_role['role'];

// Stocker le rôle en session pour utilisation dans les autres fichiers
$_SESSION['role'] = $role;

// Afficher pour débogage (à supprimer en production)
// echo $id;
// echo $role;

// Récupérer toutes les marques pour le menu déroulant
$marque_query = mysqli_query($cnx, 'SELECT * FROM marques');
$marques = mysqli_fetch_all($marque_query, MYSQLI_ASSOC);

// Requête de base pour récupérer les smartphones avec le nom de la marque
$sql = "SELECT s.*, m.nom_marque 
        FROM smartphones s
        JOIN marques m ON s.id_marque = m.id_marque";

// Tableaux pour construire dynamiquement la clause WHERE
$wheres = [];  // Conditions WHERE
$params = [];  // Valeurs des paramètres
$types = '';   // Types des paramètres (pour mysqli_stmt_bind_param)

// Ajouter une condition de recherche si un terme est saisi
if (!empty($recherche)) {
    $wheres[] = 's.nom LIKE ?';
    $params[] = '%' . trim($recherche) . '%'; // Recherche partielle
    $types .= 's'; // Type string
}

// Ajouter un filtre par marque si sélectionné
if ($marque_selected !== 'all') {
    $wheres[] = 'm.nom_marque = ?';
    $params[] = $marque_selected;
    $types .= 's';
}

// Ajouter la clause WHERE si des conditions existent
if (!empty($wheres)) {
    $sql .= ' WHERE ' . implode(' AND ', $wheres);
}

// Appliquer le tri
if ($tri === 'prix') {
    $sql .= ' ORDER BY s.prix ASC'; // Prix croissant
} else {
    $sql .= ' ORDER BY m.nom_marque ASC, s.nom ASC'; // Par marque, puis par nom
}

// Préparer la requête SQL
$stmt = mysqli_prepare($cnx, $sql);

// Lier les paramètres si nécessaire
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

// Exécuter la requête
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Récupérer tous les résultats sous forme de tableau associatif
$phones = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Regrouper les téléphones par marque pour un affichage organisé
$groupes_phones = [];

// Créer une map ID → nom_marque pour un accès rapide
$marque_map = [];
foreach ($marques as $m) {
    $marque_map[$m['id_marque']] = $m['nom_marque'];
}

// Regrouper chaque smartphone par son nom de marque
foreach ($phones as $p) {
    $nom_marque = $marque_map[$p['id_marque']] ?? 'Inconnue';
    $groupes_phones[$nom_marque][] = $p;
}
?>