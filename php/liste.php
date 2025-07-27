<?php
// Inclure le fichier de traitement contenant toute la logique métier
// Ce fichier gère :
// - La vérification de la session
// - Le rôle de l'utilisateur
// - Les filtres (marque, recherche, tri)
// - La récupération des données depuis la base
// - Le regroupement des smartphones par marque
require_once 'traitement_liste.php'
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des Smartphones</title>
    <!-- Chargement des icônes Remix Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css  ">
    <!-- Feuilles de style personnalisées -->
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/liste.css" rel="stylesheet">
</head>

<body>
    <!-- Barre latérale de navigation -->
    <aside><?php include 'sidebar.php'; ?></aside>
    
    <!-- Contenu principal -->
    <main>
        <div class="main-content">
            <h1 class="page-title"> Smartphones disponibles</h1>

            <!-- Barre d'outils : filtres et recherche -->
            <form method="GET" class="toolbar">
                <!-- Filtre par marque -->
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
                <!-- Tri -->
                <div class="select-wrapper">
                    <select id="tri-select" name="tri" onchange="this.form.submit()">
                        <option value="alpha" <?= $tri === 'alpha' ? 'selected' : '' ?>>Ordre alphabétique</option>
                        <option value="prix" <?= $tri === 'prix' ? 'selected' : '' ?>>Prix croissant</option>
                    </select>
                </div>
                <div class="separator"></div>
                <!-- Icône de recherche -->
                <div class="search-icon"><i class="ri-search-line"></i></div>
                <!-- Champ de recherche -->
                <input type="text" name="recherche" placeholder="Recherche..." value="<?= htmlspecialchars($recherche) ?>" />
                <div class="separator"></div>
                <!-- Icône "Ajouter" visible uniquement pour l'admin -->
                <?php if ($role === 'admin'): ?>
                    <div class="add-icon"><a href="ajouter_smart.php"><i class="ri-add-circle-fill"></i></a></div>
                <?php endif; ?>
            </form>

            <!-- Contenu principal : liste des smartphones -->
            <div class="list-content">
                <!-- Si aucun smartphone ne correspond -->
                <?php if (empty($phones)): ?>
                    <div class="no-results">
                        <img src="../images/no-results.svg" alt="Aucun résultat" class="no-results-img">
                        <p>Aucun téléphone trouvé. Ooops!!</p>
                    </div>
                <?php else: ?>
                    <!-- Boucle sur chaque groupe de smartphones (regroupés par marque) -->
                    <?php foreach ($groupes_phones as $nom_marque => $telephones): ?>
                        <!-- Afficher le titre de la marque -->
                        <h2 class="titre-marque"><?= htmlspecialchars($nom_marque) ?></h2>

                        <!-- Grille de cartes pour cette marque -->
                        <div class="grid">
                            <?php foreach ($telephones as $phone): ?>
                                <?php
                                // Définir le chemin de la photo (par défaut si absente)
                                $photo = !empty($phone["photo"]) ? htmlspecialchars($phone["photo"]) : 'images/default.jpg';
                                ?>
                                <div class="card">
                                    <div>
                                        <!-- Lien vers la page de détails -->
                                        <a href="details.php?id=<?= $phone['id'] ?>" class="details">
                                            <img src="..<?= $photo ?>" alt="Smartphone">
                                        </a>
                                    </div>
                                    <h3><?= htmlspecialchars($phone['nom']) ?></h3>
                                    <p><?= number_format($phone['prix'], 0, ',', ' ') ?> FCFA</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>
</html>