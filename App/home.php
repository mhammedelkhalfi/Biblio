<?php
require 'session_start.php';
require 'db.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupère la liste des livres disponibles à l'emprunt
$sql = "SELECT id, titre FROM livre WHERE id NOT IN (SELECT livre_id FROM pret WHERE dateRetour IS NULL)";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si l'utilisateur soumet un emprunt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['emprunter'])) {
    $livre_id = $_POST['livre_id'];
    $user_id = $_SESSION['user_id'];
    $date_emprunt = date('Y-m-d'); // Date actuelle
    $duree = $_POST['duree']; // Durée de l'emprunt en jours

    // Vérifie la disponibilité du livre
    $sql_check_disponibilite = "SELECT disponibilite FROM livre WHERE id = :livre_id";
    $stmt_check = $pdo->prepare($sql_check_disponibilite);
    $stmt_check->execute([':livre_id' => $livre_id]);
    $livre = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // Si le livre est indisponible, afficher un message d'alerte
    if ($livre && $livre['disponibilite'] == 'false') {
        $erreur_message = "Le livre n'est pas disponible, veuillez choisir un autre livre.";
    } else {
        // Calcul de la date de retour
        $date_retour = date('Y-m-d', strtotime("+$duree days", strtotime($date_emprunt)));

        // Insertion dans la table 'pret' pour enregistrer l'emprunt
        $sql = "INSERT INTO pret (utilisateur_id, livre_id, dateEmprunt, dateRetour) VALUES (:user_id, :livre_id, :date_emprunt, :date_retour)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':livre_id' => $livre_id, ':date_emprunt' => $date_emprunt, ':date_retour' => $date_retour]);

        // Mettre à jour la disponibilité du livre à 'false'
        $sql_update_disponibilite = "UPDATE livre SET disponibilite = 'false' WHERE id = :livre_id";
        $stmt_update = $pdo->prepare($sql_update_disponibilite);
        $stmt_update->execute([':livre_id' => $livre_id]);

        // Redirection après emprunt réussi
        header("Location: home.php?emprunt_success=true");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion des Livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Ma Bibliothèque</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="home.php">Gestion Des Livres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="historique.php">Historiques d'emprunt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profil.php">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Emprunter un Livre</h2>

    <?php if (isset($erreur_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($erreur_message); ?></div>
    <?php elseif (isset($_GET['emprunt_success']) && $_GET['emprunt_success'] == 'true'): ?>
        <div class="alert alert-success">Emprunt effectué avec succès !</div>
    <?php endif; ?>

    <form method="post" action="home.php">
        <div class="mb-3">
            <label for="livre" class="form-label">Choisissez un livre à emprunter :</label>
            <select name="livre_id" id="livre" class="form-control" required>
                <option value="">Sélectionnez un livre</option>
                <?php foreach ($livres as $livre): ?>
                    <option value="<?php echo htmlspecialchars($livre['id']); ?>"><?php echo htmlspecialchars($livre['titre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="duree" class="form-label">Durée de l'emprunt (en jours) :</label>
            <input type="number" name="duree" id="duree" class="form-control" required>
        </div>

        <button type="submit" name="emprunter" class="btn btn-primary">Emprunter</button>
    </form>
</div>

<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
