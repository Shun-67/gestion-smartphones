<?php
require_once 'init_session.php'; // Pour démarrer la session
require_once 'bd_connexion.php'; // Pour la connexion à la base de données

// Fonction pour récupérer toutes les lignes d'un résultat mysqli
function get_all_assoc(mysqli_result $result): array
{
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

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
$login= trim($formData['login']);
$password= trim($formData['password']);


if ($form == 'inscription') {
    // Traitement de l'inscription
    traitement_login_inscription($login, $password);
} elseif ($form == 'connexion' || $form == '') {
    // Traitement de la connexion
    traitement_login_connexion($login, $password);
}

// Fonction pour traiter la connexion
function traitement_login_connexion($vlogin, $vpassword)
{
    global $cnx;

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
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['login_error'] = "Mot de passe incorrect.";
            $_SESSION['passi'] = password_verify($vpassword, $utilisateur['password']);
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
function traitement_login_inscription($vlogin, $vpassword) {
    global $cnx;

    // Vérification si l'utilisateur existe déjà
    $stmt = mysqli_prepare($cnx, "SELECT id_utilisateur FROM utilisateurs WHERE login = ?");
    mysqli_stmt_bind_param($stmt, "s", $vlogin);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existingUser  = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($existingUser ) {
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
    header('Location: index.php');
    exit;
}
?>



