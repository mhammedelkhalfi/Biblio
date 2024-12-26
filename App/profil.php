<?php
require 'session_start.php';
require 'db.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupère les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM utilisateur WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([':user_id' => $user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Vérifie si un formulaire de changement de mot de passe a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifie si le mot de passe actuel est correct
    if (password_verify($old_password, $user['password'])) {
        // Vérifie si le nouveau mot de passe correspond à la confirmation
        if ($new_password === $confirm_password) {
            // Hash du nouveau mot de passe
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

            // Met à jour le mot de passe dans la base de données
            $sql_update_password = "UPDATE utilisateur SET password = :new_password WHERE id = :user_id";
            $stmt_update_password = $pdo->prepare($sql_update_password);
            $stmt_update_password->execute([':new_password' => $new_password_hashed, ':user_id' => $user_id]);

            $password_update_success = true;
        } else {
            $password_update_error = "Les mots de passe ne correspondent pas.";
        }
    } else {
        $password_update_error = "Mot de passe actuel incorrect.";
    }
}

// Vérifier les livres à retourner dans les prochaines 24 heures
$sql_alert = "SELECT livre.titre, pret.dateRetour FROM pret
              JOIN livre ON pret.livre_id = livre.id
              WHERE pret.utilisateur_id = :user_id
              AND pret.dateRetour IS NOT NULL
              AND DATEDIFF(pret.dateRetour, CURDATE()) = 1";
$stmt_alert = $pdo->prepare($sql_alert);
$stmt_alert->execute([':user_id' => $user_id]);
$alert_books = $stmt_alert->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
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
                    <a class="nav-link" href="home.php">Gestion des Livres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="historique.php">Historique des Emprunts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="profil.php">Profil</a>
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
    <h2 class="text-center mb-4">Profil Utilisateur</h2>

    <!-- Affichage des alertes -->
    <?php if (isset($password_update_success)): ?>
        <div class="alert alert-success">Mot de passe mis à jour avec succès !</div>
    <?php elseif (isset($password_update_error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($password_update_error); ?></div>
    <?php endif; ?>

    <!-- Alerte des livres à retourner -->
    <?php if (count($alert_books) > 0): ?>
        <div class="alert alert-warning">
            <strong>Attention !</strong> Vous devez retourner les livres suivants dans les 24 heures :
            <ul>
                <?php foreach ($alert_books as $book): ?>
                    <li><?php echo htmlspecialchars($book['titre']); ?> (Retour prévu le <?php echo htmlspecialchars($book['dateRetour']); ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Informations utilisateur -->
    <div class="mb-4">
        <h4>Informations personnelles</h4>
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Formulaire de changement de mot de passe -->
    <h4>Changer le mot de passe</h4>
    <form method="POST" action="profil.php">
        <div class="mb-3">
            <label for="old_password" class="form-label">Mot de passe actuel</label>
            <input type="password" name="old_password" id="old_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nouveau mot de passe</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>
        <button type="submit" name="change_password" class="btn btn-primary">Changer le mot de passe</button>
    </form>
</div>

<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
