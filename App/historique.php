<?php
require 'session_start.php';
require 'db.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupère l'historique des emprunts de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql = "SELECT pret.id, livre.titre, pret.dateEmprunt, pret.dateRetour FROM pret
        JOIN livre ON pret.livre_id = livre.id
        WHERE pret.utilisateur_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Supprimer un emprunt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer'])) {
    $pret_id = $_POST['pret_id'];

    // Supprime l'emprunt dans la base de données
    $sql_delete = "DELETE FROM pret WHERE id = :pret_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([':pret_id' => $pret_id]);

    header("Location: historique.php?supprime_success=true");
    exit;
}

// Mettre à jour un emprunt (modifier la date de retour)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mettre_a_jour'])) {
    $pret_id = $_POST['pret_id'];
    $date_retour = $_POST['date_retour'];

    // Met à jour la date de retour de l'emprunt
    $sql_update = "UPDATE pret SET dateRetour = :date_retour WHERE id = :pret_id";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([':date_retour' => $date_retour, ':pret_id' => $pret_id]);

    header("Location: historique.php?mise_a_jour_success=true");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Emprunts</title>
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
                    <a class="nav-link active" href="historique.php">Historique des Emprunts</a>
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
    <h2 class="text-center mb-4">Historique des Emprunts</h2>

    <?php if (isset($_GET['supprime_success']) && $_GET['supprime_success'] == 'true'): ?>
        <div class="alert alert-success">Emprunt supprimé avec succès !</div>
    <?php elseif (isset($_GET['mise_a_jour_success']) && $_GET['mise_a_jour_success'] == 'true'): ?>
        <div class="alert alert-success">Date de retour mise à jour avec succès !</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Titre du Livre</th>
                <th>Date d'Emprunt</th>
                <th>Date de Retour</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $emprunt): ?>
                <tr>
                    <td><?php echo htmlspecialchars($emprunt['titre']); ?></td>
                    <td><?php echo htmlspecialchars($emprunt['dateEmprunt']); ?></td>
                    <td>
                        <?php echo $emprunt['dateRetour'] ? htmlspecialchars($emprunt['dateRetour']) : "Non retourné"; ?>
                    </td>
                    <td>
                        <?php if (!$emprunt['dateRetour']): ?>
                            <!-- Formulaire pour mettre à jour la date de retour -->
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $emprunt['id']; ?>">Mettre à jour</button>

                            <!-- Modal pour mettre à jour la date de retour -->
                            <div class="modal fade" id="updateModal<?php echo $emprunt['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateModalLabel">Mettre à jour la date de retour</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="historique.php">
                                                <input type="hidden" name="pret_id" value="<?php echo $emprunt['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="date_retour" class="form-label">Date de Retour</label>
                                                    <input type="date" name="date_retour" id="date_retour" class="form-control" required>
                                                </div>
                                                <button type="submit" name="mettre_a_jour" class="btn btn-primary">Mettre à jour</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Formulaire pour supprimer l'emprunt -->
                        <form method="POST" action="historique.php" style="display:inline;">
                            <input type="hidden" name="pret_id" value="<?php echo $emprunt['id']; ?>">
                            <button type="submit" name="supprimer" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet emprunt ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
