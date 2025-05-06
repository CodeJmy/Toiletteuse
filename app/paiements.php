<?php
include_once('includes/db.php');
$search = $_GET['search'] ?? '';

$query = "
    SELECT 
        p.id_paiement,
        p.montant,
        p.type_paiement,
        p.date_paiement,
        p.statut,
        c.nom AS nom_client,
        c.prenom AS prenom_client,
        ch.nom_chien,
        pr.nom AS nom_prestation,
        r.date_heure AS date_intervention,
        r.statut AS statut_rdv
    FROM 
        paiements p
    JOIN 
        rdv r ON p.id_rdv = r.id_rdv
    JOIN 
        chiens ch ON r.id_chien = ch.id_chien
    JOIN 
        clients c ON ch.id_client = c.id_client
    JOIN 
        prestations pr ON r.id_prestation = pr.id_prestation
    WHERE 
        (r.statut = 'réalisé' OR r.statut = 'prévu')
        AND (
            c.nom LIKE :search 
            OR c.prenom LIKE :search 
            OR ch.nom_chien LIKE :search 
            OR pr.nom LIKE :search
        )
    ORDER BY 
        r.date_heure DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute([':search' => '%' . $search . '%']);
$paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiements</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include('navbar.php'); ?>

    <div class="container mt-5">

        <!-- Barre de recherche -->
        <form method="get" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher un chien ou une prestation" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <a href="paiements.php" class="btn btn-secondary ml-2">Réinitialiser</a>
        </form>
        <h2>Liste des paiements</h2>
        <a href="ajouter_paiement.php" class="btn btn-success mb-3">Ajouter un paiement</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Propriétaire</th>
                    <th>Chien</th>
                    <th>Prestation</th>
                    <th>Date/Heure de l'intervention</th>
                    <th>Montant</th>
                    <th>Type de paiement</th>
                    <th>Date de paiement</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paiements as $paiement): ?>
                    <tr>
                        <!-- Affichage client -->
                        <td><?= htmlspecialchars($paiement['nom_client']) ?> <?= htmlspecialchars($paiement['prenom_client']) ?></td>
                        <!-- Affichage nom_chien -->
                        <td><?= htmlspecialchars($paiement['nom_chien']) ?></td>
                        <!-- Affichage de la prestation prestation AS nom_prestation -->
                        <td><?= htmlspecialchars($paiement['nom_prestation']) ?></td>
                        <!-- Affichage de la date  -->
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($paiement['date_intervention']))) ?></td>
                        <!-- Affichage du montant -->
                        <td><?= number_format($paiement['montant'], 2, ',', ' ') ?> €</td>
                        <!-- Affichage du type de paiement -->
                        <td><?= htmlspecialchars($paiement['type_paiement']) ?></td>
                        <!-- Affichage de la date de paiement -->
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($paiement['date_paiement']))) ?></td>
                        <!-- Affichage du statut du paiement -->
                        <td><?= htmlspecialchars($paiement['statut']) ?></td>
                        <td>
                            <a href="modifier_paiement.php?id=<?= $paiement['id_paiement'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="supprimer_paiement.php?id=<?= $paiement['id_paiement'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce paiement ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Retour au Dashboard</a>
    </div>
</body>

</html>