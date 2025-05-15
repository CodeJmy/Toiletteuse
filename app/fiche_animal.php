<?php
include_once('includes/db.php');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id_animal = $_GET['id'] ?? null;

if (!$id_animal) {
    echo "Aucun identifiant d'animal fourni.";
    exit;
}

// Récupérer les infos de l'animal
$stmt = $pdo->prepare("
    SELECT animal.*, clients.nom AS nom_client, clients.prenom 
    FROM animal 
    JOIN clients ON animal.id_client = clients.id_client 
    WHERE id_animal = ?
");
$stmt->execute([$id_animal]);
$animal = $stmt->fetch();

if (!$animal) {
    echo "Animal introuvable.";
    exit;
}

// Récupérer tous les rendez-vous liés à l'animal
$stmtRdv = $pdo->prepare("
    SELECT rdv.*, prestations.nom AS nom_prestation, prestations.tarif
    FROM rdv
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation
    WHERE rdv.id_animal = ?
    ORDER BY rdv.date_heure DESC
");
$stmtRdv->execute([$id_animal]);
$allRdv = $stmtRdv->fetchAll();

$rdvs_avenir = [];
$rdvs_passe = [];
$now = date('Y-m-d H:i:s');

foreach ($allRdv as $rdv) {
    if ($rdv['date_heure'] >= $now && $rdv['statut'] !== 'réalisé') {
        $rdvs_avenir[] = $rdv;
    } else {
        $rdvs_passe[] = $rdv;
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche de l'animal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Fiche de l'animal : <?= htmlspecialchars($animal['nom_animal']) ?></h2>

        <table class="table table-bordered mt-3">
            <tr>
                <th>Nom</th>
                <td><?= htmlspecialchars($animal['nom_animal']) ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?= htmlspecialchars($animal['type']) ?></td>
            </tr>
            <tr>
                <th>Race</th>
                <td><?= htmlspecialchars($animal['race']) ?></td>
            </tr>
            <tr>
                <th>Date de naissance</th>
                <td><?= htmlspecialchars($animal['date_de_naissance']) ?></td>
            </tr>
            <tr>
                <th>Poids</th>
                <td><?= htmlspecialchars($animal['poids']) ?> kg</td>
            </tr>
            <tr>
                <th>Taille</th>
                <td><?= htmlspecialchars($animal['taille']) ?> cm</td>
            </tr>
            <tr>
                <th>Propriétaire</th>
                <td>
                    <a href="fiche_clients.php?id=<?= $animal['id_client'] ?>">
                        <?= htmlspecialchars($animal['nom_client']) ?> <?= htmlspecialchars($animal['prenom']) ?>
                    </a>
                </td>
            </tr>
        </table>

        <h4 class="mt-5 text-primary">📅 Rendez-vous à venir</h4>
        <?php if (count($rdvs_avenir) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Prestation</th>
                        <th>Tarif (€)</th>
                        <th>Remarque</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs_avenir as $rdv): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($rdv['date_heure'])) ?></td>
                            <td><?= date('H:i', strtotime($rdv['date_heure'])) ?></td>
                            <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                            <td><?= number_format($rdv['tarif'], 2, ',', ' ') ?></td>
                            <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                            <td><?= htmlspecialchars($rdv['statut']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Aucun rendez-vous à venir.</p>
        <?php endif; ?>

        <h4 class="mt-5 text-secondary">🕓 Rendez-vous passés</h4>
        <?php if (count($rdvs_passe) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Prestation</th>
                        <th>Tarif (€)</th>
                        <th>Remarque</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs_passe as $rdv): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($rdv['date_heure'])) ?></td>
                            <td><?= date('H:i', strtotime($rdv['date_heure'])) ?></td>
                            <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                            <td><?= number_format($rdv['tarif'], 2, ',', ' ') ?></td>
                            <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                            <td><?= htmlspecialchars($rdv['statut']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Aucun rendez-vous passé.</p>
        <?php endif; ?>


        <a href="ajouter_rdv.php" class="btn btn-success mb-3">Ajouter un rendez-vous</a>
        <a href="animal.php" class="btn btn-secondary mb-3">Retour à la liste des animaux</a>
    </div>
</body>

</html>