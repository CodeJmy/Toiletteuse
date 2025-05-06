<?php
include_once('includes/db.php');
$id = $_GET['id'];
$chien = $pdo->query('SELECT * FROM chiens WHERE id_chien = '.$id)->fetch();
$clients = $pdo->query('SELECT * FROM clients')->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare('UPDATE chiens SET id_client=?, nom_chien=?, race=?, date_de_naissance=?, poids=?, taille=?, remarques=? WHERE id_chien=?');
    $stmt->execute([
        $_POST['id_client'],
        $_POST['nom_chien'],
        $_POST['race'],
        $_POST['date_de_naissance'],
        $_POST['poids'],
        $_POST['taille'],
        $_POST['remarques'],
        $id
    ]);
    header('Location: chiens.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Chien</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?> <!-- Barre de navigation -->

<div class="container mt-5">
    <h2>Modifier un chien</h2>
    <form method="post">
        <div class="form-group">
            <label>Propriétaire</label>
            <select name="id_client" class="form-control" required>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id_client'] ?>" <?= $chien['id_client'] == $client['id_client'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($client['nom']) ?> <?= htmlspecialchars($client['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Nom du chien</label>
            <input type="text" name="nom_chien" class="form-control" value="<?= htmlspecialchars($chien['nom_chien']) ?>" required>
        </div>
        <div class="form-group">
            <label>Race</label>
            <input type="text" name="race" class="form-control" value="<?= htmlspecialchars($chien['race']) ?>">
        </div>
        <div class="form-group">
            <label>Date de naissance</label>
            <input type="date" name="date_de_naissance" class="form-control" value="<?= htmlspecialchars($chien['date_de_naissance']) ?>">
        </div>
        <div class="form-group">
            <label>Poids (kg)</label>
            <input type="number" step="0.1" name="poids" class="form-control" value="<?= htmlspecialchars($chien['poids']) ?>">
        </div>
        <div class="form-group">
            <label>Taille (cm)</label>
            <input type="number" step="0.1" name="taille" class="form-control" value="<?= htmlspecialchars($chien['taille']) ?>">
        </div>
        <div class="form-group">
            <label>Remarques</label>
            <textarea name="remarques" class="form-control"><?= htmlspecialchars($chien['remarques']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="chiens.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
