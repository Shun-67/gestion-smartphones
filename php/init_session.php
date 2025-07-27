<?php
// Vérifie si la session est déjà active
// session_status() renvoie l'état actuel de la session
// PHP_SESSION_NONE signifie qu'aucune session n'est en cours
if (session_status() === PHP_SESSION_NONE) {
    // Démarre la session uniquement si ce n'est pas déjà fait
    // Cela permet d'éviter l'erreur "session already started"
    session_start();
}

header("Cache-Control: no-cache, no-store, must-revalidate"); // empêche mise en cache
header("Pragma: no-cache"); // compatibilité ancienne
header("Expires: 0"); // pour les proxies
?>