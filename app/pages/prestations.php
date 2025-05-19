<?php
include_once('includes/db.php');

// Gestion de la suppression
if (isset($_GET['supprimer'])) {
    $id = filter_input(INPUT_GET, 'supprimer', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM prestations WHERE id_prestation = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Prestation supprimée avec succès";
        header('Location: prestations.php');
        exit;
    }
}

// Récupération des prestations
$prestations = $pdo->query("SELECT * FROM prestations ORDER BY nom")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Prestations</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Gestion des Prestations</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <a href="index.php?page=ajouter_prestation" class="btn btn-success mb-3">Ajouter une prestation</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Tarif (€)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestations as $prestation): ?>
                    <tr>
                        <td><?= htmlspecialchars($prestation['nom']) ?></td>
                        <td><?= number_format($prestation['tarif'], 2, ',', ' ') ?></td>
                        <td>
                            <a href="index.php?page=modifier_prestation&id=<?= $prestation['id_prestation'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="index.php?page=supprimer_prestations&id=<?= $prestation['id_prestation'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Supprimer cette prestation ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php?page=dashboard" class="btn btn-secondary">Retour au Dashboard</a>
    </div>
</body>

</html>