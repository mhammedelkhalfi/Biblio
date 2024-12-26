<?php
require 'db.php';

// Vérifiez si la connexion est établie
if (!isset($pdo)) {
    die("La connexion à la base de données n'a pas été établie.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $auteur = $_POST['auteur'] ?? '';

    if (!empty($titre) && !empty($auteur)) {
        $stmt = $pdo->prepare("INSERT INTO livre (titre, auteur, disponibilite) VALUES (:titre, :auteur, 1)");
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':auteur', $auteur);

        if ($stmt->execute()) {
            echo "Livre ajouté avec succès.";
        } else {
            echo "Erreur lors de l'ajout du livre.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>
