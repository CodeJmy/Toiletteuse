<?php
session_start();
include_once('includes/db.php');

// Vérification et sécurisation de l'ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "ID de paiement invalide";
    header('Location: paiements.php');
    exit;
}

// Récupération du paiement avec requête préparée
$stmt = $pdo->prepare('SELECT * FROM paiements WHERE id_paiement = ?');
$stmt->execute([$id]);
$paiement = $stmt->fetch();

if (!$paiement) {
    $_SESSION['error'] = "Paiement introuvable";
    header('Location: paiements.php');
    exit;
}

$stmt->execute();
$rdvs = $stmt->fetchAll();

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $pdo->prepare('
            UPDATE paiements 
            SET montant=?, type_paiement=?, date_paiement=?, statut=? 
            WHERE id_paiement=?
        ');
        $stmt->execute([
            filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT),
            htmlspecialchars($_POST['type_paiement']),
            $_POST['date_paiement'],
            $_POST['statut'],
            $id
        ]);

        $_SESSION['success'] = "Paiement mis à jour avec succès";
        header('Location: paiements.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la mise à jour: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Paiement</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Modifier un paiement</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Montant (€)</label>
                <input type="number" step="0.01" name="montant" class="form-control"
                    value="<?= htmlspecialchars($paiement['montant']) ?>" required min="0">
            </div>
            <div class="form-group">
                <label>Type de paiement</label>
                <select name="type_paiement" class="form-control" required>
                    <option value="Espèces" <?= $paiement['type_paiement'] == 'Espèces' ? 'selected' : '' ?>>Espèces</option>
                    <option value="Carte bancaire" <?= $paiement['type_paiement'] == 'Carte bancaire' ? 'selected' : '' ?>>Carte bancaire</option>
                    <option value="Chèque" <?= $paiement['type_paiement'] == 'Chèque' ? 'selected' : '' ?>>Chèque</option>
                    <option value="Virement" <?= $paiement['type_paiement'] == 'Virement' ? 'selected' : '' ?>>Virement</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date de paiement</label>
                <input type="date" name="date_paiement" class="form-control"
                    value="<?= htmlspecialchars(substr($paiement['date_paiement'], 0, 10)) ?>" required>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut" class="form-control" required>
                    <option value="payé" <?= $paiement['statut'] == 'payé' ? 'selected' : '' ?>>Payé</option>
                    <option value="en attente" <?= $paiement['statut'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="annulé" <?= $paiement['statut'] == 'annulé' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Modifier</button>
            <a href="paiements.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>