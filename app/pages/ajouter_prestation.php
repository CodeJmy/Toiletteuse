<?php
include_once('includes/db.php');
include_once('includes/auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !checkToken($_POST['csrf_token'])) {
        die("Token CSRF invalide ou expiré.");
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = trim($_POST['nom']);
        $tarif = filter_input(INPUT_POST, 'tarif', FILTER_VALIDATE_FLOAT);

        if (empty($nom) || $tarif === false) {
            $_SESSION['erreur'] = "Veuillez remplir tous les champs correctement";
        } else {
            $stmt = $pdo->prepare("INSERT INTO prestations (nom, tarif) VALUES (?, ?)");
            $stmt->execute([$nom, $tarif]);
            $_SESSION['message'] = "Prestation ajoutée avec succès";
            header('Location: index.php?page=prestations');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Prestation</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Ajouter une prestation</h2>

        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['erreur'] ?></div>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= generateToken() ?>">
            <div class="form-group">
                <label>Nom de la prestation</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Tarif (€)</label>
                <input type="number" step="0.01" name="tarif" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="prestations.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>