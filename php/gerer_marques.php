<?php
require_once 'bd_connexion.php';
require_once 'init_session.php';

// Vérifier le rôle admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: liste.php');
    exit;
}

// Gestion ajout
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_marque'])) {
    $nom_marque = trim($_POST['nom_marque']);
    if ($nom_marque === '') {
        $errors[] = "Le nom de la marque est requis.";
    } else {
        // Vérifier doublon
        $stmt = $cnx->prepare("SELECT id_marque FROM marques WHERE nom_marque = ?");
        $stmt->execute([$nom_marque]);
        if ($stmt->fetch()) {
            $errors[] = "Cette marque existe déjà.";
        } else {
            // Insérer
            $stmt = $cnx->prepare("INSERT INTO marques (nom_marque) VALUES (?)");
            if ($stmt->execute([$nom_marque])) {
                header('Location: gerer_marques.php');
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout.";
            }
        }
    }
}

// Gestion suppression (GET param)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $cnx->prepare("DELETE FROM marques WHERE id_marque = ?");
    $stmt->execute([$id]);
    header('Location: gerer_marques.php');
    exit;
}

// Gestion modification (simple)
// On affiche un formulaire si ?edit=ID est dans l’URL
$editMarque = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $cnx->prepare("SELECT * FROM marques WHERE id_marque = ?");
    $stmt->execute([$id]);
    $editMarque = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['edit_nom_marque'])) {
    $id = intval($_POST['edit_id']);
    $nom_marque = trim($_POST['edit_nom_marque']);
    if ($nom_marque !== '') {
        $stmt = $cnx->prepare("UPDATE marques SET nom_marque = ? WHERE id_marque = ?");
        $stmt->execute([$nom_marque, $id]);
        header('Location: gerer_marques.php');
        exit;
    } else {
        $errors[] = "Le nom de la marque est requis.";
    }
}

// Récupérer la liste des marques
$stmt = $cnx->query("SELECT * FROM marques ORDER BY nom_marque ASC");
$marques = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gérer Marques</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 700px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        a.btn { padding: 4px 10px; background: #007bff; color: white; border-radius: 4px; text-decoration: none; font-size: 14px; }
        a.btn:hover { background: #0056b3; }
        form.inline { display: inline; }
        .error { color: red; margin-bottom: 10px; }
        input[type="text"] { padding: 6px; width: 300px; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 6px 12px; border-radius: 5px; border: none; background: #28a745; color: white; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>

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

<a href="ajouter_smartphone.php">← Retour au formulaire d'ajout smartphone</a>

</body>
</html>
