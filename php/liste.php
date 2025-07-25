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
    <link href="../css/liste.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2> Smartphones disponibles</h2>
        <div class="search-bar">
            <div class="filter">
                All
                <span class="span_test"></span>
            </div>
            <div class="separator"></div>
            <div class="search-icon"><i class="ri-search-line"></i></div>
            <input type="text" placeholder="Search 3058 Icons">
            <div class="separator"></div>
            <div class="add-icon"><i class="ri-add-circle-fill"></i></div>
        </div>

        <form method="GET" class="toolbar">
            <div class="toolbar-left">
                <label for="marque-select">Filtrer :</label>
                <div class="select-wrapper">
                    <select id="marque-select" name="marque" onchange="this.form.submit()">
                        <option value="all" <?= (!isset($_GET['marque']) || $_GET['marque'] === 'all') ? 'selected' : '' ?>>All</option>
                        <option value="Samsung" <?= (isset($_GET['marque']) && $_GET['marque'] === 'Samsung') ? 'selected' : '' ?>>Samsung</option>
                        <option value="Apple" <?= (isset($_GET['marque']) && $_GET['marque'] === 'Apple') ? 'selected' : '' ?>>Apple</option>
                        <option value="Huawei" <?= (isset($_GET['marque']) && $_GET['marque'] === 'Huawei') ? 'selected' : '' ?>>Huawei</option>
                    </select>
                </div>
            </div>

            <div class="toolbar-right">
                <a href="ajouter.php" class="btn">➕ Ajouter</a>
                <input type="text" name="recherche" placeholder="Recherche..." />
                <?php if ($role === 'admin'): ?>
                    <a href="parametres.php" class="btn">⚙️ Paramètres</a>
                <?php endif; ?>
            </div>
        </form>


        <div class="grid">
            <?php foreach ($phones as $phone): ?>
                <div class="card">
                    <?php
                    $photo = !empty($phone["photo"]) ? htmlspecialchars($phone["photo"]) : 'images/default.jpg';
                    ?>
                    <div>
                        <img src="<?= htmlspecialchars($phone["photo"]) ?>" alt="Smartphone">
                    </div>
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