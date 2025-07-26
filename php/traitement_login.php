<?php
require_once 'init_session.php'; // Pour démarrer la session
require_once 'bd_connexion.php'; // Pour la connexion à la base de données

// Vérifier si les données proviennent bien de la validation
if (!isset($_SESSION['form_data'])) {
    header('Location: login_form.php');
    exit;
}

// Récupération des données issues du formulaire et on vide $_SESSION['form_data']
$formData = $_SESSION['form_data'];
$form = $_GET['form'] ?? ''; // Utiliser une valeur par défaut
unset($_SESSION['form_data']);

// Extraction des variables et de leurs valeurs
// extract($formData);



if ($form == 'inscription') {
    // Traitement de l'inscription
    traitement_login_inscription($formData);
} elseif ($form == 'connexion' || $form == '') {
    // Traitement de la connexion
    traitement_login_connexion($formData);
}

// Fonction pour traiter la connexion
function traitement_login_connexion($vformData)
{
    global $cnx;

    global $formData;
    $_SESSION['old_input'] = $formData;

    $vlogin = trim($vformData['login']);
    $vpassword = trim($vformData['password']);

    // Préparer la requête pour éviter les injections SQL
    $stmt = mysqli_prepare($cnx, 'SELECT id_utilisateur, password FROM utilisateurs WHERE login = ?');
    mysqli_stmt_bind_param($stmt, "s", $vlogin);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $utilisateur = mysqli_fetch_assoc($result);


    if ($utilisateur) {
        // Vérifier le mot de passe
        if (password_verify($vpassword, $utilisateur['password'])) {
            $_SESSION['id'] = $utilisateur['id_utilisateur'];
            unset($_SESSION['old_input']);
            header('Location: liste.php');
            exit;
        } else {
            $_SESSION['login_error'] = "Mot de passe incorrect.";
            header('Location: login_form.php?form=connexion');
            exit;
        }
    } else {
        $_SESSION['login_error'] = "Nom d'utilisateur non trouvé.";
        header('Location: login_form.php?form=connexion');
        exit;
    }
}

// Fonction pour traiter l'inscription
function traitement_login_inscription($vformData)
{
    global $cnx;

    global $formData;
    $_SESSION['old_input'] = $formData;

    $vlogin = trim($vformData['login']);
    $vpassword = trim($vformData['password']);

    // Vérification si l'utilisateur existe déjà
    $stmt = mysqli_prepare($cnx, "SELECT id_utilisateur FROM utilisateurs WHERE login = ?");
    mysqli_stmt_bind_param($stmt, "s", $vlogin);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existingUser  = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($existingUser) {
        $_SESSION['login_error'] = "Ce nom d'utilisateur est déjà pris.";
        header('Location: login_form.php?form=inscription');
        exit;
    }

    // Préparer l'insertion de l'utilisateur
    $stmt = mysqli_prepare($cnx, "INSERT INTO utilisateurs (login, password) VALUES (?, ?)");
    $hashedPassword = password_hash(trim($vpassword), PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "ss", trim($vlogin), $hashedPassword);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION['id'] = mysqli_insert_id($cnx); // Récupérer l'ID de l'utilisateur nouvellement créé
    unset($_SESSION['old_input']);
    header('Location: liste.php?');
    exit;
}
