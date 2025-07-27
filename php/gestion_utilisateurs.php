<?php
// Inclure les fichiers nécessaires
require_once 'bd_connexion.php'; // Connexion à la base de données
require_once 'init_session.php'; // Démarrage de la session

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    // Si non, rediriger vers la page de connexion
    header("Location: login_form.php");
    exit;
}

// Récupérer le filtre de rôle depuis l'URL (ex: ?role=admin)
// Par défaut : 'all' → affiche tous les utilisateurs
$roleFilter = $_GET['role'] ?? 'all';

// Récupérer le rôle de l'utilisateur connecté
$role = $_SESSION['role'];

// Traitement de l'action : basculer le rôle d'un utilisateur
if (isset($_GET['toggle_admin'])) {
    $userId = intval($_GET['toggle_admin']); // Sécuriser l'ID

    // Récupérer le rôle actuel de l'utilisateur
    $stmt = $cnx->prepare("SELECT role FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    
    if ($stmt->fetch()) {
        // Inverser le rôle : admin ↔ simple
        $newRole = $role === 'admin' ? 'simple' : 'admin';
        $stmt->close();

        // Mettre à jour le rôle dans la base
        $stmtUpdate = $cnx->prepare("UPDATE utilisateurs SET role = ? WHERE id_utilisateur = ?");
        $stmtUpdate->bind_param("si", $newRole, $userId);
        $stmtUpdate->execute();
    }
    // Rediriger pour éviter la resoumission
    header("Location: gestion_utilisateurs.php?role=$roleFilter");
    exit;
}

// Récupération des utilisateurs depuis la base
$sql = "SELECT id_utilisateur, login, role FROM utilisateurs";
$params = [];

// Ajouter un filtre si un rôle est sélectionné
if ($roleFilter !== 'all') {
    $sql .= " WHERE role = ?";
    $params[] = $roleFilter;
}

// Trier par nom d'utilisateur
$sql .= " ORDER BY login ASC";

// Préparer et exécuter la requête
$stmt = $cnx->prepare($sql);
if ($params) {
    $stmt->bind_param("s", ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Récupérer tous les utilisateurs sous forme de tableau associatif
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <!-- Chargement des icônes Remix Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css  ">
    <!-- Feuilles de style personnalisées -->
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/gestion.css" rel="stylesheet">
    <title>Gestion Utilisateurs</title>
</head>

<body>
    <!-- Barre latérale de navigation -->
    <aside><?php include 'sidebar.php'; ?></aside>
    
    <!-- Contenu principal -->
    <main>
        <h1 class="page-title">Gestion des utilisateurs</h1>

        <!-- Formulaire de filtrage par rôle -->
        <form method="get" style="margin-bottom: 20px;">
            <label for="role">Filtrer par rôle :</label>
            <select name="role" id="role" onchange="this.form.submit()">
                <option value="all" <?= $roleFilter === 'all' ? 'selected' : '' ?>>Tous</option>
                <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Administrateurs</option>
                <option value="simple" <?= $roleFilter === 'simple' ? 'selected' : '' ?>>Simples utilisateurs</option>
            </select>
        </form>

        <!-- Tableau des utilisateurs -->
        <div class="list">
            <table>
                <thead>
                    <tr>
                        <th>Nom d’utilisateur</th>
                        <th>Rôle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Boucle sur chaque utilisateur -->
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['login']) ?></td>
                            <td><?= htmlspecialchars($u['role']) ?></td>
                            <td>
                                <?php
                                // Déterminer si l'utilisateur est admin
                                $isAdmin = $u['role'] === 'admin';
                                // Définir le libellé du bouton
                                $labelRole = $isAdmin ? 'Passer en utilisateur simple' : 'Passer en admin';
                                ?>
                                <!-- Lien pour basculer le rôle -->
                                <a href="gestion_utilisateurs.php?toggle_admin=<?= $u['id_utilisateur'] ?>&role=<?= $roleFilter ?>" class="icon icon-edit"
                                    onclick="return confirm('Confirmer la modification du rôle ?');">
                                    <i class="ri-user-settings-line"></i>
                                    <?= $labelRole ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <!-- Message si aucun utilisateur trouvé -->
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4">Aucun utilisateur trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Lien de retour vers les paramètres -->
        <button class="retour"><a href="parametres.php">← Retour aux paramètres</a></button>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>
</html>