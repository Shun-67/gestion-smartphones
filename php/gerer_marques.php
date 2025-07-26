<?php require_once 'traitement_gestion.php' ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/gerer_marques.css" rel="stylesheet">
    <title>Gérer Marques</title>
</head>

<body>
    <aside><?php include 'sidebar.php'; ?></aside>
    <main>
        <h1>Gérer les Marques</h1>

        <?php if ($errors): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($editMarque): ?>
            <h2>Modifier la marque</h2>
            <form method="post" action="gerer_marques.php">
                <input type="hidden" name="edit_id" value="<?= $editMarque['id_marque'] ?>">
                <input type="text" name="edit_nom_marque" value="<?= htmlspecialchars($editMarque['nom_marque']) ?>" required>
                <button type="submit">Enregistrer</button>
                <a href="gerer_marques.php">Annuler</a>
            </form>
        <?php else: ?>
            <h2>Ajouter une marque</h2>
            <form method="post" action="gerer_marques.php">
                <input type="text" name="nom_marque" placeholder="Nouvelle marque" required>
                <button type="submit">Ajouter</button>
            </form>
        <?php endif; ?>

        <h2>Liste des marques</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($marques as $m): ?>
                    <tr>
                        <td><?= $m['id_marque'] ?></td>
                        <td><?= htmlspecialchars($m['nom_marque']) ?></td>
                        <td>
                            <a href="gerer_marques.php?edit=<?= $m['id_marque'] ?>" class="btn">Modifier</a>
                            <a href="gerer_marques.php?delete=<?= $m['id_marque'] ?>" class="btn" onclick="return confirm('Confirmer la suppression ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="ajouter_smart.php">← Retour au formulaire d'ajout smartphone</a>

    </main>

</body>

</html>