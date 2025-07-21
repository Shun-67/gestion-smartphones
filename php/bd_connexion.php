<?php
//Définition des paramètres de connexions
define('SERVER', 'localhost');
define('USER', 'root');
define('PASS', '');
define('DBNAME', 'gestion_smartphones'); // mabdd doit être créé dans phpMyAdmin

//Tentative de connexion aux données
$cnx = mysqli_connect(SERVER, USER, PASS, DBNAME) or die(mysqli_connect_error());

echo "Connexion aux données!\n";

//Instruction de requête mysqli.query
?>