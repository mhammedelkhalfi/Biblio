<?php
require 'db.php';
session_start(); // Démarrage de la session

// Vérifiez si l'utilisateur est un administrateur
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

// Récupérer tous les utilisateurs
$sql_utilisateurs = "SELECT id, nom FROM utilisateur";
$stmt_utilisateurs = $pdo->prepare($sql_utilisateurs);
$stmt_utilisateurs->execute();
$utilisateurs = $stmt_utilisateurs->fetchAll(PDO::FETCH_ASSOC);
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
                        <a class="nav-link" href="admin_dashboard.php">Tableau de bord Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Historique des Emprunts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Historique des Emprunts</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom Utilisateur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                        <td>
                            <!-- Bouton pour afficher l'historique des emprunts -->
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#historiqueModal<?php echo $utilisateur['id']; ?>">Afficher l'historique</button>

                            <!-- Modal pour afficher l'historique des emprunts -->
                            <div class="modal fade" id="historiqueModal<?php echo $utilisateur['id']; ?>" tabindex="-1" aria-labelledby="historiqueModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="historiqueModalLabel">Historique des Emprunts de <?php echo htmlspecialchars($utilisateur['nom']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Titre du Livre</th>
                                                        <th>Date d'Emprunt</th>
                                                        <th>Date de Retour</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Récupérer les emprunts de cet utilisateur
                                                    $sql_emprunts = "SELECT l.titre AS livre_titre, p.dateEmprunt, p.dateRetour 
                                                                     FROM pret p 
                                                                     JOIN livre l ON p.livre_id = l.id 
                                                                     WHERE p.utilisateur_id = :utilisateur_id";
                                                    $stmt_emprunts = $pdo->prepare($sql_emprunts);
                                                    $stmt_emprunts->execute([':utilisateur_id' => $utilisateur['id']]);
                                                    $emprunts_utilisateur = $stmt_emprunts->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($emprunts_utilisateur as $emprunt): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($emprunt['livre_titre']); ?></td>
                                                            <td><?php echo htmlspecialchars($emprunt['dateEmprunt']); ?></td>
                                                            <td><?php echo $emprunt['dateRetour'] ? htmlspecialchars($emprunt['dateRetour']) : 'Non retourné'; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
