<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/liste_smart.css">
    <title>Gestion des Smartphones</title>
</head>
<body>
    <div class="container">
        <!-- Barre de navigation latérale -->
        <div class="sidebar">
            <h2>Mon Compte</h2>
            <a href="view_smartphones.php">Smartphones</a>
            <a href="add_smartphone.php">Ajouter un Smartphone</a>
            <a href="account.php">Mon Compte</a>
            <a href="settings.php" class="admin-only">Paramètres</a> <!-- Visible uniquement pour les admins -->
            <a href="logout.php">Déconnexion</a>
        </div>

        <!-- Contenu principal -->
        <div class="main-content">
            <h1>Bienvenue dans la Gestion des Smartphones</h1>
            <!-- Contenu dynamique ici -->
            <div class="alert alert-success">Smartphone ajouté avec succès !</div>
            <div class="smartphone-card">
                <h2>Nom du Smartphone</h2>
                <img src="url_de_l_image.jpg" alt="Nom du Smartphone">
                <p>Prix: 499 €</p>
                <a href="view_smartphone.php?id=1">Détails</a>
                <a href="edit_smartphone.php?id=1">Modifier</a>
                <a href="delete_smartphone.php?id=1">Supprimer</a>
            </div>
        </div>
    </div>
</body>
</html>
