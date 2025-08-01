<?php
// Inclure les fichiers nécessaires
require_once 'bd_connexion.php'; // Connexion à la base de données
require_once 'init_session.php'; // Démarrage de la session

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit;
}

// Définir la page active pour la barre latérale
$currentPage = 'liste.php';

// Récupérer le rôle de l'utilisateur
$role = $_SESSION['role'];

// Vérifier que :
// - L'ID est présent
// - L'ID est numérique
// - L'utilisateur est admin
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $role !== 'admin') {
    // Sinon, rediriger vers la liste
    header('Location: liste.php');
    exit;
}

// Sécuriser l'ID
$id = intval($_GET['id']);

// Récupérer les données du smartphone à modifier
$sql = "SELECT * FROM smartphones WHERE id = ?";
$stmt = mysqli_prepare($cnx, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$phone = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Si aucun smartphone trouvé, afficher une erreur
if (!$phone) {
    echo "<p>Smartphone introuvable.</p>";
    exit;
}

// Récupérer toutes les marques pour le menu déroulant
$result = mysqli_query($cnx, "SELECT id_marque, nom_marque FROM marques");
$marques = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// Récupérer toutes les capacités RAM
$result = mysqli_query($cnx, "SELECT id_ram, capacite_ram FROM rams");
$rams = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// Récupérer toutes les capacités ROM
$result = mysqli_query($cnx, "SELECT id_rom, capacite_rom FROM roms");
$roms = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// Récupérer toutes les couleurs (avec code hexadécimal)
$result = mysqli_query($cnx, "SELECT id_couleur, nom_couleur, code_hex FROM couleurs");
$couleurs = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// Récupérer les couleurs déjà associées à ce smartphone
$sql = "SELECT id_couleur FROM smartphone_couleurs WHERE id = ?";
$stmt = mysqli_prepare($cnx, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
// Extraire uniquement les ID des couleurs sélectionnées
$couleurs_selectionnees = array_column(mysqli_fetch_all($result, MYSQLI_ASSOC), 'id_couleur');
mysqli_stmt_close($stmt);

// Tableau pour stocker les erreurs de validation
$errors = [];

// Vérifier si le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $marque_id = intval($_POST['id_marque'] ?? 0);
    $ram_id = intval($_POST['id_ram'] ?? 0);
    $rom_id = intval($_POST['id_rom'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $ecran = trim($_POST['ecran'] ?? '');
    $couleurs_id = $_POST['couleurs'] ?? [];

    // Validation des champs obligatoires
    if (!$nom) $errors['nom'] = "Le nom est requis.";
    if ($prix <= 0) $errors['prix'] = "Le prix doit être positif.";
    if (!$marque_id) $errors['marque'] = "Choisissez une marque.";
    if (!$ram_id) $errors['ram'] = "Choisissez une RAM.";
    if (!$rom_id) $errors['rom'] = "Choisissez une ROM.";
    if (!$description) $errors['description'] = "La description est requise.";
    if (!$ecran) $errors['ecran'] = "Le type d'écran est requis.";
    if (!$couleurs_id) $errors['couleurs'] = "Choisissez au moins une couleur.";

    // Gestion du téléchargement de la nouvelle image
    $photo = $phone['photo']; // Garder l'ancienne photo par défaut
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['photo']['tmp_name'];
        $original_name = basename($_FILES['photo']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Vérifier l'extension et le type MIME
        if (in_array($ext, $allowed) && getimagesize($tmp_name)) {
            // Générer un nom unique
            $new_name = uniqid('img_', true) . '.' . $ext;
            $upload_dir = '/images/';
            $photo = $upload_dir . $new_name;
            // Déplacer le fichier vers le dossier images
            move_uploaded_file($tmp_name, '..' . $photo);
        } else {
            $errors['photo'] = "Image non valide.";
        }
    }

    // Si aucune erreur, procéder à la mise à jour
    if (empty($errors)) {
        // Mettre à jour les informations du smartphone
        $stmt = mysqli_prepare($cnx, "UPDATE smartphones SET nom=?, prix=?, photo=?, id_marque=?, id_ram=?, id_rom=?, description=?, ecran=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sdsiiissi", $nom, $prix, $photo, $marque_id, $ram_id, $rom_id, $description, $ecran, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Supprimer les anciennes associations de couleurs
        mysqli_query($cnx, "DELETE FROM smartphone_couleurs WHERE id = $id");
        // Insérer les nouvelles associations
        $stmtC = mysqli_prepare($cnx, "INSERT INTO smartphone_couleurs (id, id_couleur) VALUES (?, ?)");
        foreach ($couleurs_id as $cid) {
            mysqli_stmt_bind_param($stmtC, "ii", $id, $cid);
            mysqli_stmt_execute($stmtC);
        }
        mysqli_stmt_close($stmtC);

        // Rediriger vers la page de détails
        header("Location: details.php?id=$id");
        exit;
    }
}
?>