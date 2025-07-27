<?php
// Démarre la session si elle n'est pas déjà active
session_start();

// Supprime toutes les variables de session
// $_SESSION devient un tableau vide
session_unset();

// Détruit complètement la session
// Le fichier de session sur le serveur est supprimé
session_destroy();

// Redirige l'utilisateur vers la page de connexion
header('Location: login_form.php');

// Termine l'exécution du script immédiatement
// Empêche toute exécution ultérieure
exit;
?>