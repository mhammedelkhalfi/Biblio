<?php
// Paramètres de connexion à la base de données
/*$host = 'localhost';  // Hôte
$dbname = 'biblio';  // Nom de la base de données
$username = 'root';  // Nom d'utilisateur
$password = '';  // Mot de passe

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Pour activer les exceptions en cas d'erreur
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}*/
?>
<?php
// Paramètres de connexion à la base de données
$host = 'mysql-container';  // Nom du conteneur MySQL défini dans Terraform
$dbname = 'biblio';         // Nom de la base de données
$username = 'root';         // Nom d'utilisateur
$password = 'root';         // Mot de passe

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Activer les exceptions
} catch (PDOException $e) {
    // Gestion des erreurs
    die("Erreur de connexion : " . $e->getMessage());
}
?>
