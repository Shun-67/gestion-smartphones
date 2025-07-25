<?php
require_once 'bd_connexion.php'; // Connexion MySQL
require_once 'init_session.php';
// require_once 'init_ajout.php';

//Récupérer les données transmises
$id = $_SESSION['id'];
$marque_selected = $_GET['marque'] ?? 'all';
$tri = $_GET['tri'] ?? 'alpha';

//Récupérer le role de l'utilisateur
$stmt = mysqli_prepare($cnx, 'SELECT role FROM utilisateurs WHERE id_utilisateur = ?');
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$user_role = mysqli_fetch_assoc($result) ?? 'simple';
$role = $user_role['role'];

//Récupérer les marques
$marque_query = mysqli_query($cnx, 'SELECT * FROM marques');
$marques = mysqli_fetch_all($marque_query, MYSQLI_ASSOC);
foreach ($marques as $m) {
    if ($marque_selected !== 'all' && $marque_selected == $m['id_marque']) {
        $id_marque_selected = $m['id_marque'];
    }
}

// Récupérer les smartphones
$sql = "SELECT s.*, m.nom_marque 
        FROM smartphones s
        JOIN marques m ON s.id_marque = m.id_marque";

// Ajout du filtre de marque
$params = [];
$types = "";

if ($marque_selected !== 'all') {
    $sql .= " WHERE m.nom_marque = ?";
    $params[] = $marque_selected;
    $types .= "s";
}

// Ajout du tri
if ($tri === 'prix') {
    $sql .= " ORDER BY s.prix ASC";
} else {
    $sql .= " ORDER BY s.nom ASC";
}

$stmt = mysqli_prepare($cnx, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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

        <form method="GET" class="toolbar">
            <div class="select-wrapper">
                <select id="marque-select" name="marque" onchange="this.form.submit()">
                    <option value="all" <?= $marque_selected === 'all' ? 'selected' : '' ?>>All</option>
                    <?php foreach ($marques as $m): ?>
                        <option value="<?= htmlspecialchars($m['nom_marque']) ?>" <?= $marque_selected === $m['nom_marque'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nom_marque']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="separator"></div>
            <div class="select-wrapper">
                <select id="tri-select" name="tri" onchange="this.form.submit()">
                    <option value="alpha" <?= $tri === 'alpha' ? 'selected' : '' ?>>Ordre alphabétique</option>
                    <option value="prix" <?= $tri === 'prix' ? 'selected' : '' ?>>Prix croissant</option>
                </select>
            </div>
            <div class="separator"></div>
            <div class="search-icon"><i class="ri-search-line"></i></div>
            <input type="text" name="recherche" placeholder="Recherche..." />
            <div class="separator"></div>
            <?php if ($role === 'admin'): ?>
                <div class="add-icon"><i class="ri-add-circle-fill"></i></div>
            <?php endif; ?>

        </form>

        <?php $id_marque_actuel = $id_marque_selected ?? null; ?>

        <?php foreach ($phones as $phone): ?>
            <?php if ($id_marque_actuel !== $phone['id_marque']): ?>
                <?php $id_marque_actuel =  $phone['id_marque']; ?>
                <?php foreach ($marques as $m) {
                    if ($id_marque_actuel == $m['id_marque']) {
                        $nom_marque_actuel = $m['nom_marque'];
                    }
                }
                ?>
                <h2 class="titre-marque"><?= htmlspecialchars($nom_marque_actuel) ?></h2>
            <?php endif; ?>
            <div class="grid">
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

            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>