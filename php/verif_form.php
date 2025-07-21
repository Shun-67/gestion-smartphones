<?php
require_once 'init_session.php'; // Pour démarrer la session

$form = $_GET['form'] ?? '';

function validateFormFields($data, $form) {
    $errors = [];
    
    if (empty($data['login'])) {
        $errors['login'] = "*Nom d'utilisateur requis.";
    }
    
    if (empty($data['password'])) {
        $errors['password'] = "*Mot de passe requis.";
    }
    
    if ($form == 'inscription' && empty($data['confirm_password'])) {
        $errors['confirm_password'] = "*Confirmation du mot de passe requise.";
    } elseif ($form == 'inscription' && $data['password'] != $data['confirm_password']) {
        $errors['confirm_password'] = "*Les mots de passe ne correspondent pas.";
    }
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = validateFormFields($_POST, $form);
    
    if (empty($errors)) {
        // Stocker les données en session pour éviter de les perdre
        $_SESSION['form_data'] = $_POST;
        header('Location: traitement_login.php?form=' . $form);
        exit;
    } else {
        // Rediriger vers le formulaire avec les erreurs
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: login_form.php?form=' . $form);
        exit;
    }
}
