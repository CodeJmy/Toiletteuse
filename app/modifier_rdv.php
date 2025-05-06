<?php
include_once('includes/db.php');
$id = $_GET['id'];
$rdv = $pdo->query('SELECT * FROM rdv WHERE id_rdv = '.$id)->fetch();
$chiens = $pdo->query('SELECT * FROM chiens')->fetchAll();
$prestations = $pdo->query('SELECT * FROM prestations')->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare('UPDATE rdv SET id_chien=?, id_prestation=?, date_heure=?, remarque=?, statut=? WHERE id_rdv=?');
    $stmt->execute([
        $_POST['id_chien'],
        $_POST['id_prestation'],
        $_POST['date_heure'],
        $_POST['remarque'],
        $_POST['statut'],
        $id
    ]);
    header('Location: rdv.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier RDV</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Modifier un rendez-vous</h2>
    <form method="post">
        <div class="form-group">
            <label>Chien</label>
            <select name="id_chien" class="form-control" required>
                <?php foreach ($chiens as $chien): ?>
                    <option value="<?= $chien['id_chien'] ?>" <?= $chien['id_chien'] == $rdv['id_chien'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($chien['nom_chien']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Prestation</label>
            <select name="id_prestation" class="form-control" required>
                <?php foreach ($prestations as $prestation): ?>
                    <option value="<?= $prestation['id_prestation'] ?>" <?= $prestation['id_prestation'] == $rdv['id_prestation'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prestation['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date et heure</label>
            <input type="datetime-local" name="date_heure" class="form-control" value="<?= date('d-m-Y\TH:i', strtotime($rdv['date_heure'])) ?>" required>
        </div>
        <div class="form-group">
            <label>Remarque</label>
            <textarea name="remarque" class="form-control"><?= htmlspecialchars($rdv['remarque']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Statut</label>
            <select name="statut" class="form-control" required>
                <option value="prévu" <?= $rdv['statut'] == 'prévu' ? 'selected' : '' ?>>Prévu</option>
                <option value="réalisé" <?= $rdv['statut'] == 'réalisé' ? 'selected' : '' ?>>Réalisé</option>
                <option value="annulé" <?= $rdv['statut'] == 'annulé' ? 'selected' : '' ?>>Annulé</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="rdv.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
