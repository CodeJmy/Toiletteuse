<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <a class="navbar-brand" href="index.php?page=dashboard">Toilettage App</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php?page=clients">Clients</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=animal">Animaux</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=rdv">Rendez-vous</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=paiements">Paiements</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=prestations">Prestations</a></li>
            </ul>
        </div>
    </nav>

    <!-- jQuery (requis par Bootstrap 4) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Popper.js (requis pour le menu burger) -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>