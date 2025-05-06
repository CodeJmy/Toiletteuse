<?php
include_once('includes/db.php');
$clients = $pdo->query('SELECT * FROM clients')->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare('INSERT INTO chiens (id_client, Nom_chien, Race, Date_de_naissance, poids, taille, remarques) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $_POST['id_client'],
        $_POST['Nom_chien'],
        $_POST['Race'],
        $_POST['Date_de_naissance'],
        $_POST['poids'],
        $_POST['taille'],
        $_POST['remarques']
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
    <title>Ajouter Chien</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2>Ajouter un chien</h2>
        <form method="post">
            <div class="form-group">
                <label>Propriétaire</label>
                <select name="id_client" class="form-control" required>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id_client'] ?>"><?= htmlspecialchars($client['nom']) ?> <?= htmlspecialchars($client['prenom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Nom du chien</label>
                <input type="text" name="Nom_chien" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Race</label>
                <input type="text" name="Race" class="form-control">
            </div>
            <div class="form-group">
                <label>Date de naissance</label>
                <input type="date" name="Date_de_naissance" class="form-control">
            </div>
            <div class="form-group">
                <label>Poids (kg)</label>
                <input type="number" step="0.1" name="poids" class="form-control">
            </div>
            <div class="form-group">
                <label>Taille (cm)</label>
                <input type="number" step="0.1" name="taille" class="form-control">
            </div>
            <div class="form-group">
                <label>Remarques</label>
                <textarea name="remarques" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="chiens.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>