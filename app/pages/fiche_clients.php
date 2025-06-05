<?php
include_once('includes/db.php');

// Vérifie si un ID client est passé en GET
if (!isset($_GET['id'])) {
    echo "Aucun client sélectionné.";
    exit;
}

$id_client = (int) $_GET['id'];

// Récupérer les infos du client
$sqlClient = "SELECT * FROM clients WHERE id_client = :id_client";
$stmtClient = $pdo->prepare($sqlClient);
$stmtClient->execute(['id_client' => $id_client]);
$client = $stmtClient->fetch();

if (!$client) {
    echo "Client introuvable.";
    exit;
}

// Récupérer les animaux du client
$sqlAnimal = "SELECT * FROM animal WHERE id_client = :id_client";
$stmtAnimal = $pdo->prepare($sqlAnimal);
$stmtAnimal->execute(['id_client' => $id_client]);
$animals = $stmtAnimal->fetchAll();

// Récupérer les rendez-vous du client via ses animaux
$sqlRdv = "
    SELECT rdv.*, animal.nom_animal, prestations.nom AS nom_prestation 
    FROM rdv 
    JOIN animal ON rdv.id_animal = animal.id_animal 
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation 
    WHERE animal.id_client = :id_client 
    ORDER BY rdv.date_heure DESC
";
$stmtRdv = $pdo->prepare($sqlRdv);
$stmtRdv->execute(['id_client' => $id_client]);
$rdvs = $stmtRdv->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche Client</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Fiche du client : <?= htmlspecialchars($client['prenom']) . ' ' . htmlspecialchars($client['nom']) ?></h2>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($client['telephone']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($client['email']) ?></p>
        <p><strong>Adresse : </strong> <?= htmlspecialchars($client['code_postal']) ?></p>
        <p><strong>Ville : </strong> <?= htmlspecialchars($client['ville']) ?></p>

        <hr>
        <h4>Animal</h4>
        <?php if (count($animals) > 0): ?>
            <ul>
                <?php foreach ($animals as $animal): ?>
                    <li><a href="index.php?page=fiche_animal&id=<?= $animal['id_animal'] ?>">
                            <?= htmlspecialchars($animal['nom_animal'] ?? '') ?>
                        </a>
                        (<?= htmlspecialchars($animal['race']) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun animal enregistré pour ce client.</p>
        <?php endif; ?>

        <hr>
        <h4>Historique des rendez-vous</h4>
        <?php if (count($rdvs) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Animal</th>
                        <th>Prestation</th>
                        <th>Date/Heure</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs as $rdv): ?>
                        <tr>
                            <td><?= htmlspecialchars($rdv['nom_animal']) ?></td>
                            <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rdv['date_heure'])) ?></td>
                            <td><?= htmlspecialchars($rdv['statut']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun rendez-vous trouvé pour ce client.</p>
        <?php endif; ?>

        <a href="index.php?page=clients" class="btn btn-secondary mt-3">Retour à la liste des clients</a>
    </div>
</body>

</html>