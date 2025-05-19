<?php
session_start();

$page = $_GET['page'] ?? 'login';  // page par défaut : login

// Pages autorisées
$pages_publiques = ['login'];
$pages_privees = [
    'dashboard',
    'accueil',
    'clients',
    'animal',
    'rdv',
    'paiements',
    'prestations',
    'fiche_clients',
    'fiche_animal',
    'fiche_rdv',
    // ajoute ici tes pages privées
    'ajouter_prestation',
    'ajouter_rdv',
    'ajouter_animal',
    'ajouter_client',
    'ajouter_paiement',
    'modifier_prestation',
    'modifier_rdv',
    'modifier_animal',
    'modifier_client',
    'modifier_paiement',
    'supprimer_prestations',
    'supprimer_rdv',
    'supprimer_animal',
    'supprimer_client',
    'supprimer_paiement'

];

if (in_array($page, $pages_privees)) {
    include_once 'includes/auth.php'; // sécurité
    include_once "pages/{$page}.php";
} elseif (in_array($page, $pages_publiques)) {
    include_once "{$page}.php";
} else {
    echo "Page introuvable.";
}
?>
