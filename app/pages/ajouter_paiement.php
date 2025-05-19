<?php
include_once('includes/db.php');

$id_rdv_selectionne = $_GET['id_rdv'] ?? null;

$rdv = $pdo->query('
    SELECT rdv.*, animal.nom_animal, prestations.nom AS nom_prestation, prestations.tarif
    FROM rdv
    JOIN animal ON rdv.id_animal = animal.id_animal
    JOIN prestations ON rdv.id_prestation = prestations.id_prestation
    WHERE rdv.id_rdv NOT IN (SELECT id_rdv FROM paiements)
')->fetchAll();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rdv = (int)$_POST['id_rdv'];
    $montant = (float)$_POST['montant'];
    $type_paiement = trim($_POST['type_paiement']);
    $date_paiement = $_POST['date_paiement'];
    $statut = $_POST['statut'];

    if ($id_rdv && $montant > 0 && $type_paiement && $date_paiement && $statut) {
        $stmt = $pdo->prepare('INSERT INTO paiements (id_rdv, montant, type_paiement, date_paiement, statut) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$id_rdv, $montant, $type_paiement, $date_paiement, $statut]);
        $updateRdv = $pdo->prepare('UPDATE rdv SET statut = "réalisé" WHERE id_rdv = ?');
        $updateRdv->execute([$id_rdv]);

        header('Location: index.php?page=paiements');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Paiement</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php' ?>

    <div class="container mt-5">
        <h2>Ajouter un paiement</h2>
        <form method="post">
            <div class="form-group">
                <label>Intervention</label>
                <select name="id_rdv" class="form-control" required id="select-rdv">
                    <?php foreach ($rdv as $rdvs): ?>
                        <option
                            value="<?= $rdvs['id_rdv'] ?>"
                            data-tarif="<?= $rdvs['tarif'] ?>"
                            <?= ($rdvs['id_rdv'] == $id_rdv_selectionne) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rdvs['nom_animal']) ?> - <?= htmlspecialchars($rdvs['nom_prestation']) ?> (<?= date('d/m/Y H:i', strtotime($rdvs['date_heure'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>


            </div>
            <div class="form-group">
                <label>Montant (€)</label>
                <input type="number" step="0.01" name="montant" id="input-montant" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Type de paiement</label>
                <select name="type_paiement" class="form-control" required>
                    <option value="">-- Sélectionnez --</option>
                    <option value="Espèce" <?= isset($_POST['type_paiement']) && $_POST['type_paiement'] == 'Espèce' ? 'selected' : '' ?>>Espèces</option>
                    <option value="Carte" <?= isset($_POST['type_paiement']) && $_POST['type_paiement'] == 'Carte' ? 'selected' : '' ?>>Cartes</option>
                </select>

            </div>


            <div class="form-group">
                <label>Date de paiement</label>
                <input type="date" name="date_paiement" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut" class="form-control" required>
                    <option value="en attente">En attente</option>
                    <option value="payé" selected>Payé</option>
                    <option value="remboursé">Remboursé</option>
                </select>

            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="index.php?page=paiements" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectRdv = document.getElementById("select-rdv");
            const inputMontant = document.getElementById("input-montant");

            function updateMontant() {
                const selectedOption = selectRdv.options[selectRdv.selectedIndex];
                const tarif = selectedOption.getAttribute("data-tarif");
                if (tarif) {
                    inputMontant.value = tarif;
                }
            }

            selectRdv.addEventListener("change", updateMontant);

            updateMontant();
        });
    </script>

</body>

</html>