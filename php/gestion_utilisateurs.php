<?php
require_once 'bd_connexion.php'; // Connexion à la BDD
require_once 'init_session.php';

if (!isset($_SESSION['id'])) {
    header("Location: login_form.php");
    exit;
}

// Gestion des rôles
$roleFilter = $_GET['role'] ?? 'all';
$role = $_SESSION['role'];

// Traitement des actions
if (isset($_GET['toggle_admin'])) {
    $userId = intval($_GET['toggle_admin']);
    $stmt = $cnx->prepare("SELECT role FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    if ($stmt->fetch()) {
        $newRole = $role === 'admin' ? 'simple' : 'admin';
        $stmt->close();

        $stmtUpdate = $cnx->prepare("UPDATE utilisateurs SET role = ? WHERE id_utilisateur = ?");
        $stmtUpdate->bind_param("si", $newRole, $userId);
        $stmtUpdate->execute();
    }
    header("Location: gestion_utilisateurs.php?role=$roleFilter");
    exit;
}

// Récupération des utilisateurs
$sql = "SELECT id_utilisateur, login, role FROM utilisateurs";
$params = [];
if ($roleFilter !== 'all') {
    $sql .= " WHERE role = ?";
    $params[] = $roleFilter;
}
$sql .= " ORDER BY login ASC";

$stmt = $cnx->prepare($sql);
if ($params) {
    $stmt->bind_param("s", ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/gestion.css" rel="stylesheet">
    <title>Gestion Utilisateurs</title>
</head>

<body>
    <aside><?php include 'sidebar.php'; ?></aside>
    <main>
        <h1 class="page-title">Gestion des utilisateurs</h1>

        <!-- Filtrage par rôle -->
        <form method="get" style="margin-bottom: 20px;">
            <label for="role">Filtrer par rôle :</label>
            <select name="role" id="role" onchange="this.form.submit()">
                <option value="all" <?= $roleFilter === 'all' ? 'selected' : '' ?>>Tous</option>
                <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Administrateurs</option>
                <option value="simple" <?= $roleFilter === 'simple' ? 'selected' : '' ?>>Simples utilisateurs</option>
            </select>
        </form>

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
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['login']) ?></td>
                            <td><?= htmlspecialchars($u['role']) ?></td>
                            <td>
                                <?php
                                $isAdmin = $u['role'] === 'admin'; // Ou 1 si tu utilises des entiers
                                $labelRole = $isAdmin ? 'Passer en utilisateur simple' : 'Passer en admin';
                                $newRole = $isAdmin ? 'utilisateur' : 'admin';
                                ?>
                                <a href="gestion_utilisateurs.php?toggle_admin=<?= $u['id_utilisateur'] ?>&role=<?= $roleFilter ?>" class="icon icon-edit"
                                    onclick="return confirm('Confirmer la modification du rôle ?');">
                                    <i class="ri-user-settings-line"></i>
                                    <?= $labelRole ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4">Aucun utilisateur trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button class="retour"><a href="parametres.php">← Retour aux paramètres</a></button>
    </main>
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>

</html>