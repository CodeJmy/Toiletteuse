<?php
include_once('includes/db.php');
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'nom_chien';

$allowedSort = ['nom_chien', 'race', 'date_de_naissance'];

if (!in_array($sort, $allowedSort)) {
    $sort = 'nom_chien';
}

$sql = "
    SELECT chiens.*, clients.nom, clients.prenom 
    FROM chiens
    JOIN clients ON chiens.id_client = clients.id_client
    WHERE Nom_chien LIKE :search OR Race LIKE :search OR clients.nom LIKE :search
    ORDER BY $sort ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':search' => '%' . $search . '%'
]);
$chiens = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chiens</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Liste des chiens</h2>
        <!-- Barre de recherche -->
        <form method="get" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher" value="<?= htmlspecialchars($search) ?>">

            <select name="sort" class="form-control mr-2">
                <option value="nom_chien" <?= $sort == 'nom_chien' ? 'selected' : '' ?>>Nom du chien</option>
                <option value="Race" <?= $sort == 'Race' ? 'selected' : '' ?>>Race</option>
                <option value="date_de_naissance" <?= $sort == 'date_de_naissance' ? 'selected' : '' ?>>Date de naissance</option>
            </select>

            <button type="submit" class="btn btn-primary">Rechercher / Trier</button>
            <a href="chiens.php" class="btn btn-secondary ml-2">Réinitialiser</a>
        </form>
        <a href="ajouter_chien.php" class="btn btn-success mb-3">Ajouter un chien</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Race</th>
                    <th>Date de naissance</th>
                    <th>Propriétaire</th>
                    <th>Poids</th>
                    <th>Taille</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($chiens) > 0): ?>
                    <?php foreach ($chiens as $chien): ?>
                        <tr>
                            <td><?= htmlspecialchars($chien['nom_chien']) ?></td>
                            <td><?= htmlspecialchars($chien['race']) ?></td>
                            <td><?= htmlspecialchars($chien['date_de_naissance']) ?></td>
                            <td><?= htmlspecialchars($chien['nom']) ?> <?= htmlspecialchars($chien['prenom']) ?></td>
                            <td><?= htmlspecialchars($chien['poids']) ?> kg</td>
                            <td><?= htmlspecialchars($chien['taille']) ?> cm</td>
                            <td>
                                <a href="modifier_chien.php?id=<?= $chien['id_chien'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="supprimer_chien.php?id=<?= $chien['id_chien'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun chien trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Retour au dashboard</a>
    </div>
</body>

</html>