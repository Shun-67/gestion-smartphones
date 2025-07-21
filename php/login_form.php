<?php
require_once 'init_session.php'; // Pour démarrer la session
$empty_errors = $_SESSION['form_errors'] ?? [];
$login_error = $_SESSION['login_error'] ?? '';
$oldInput = $_SESSION['old_input'] ?? [];

$form = $_GET['form'] ?? '';

// Nettoyer les erreurs et les anciennes entrées après utilisation
unset($_SESSION['form_errors'], $_SESSION['old_input']);
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="../css/login_form.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <?php if (isset($_GET['erreur'])): ?>
        <p style='color: red;'>Identifiants incorrects</p>
    <?php endif; ?>

    <div class="body-container">
        <div class="bg-overlay"></div>
        <div class="login-container">
            <div class="login-card">
                <div class="p-lg-5 p-4">
                    <h5 class="text-primary">Bienvenue!</h5>
                    <?php if ($form == 'connexion'): ?>
                        <p class="text-muted">Se connecter pour continuer...</p>
                    <?php elseif ($form == 'inscription' ): ?>
                        <p class="text-muted">S'inscrire pour continuer..</p>
                    <?php endif; ?>
                    <!-- <p class="text-muted">Se conecter pour continuer...</p> -->

                    <?php if (isset($_SESSION['login_error'])): ?>
                        <div class="login-error-message"><?= $_SESSION['login_error'] ?></div>
                        <?php unset($_SESSION['login_error']); ?>
                    <?php endif; ?>

                    <?php if ($form == 'connexion' || $form == ''){$action = 'verif_form.php';}
                        elseif ($form == 'inscription') {$action = 'verif_form.php?form=inscription';}   
                    ?>

                    <form method="POST" action=<?= $action ?>>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="login"
                                value="<?= htmlspecialchars($oldInput['login'] ?? '') ?>" placeholder="Nom d'utilisateur">
                            <?php if (isset($empty_errors['login'])): ?>
                                <span class="error-message"><?= $empty_errors['login'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password-input">Mot de passe</label>
                            <input type="password" class="form-control" name="password"
                                placeholder="Mot de passe" id="password">
                            <?php if (isset($empty_errors['password'])): ?>
                                <span class="error-message"><?= $empty_errors['password'] ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if ($form == 'inscription'): ?>
                            <div class="mb-3">
                                <label class="form-label" for="confirm-password">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" name="confirm_password"
                                    placeholder="Confirmer le mot de passe" id="confirm-password">
                                <?php if (isset($empty_errors['confirm_password'])): ?>
                                    <span class="error-message"><?= $empty_errors['confirm_password'] ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>


                        <div class="mt-4">
                            <?php if ($form === 'connexion' || $form == ''): ?>
                                <button class="btn btn-succes w-100" type="submit">Se connecter</button>
                            <?php elseif ($form === 'inscription'): ?>
                                <button class="btn btn-succes w-100" type="submit">S'inscrire</button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if($form === 'connexion' || $form == ''): ?>
                        <div class="mt-5 text-center">
                            <p class="mb-0">Pas encore inscrit ? <a href="login_form.php?form=inscription" class="fw-semibold text-primary text-decoration-underline"> Créer un compte</a></p>
                        </div>
                    <?php elseif($form === 'inscription'): ?>
                        <div class="mt-5 text-center">
                            <p class="mb-0">Déjà inscrit ? <a href="login_form.php?form=connexion" class="fw-semibold text-primary text-decoration-underline"> Se connecter</a></p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</body>

</html>