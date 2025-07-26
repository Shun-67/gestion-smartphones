<?php
require_once 'bd_connexion.php';
require_once 'init_session.php';

$role = $_SESSION['role'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT s.*, nom_marque, capacite_ram, capacite_rom
        FROM smartphones s
        LEFT JOIN marques m ON s.id_marque = m.id_marque
        LEFT JOIN rams ON s.id_ram = rams.id_ram
        LEFT JOIN roms ON s.id_rom = roms.id_rom
        WHERE s.id = ?";
$stmt = mysqli_prepare($cnx, $sql);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$phone = mysqli_fetch_assoc($result);
extract($phone);

if (!$phone) {
    echo "<p>Ce téléphone n'existe pas.</p>";
    exit;
}

// Couleurs
$sql_couleurs = "SELECT nom_couleur, code_hex
                 FROM smartphone_couleurs sc
                 JOIN couleurs c ON sc.id_couleur = c.id_couleur
                 WHERE sc.id = ?";
$stmt = mysqli_prepare($cnx, $sql_couleurs);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$couleurs = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails - <?= htmlspecialchars($nom) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="../css/details.css">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="container">
        <div class="card">
            <img src="<?= htmlspecialchars($photo) ?>" alt="Photo du téléphone" class="photo">

            <div class="info">
                <h1><?= htmlspecialchars($nom) ?></h1>
                <p><strong>Marque :</strong> <?= htmlspecialchars($nom_marque) ?></p>
                <p><strong>Prix :</strong> <?= number_format($prix, 0, ',', ' ') ?> FCFA</p>
                <p><strong>RAM :</strong> <?= htmlspecialchars($capacite_ram) ?></p>
                <p><strong>ROM :</strong> <?= htmlspecialchars($capacite_rom) ?></p>

                <?php if ($couleurs): ?>
                    <p><strong>Couleurs disponibles :</strong></p>
                    <div class="couleurs">
                        <?php foreach ($couleurs as $c): ?>
                            <span class="pastille" title="<?= htmlspecialchars($c['nom_couleur']) ?>"
                                style="background-color: <?= htmlspecialchars($c['code_hex']) ?>;"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="actions">
                    <a href="liste.php" class="btn">← Retour</a>

                    <?php if ($role === 'admin'): ?>
                        <a href="modifier.php?id=<?= $phone['id'] ?>" class="btn modifier">Modifier</a>
                        <a href="supprimer.php?id=<?= $phone['id'] ?>" class="btn supprimer"
                            onclick="return confirm('Supprimer ce téléphone ?')">Supprimer</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>