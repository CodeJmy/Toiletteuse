<?php
include_once('includes/db.php');
include_once('includes/auth.php');
$id_animal_preselectionne = $_GET['id_animal'] ?? null;
$animals = $pdo->query('SELECT * FROM animal')->fetchAll();
$prestations = $pdo->query('SELECT * FROM prestations')->fetchAll();
$id_client = null;
if ($id_animal_preselectionne) {
    $stmt = $pdo->prepare("SELECT id_client FROM animal WHERE id_animal = ?");
    $stmt->execute([$id_animal_preselectionne]);
    $result = $stmt->fetch();
    if ($result) {
        $id_client = $result['id_client'];
    }
}
if ($id_client) {
    $stmt = $pdo->prepare("SELECT * FROM animal WHERE id_client = ?");
    $stmt->execute([$id_client]);
    $animals = $stmt->fetchAll();
} else {
    // Sinon, tous les animaux
    $animals = $pdo->query('SELECT * FROM animal')->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !checkToken($_POST['csrf_token'])) {
        die("Token CSRF invalide ou expiré.");
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $pdo->prepare('INSERT INTO rdv (id_animal, id_prestation, date_heure, remarque, statut) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $_POST['id_animal'],
            $_POST['id_prestation'],
            $_POST['date_heure'],
            $_POST['remarque'],
            $_POST['statut']
        ]);
        header('Location: index.php?page=rdv');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter RDV</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Ajouter un rendez-vous</h2>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= generateToken() ?>">
            <div class="form-group">
                <label>Animal</label>
                <select name="id_animal" class="form-control" required>
                    <?php foreach ($animals as $animal): ?>
                        <option value="<?= $animal['id_animal'] ?>"
                            <?= ($id_animal_preselectionne == $animal['id_animal']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($animal['nom_animal']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>


            </div>
            <div class="form-group">
                <label>Prestation</label>
                <select name="id_prestation" class="form-control" required>
                    <?php foreach ($prestations as $prestation): ?>
                        <option value="<?= $prestation['id_prestation'] ?>"><?= htmlspecialchars($prestation['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date et heure</label>
                <input type="datetime-local" name="date_heure" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Remarque</label>
                <textarea name="remarque" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut" class="form-control" required>
                    <option value="prévu">Prévu</option>
                    <option value="effectué">Effectué</option>
                    <option value="annulé">Annulé</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="rdv.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>