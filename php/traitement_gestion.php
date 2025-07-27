<?php
require_once 'bd_connexion.php';
require_once 'init_session.php';

if (!isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit;
}

$currentPage = 'parametres.php';
$role = $_SESSION['role'];

// Vérifier le rôle admin
if ($role !== 'admin') {
    header('Location: liste.php');
    exit;
}

$types_valides = [
    'marque'  => ['table' => 'marques',  'champ' => 'nom_marque',    'label' => 'Marque',   'type1' => 's'],
    'ram'     => ['table' => 'rams',     'champ' => 'capacite_ram',  'label' => 'RAM (Go)', 'type1' => 'i'],
    'rom'     => ['table' => 'roms',     'champ' => 'capacite_rom',  'label' => 'ROM (Go)', 'type1' => 'i'],
    'couleur' => ['table' => 'couleurs', 'champ' => 'nom_couleur',   'label' => 'Couleur',  'type1' => 's']
];

$type = $_GET['type'] ?? '';
if (!isset($types_valides[$type])) {
    die("Type de gestion invalide.");
}

$table = $types_valides[$type]['table'];
$champ = $types_valides[$type]['champ'];
$label = $types_valides[$type]['label'];
$champ_type = $types_valides[$type]['type1'];

$errors_type = [
    'marque' => ['requis' => 'Le nom de la marque est requis.', 'exist' => 'Cette marque existe déjà.'],
    'ram' => ['requis' => 'La capacité de la ram est requise.', 'exist' => 'Cette capacité existe déjà.'],
    'rom' => ['requis' => 'La capacité de la ram est requise.', 'exist' => 'Cette capacité existe déjà.'],
    'couleur' => ['requis' => 'Le nom de la couleur est requis.', 'exist' => 'Cette couleur existe déjà.'],
];

// Gestion ajout
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valeur'])) {
    $valeur = trim($_POST['valeur']);

    if ($type === 'couleur' && isset($_POST['code_hex'])) {
        $code_hex = trim($_POST['code_hex']);

        if ($valeur !== '' && preg_match('/^#[0-9A-Fa-f]{6}$/', $code_hex)) {
            // Vérifie si la couleur existe déjà
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
                $sql = "INSERT INTO couleurs (nom_couleur, code_hex) VALUES (?, ?)";
                $stmt = mysqli_prepare($cnx, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $valeur, $code_hex);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        if ($valeur === '') {
            $errors[] = $errors_type[$type]['requis'];
        } else {
            if ($champ_type === 'i') {
                $valeur = (int)$valeur;
            }

            // Vérifier doublon
            $stmt = mysqli_prepare($cnx, "SELECT * FROM $table WHERE $champ = ?");
            mysqli_stmt_bind_param($stmt, $champ_type, $valeur);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $exists = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($exists) {
                $errors[] = $errors_type[$type]['exist'];
            } else {

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




// Gestion suppression (GET param)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($cnx, "DELETE FROM $table WHERE id_$type = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: gestion.php?type=$type");
    exit;
}


// Gestion modification (simple)
// On affiche un formulaire si ?edit=ID est dans l’URL
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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['edit_valeur'])) {
    $id = intval($_POST['edit_id']);
    $valeur = trim($_POST['edit_valeur']);
    if ($valeur !== '') {
        if ($champ_type === 'i') {
            $valeur = (int)$valeur;
        }

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



// Récupérer la liste des marques
$sql = "SELECT * FROM $table ORDER BY $champ ASC";
$result = mysqli_query($cnx, $sql);
$items = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_free_result($result);
