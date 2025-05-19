<?php
include_once('includes/db.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID RDV invalide.");
}

// Récupère les infos du RDV
$sql = "
    SELECT rdv.*, animal.nom_animal, prestations.nom AS nom_prestation, prestations.tarif
    FROM rdv
    JOIN animal ON rdv.id_animal = animal.id_animal
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation
    WHERE id_rdv = :id
";
// On suppose que tu as déjà récupéré $id_rdv dans l'URL
$id_rdv = $_GET['id'] ?? null;

if ($id_rdv) {
    // Paiements associés
    $stmt = $pdo->prepare('
        SELECT * FROM paiements 
        WHERE id_rdv = ?
        ORDER BY date_paiement DESC
    ');
    $stmt->execute([$id_rdv]);
    $paiements = $stmt->fetchAll();

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $rdv = $stmt->fetch();

    if (!$rdv) {
        die("RDV non trouvé.");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du RDV</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Détail du rendez-vous</h2>

        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Animal :</strong> <?= htmlspecialchars($rdv['nom_animal']) ?></li>
            <li class="list-group-item"><strong>Prestation :</strong> <?= htmlspecialchars($rdv['nom_prestation']) ?></li>
            <li class="list-group-item"><strong>Tarif :</strong> <?= number_format($rdv['tarif'], 2, ',', ' ') ?> €</li>
            <li class="list-group-item"><strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($rdv['date_heure'])) ?></li>
            <li class="list-group-item"><strong>Statut :</strong> <?= htmlspecialchars($rdv['statut']) ?></li>
            <li class="list-group-item"><strong>Remarque :</strong> <?= nl2br(htmlspecialchars($rdv['remarque'])) ?></li>
        </ul>

        <a href="index.php?page=ajouter_paiement&id_rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-success">Ajouter un paiement</a>
        <a href="index.php?page=modifier_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning">Modifier le RDV</a>
        <a href="index.php?page=rdv" class="btn btn-secondary">Retour à la liste</a>
    </div>

    <?php if (!empty($paiements)): ?>
        <h4 class="mt-4">Paiement(s) associé(s)</h4>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Montant (€)</th>
                    <th>Type</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paiements as $paiement): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($paiement['date_paiement']))) ?></td>
                        <td><?= number_format($paiement['montant'], 2, ',', ' ') ?></td>
                        <td><?= htmlspecialchars($paiement['type_paiement']) ?></td>
                        <td><?= htmlspecialchars($paiement['statut']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun paiement enregistré pour ce rendez-vous.</p>
    <?php endif; ?>

</body>

</html>