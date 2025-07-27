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
$currentPage = 'parametres.php';

// Récupérer le rôle de l'utilisateur
$role = $_SESSION['role'];

// Vérifier que seul l'administrateur peut accéder à cette page
if ($role !== 'admin') {
    header('Location: liste.php');
    exit;
}

// Tableau de configuration pour gérer différents types d'éléments
$types_valides = [
    'marque'  => ['table' => 'marques',  'champ' => 'nom_marque',    'label' => 'Marque',   'type1' => 's'],
    'ram'     => ['table' => 'rams',     'champ' => 'capacite_ram',  'label' => 'RAM (Go)', 'type1' => 'i'],
    'rom'     => ['table' => 'roms',     'champ' => 'capacite_rom',  'label' => 'ROM (Go)', 'type1' => 'i'],
    'couleur' => ['table' => 'couleurs', 'champ' => 'nom_couleur',   'label' => 'Couleur',  'type1' => 's']
];

// Récupérer le type d'élément à gérer (ex: ?type=marque)
$type = $_GET['type'] ?? '';

// Vérifier que le type est valide
if (!isset($types_valides[$type])) {
    die("Type de gestion invalide.");
}

// Récupérer les informations correspondantes au type
$table = $types_valides[$type]['table'];       // Nom de la table
$champ = $types_valides[$type]['champ'];       // Champ à manipuler
$label = $types_valides[$type]['label'];       // Libellé pour l'affichage
$champ_type = $types_valides[$type]['type1'];  // Type pour mysqli (s=string, i=integer)

// Messages d'erreur spécifiques à chaque type
$errors_type = [
    'marque' => ['requis' => 'Le nom de la marque est requis.', 'exist' => 'Cette marque existe déjà.'],
    'ram' => ['requis' => 'La capacité de la RAM est requise.', 'exist' => 'Cette capacité existe déjà.'],
    'rom' => ['requis' => 'La capacité de la ROM est requise.', 'exist' => 'Cette capacité existe déjà.'],
    'couleur' => ['requis' => 'Le nom de la couleur est requis.', 'exist' => 'Cette couleur existe déjà.'],
];

// Tableau pour stocker les erreurs de validation
$errors = [];

// Gestion de l'ajout d'un nouvel élément
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valeur'])) {
    $valeur = trim($_POST['valeur']);

    // Cas spécial : ajout d'une couleur (avec code hexadécimal)
    if ($type === 'couleur' && isset($_POST['code_hex'])) {
        $code_hex = trim($_POST['code_hex']);

        // Vérifier que le nom et le code hex sont valides
        if ($valeur !== '' && preg_match('/^#[0-9A-Fa-f]{6}$/', $code_hex)) {
            // Vérifier si la couleur existe déjà
            $sql = "SELECT * FROM couleurs WHERE nom_couleur = ?";
            $stmt = mysqli_prepare($cnx, $sql);
            mysqli_stmt_bind_param($stmt, "s", $valeur);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $exists = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($exists) {
                $errors[] = $errors_type[$type]['exist'];
            } else {
                // Insérer la nouvelle couleur
                $sql = "INSERT INTO couleurs (nom_couleur, code_hex) VALUES (?, ?)";
                $stmt = mysqli_prepare($cnx, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $valeur, $code_hex);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        // Ajout pour les autres types (marque, RAM, ROM)
        if ($valeur === '') {
            $errors[] = $errors_type[$type]['requis'];
        } else {
            // Convertir en entier si nécessaire
            if ($champ_type === 'i') {
                $valeur = (int)$valeur;
            }

            // Vérifier si l'élément existe déjà
            $stmt = mysqli_prepare($cnx, "SELECT * FROM $table WHERE $champ = ?");
            mysqli_stmt_bind_param($stmt, $champ_type, $valeur);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $exists = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($exists) {
                $errors[] = $errors_type[$type]['exist'];
            } else {
                // Insérer le nouvel élément
                if (empty($errors)) {
                    $stmt = mysqli_prepare($cnx, "INSERT INTO $table ($champ) VALUES (?)");
                    mysqli_stmt_bind_param($stmt, $champ_type, $valeur);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        header("Location: gestion.php?type=$type");
                        exit;
                    } else {
                        $errors[] = "Erreur lors de l'ajout.";
                        mysqli_stmt_close($stmt);
                    }
                }
            }
        }
    }
}

// Gestion de la suppression d'un élément
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($cnx, "DELETE FROM $table WHERE id_$type = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Rediriger après suppression
    header("Location: gestion.php?type=$type");
    exit;
}

// Gestion de la modification : chargement des données si ?edit=ID
$editElement = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = mysqli_prepare($cnx, "SELECT * FROM $table WHERE id_$type = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editElement = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Gestion de la soumission du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['edit_valeur'])) {
    $id = intval($_POST['edit_id']);
    $valeur = trim($_POST['edit_valeur']);
    if ($valeur !== '') {
        if ($champ_type === 'i') {
            $valeur = (int)$valeur;
        }

        // Cas spécial : modification d'une couleur
        if ($type === 'couleur' && isset($_POST['edit_code_hex'])) {
            $code_hex = trim($_POST['edit_code_hex']);
            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $code_hex)) {
                $stmt = mysqli_prepare($cnx, "UPDATE couleurs SET nom_couleur = ?, code_hex = ? WHERE id_couleur = ?");
                mysqli_stmt_bind_param($stmt, "ssi", $valeur, $code_hex, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                header("Location: gestion.php?type=$type");
                exit;
            } else {
                $errors[] = "Le code hexadécimal est invalide.";
            }
        } else {
            // Modification pour les autres types
            $stmt = mysqli_prepare($cnx, "UPDATE $table SET $champ = ? WHERE id_$type = ?");
            mysqli_stmt_bind_param($stmt, $champ_type . "i", $valeur, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: gestion.php?type=$type");
            exit;
        }
    } else {
        $errors[] = $errors_type[$type]['requis'];
    }
}

// Récupérer la liste complète des éléments pour affichage
$sql = "SELECT * FROM $table ORDER BY $champ ASC";
$result = mysqli_query($cnx, $sql);
$items = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_free_result($result);
?>