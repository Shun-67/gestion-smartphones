<?php require_once 'traitement_liste.php' ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Liste des Smartphones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/liste.css" rel="stylesheet">
</head>

<body>
    <aside><?php include 'sidebar.php'; ?></aside>
    <main>
        <div class="main-content">
            <h2 class="page-title"> Smartphones disponibles</h2>

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
                <input type="text" name="recherche" placeholder="Recherche..." value="<?= htmlspecialchars($recherche) ?>" />
                <div class="separator"></div>
                <?php if ($role === 'admin'): ?>
                    <div class="add-icon"><a href="ajouter_smart.php"><i class="ri-add-circle-fill"></i></a></div>
                <?php endif; ?>

            </form>

            <div class="list-content">
                <?php if (empty($phones)): ?>
                    <div class="no-results">
                        <img src="../images/no-results.svg" alt="Aucun résultat" class="no-results-img">
                        <p>Aucun téléphone trouvé. Ooops!!</p>

                    </div>
                <?php else: ?>
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
                                    <a href="details.php?id=<?= $phone['id'] ?>" class="details"><img src="<?= htmlspecialchars($phone["photo"]) ?>" alt="Smartphone"></a>
                                </div>
                                <h3><?= htmlspecialchars($phone['nom']) ?></h3>
                                <p><?= number_format($phone['prix'], 0, ',', ' ') ?> FCFA</p>

                                <!-- <div class="actions">
                                <a href="details.php?id=<?= $phone['id'] ?>" class="details"><i class="ri-eye-fill"></i></a>
                                <?php if ($role === 'admin'): ?>
                                    <a href="modifier.php?id=<?= $phone['id'] ?>" class="edit"><i class="ri-edit-2-fill"></i></a>
                                    <a href="supprimer.php?id=<?= $phone['id'] ?>" class="delete" onclick="return confirm('Supprimer ce smartphone ?')"><i class="ri-delete-bin-6-fill"></i></a>
                                <?php endif; ?>
                            </div> -->
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>

</body>

</html>