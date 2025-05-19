<?php
include_once('includes/db.php');
include_once('includes/auth.php');

// Vérification et sécurisation de l'ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "ID de animal invalide";
    header('Location: index.php?page=animal');
    exit;
}

$animal = $pdo->query('SELECT * FROM animal WHERE id_animal = ' . $id)->fetch();
$clients = $pdo->query('SELECT * FROM clients')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !checkToken($_POST['csrf_token'])) {
        die("Token CSRF invalide ou expiré.");
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $pdo->prepare('UPDATE animal SET id_client=?, nom_animal=?, race=?, date_de_naissance=?, poids=?, taille=?, remarques=? WHERE id_animal=?');
        $stmt->execute([
            $_POST['id_client'],
            $_POST['nom_animal'],
            $_POST['race'],
            $_POST['date_de_naissance'],
            $_POST['poids'],
            $_POST['taille'],
            $_POST['remarques'],
            $id
        ]);
        header('Location: index.php?page=animal');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier animal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Modifier un animal</h2>
        <form method="post" action="index.php?page=modifier_animal&id=<?= $id ?>">
            <input type="hidden" name="csrf_token" value="<?= generateToken() ?>">
            <div class="form-group">
                <label>Propriétaire</label>
                <select name="id_client" class="form-control" required>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id_client'] ?>" <?= $animal['id_client'] == $client['id_client'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['nom']) ?> <?= htmlspecialchars($client['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Nom de l'animal</label>
                <input type="text" name="nom_animal" class="form-control" value="<?= htmlspecialchars($animal['nom_animal']) ?>" required>
            </div>
            <div class="form-group">
                <label>Race</label>
                <input type="text" name="race" class="form-control" value="<?= htmlspecialchars($animal['race']) ?>">
            </div>
            <div class="form-group">
                <label>Date de naissance</label>
                <input type="date" name="date_de_naissance" class="form-control" value="<?= htmlspecialchars($animal['date_de_naissance']) ?>">
            </div>
            <div class="form-group">
                <label>Poids (kg)</label>
                <input type="number" step="0.1" name="poids" class="form-control" value="<?= htmlspecialchars($animal['poids']) ?>">
            </div>
            <div class="form-group">
                <label>Taille (cm)</label>
                <input type="number" step="0.1" name="taille" class="form-control" value="<?= htmlspecialchars($animal['taille']) ?>">
            </div>
            <div class="form-group">
                <label>Remarques</label>
                <textarea name="remarques" class="form-control"><?= htmlspecialchars($animal['remarques']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="index.php?page=animal" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>