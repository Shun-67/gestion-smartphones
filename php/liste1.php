<?php
require_once 'bd_connexion.php'; // Connexion MySQL
require_once 'init_session.php';
// require_once 'test_ajout.php';   
$id = $_SESSION['id'];

$stmt = mysqli_prepare($cnx, 'SELECT role FROM utilisateurs WHERE id_utilisateur = ?');
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$user_role = mysqli_fetch_assoc($result) ?? 'simple';
$role = $user_role['role'];

// Récupération des smartphones
$stmt = mysqli_prepare($cnx, 'SELECT * FROM smartphones');
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$phones = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Liste des Smartphones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/sidebar.css" rel="stylesheet">
    <link href="../css/liste1.css" rel="stylesheet">
</head>

<body>

    <div class="search-bar">
        <div class="filter">
            All
            <span class="span_test"></span>
        </div>
        <div class="separator"></div>
        <div class="search-icon"><i class="ri-search-line"></i></div>
        <input type="text" placeholder="Search 3058 Icons">
        <div class="separator"></div>
        <div class="folder-icon"><i class="ri-folder-line"></i></div>
    </div>

    <?php include 'sidebar.php'; ?>
    <div class="toolbar">
        <a href="ajouter.php" title="Ajouter"><i class="ri-add-line"></i></a>
        <a href="rechercher.php" title="Rechercher"><i class="ri-search-line"></i></a>
        <a href="exporter.php" title="Exporter"><i class="ri-download-2-line"></i></a>
        <div class="position">
            <span class="span_test">All</span>
        </div>
    </div>
    <?= $role ?>
    <div class="main-content">
        <h2>📱 Smartphones disponibles</h2>

        <div class="grid">
            <?php foreach ($phones as $phone): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($phone["photo"]) ?>" alt="Smartphone">
                    <h3><?= htmlspecialchars($phone['nom']) ?></h3>
                    <p><?= number_format($phone['prix'], 0, ',', ' ') ?> FCFA</p>

                    <div class="actions">
                        <a href="details.php?id=<?= $phone['id'] ?>" class="details"><i class="ri-eye-fill"></i></a>
                        <?php if ($role === 'admin'): ?>
                            <a href="modifier.php?id=<?= $phone['id'] ?>" class="edit"><i class="ri-edit-2-fill"></i></a>
                            <a href="supprimer.php?id=<?= $phone['id'] ?>" class="delete" onclick="return confirm('Supprimer ce smartphone ?')"><i class="ri-delete-bin-6-fill"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>