<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

// Connexion à la base
include_once('includes/db.php');

// Comptages
$nb_clients = $pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn();
$nb_animals = $pdo->query('SELECT COUNT(*) FROM animal')->fetchColumn();
$nb_rdv_today = $pdo->query("SELECT COUNT(*) FROM rdv WHERE DATE(date_heure) = CURDATE() AND statut = 'prévu'")->fetchColumn();
$total_paiements = $pdo->query("SELECT SUM(montant) FROM paiements WHERE statut = 'payé' AND MONTH(date_paiement) = MONTH(CURDATE())")->fetchColumn();
$total = $pdo->query("SELECT SUM(montant) FROM paiements WHERE statut = 'payé'")->fetchColumn();

// Récupérer les 6 derniers mois de paiements
$requete = $pdo->query("
    SELECT 
        DATE_FORMAT(date_paiement, '%b') AS mois,
        SUM(montant) AS total
    FROM paiements
    WHERE statut = 'payé'
      AND date_paiement >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY MONTH(date_paiement)
    ORDER BY date_paiement ASC
");

$mois = [];
$totaux = [];

while ($row = $requete->fetch()) {
    $mois[] = $row['mois'];
    $totaux[] = $row['total'];
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@material-ui/core@latest/dist/material-ui.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container-fluid">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Toilettage Dashboard</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Dashboard Cards -->
        <div class="row mt-4">

            <div class="col-md-3">
                <a href="clients.php" style="text-decoration: none;">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Clients</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $nb_clients ?></h5>
                            <p class="card-text">Total des clients enregistrés</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="animal.php" style="text-decoration: none;">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Animaux</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $nb_animals ?></h5>
                            <p class="card-text">Animaux suivis</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="rdv.php" style="text-decoration: none;">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">RDV Aujourd'hui</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $nb_rdv_today ?></h5>
                            <p class="card-text">Rendez-vous prévus aujourd'hui</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="paiements.php" style="text-decoration: none;">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Paiements</div>
                        <div class="card-body">
                            <h5 class="card-title"><?= number_format($total_paiements ?? 0, 2) ?> €</h5>
                            <p class="card-text">Paiements encaissés ce mois-ci</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <!-- Tableau des prochains RDV -->
        <div class="row mt-4">
            <div class="col-12">
                <h4>Prochains rendez-vous</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom de l'animal</th>
                            <th>Prestation</th>
                            <th>Date & Heure</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rdvs = $pdo->query("SELECT animal.nom_animal, prestations.nom AS prestation, rdv.date_heure, rdv.statut
                                     FROM rdv 
                                     JOIN animal ON rdv.id_animal = animal.id_animal
                                     JOIN prestations ON rdv.id_prestation = prestations.id_prestation
                                     WHERE DATE(rdv.date_heure) >= CURDATE()
                                     ORDER BY rdv.date_heure ASC
                                     LIMIT 5");
                        foreach ($rdvs as $rdv) {
                            echo "<tr>
                            <td>{$rdv['nom_animal']}</td>
                            <td>{$rdv['prestation']}</td>
                            <td>" . date('d/m/Y H:i', strtotime($rdv['date_heure'])) . "</td>
                            <td>{$rdv['statut']}</td>
                          </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        Statistiques des paiements (sur 6 mois)
                    </div>
                    <div class="card-body">
                        <canvas id="paiementsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const moisLabels = <?= json_encode($mois) ?>;
        const paiementsData = <?= json_encode($totaux) ?>;

        const ctx = document.getElementById('paiementsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: moisLabels,
                datasets: [{
                    label: 'Paiements en €',
                    data: paiementsData,
                    backgroundColor: 'rgba(33, 150, 243, 0.2)',
                    borderColor: 'rgba(33, 150, 243, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>


</body>

</html>