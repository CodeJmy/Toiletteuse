<?php
include_once('includes/db.php');
include_once 'includes/auth.php';

$search = $_GET['search'] ?? '';
$trier = $_GET['trier'] ?? 'date_creation_client';

$trieAuto = ['nom', 'prenom', 'telephone', 'date_creation_client'];

if (!in_array($trier, $trieAuto)) {
    $trier = 'date_creation_client';
}

$sql = "
    SELECT * FROM clients
    WHERE nom LIKE :search OR prenom LIKE :search OR telephone LIKE :search
    ORDER BY $trier ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':search' => '%' . $search . '%'
]);
$clients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php' ?>


    <div class="container mt-5">
        <h2>Liste des clients</h2>
        <!-- Formulaire de recherche + tri -->
        <form method="get" action="index.php" class="form-inline mb-4">
            <input type="hidden" name="page" value="clients">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher" value="<?= htmlspecialchars($search) ?>">

            <select name="trier" class="form-control mr-2">
                <option value="date_creation_client" <?= $trier == 'date_creation_client' ? 'selected' : '' ?>>Date de création</option>
                <option value="nom" <?= $trier == 'nom' ? 'selected' : '' ?>>Nom</option>
                <option value="prenom" <?= $trier == 'prenom' ? 'selected' : '' ?>>Prénom</option>
                <option value="telephone" <?= $trier == 'telephone' ? 'selected' : '' ?>>Téléphone</option>
            </select>

            <button type="submit" class="btn btn-primary">Rechercher / Trier</button>
            <a href="index.php?page=clients" class="btn btn-secondary ml-2">Réinitialiser</a>
        </form>

        <a href="index.php?page=ajouter_client" class="btn btn-success mb-3">Ajouter un client</a>

        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($clients) > 0): ?>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td>
                                <a href="index.php?page=fiche_clients&id=<?= $client['id_client'] ?>">
                                    <?= htmlspecialchars($client['nom']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="index.php?page=fiche_clients&id=<?= $client['id_client'] ?>">
                                    <?= htmlspecialchars($client['prenom']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($client['telephone']) ?></td>
                            <td><?= htmlspecialchars($client['email']) ?></td>
                            <td>
                                <a href="index.php?page=fiche_clients&id=<?= $client['id_client'] ?>" class="btn btn-info btn-sm">Voir</a>
                                <a href="index.php?page=modifier_client&id=<?= $client['id_client'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="index.php?page=supprimer_client&id=<?= $client['id_client'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun client trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="index.php?page=dashboard" class="btn btn-secondary">Retour au Dashboard</a>
    </div>

</body>

</html>