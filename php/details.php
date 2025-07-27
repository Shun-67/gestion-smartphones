<?php
// Inclure les fichiers nécessaires
require_once 'bd_connexion.php'; // Connexion à la base de données
require_once 'init_session.php'; // Démarrage de la session

// Définir la page active pour la barre latérale
$currentPage = 'liste.php';

// Récupérer le rôle de l'utilisateur depuis la session
$role = $_SESSION['role'];

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    // Si non, rediriger vers la page de connexion
    header("Location: login_form.php");
    exit;
}

// Récupérer l'ID du smartphone depuis l'URL
$id = intval($_GET['id']);

// Requête pour récupérer les détails du smartphone
$sql = "SELECT s.*, nom_marque, capacite_ram, capacite_rom
        FROM smartphones s
        LEFT JOIN marques m ON s.id_marque = m.id_marque
        LEFT JOIN rams ON s.id_ram = rams.id_ram
        LEFT JOIN roms ON s.id_rom = roms.id_rom
        WHERE s.id = ?";
// Préparation de la requête (sécurité contre les injections SQL)
$stmt = mysqli_prepare($cnx, $sql);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
// Récupération des données du smartphone
$phone = mysqli_fetch_assoc($result);
// Extraire les champs dans des variables individuelles (ex: $nom, $prix, etc.)
extract($phone);

// Si aucun smartphone trouvé, afficher un message d'erreur
if (!$phone) {
    echo "<p>Ce téléphone n'existe pas.</p>";
    exit;
}

// Requête pour récupérer les couleurs disponibles pour ce smartphone
$sql_couleurs = "SELECT nom_couleur, code_hex
                 FROM smartphone_couleurs sc
                 JOIN couleurs c ON sc.id_couleur = c.id_couleur
                 WHERE sc.id = ?";
$stmt = mysqli_prepare($cnx, $sql_couleurs);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
// Récupérer toutes les couleurs associées
$couleurs = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails - <?= htmlspecialchars($nom) ?></title>
    <!-- Icônes Remix Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css  ">
    <!-- Styles CSS -->
    <link rel="stylesheet" href="../css/details.css">
    <link rel="stylesheet" href="../css/layout.css">
</head>
<body>
    <!-- Barre latérale de navigation -->
    <aside><?php include 'sidebar.php'; ?></aside>

    <!-- Contenu principal -->
    <main>
        <div class="container">
            <h1 class="page-title">Détails du smartphone</h1>
            <div class="card">
                <!-- Photo du smartphone -->
                <img src="..<?= htmlspecialchars($photo) ?>" alt="Photo du téléphone" class="photo">

                <!-- Informations du smartphone -->
                <div class="info">
                    <h2><?= htmlspecialchars($nom) ?></h2>
                    <p><strong>Marque :</strong> <?= htmlspecialchars($nom_marque) ?></p>
                    <p><strong>Prix :</strong> <?= number_format($prix, 0, ',', ' ') ?> FCFA</p>
                    <p><strong>RAM :</strong> <?= htmlspecialchars($capacite_ram) ?> Go</p>
                    <p><strong>ROM :</strong> <?= htmlspecialchars($capacite_rom) ?> Go</p>
                    <p><strong>Écran :</strong> <?= htmlspecialchars($ecran) ?></p>
                    
                    <!-- Description -->
                    <div>
                        <p><strong>Description :</strong></p><br>
                        <p><?= htmlspecialchars($description) ?></p>
                    </div>

                    <!-- Affichage des couleurs disponibles -->
                    <?php if ($couleurs): ?>
                        <div>
                            <p><strong>Couleurs disponibles :</strong></p>
                            <div class="couleurs">
                                <?php foreach ($couleurs as $c): ?>
                                    <!-- Petit cercle de couleur (pastille) -->
                                    <span class="pastille" title="<?= htmlspecialchars($c['nom_couleur']) ?>"
                                        style="background-color: <?= htmlspecialchars($c['code_hex']) ?>;"></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Actions selon le rôle -->
                    <div class="actions">
                        <!-- Retour à la liste -->
                        <a href="liste.php" class="btn">← Retour</a>

                        <!-- Boutons visibles uniquement pour l'admin -->
                        <?php if ($role === 'admin'): ?>
                            <a href="modifier.php?id=<?= $phone['id'] ?>" class="btn modifier">Modifier</a>
                            <a href="supprimer.php?id=<?= $phone['id'] ?>" class="btn supprimer"
                                onclick="return confirm('Supprimer ce téléphone ?')">Supprimer</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>
</html>