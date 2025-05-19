<?php
include_once('includes/db.php');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'nom_animal';

$allowedSort = ['nom_animal', 'race', 'date_de_naissance'];

if (!in_array($sort, $allowedSort)) {
    $sort = 'nom_animal';
}

$sql = "
    SELECT animal.*, clients.nom, clients.prenom 
    FROM animal
    JOIN clients ON animal.id_client = clients.id_client
    WHERE nom_animal LIKE :search OR race LIKE :search OR clients.nom LIKE :search
    ORDER BY $sort ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':search' => '%' . $search . '%'
]);
$animals = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animaux</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Liste des Animaux</h2>
        <!-- Barre de recherche -->
        <form method="get" action="index.php" class="form-inline mb-4">
            <input type="hidden" name="page" value="animal">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher" value="<?= htmlspecialchars($search) ?>">

            <select name="sort" class="form-control mr-2">
                <option value="nom_animal" <?= $sort == 'nom_animal' ? 'selected' : '' ?>>Nom de l'animal</option>
                <option value="race" <?= $sort == 'race' ? 'selected' : '' ?>>Race</option>
                <option value="date_de_naissance" <?= $sort == 'date_de_naissance' ? 'selected' : '' ?>>Date de naissance</option>
            </select>

            <button type="submit" class="btn btn-primary">Rechercher / Trier</button>
            <a href="index.php?page=animal" class="btn btn-secondary ml-2">Réinitialiser</a>
        </form>
        <a href="index.php?page=ajouter_animal" class="btn btn-success mb-3">Ajouter un animal</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Race</th>
                    <th>Date de naissance</th>
                    <th>Propriétaire</th>
                    <th>Poids</th>
                    <th>Taille</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($animals) > 0): ?>
                    <?php foreach ($animals as $animal): ?>
                        <tr>
                            <td>
                                <a href="index.php?page=fiche_animal&id=<?= $animal['id_animal'] ?>">
                                    <?= htmlspecialchars($animal['nom_animal']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($animal['type']) ?></td>
                            <td><?= htmlspecialchars($animal['race'] ?? '') ?></td>
                            <td><?= htmlspecialchars($animal['date_de_naissance'] ?? '') ?></td>
                            <td>
                                <a href="index.php?page=fiche_animal&id=<?= $animal['id_animal'] ?>">
                                    <?= htmlspecialchars($animal['nom']) ?> <?= htmlspecialchars($animal['prenom']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($animal['poids']) ?> kg</td>
                            <td><?= htmlspecialchars($animal['taille']) ?> cm</td>
                            <td>
                                <a href="index.php?page=fiche_animal&id=<?= $animal['id_animal'] ?>" class="btn btn-info btn-sm">Voir</a>
                                <a href="index.php?page=modifier_animal&id=<?= $animal['id_animal'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="index.php?page=supprimer_animal&id=<?= $animal['id_animal'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun animal trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="index.php?page=dashboard" class="btn btn-secondary">Retour au dashboard</a>
    </div>
</body>

</html>