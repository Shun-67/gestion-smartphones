<?php
// Inclure le fichier de traitement contenant toute la logique métier
// Ce fichier gère :
// - L'ajout, la modification, la suppression des éléments (marques, RAM, ROM, couleurs)
// - La vérification du rôle admin
// - La récupération des données
require_once 'traitement_gestion.php'
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <!-- Chargement des icônes Remix Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css  ">
    <!-- Feuilles de style personnalisées -->
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/gestion.css" rel="stylesheet">
    <title>Gérer Marques</title>
</head>

<body>
    <!-- Barre latérale de navigation -->
    <aside><?php include 'sidebar.php'; ?></aside>
    
    <!-- Contenu principal -->
    <main>
        <!-- Titre dynamique selon le type d'élément géré -->
        <h1 class="page-title">Gestion des <?= htmlspecialchars($label) ?>s</h1>

        <!-- Affichage des erreurs de validation -->
        <?php if ($errors): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Section d'ajout ou de modification -->
        <div class="add-edit">
            <?php
            // Définir le pas de saisie pour les champs numériques
            $step = '1';
            if ($type === 'ram') $step = '4';      // RAM : incréments de 4 Go
            elseif ($type === 'rom') $step = '64'; // ROM : incréments de 64 Go
            ?>
            
            <!-- Formulaire de modification (si ?edit=ID est présent) -->
            <?php if ($editElement): ?>
                <form method="post">
                    <!-- Champ caché pour l'ID -->
                    <input type="hidden" name="edit_id" value="<?= $editElement["id_$type"] ?>">
                    
                    <!-- Formulaire spécifique pour les couleurs -->
                    <?php if ($type === 'couleur'): ?>
                        <label>Nom de la couleur :</label>
                        <input type="text" name="edit_valeur" value="<?= htmlspecialchars($editElement['nom_couleur']) ?>">
                        <label>Code hexadécimal :</label>
                        <input type="color" name="edit_code_hex" value="<?= htmlspecialchars($editElement['code_hex']) ?>" class="color-input">
                    <?php else: ?>
                        <!-- Formulaire générique pour marque, RAM, ROM -->
                        <label for="edit_valeur">Modifier <?= $label ?> :</label>
                        <input type="<?= $champ_type === 'i' ? 'number' : 'text' ?>" 
                               id="edit_valeur" 
                               name="edit_valeur" 
                               value="<?= htmlspecialchars($editElement[$champ]) ?>" 
                               step="<?= $champ_type === 'i' ? $step : '' ?>">
                    <?php endif; ?>
                    <button type="submit">Enregistrer</button>
                    <button><a href="gestion.php?type=<?= $type ?>">Annuler</a></button>
                </form>
            <?php else: ?>
                <!-- Formulaire d'ajout -->
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
                        <input type="<?= $champ_type === 'i' ? 'number' : 'text' ?>" 
                               id="add_valeur" 
                               name="valeur" 
                               placeholder="Nouvelle <?= $label ?>" 
                               step="<?= $champ_type === 'i' ? $step : '' ?>">
                        <button type="submit">Ajouter</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Tableau des éléments existants -->
        <div class="list">
            <table>
                <thead>
                    <tr>
                        <th><?= htmlspecialchars($label) ?></th>
                        <th class="action"></th>
                        <th class="action"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Boucle sur tous les éléments -->
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <!-- Affichage de la couleur avec pastille -->
                                    <?php if ($type === 'couleur'): ?>
                                        <span class="color-badge" style="background-color:<?= htmlspecialchars($item['code_hex']) ?>;"></span>
                                    <?php endif; ?>
                                    <span style="line-height: 0;"><?= htmlspecialchars($item[$champ]) ?></span>
                                </div>
                            </td>
                            <td>
                                <!-- Lien pour modifier -->
                                <a href="gestion.php?type=<?= $type ?>&edit=<?= $item["id_$type"] ?>" class="icon icon-edit">
                                    <i class="ri-edit-fill"></i>
                                </a>
                            </td>
                            <td>
                                <!-- Lien pour supprimer (avec confirmation) -->
                                <a href="gestion.php?type=<?= $type ?>&delete=<?= $item["id_$type"] ?>" 
                                   class="icon icon-delete" 
                                   onclick="return confirm('Confirmer la suppression ?');">
                                    <i class="ri-delete-bin-5-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Lien de retour selon l'origine -->
        <?php $from = $_GET['from'] ?? ''; ?>
        <?php if ($from == 'formulaire'): ?>
            <button class="retour"><a href="ajouter_smart.php">← Retour au formulaire</a></button>
        <?php elseif ($from == 'parametres'): ?>
            <button class="retour"><a href="parametres.php">← Retour aux paramètres</a></button>
        <?php endif; ?>

    </main>

    <!-- Pied de page -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Smartphone App - Tous droits réservés.</p>
    </footer>
</body>
</html>