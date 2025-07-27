<?php require_once 'traitement_modifier.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/modifier.css" rel="stylesheet">
    <title>Modifier</title>
</head>

<body>
    <aside><?php include 'sidebar.php'; ?></aside>
    <main>
        <form method="post" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($phone['nom']) ?>" required>

            <label for="marque">Marque :</label>
            <select name="id_marque" id="marque" required>
                <?php foreach ($marques as $marque): ?>
                    <option value="<?= $marque['id_marque'] ?>" <?= $marque['id_marque'] == $phone['id_marque'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($marque['nom_marque']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="ram">RAM :</label>
            <select name="id_ram" id="ram" required>
                <?php foreach ($rams as $ram): ?>
                    <option value="<?= $ram['id_ram'] ?>" <?= $ram['id_ram'] == $phone['id_ram'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ram['capacite_ram']) ?> Go
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="rom">ROM :</label>
            <select name="id_rom" id="rom" required>
                <?php foreach ($roms as $rom): ?>
                    <option value="<?= $rom['id_rom'] ?>" <?= $rom['id_rom'] == $phone['id_rom'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($rom['capacite_rom']) ?> Go
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="prix">Prix :</label>
            <input type="number" name="prix" id="prix" value="<?= htmlspecialchars($phone['prix']) ?>" required>

            <label for="ecran">Écran :</label>
            <input type="text" name="ecran" id="ecran" value="<?= htmlspecialchars($phone['ecran']) ?>" required>

            <label for="description">Description :</label>
            <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($phone['description']) ?></textarea>

            <label for="couleurs[]">Couleurs :</label>
            <div class="checkboxes">
                <?php foreach ($couleurs as $couleur): ?>
                    <label>
                        <input type="checkbox" name="couleurs[]" value="<?= $couleur['id_couleur'] ?>"
                            <?= in_array($couleur['id_couleur'], $couleurs_selectionnees) ? 'checked' : '' ?>>
                        <span class="pastille" style="background-color: <?= htmlspecialchars($couleur['code_hex']) ?>;" title="<?= htmlspecialchars($couleur['nom_couleur']) ?>"></span>
                        <?= htmlspecialchars($couleur['nom_couleur']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <label for="photo">Photo (laisser vide pour ne pas changer) :</label>
            <input type="file" name="photo" id="photo" accept="image/*">

            <button type="submit" class="btn modifier">Enregistrer les modifications</button>
            <button class="btn modifier"><a href="details.php?id=<?= $phone['id'] ?>">Annuler</a></button>
        </form>
    </main>

</body>

</html>