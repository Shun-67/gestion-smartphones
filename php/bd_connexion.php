<?php
// Définition des constantes de connexion à la base de données
define('SERVER', 'localhost');     // Hôte de la base MySQL (local)
define('USER', 'root');            // Nom d'utilisateur MySQL (par défaut sur XAMPP)
define('PASS', '');                // Mot de passe (vide par défaut sur XAMPP)
define('DBNAME', 'gestion_smartphones'); // Nom de la base de données


// Tentative de connexion à la base de données MySQL
// Si la connexion échoue, un message d'erreur est affiché
$cnx = mysqli_connect(SERVER, USER, PASS, DBNAME) or die(mysqli_connect_error());

// À ce stade, $cnx contient l'objet de connexion
// Il sera utilisé dans les autres fichiers pour exécuter des requêtes SQL

// Instruction de requête mysqli.query
// (Commentaire laissé pour rappeler l'utilisation future de mysqli_query)
?>