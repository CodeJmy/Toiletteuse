<?php
include_once('includes/db.php');
include_once('includes/auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !checkToken($_POST['csrf_token'])) {
        die("Token CSRF invalide ou expiré.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $pdo->prepare('INSERT INTO clients (nom, prenom, telephone, email, adresse, code_postal, ville, date_creation_client) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['telephone'],
            $_POST['email'],
            $_POST['adresse'],
            $_POST['code_postal'],
            $_POST['ville'],
        ]);
        header('Location: index.php?page=clients');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Client</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Ajouter un client</h2>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= generateToken() ?>">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" class="form-control">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="adresse" class="form-control">
            </div>
            <div class="form-group">
                <label>Code Postal</label>
                <input type="text" name="code_postal" class="form-control">
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input type="text" name="ville" class="form-control">
            </div>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="index.php?page=clients" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>