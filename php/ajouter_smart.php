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

    if (!$nom) $errors['nom'] = "Le nom est requis.";
    if ($prix <= 0) $errors['prix'] = "Le prix doit être positif.";
    if (!$prix) $errors['prix'] = "Le prix est requis.";
    if (!$photo) $errors['photo'] = "Le lien de la photo est requis.";
    if (!$marque_id) $errors['marque'] = "Sélectionnez une marque.";
    if (!$ram_id) $errors['ram'] = "Sélectionnez une RAM.";
    if (!$rom_id) $errors['rom'] = "Sélectionnez une ROM.";
    if (!$description) $errors['description'] = "La description est requise.";
    if (!$ecran) $errors['ecran'] = "Le type d'écran est requis.";
    if (!$couleurs_id) $errors['couleurs'] = "Sélectionnez au moins une couleur.";

    if (empty($errors)) {
<<<<<<< HEAD
    // Insertion du smartphone
    $stmt = mysqli_prepare($cnx, "INSERT INTO smartphones 
        (nom, prix, photo, id_marque, id_ram, id_rom, description, ecran)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "sdssiiis", 
        $nom, $prix, $photo, $marque_id, $ram_id, $rom_id, $description, $ecran);
    
    mysqli_stmt_execute($stmt);
    $smartphone_id = mysqli_insert_id($cnx);
    mysqli_stmt_close($stmt);

    // Insertion des couleurs associées
    $stmtC = mysqli_prepare($cnx, "INSERT INTO smartphone_couleurs (id, id_couleur) VALUES (?, ?)");

    foreach ($couleurs_id as $cid) {
        mysqli_stmt_bind_param($stmtC, "ii", $smartphone_id, $cid);
        mysqli_stmt_execute($stmtC);
=======
        // Insertion du smartphone
        $stmt = mysqli_prepare($cnx, "INSERT INTO smartphones 
        (nom, prix, photo, id_marque, id_ram, id_rom, description, ecran)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        mysqli_stmt_bind_param(
            $stmt,
            "sdssiiis",
            $nom,
            $prix,
            $photo,
            $marque_id,
            $ram_id,
            $rom_id,
            $description,
            $ecran
        );

        mysqli_stmt_execute($stmt);
        $smartphone_id = mysqli_insert_id($cnx);
        mysqli_stmt_close($stmt);

        // Insertion des couleurs associées
        $stmtC = mysqli_prepare($cnx, "INSERT INTO smartphone_couleurs (id, id_couleur) VALUES (?, ?)");

        foreach ($couleurs_id as $cid) {
            mysqli_stmt_bind_param($stmtC, "ii", $smartphone_id, $cid);
            mysqli_stmt_execute($stmtC);
        }
        mysqli_stmt_close($stmtC);

        // Redirection
        header("Location: details.php?id=" . $smartphone_id);
        exit;
>>>>>>> a2b01e9ea09a3eea26d22502af267173244f8d02
    }
    mysqli_stmt_close($stmtC);

    // Redirection
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
    <link href="../css/ajouter_smart.css" rel="stylesheet">
    <title>Ajouter smartphone</title>
</head>

<body>

    <aside><?php include 'sidebar.php'; ?></aside>
    <main>
        <div class="container">
            <h1 class="page-title">Ajouter un smartphone</h1>

            <form method="post" class="form-ajout">

                <div class="fill-container">
                    <label>Nom :</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                    <?php if (isset($errors['marque'])): ?>
                        <span class="error-message"><?= $errors['nom'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="fill-container">
                    <label>Prix (FCFA) :</label>
                    <input type="number" name="prix" value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" step="100">
                    <?php if (isset($errors['marque'])): ?>
                        <span class="error-message"><?= $errors['prix'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="fill-container">
                    <label>Photo (URL ou chemin) :</label>
                    <input type="text" name="photo" value="<?= htmlspecialchars($_POST['photo'] ?? '') ?>">
                    <?php if (isset($errors['marque'])): ?>
                        <span class="error-message"><?= $errors['photo'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="fill-container">
                    <label>Marque :</label>
                    <div class="select-add-container">
                        <select name="marque_id">
                            <option value="">-- Choisir une marque --</option>
                            <?php foreach ($marques as $m): ?>
                                <option value="<?= $m['id_marque'] ?>" <?= ($_POST['marque_id'] ?? '') == $m['id_marque'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nom_marque']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="gerer_marques.php?type=marque" class="btn-gestion">Gérer</a>
                    </div>
                    <?php if (isset($errors['marque'])): ?>
                        <span class="error-message"><?= $errors['marque'] ?></span>
                    <?php endif; ?>
                </div>


                <div class="fill-container">
                    <label>RAM :</label>
                    <div class="select-add-container">
                        <select name="ram_id">
                            <option value="">-- Choisir la RAM --</option>
                            <?php foreach ($rams as $r): ?>
                                <option value="<?= $r['id_ram'] ?>" <?= ($_POST['ram_id'] ?? '') == $r['id_ram'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['capacite_ram']) ?> Go
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="gerer_marques.php?type=ram" class="btn-gestion">Gérer</a>
                    </div>
                    <?php if (isset($errors['ram'])): ?>
                        <span class="error-message"><?= $errors['ram'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="fill-container">
                    <label>ROM :</label>
                    <div class="select-add-container">
                        <select name="rom_id">
                            <option value="">-- Choisir la ROM --</option>
                            <?php foreach ($roms as $r): ?>
                                <option value="<?= $r['id_rom'] ?>" <?= ($_POST['rom_id'] ?? '') == $r['id_rom'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['capacite_rom']) ?> Go
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="gerer_marques.php?type=rom" class="btn-gestion">Gérer</a>
                    </div>
                    <?php if (isset($errors['rom'])): ?>
                        <span class="error-message"><?= $errors['rom'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="fill-container">
                    <label>Écran :</label>
                    <input type="text" name="ecran" value="<?= htmlspecialchars($_POST['ecran'] ?? '') ?>">
                    <?php if (isset($errors['ecran'])): ?>
                        <span class="error-message"><?= $errors['ecran'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="fill-container">
                    <label>Description :</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <span class="error-message"><?= $errors['description'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="fill-container">
                    <div class="select-color-container">
                        <label>Couleurs disponibles :</label>
                        <a href="gerer_marques.php?type=couleurs" class="btn-gestion">Gérer</a>
                    </div>
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
                    <?php if (isset($errors['couleurs'])): ?>
                        <span class="error-message"><?= $errors['couleurs'] ?></span>
                    <?php endif; ?>
                </div>
                <br>
                <button type="submit" class="btn">Ajouter</button>
            </form>
        </div>
    </main>
</body>

</html>