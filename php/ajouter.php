<?php
require_once 'bd_connexion.php';
require_once 'init_session.php';

// Vérifie si l'utilisateur est admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: liste.php');
    exit;
}

// Récupération des options pour les listes déroulantes
$marques = $cnx->query("SELECT id, nom FROM marques")->fetchAll();
$rams = $cnx->query("SELECT id, taille FROM ram")->fetchAll();
$roms = $cnx->query("SELECT id, taille FROM rom")->fetchAll();
$couleurs = $cnx->query("SELECT id, nom FROM couleurs")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $marque_id = intval($_POST['marque_id'] ?? 0);
    $ram_id = intval($_POST['ram_id'] ?? 0);
    $rom_id = intval($_POST['rom_id'] ?? 0);
    $photo = trim($_POST['photo'] ?? '');
    $couleurs_id = $_POST['couleurs'] ?? [];

    if (!$nom) $errors[] = "Nom requis.";
    if ($prix <= 0) $errors[] = "Prix invalide.";
    if (!$marque_id || !$ram_id || !$rom_id) $errors[] = "Tous les champs doivent être remplis.";
    if (!$photo) $errors[] = "Lien de la photo requis.";

    if (empty($errors)) {
        // Insertion dans smartphones
        $stmt = $cnx->prepare("INSERT INTO smartphones (nom, prix, photo, marque_id, ram_id, rom_id)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prix, $photo, $marque_id, $ram_id, $rom_id]);
        $smartphone_id = $cnx->lastInsertId();

        // Insertion des couleurs
        $stmt = $cnx->prepare("INSERT INTO smartphone_couleur (smartphone_id, couleur_id) VALUES (?, ?)");
        foreach ($couleurs_id as $cid) {
            $stmt->execute([$smartphone_id, $cid]);
        }

        header("Location: details.php?id=" . $smartphone_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/ajouter.css" rel="stylesheet">
    <title>Ajouter smartphone</title>
</head>

<body>
    <aside><?php require_once 'sidebar.php' ?></aside>
    <main>
        <div class="container">
            <h2 class="page-title">Ajouter un smartphone</h2>

            <?php if ($errors): ?>
                <div style="color: red; margin-bottom: 15px;">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="form-ajout">
                <label>Nom :</label>
                <input type="text" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

                <label>Prix (FCFA) :</label>
                <input type="number" name="prix" step="1" required value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>">

                <label>Photo (URL ou chemin) :</label>
                <input type="text" name="photo" required value="<?= htmlspecialchars($_POST['photo'] ?? '') ?>">

                <label>Marque :</label>
                <select name="marque_id" required>
                    <option value="">-- Choisir une marque --</option>
                    <?php foreach ($marques as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($_POST['marque_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>RAM :</label>
                <select name="ram_id" required>
                    <option value="">-- Choisir la RAM --</option>
                    <?php foreach ($rams as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($_POST['ram_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['taille']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>ROM :</label>
                <select name="rom_id" required>
                    <option value="">-- Choisir la ROM --</option>
                    <?php foreach ($roms as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($_POST['rom_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['taille']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Couleurs disponibles :</label>
                <div class="checkboxes">
                    <?php foreach ($couleurs as $c): ?>
                        <label>
                            <input type="checkbox" name="couleurs[]" value="<?= $c['id'] ?>"
                                <?= in_array($c['id'], $_POST['couleurs'] ?? []) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($c['nom']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <br>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        </div>
    </main>
</body>

</html>