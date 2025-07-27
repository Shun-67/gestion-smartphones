<?php require_once 'traitement_gestion.php' ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/gestion.css" rel="stylesheet">
    <title>Gérer Marques</title>
</head>

<body>
    <aside><?php include 'sidebar.php'; ?></aside>
    <main>
        <h1 class="page-title">Gestion des <?= htmlspecialchars($label) ?>s</h1>

        <?php if ($errors): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="add-edit">
            <?php
            $step = '1';
            if ($type === 'ram') $step = '4';
            elseif ($type === 'rom') $step = '64';
            ?>
            <?php if ($editElement): ?>
                <form method="post">
                    <input type="hidden" name="edit_id" value="<?= $editElement["id_$type"] ?>">
                    <?php if ($type === 'couleur'): ?>
                        <label>Nom de la couleur :</label>
                        <input type="text" name="edit_valeur" value="<?= htmlspecialchars($editElement['nom_couleur']) ?>">
                        <label>Code hexadécimal :</label>
                        <input type="color" name="edit_code_hex" value="<?= htmlspecialchars($editElement['code_hex']) ?>" class="color-input">
                    <?php else: ?>
                        <label for="edit_valeur">Modifier <?= $label ?> :</label>
                        <input type="<?= $champ_type === 'i' ? 'number' : 'text' ?>" id="edit_valeur" name="edit_valeur" value="<?= htmlspecialchars($editElement[$champ]) ?>" step="<?= $champ_type === 'i' ? $step : '' ?>">
                    <?php endif; ?>
                    <button type="submit">Enregistrer</button>
                    <button><a href="gestion.php?type=<?= $type ?>">Annuler</a></button>
                </form>
            <?php else: ?>
                <?php if ($type === 'couleur'): ?>
                    <form method="post">
                        <label>Nom de la couleur :</label>
                        <input type="text" name="valeur">
                        <label>Code hexadécimal :</label>
                        <input type="color" name="code_hex" class="color-input">
                        <button type="submit">Ajouter</button>
                    </form>
                <?php else: ?>
                    <form method="post">
                        <label for="add_valeur">Ajouter <?= $label ?> :</label>
                        <input type="<?= $champ_type === 'i' ? 'number' : 'text' ?>" id="add_valeur" name="valeur" placeholder="Nouvelle <?= $label ?>" step="<?= $champ_type === 'i' ? $step : '' ?>">
                        <button type="submit">Ajouter</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="list">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= htmlspecialchars($label) ?></th>
                        <th class="action"></th>
                        <th class="action"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item["id_$type"]) ?></td>
                            <td><?= htmlspecialchars($item[$champ]) ?></td>
                            <td>
                                <a href="gestion.php?type=<?= $type ?>&edit=<?= $item["id_$type"] ?>" class="icon icon-edit"><i class="ri-edit-fill"></i></a>
                            </td>
                            <td>
                                <a href="gestion.php?type=<?= $type ?>&delete=<?= $item["id_$type"] ?>" class="icon icon-delete" onclick="return confirm('Confirmer la suppression ?');"><i class="ri-delete-bin-5-fill"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <button class="retour"><a href="ajouter_smart.php">← Retour au formulaire d'ajout smartphone</a></button>

    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>

</html>