<?php
include_once('includes/db.php');
$id = $_GET['id'];
$client = $pdo->query("SELECT * FROM clients WHERE id_client = $id")->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare('UPDATE clients SET nom=?, prenom=?, telephone=?, email=?, adresse=?, code_postal=?, ville=? WHERE id_client=?');
    $stmt->execute([
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['email'],
        $_POST['adresse'],
        $_POST['code_postal'],
        $_POST['ville'],
        $id
    ]);
    header('Location: clients.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Client</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Modifier le client</h2>
        <form method="post">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($client['nom']) ?>" required>
            </div>
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($client['prenom']) ?>" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($client['telephone']) ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>">
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($client['adresse']) ?>">
            </div>
            <div class="form-group">
                <label>Code Postal</label>
                <input type="text" name="code_postal" class="form-control" value="<?= htmlspecialchars($client['code_postal']) ?>">
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($client['ville']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="clients.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>