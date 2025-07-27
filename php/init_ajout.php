<?php
require_once 'bd_connexion.php';

//Formater la requête SQL
// $sql1 = "INSERT INTO marques (nom_marque) VALUES ('Apple'), ('Samsung'), ('Xiaomi')";

// $sql2 = "INSERT INTO rams (capacite_ram) VALUES (4), (6), (8)";

// $sql3 = "INSERT INTO roms (capacite_rom) VALUES (128), (256), (512)";

// $sql4 = "INSERT INTO couleurs (nom_couleur) VALUES ('Rouge'), ('Orange'), ('Jaune'), ('Vert'), ('Bleue'), ('Violet'), ('Noir'), ('Gris'), ('Blanc')";

// $sql4 = "INSERT INTO couleurs (nom_couleur, code_hex) VALUES
// ('Rouge', '#FF0000'),
// ('Orange', '#FFA500'),
// ('Jaune', '#FFFF00'),
// ('Vert', '#008000'),
// ('Bleu', '#0000FF'),
// ('Indigo', '#4B0082'),
// ('Violet', '#EE82EE'),
// ('Blanc', '#FFFFFF'),
// ('Noir', '#000000'),
// ('Gris', '#808080')
// ";

$sql5 = "INSERT INTO smartphone_couleurs (id, id_couleur)
VALUES
(1, 7), (1, 3),
(2, 4), (2, 6),
(3, 1), (3, 4)";

$sql = "INSERT INTO smartphones (nom, description, prix, photo, id_marque, id_ram, id_rom, ecran)
VALUES
('iPhone 13', 'Dernier iPhone avec puce A15', 999, '/images/iphone_13.png', 1, 2, 3, '6.1\" OLED'),
('Galaxy S24', 'Smartphone Samsung haut de gamme', 799, '/images/galaxy_s24.png', 2, 2, 2, '6.2\" AMOLED'),
('Redmi Note 10', 'Bon rapport qualité/prix', 199, '/images/redmi_note_11.png', 3, 1, 1, '6.5\" LCD')";



// //Exécuter la requête
// $inserted1 = mysqli_query($cnx, $sql1) or die('<br> Erreur pour inserer : ' . mysqli_error($cnx));
// $inserted2 = mysqli_query($cnx, $sql2) or die('<br> Erreur pour inserer : ' . mysqli_error($cnx));
// $inserted3 = mysqli_query($cnx, $sql3) or die('<br> Erreur pour inserer : ' . mysqli_error($cnx));
// $inserted4 = mysqli_query($cnx, $sql4) or die('<br> Erreur pour inserer : ' . mysqli_error($cnx));
$inserted = mysqli_query($cnx, $sql) or die('<br> Erreur pour inserer : ' . mysqli_error($cnx));
$inserted5 = mysqli_query($cnx, $sql5) or die('<br> Erreur pour inserer : ' . mysqli_error($cnx));


?>