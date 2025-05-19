<?php
include_once('includes/db.php');
include_once('includes/auth.php');

// Vérification et sécurisation de l'ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "ID de prestation invalide";
    header('Location: index.php?page=prestations');
    exit;
}

// Récupération de la prestation
$prestation = $pdo->query("SELECT * FROM prestations WHERE id_prestation = $id")->fetch();

if (!$prestation) {
    $_SESSION['erreur'] = "Prestation introuvable";
    header('Location: index.php?page=prestations');
    exit;
}

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
            $stmt = $pdo->prepare("UPDATE prestations SET nom = ?, tarif = ? WHERE id_prestation = ?");
            $stmt->execute([$nom, $tarif, $id]);
            $_SESSION['message'] = "Prestation modifiée avec succès";
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
    <title>Modifier une Prestation</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Modifier la prestation</h2>

        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['erreur'] ?></div>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <form method="post" action="index.php?page=modifier_prestation&id=<?= $id ?>">
            <input type="hidden" name="csrf_token" value="<?= generateToken() ?>">
            <div class="form-group">
                <label>Nom de la prestation</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($prestation['nom']) ?>" required>
            </div>
            <div class="form-group">
                <label>Tarif (€)</label>
                <input type="number" step="0.01" name="tarif" class="form-control" value="<?= htmlspecialchars($prestation['tarif']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Modifier</button>
            <a href="prestations.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>