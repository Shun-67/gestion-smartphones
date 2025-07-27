<?php
require_once 'init_session.php'; // ou tout fichier qui initialise la session

if (!isset($_SESSION['role'])) {
    header("Location: login_form.php");
    exit;
}

$role = $_SESSION['role'];
$currentPage = 'parametres.php';

if ($role !== 'admin') {
    header('Location: index.php'); // ou toute autre page d'accueil
    exit;
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/parametres.css" rel="stylesheet">
    <title>Paramètres</title>
</head>

<body>
    <aside><?php include 'sidebar.php'; ?></aside>

    <main>
        <div class="container">
            <h1 class="page-title">Paramètres</h1>

            <div class="settings-grid">
                <a class="setting-card" href="gestion_utilisateurs.php">
                    <h2>Utilisateurs</h2>
                    <p>Gérer les comptes utilisateurs (admin et simples).</p>
                </a>

                <a class="setting-card" href="gestion.php?type=marque&from=parametres">
                    <h2>Marques</h2>
                    <p>Ajouter, modifier ou supprimer des marques.</p>
                </a>

                <a class="setting-card" href="gestion.php?type=ram&from=parametres">
                    <h2>RAM</h2>
                    <p>Gérer les tailles de mémoire RAM.</p>
                </a>

                <a class="setting-card" href="gestion.php?type=rom&from=parametres">
                    <h2>ROM</h2>
                    <p>Gérer les capacités de stockage.</p>
                </a>

                <a class="setting-card" href="gestion.php?type=couleur&from=parametres">
                    <h2>Couleurs</h2>
                    <p>Ajouter, modifier ou supprimer les couleurs disponibles.</p>
                </a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>

</html>