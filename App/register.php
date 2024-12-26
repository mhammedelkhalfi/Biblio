<?php
require 'db.php';
require 'session_start.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role']; // Récupération du rôle

    try {
        $sql = "INSERT INTO utilisateur (nom, email, password, role) VALUES (:nom, :email, :password, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role,
        ]);
        $message = "Enregistrement réussi ! Vous pouvez vous connecter.";
        $messageType = "success";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "L'adresse email est déjà utilisée.";
            $messageType = "danger";
        } else {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center mb-4">Créer un compte</h3>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="register.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom :</label>
                            <input type="text" name="nom" id="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email :</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe :</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle :</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="USER">Utilisateur</option>
                                <option value="ADMIN">Administrateur</option>
                            </select>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">S'inscrire</button>
                            <p class="text-center">
                        Vous n'avez pas de compte ? 
                        <a href="login.php" class="text-decoration-none">Login</a>
                    </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
