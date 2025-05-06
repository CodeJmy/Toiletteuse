<?php
include_once('includes/db.php');
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_heure';

$allowedSort = ['date_heure', 'Nom_chien', 'nom_prestation'];

if (!in_array($sort, $allowedSort)) {
    $sort = 'date_heure';
}

$sql = "
    SELECT rdv.*, chiens.nom_chien, prestations.nom AS nom_prestation
    FROM rdv
    JOIN chiens ON rdv.id_chien = chiens.id_chien
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation
    WHERE chiens.nom_chien LIKE :search OR prestations.nom LIKE :search
    ORDER BY $sort ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':search' => '%' . $search . '%'
]);
$rdvs = $stmt->fetchAll();

// Récupérer les rendez-vous du jour
$sql_today = "
    SELECT rdv.*, chiens.nom_chien, prestations.nom AS nom_prestation
    FROM rdv
    JOIN chiens ON rdv.id_chien = chiens.id_chien
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation
    WHERE DATE(rdv.date_heure) = CURDATE()
    ORDER BY rdv.date_heure ASC
";

$stmt_today = $pdo->query($sql_today);
$rdvs_today = $stmt_today->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Rendez-vous</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Liste des rendez-vous</h2>

        <h3 class="mt-5 text-primary">📅 Rendez-vous prévus aujourd’hui</h3>

        <?php if (count($rdvs_today) > 0): ?>
            <table class="table table-bordered table-striped mt-3">
                <thead>
                    <tr>
                        <th>Chien</th>
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
                                <a href="fiche_rdv.php?id=<?= $rdv['id_rdv'] ?>">
                                    <?= htmlspecialchars($rdv['nom_chien']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($rdv['date_heure']))) ?></td>
                            <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                            <td><?= htmlspecialchars($rdv['statut']) ?></td>
                            <td>
                                <a href="fiche_rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-info btn-sm">Voir</a>
                                <a href="modifier_rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="supprimer_rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce RDV ?');">Supprimer</a>
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
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher" value="<?= htmlspecialchars($search) ?>">

            <select name="sort" class="form-control mr-2">
                <option value="date_heure" <?= $sort == 'date_heure' ? 'selected' : '' ?>>Date/Heure</option>
                <option value="Nom_chien" <?= $sort == 'nom_chien' ? 'selected' : '' ?>>Nom du chien</option>
                <option value="nom_prestation" <?= $sort == 'nom_prestation' ? 'selected' : '' ?>>Prestation</option>
            </select>

            <button type="submit" class="btn btn-primary">Rechercher / Trier</button>
            <a href="rdv.php" class="btn btn-secondary ml-2">Réinitialiser</a>
        </form>
        <a href="ajouter_rdv.php" class="btn btn-success mb-3">Ajouter un rendez-vous</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Chien</th>
                    <th>Prestation</th>
                    <th>Date & Heure</th>
                    <th>Remarque</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rdvs) > 0): ?>
                    <?php foreach ($rdvs as $rdv): ?>
                        <tr>
                            <td>
                                <a href="fiche_rdv.php?id=<?= $rdv['id_rdv'] ?>">
                                    <?= htmlspecialchars($rdv['nom_chien']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($rdv['nom_prestation']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($rdv['date_heure']))) ?></td>
                            <td><?= htmlspecialchars($rdv['remarque']) ?></td>
                            <td><?= htmlspecialchars($rdv['statut']) ?></td>
                            <td>
                                <a href="fiche_rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-info btn-sm">Voir</a>
                                <a href="modifier_rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="supprimer_rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce RDV ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun rendez-vous trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Retour au Dashboard</a>
    </div>
</body>

</html>