<?php
require 'db.php';
session_start(); // Démarrage de la session

// Vérifiez si l'utilisateur est un administrateur
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

// Variable pour stocker les messages de succès ou d'erreur
$message = '';

// Récupérer les livres de la base de données
$sql = "SELECT * FROM livre";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gestion de l'ajout, modification et suppression de livres
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        // Ajouter un nouveau livre
        $titre = $_POST['titre'];
        $auteur = $_POST['auteur'];
        $disponibilite = $_POST['disponibilite'];

        $sql = "INSERT INTO livre (titre, auteur, disponibilite) VALUES (:titre, :auteur, :disponibilite)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([':titre' => $titre, ':auteur' => $auteur, ':disponibilite' => $disponibilite])) {
            $message = "Livre ajouté avec succès.";
        } else {
            $message = "Erreur lors de l'ajout du livre.";
        }
    } elseif (isset($_POST['edit'])) {
        // Modifier un livre
        $id = $_POST['id'];
        $titre = $_POST['titre'];
        $auteur = $_POST['auteur'];
        $disponibilite = $_POST['disponibilite'];

        $sql = "UPDATE livre SET titre = :titre, auteur = :auteur, disponibilite = :disponibilite WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([':id' => $id, ':titre' => $titre, ':auteur' => $auteur, ':disponibilite' => $disponibilite])) {
            $message = "Livre modifié avec succès.";
        } else {
            $message = "Erreur lors de la modification du livre.";
        }
    } elseif (isset($_POST['delete'])) {
        // Supprimer un livre
        $id = $_POST['id'];
        $sql = "DELETE FROM livre WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([':id' => $id])) {
            $message = "Livre supprimé avec succès.";
        } else {
            $message = "Erreur lors de la suppression du livre.";
        }
    }

    // Rafraîchir la page après l'action pour afficher le message
    header("Location: admin_dashboard.php?message=" . urlencode($message));
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
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
                        <a class="nav-link active" aria-current="page" href="home.php">Gestion Des Livres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="AllHistoriqueAdmin.php">Historiques d'emprunt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Tableau de bord Administrateur</h2>

        <!-- Affichage du message de succès ou d'erreur -->
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <h4>Livres</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Disponibilité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($livre['id']); ?></td>
                        <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                        <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                        <td><?php echo $livre['disponibilite'] === 'true' ? 'Disponible' : 'Indisponible'; ?></td>
                        <td>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $livre['id']; ?>">Modifier</button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $livre['id']; ?>">Supprimer</button>
                        </td>
                    </tr>

                    <!-- Modal Modifier -->
                    <div class="modal fade" id="editModal<?php echo $livre['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" action="admin_dashboard.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Modifier le livre</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $livre['id']; ?>">
                                        <div class="mb-3">
                                            <label for="titre" class="form-label">Titre :</label>
                                            <input type="text" name="titre" id="titre" class="form-control" value="<?php echo $livre['titre']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="auteur" class="form-label">Auteur :</label>
                                            <input type="text" name="auteur" id="auteur" class="form-control" value="<?php echo $livre['auteur']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="disponibilite" class="form-label">Disponibilité :</label>
                                            <select name="disponibilite" id="disponibilite" class="form-control" required>
                                                <option value="1" <?php echo $livre['disponibilite'] ? 'selected' : ''; ?>>Disponible</option>
                                                <option value="0" <?php echo !$livre['disponibilite'] ? 'selected' : ''; ?>>Indisponible</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" name="edit" class="btn btn-primary">Modifier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Supprimer -->
                    <div class="modal fade" id="deleteModal<?php echo $livre['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" action="admin_dashboard.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes-vous sûr de vouloir supprimer ce livre ?</p>
                                        <input type="hidden" name="id" value="<?php echo $livre['id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Bouton d'ajout de livre -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Ajouter un livre</button>

        <!-- Modal Ajouter Livre -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="admin_dashboard.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Ajouter un nouveau livre</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre :</label>
                                <input type="text" name="titre" id="titre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="auteur" class="form-label">Auteur :</label>
                                <input type="text" name="auteur" id="auteur" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="disponibilite" class="form-label">Disponibilité :</label>
                                <select name="disponibilite" id="disponibilite" class="form-control" required>
                                    <option value="1">Disponible</option>
                                    <option value="0">Indisponible</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
