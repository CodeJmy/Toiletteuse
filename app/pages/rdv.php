<?php
include_once('includes/db.php');
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_heure';

$allowedSort = ['date_heure', 'nom_animal', 'nom_prestation'];

if (!in_array($sort, $allowedSort)) {
    $sort = 'date_heure';
}

$sql = "
    SELECT rdv.*, animal.nom_animal, prestations.nom AS nom_prestation
    FROM rdv
    LEFT JOIN animal ON rdv.id_animal = animal.id_animal
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation
    WHERE (animal.nom_animal LIKE :search OR prestations.nom LIKE :search OR :search = '%%')
    ORDER BY $sort ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':search' => '%' . $search . '%'
]);
$rdvs = $stmt->fetchAll();

$rdvs_a_mettre_a_jour = [];
$rdvs_avenir = [];
$rdvs_realises = [];
$now = date('Y-m-d H:i:s');

foreach ($rdvs as $rdv) {
    if (strtolower($rdv['statut']) === 'réalisé') {
        $rdvs_realises[] = $rdv;
    } elseif ($rdv['date_heure'] >= $now && date('Y-m-d', strtotime($rdv['date_heure'])) !== date('Y-m-d')) {
        $rdvs_avenir[] = $rdv;
    } else {
        // Statut pas "réalisé" et date passée => à mettre à jour
        $rdvs_a_mettre_a_jour[] = $rdv;
    }
}


$filtre = $_GET['filtre'] ?? 'avenir';

// Récupérer les rendez-vous du jour
$sql_today = "
    SELECT rdv.*, animal.nom_animal, prestations.nom AS nom_prestation
    FROM rdv
    LEFT JOIN animal ON rdv.id_animal = animal.id_animal
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation
    WHERE DATE(rdv.date_heure) = CURDATE()
    AND rdv.statut = 'Prévu'
    ORDER BY rdv.date_heure ASC
";


$stmt_today = $pdo->query($sql_today);
$rdvs_today = $stmt_today->fetchAll();




?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>
    <div class="d-flex justify-content-center">
        <a href="index.php?page=rdv&filtre=avenir" class="btn btn-outline-success <?= $filtre === 'avenir' ? 'active' : '' ?>">À venir</a>
        <a href="index.php?page=rdv&filtre=realises" class="btn btn-outline-secondary <?= $filtre === 'realises' ? 'active' : '' ?>">Réalisés</a>
    </div>

    <div class="container mt-5">
        <h2>Liste des rendez-vous</h2>

        <h3 class="mt-5 text-primary">📅 Rendez-vous prévus aujourd’hui</h3>

        <?php if (count($rdvs_today) > 0): ?>
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Animal</th>
                        <th>Prestation</th>
                        <th>Heure</th>
                        <th>Remarque</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs_today as $rdv): ?>
                        <tr>
                            <td>
                                <a href="index.php?page=fiche_rdv&id=<?= $rdv['id_rdv'] ?>">
                                    <?= $rdv['nom_animal'] !== null ? htmlspecialchars($rdv['nom_animal']) : 'N/A' ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($rdv['date_heure']))) ?></td>
                            <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                            <td><?= htmlspecialchars($rdv['statut']) ?></td>
                            <td>
                                <a href="index.php?page=fiche_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-info btn-sm">Voir</a>
                                <a href="index.php?page=modifier_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="index.php?page=supprimer_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce RDV ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Aucun rendez-vous prévu aujourd’hui.</p>
        <?php endif; ?>


        <!-- Barre de recherche -->
        <form method="get" class="form-inline mb-4">
            <input type="hidden" name="page" value="rdv">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher" value="<?= htmlspecialchars($search) ?>">

            <select name="sort" class="form-control mr-2">
                <option value="date_heure" <?= $sort == 'date_heure' ? 'selected' : '' ?>>Date/Heure</option>
                <option value="nom_animal" <?= $sort == 'nom_animal' ? 'selected' : '' ?>>Nom de l'animal</option>
                <option value="nom_prestation" <?= $sort == 'nom_prestation' ? 'selected' : '' ?>>Prestation</option>
            </select>

            <button type="submit" class="btn btn-primary">Rechercher / Trier</button>
            <a href="index.php?page=rdv" class="btn btn-secondary ml-2">Réinitialiser</a>
        </form>
        <a href="index.php?page=ajouter_rdv" class="btn btn-success mb-3">Ajouter un rendez-vous</a>

        <?php if ($filtre === 'avenir'): ?>
            <h3 class="mt-4 text-success">📅 Rendez-vous à venir</h3>
            <?php
            function afficherStatutStylise($statut)
            {
                switch ($statut) {
                    case 'Prévu':
                        return '<span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> En attente</span>';
                    case 'Réalisé':
                        return '<span class="badge badge-success"><i class="fas fa-check-double"></i> Réalisé</span>';
                    case 'Annulé':
                        return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Annulé</span>';
                    default:
                        return '<span class="badge badge-secondary"><i class="fas fa-question-circle"></i> ' . htmlspecialchars($statut) . '</span>';
                }
            }
            ?>

            <?php if (count($rdvs_avenir) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Prestation</th>
                            <th>Date & Heure</th>
                            <th>Remarque</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rdvs_avenir as $rdv): ?>
                            <tr>
                                <td><a href="index.php?page=fiche_rdv&id=<?= $rdv['id_rdv'] ?>"><?= $rdv['nom_animal'] !== null ? htmlspecialchars($rdv['nom_animal']) : 'N/A' ?></a></td>
                                <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($rdv['date_heure']))) ?></td>
                                <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                                <td><?= htmlspecialchars($rdv['statut']) ?></td>
                                <td>
                                    <a href="index.php?page=fiche_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-info btn-sm">Voir</a>
                                    <a href="index.php?page=modifier_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    <a href="index.php?page=supprimer_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce RDV ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">Aucun rendez-vous à venir.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (count($rdvs_a_mettre_a_jour) > 0): ?>
            <h3 class="mt-5 text-danger">⚠️ Rendez-vous à mettre à jour</h3>
            <p class="text-muted">Ces rendez-vous sont passés mais n'ont pas encore été marqués comme <strong>Réalisés</strong>.</p>
            <table class="table table-bordered table-warning">
                <thead>
                    <tr>
                        <th>Animal</th>
                        <th>Prestation</th>
                        <th>Date & Heure</th>
                        <th>Remarque</th>
                        <th>Statut actuel</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs_a_mettre_a_jour as $rdv): ?>
                        <tr>
                            <td><?= $rdv['nom_animal'] !== null ? htmlspecialchars($rdv['nom_animal']) : 'N/A' ?></td>
                            <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($rdv['date_heure']))) ?></td>
                            <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                            <td><strong><?= htmlspecialchars($rdv['statut']) ?></strong></td>
                            <td>
                                <a href="index.php?page=modifier_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm">Mettre à jour</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>



        <?php if ($filtre === 'realises'): ?>
            <h3 class="mt-5 text-secondary">✅ Rendez-vous réalisés</h3>
            <?php if (count($rdvs_realises) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Prestation</th>
                            <th>Date & Heure</th>
                            <th>Remarque</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rdvs_realises as $rdv): ?>
                            <tr class="text-muted">
                                <td><a href="index.php?page=fiche_rdv&id=<?= $rdv['id_rdv'] ?>"><?= $rdv['nom_animal'] !== null ? htmlspecialchars($rdv['nom_animal']) : 'N/A' ?></a></td>
                                <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($rdv['date_heure']))) ?></td>
                                <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                                <td><?= htmlspecialchars($rdv['statut']) ?></td>
                                <td>
                                    <a href="index.php?page=fiche_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-info btn-sm">Voir</a>
                                    <a href="index.php?page=modifier_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    <a href="index.php?page=supprimer_rdv&id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce RDV ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">Aucun rendez-vous réalisé.</p>
            <?php endif; ?>
        <?php endif; ?>

        <a href="index.php?page=dashboard" class="btn btn-secondary">Retour au Dashboard</a>
    </div>
</body>

</html>