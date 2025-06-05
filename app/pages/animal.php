<?php
include_once('includes/db.php');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'nom_animal';
$filtre = $_GET['filtre'] ?? 'tous';

$allowedSort = ['nom_animal', 'race', 'date_de_naissance'];

if (!in_array($sort, $allowedSort)) {
    $sort = 'nom_animal';
}

// Construction de la requête SQL
$sql = "
    SELECT animal.*, clients.nom, clients.prenom 
    FROM animal
    LEFT JOIN clients ON animal.id_client = clients.id_client
    WHERE (nom_animal LIKE :search OR race LIKE :search OR clients.nom LIKE :search)
";

// Filtrage par propriétaire
if ($filtre === 'sans_proprietaire') {
    $sql .= " AND animal.id_client IS NULL";
} elseif ($filtre === 'avec_proprietaire') {
    $sql .= " AND animal.id_client IS NOT NULL";
}

$sql .= " ORDER BY $sort ASC";

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

        <!-- Barre de recherche + tri + filtre -->
        <form method="get" action="index.php" class="form-inline mb-4">
            <input type="hidden" name="page" value="animal">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher" value="<?= htmlspecialchars($search) ?>">

            <select name="sort" class="form-control mr-2">
                <option value="nom_animal" <?= $sort == 'nom_animal' ? 'selected' : '' ?>>Nom de l'animal</option>
                <option value="race" <?= $sort == 'race' ? 'selected' : '' ?>>Race</option>
                <option value="date_de_naissance" <?= $sort == 'date_de_naissance' ? 'selected' : '' ?>>Date de naissance</option>
            </select>

            <select name="filtre" class="form-control mr-2">
                <option value="tous" <?= $filtre == 'tous' ? 'selected' : '' ?>>Tous les animaux</option>
                <option value="avec_proprietaire" <?= $filtre == 'avec_proprietaire' ? 'selected' : '' ?>>Avec propriétaire</option>
                <option value="sans_proprietaire" <?= $filtre == 'sans_proprietaire' ? 'selected' : '' ?>>Sans propriétaire</option>
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
                                    <?= htmlspecialchars($animal['nom_animal'] ?? '') ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($animal['type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($animal['race'] ?? '') ?></td>
                            <td><?= htmlspecialchars($animal['date_de_naissance'] ?? '') ?></td>
                            <td>
                                <?php if ($animal['nom'] && $animal['id_client']): ?>
                                    <a href="index.php?page=fiche_clients&id=<?= $animal['id_client'] ?>">
                                        <?= htmlspecialchars($animal['nom'] ?? '') ?> <?= htmlspecialchars($animal['prenom'] ?? '') ?>
                                    </a>
                                <?php else: ?>
                                    <em>Aucun propriétaire</em>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($animal['poids'] ?? '') ?> kg</td>
                            <td><?= htmlspecialchars($animal['taille'] ?? '') ?> cm</td>
                            <td>
                                <a href="index.php?page=fiche_animal&id=<?= $animal['id_animal'] ?>" class="btn btn-info btn-sm">Voir</a>
                                <a href="index.php?page=modifier_animal&id=<?= $animal['id_animal'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <!-- Bouton Supprimer supprimé -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucun animal trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="index.php?page=dashboard" class="btn btn-secondary">Retour au dashboard</a>
    </div>
</body>

</html>