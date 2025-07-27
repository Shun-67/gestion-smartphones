<?php
require_once 'bd_connexion.php';
require_once 'init_session.php';

if (!isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit;
}

$currentPage = 'compte.php';

$role = $_SESSION['role'];

$user_id = $_SESSION['id'];
$errors = [];
$success = "";

// Récupérer login actuel
$stmt = mysqli_prepare($cnx, "SELECT login FROM utilisateurs WHERE id_utilisateur = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    die("Utilisateur introuvable.");
}

// Traitement suppression compte
if (isset($_POST['delete_account'])) {
    // Supprimer utilisateur
    $stmt = mysqli_prepare($cnx, "DELETE FROM utilisateurs WHERE id_utilisateur = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        session_destroy();
        header("Location: login_form.php");
        exit;
    } else {
        $errors[] = "Erreur lors de la suppression du compte.";
    }
}

// Traitement modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_account'])) {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$login) $errors[] = "Le login est requis.";

    // Vérifier unicité si login modifié
    if ($login !== $user['login']) {
        $stmt = mysqli_prepare($cnx, "SELECT COUNT(*) FROM utilisateurs WHERE login = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, "si", $login, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        if ($count > 0) {
            $errors[] = "Ce login est déjà utilisé.";
        }
    }

    // Mot de passe optionnel
    if ($password !== '') {
        if (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }
        if ($password !== $password_confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
    }

    if (empty($errors)) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($cnx, "UPDATE utilisateurs SET login = ?, password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $login, $hash, $user_id);
        } else {
            $stmt = mysqli_prepare($cnx, "UPDATE utilisateurs SET login = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $login, $user_id);
        }
        if (mysqli_stmt_execute($stmt)) {
            $success = "Profil mis à jour avec succès.";
            $user['login'] = $login;
        } else {
            $errors[] = "Erreur lors de la mise à jour.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Mon compte</title>
    <link href="../css/layout.css" rel="stylesheet" />
    <link href="../css/compte.css" rel="stylesheet" />
</head>

<body>
    <aside><?php include 'sidebar.php'; ?></aside>
    <main>
        <h1>Mon compte</h1>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="mon_compte.php" class="form-ajout">
            <label>Login :</label>
            <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required>

            <label>Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" name="password">

            <label>Confirmer nouveau mot de passe :</label>
            <input type="password" name="password_confirm">

            <button type="submit" class="btn">Modifier mon profil</button>
        </form>

        <form method="post" action="mon_compte.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
            <button type="submit" name="delete_account" class="delete-btn">Supprimer mon compte</button>
        </form>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>

</html>