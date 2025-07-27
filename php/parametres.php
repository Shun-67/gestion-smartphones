<?php
// Inclure le fichier d'initialisation de session
require_once 'init_session.php'; // ou tout fichier qui initialise la session

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    // Si non, rediriger vers la page de connexion
    header("Location: login_form.php");
    exit;
}

// Récupérer le rôle de l'utilisateur
$role = $_SESSION['role'];

// Définir la page active pour la barre latérale
$currentPage = 'parametres.php';

// Vérifier que seul l'administrateur peut accéder à cette page
if ($role !== 'admin') {
    // Si ce n'est pas un admin, rediriger vers la page d'accueil
    header('Location: index.php'); // ou toute autre page d'accueil
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Chargement des icônes Remix Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css  ">
    <!-- Feuilles de style personnalisées -->
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/parametres.css" rel="stylesheet">
    <title>Paramètres</title>
</head>

<body>
    <!-- Barre latérale de navigation -->
    <aside><?php include 'sidebar.php'; ?></aside>

    <!-- Contenu principal -->
    <main>
        <div class="container">
            <h1 class="page-title">Paramètres</h1>

            <!-- Grille des options de gestion -->
            <div class="settings-grid">
                <!-- Lien vers la gestion des utilisateurs -->
                <a class="setting-card" href="gestion_utilisateurs.php">
                    <h2>Utilisateurs</h2>
                    <p>Gérer les comptes utilisateurs (admin et simples).</p>
                </a>

                <!-- Lien vers la gestion des marques -->
                <a class="setting-card" href="gestion.php?type=marque&from=parametres">
                    <h2>Marques</h2>
                    <p>Ajouter, modifier ou supprimer des marques.</p>
                </a>

                <!-- Lien vers la gestion des RAM -->
                <a class="setting-card" href="gestion.php?type=ram&from=parametres">
                    <h2>RAM</h2>
                    <p>Gérer les tailles de mémoire RAM.</p>
                </a>

                <!-- Lien vers la gestion des ROM -->
                <a class="setting-card" href="gestion.php?type=rom&from=parametres">
                    <h2>ROM</h2>
                    <p>Gérer les capacités de stockage.</p>
                </a>

                <!-- Lien vers la gestion des couleurs -->
                <a class="setting-card" href="gestion.php?type=couleur&from=parametres">
                    <h2>Couleurs</h2>
                    <p>Ajouter, modifier ou supprimer les couleurs disponibles.</p>
                </a>
            </div>
        </div>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>
</html>