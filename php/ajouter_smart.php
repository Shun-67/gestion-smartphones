<?php
require_once 'bd_connexion.php';
require_once 'init_session.php';

$currentPage = 'parametres.php';

$role = $_SESSION['role'];

if ($role !== 'admin') {
    header('Location: liste.php');
    exit;
}

// Récupération des options
$sql = "SELECT s.*, m.nom_marque AS marque, r.capacite_ram AS ram, ro.capacite_rom AS rom
        FROM smartphones s
        LEFT JOIN marques m ON s.id_marque = m.id_marque
        LEFT JOIN rams r ON s.id_ram = r.id_ram
        LEFT JOIN roms ro ON s.id_rom = ro.id_rom
        WHERE s.id = ?";

$stmt = mysqli_prepare($cnx, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$phone = mysqli_fetch_assoc($result);

// Marques
$result = mysqli_query($cnx, "SELECT id_marque, nom_marque FROM marques");
$marques = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_free_result($result);

// RAM
$result = mysqli_query($cnx, "SELECT id_ram, capacite_ram FROM rams");
$rams = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_free_result($result);

// ROM
$result = mysqli_query($cnx, "SELECT id_rom, capacite_rom FROM roms");
$roms = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_free_result($result);

// Couleurs
$result = mysqli_query($cnx, "SELECT id_couleur, nom_couleur, code_hex FROM couleurs");
$couleurs = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_free_result($result);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $photo = trim($_POST['photo'] ?? '');
    $marque_id = intval($_POST['marque_id'] ?? 0);
    $ram_id = intval($_POST['ram_id'] ?? 0);
    $rom_id = intval($_POST['rom_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $ecran = trim($_POST['ecran'] ?? '');
    $couleurs_id = $_POST['couleurs'] ?? [];

    if (!$nom) $errors[] = "Le nom est requis.";
    if ($prix <= 0) $errors[] = "Le prix doit être positif.";
    if (!$photo) $errors[] = "Le lien de la photo est requis.";
    if (!$marque_id || !$ram_id || !$rom_id) $errors[] = "Sélectionnez marque, RAM et ROM.";
    if (!$description) $errors[] = "La description est requise.";
    if (!$ecran) $errors[] = "Le type d’écran est requis.";

    if (empty($errors)) {
        $stmt = $cnx->prepare("INSERT INTO smartphones 
            (nom, prix, photo, id_marque, id_ram, id_rom, description, ecran)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prix, $photo, $marque_id, $ram_id, $rom_id, $description, $ecran]);

        $smartphone_id = $cnx->lastInsertId();

        $stmtC = $cnx->prepare("INSERT INTO smartphone_couleurs (id, id_couleur) VALUES (?, ?)");
        foreach ($couleurs_id as $cid) {
            $stmtC->execute([$smartphone_id, $cid]);
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

    <aside><?php include 'sidebar.php'; ?></aside>
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
                <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>

                <label>Prix (FCFA) :</label>
                <input type="number" name="prix" value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" step="1" required>

                <label>Photo (URL ou chemin) :</label>
                <input type="text" name="photo" value="<?= htmlspecialchars($_POST['photo'] ?? '') ?>" required>

                <label>Marque :</label>
                <!-- <select name="marque_id" required>
                    <option value="">-- Choisir une marque --</option>
                    <?php foreach ($marques as $m): ?>
                        <option value="<?= $m['id_marque'] ?>" <?= ($_POST['marque_id'] ?? '') == $m['id_marque'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nom_marque']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <a href="?add=marque<?= isset($_GET['id']) ? '&id=' . (int)$_GET['id'] : '' ?>">Ajouter une marque</a> -->
                <div class="select-add-container">
                    <select name="marque_id" required>
                        <option value="">-- Choisir une marque --</option>
                        <?php foreach ($marques as $m): ?>
                            <option value="<?= $m['id_marque'] ?>" <?= ($_POST['marque_id'] ?? '') == $m['id_marque'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nom_marque']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="gerer_marques.php<?= isset($_GET['id']) ? '&id=' . (int)$_GET['id'] : '' ?>" class="btn-gestion">+ Gérer</a>
                </div>

                <label>RAM :</label>
                <select name="ram_id" required>
                    <option value="">-- Choisir la RAM --</option>
                    <?php foreach ($rams as $r): ?>
                        <option value="<?= $r['id_ram'] ?>" <?= ($_POST['ram_id'] ?? '') == $r['id_ram'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['capacite_ram']) ?> Go
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>ROM :</label>
                <select name="rom_id" required>
                    <option value="">-- Choisir la ROM --</option>
                    <?php foreach ($roms as $r): ?>
                        <option value="<?= $r['id_rom'] ?>" <?= ($_POST['rom_id'] ?? '') == $r['id_rom'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['capacite_rom']) ?> Go
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Écran :</label>
                <input type="text" name="ecran" value="<?= htmlspecialchars($_POST['ecran'] ?? '') ?>" required>

                <label>Description :</label>
                <textarea name="description" rows="4" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

                <label>Couleurs disponibles :</label>
                <div class="checkboxes">
                    <?php foreach ($couleurs as $c): ?>
                        <label>
                            <input type="checkbox" name="couleurs[]" value="<?= $c['id_couleur'] ?>"
                                <?= in_array($c['id_couleur'], $_POST['couleurs'] ?? []) ? 'checked' : '' ?>>
                            <span class="color-badge" style="background-color: <?= htmlspecialchars($c['code_hex']) ?>;"></span>
                            <?= htmlspecialchars($c['nom_couleur']) ?>
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