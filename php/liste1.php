<?php
require_once 'init_session.php';
require_once 'bd_connexion.php';

// Récupérer les marques
$marques = mysqli_query($cnx, "SELECT id_marque, nom_marque FROM marques");

// Traitement des filtres
$marqueId = $_GET['marque'] ?? 'all';
$recherche = $_GET['recherche'] ?? '';
$tri = $_GET['tri'] ?? 'alpha';

$role = $_SESSION['role'];

$smartphones = [];
$nomMarqueSelectionnee = null;

if ($marqueId !== 'all') {
    // Récupérer le nom de la marque sélectionnée
    $stmt_marque = mysqli_prepare($cnx, "SELECT nom_marque FROM marques WHERE id_marque = ?");
    mysqli_stmt_bind_param($stmt_marque, "i", $marqueId);
    mysqli_stmt_execute($stmt_marque);
    mysqli_stmt_bind_result($stmt_marque, $nomMarqueSelectionnee);
    mysqli_stmt_fetch($stmt_marque);
    mysqli_stmt_close($stmt_marque);

    // Requête avec filtre de marque
    $query = "SELECT s.* FROM smartphones s WHERE s.marque_id = ?";
    if (!empty($recherche)) {
        $query .= " AND s.nom LIKE ?";
    }
    $query .= ($tri === 'prix') ? " ORDER BY s.prix ASC" : " ORDER BY s.nom ASC";

    $stmt = mysqli_prepare($cnx, $query);
    if (!empty($recherche)) {
        $searchTerm = "%$recherche%";
        mysqli_stmt_bind_param($stmt, "is", $marqueId, $searchTerm);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $marqueId);
    }
} else {
    // Requête sans filtre de marque
    $query = "SELECT s.* FROM smartphones s WHERE 1";
    if (!empty($recherche)) {
        $query .= " AND s.nom LIKE ?";
    }
    $query .= ($tri === 'prix') ? " ORDER BY s.prix ASC" : " ORDER BY s.nom ASC";

    $stmt = mysqli_prepare($cnx, $query);
    if (!empty($recherche)) {
        $searchTerm = "%$recherche%";
        mysqli_stmt_bind_param($stmt, "s", $searchTerm);
    }
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $smartphones[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste Smartphones</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; }
        .toolbar {
            position: sticky; top: 0; background: #f8f8f8;
            display: flex; gap: 1rem; align-items: center;
            padding: 10px 20px; box-shadow: 0 1px 5px rgba(0,0,0,0.1);
            z-index: 100;
        }
        .toolbar form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .toolbar input, select { padding: 5px; }
        .grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px; padding: 20px;
        }
        .card {
            border: 1px solid #ddd; border-radius: 8px;
            padding: 10px; background: #fff; text-align: center;
        }
        .card img { width: 100%; height: auto; object-fit: contain; }
        .titre-marque {
            margin: 20px; font-size: 1.5em; color: #007bff;
            border-bottom: 2px solid #007bff; padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <form method="get">
            <select name="marque">
                <option value="all">Toutes les marques</option>
                <?php while ($m = mysqli_fetch_assoc($marques)): ?>
                    <option value="<?= $m['id_marque'] ?>" <?= ($marqueId == $m['id_marque']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom_marque']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="text" name="recherche" placeholder="Recherche..." value="<?= htmlspecialchars($recherche) ?>">

            <select name="tri">
                <option value="alpha" <?= $tri === 'alpha' ? 'selected' : '' ?>>Ordre alphabétique</option>
                <option value="prix" <?= $tri === 'prix' ? 'selected' : '' ?>>Prix croissant</option>
            </select>

            <button type="submit">Rechercher</button>
            <?php if ($role === 'admin'): ?>
                <a href="ajouter.php" title="Ajouter"><i class="ri-add-line"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($marqueId !== 'all' && $nomMarqueSelectionnee): ?>
        <h2 class="titre-marque"><?= htmlspecialchars($nomMarqueSelectionnee) ?></h2>
    <?php endif; ?>

    <div class="grid">
        <?php foreach($smartphones as $phone): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($phone['photo']) ?>" alt="Smartphone">
                <h3><?= htmlspecialchars($phone['nom']) ?></h3>
                <p><?= number_format($phone['prix'], 0, ',', ' ') ?> FCFA</p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>