<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/sidebar.css">
<?php
// Déterminer la page active à partir de l'URL courante
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
$basePath = rtrim(BASE_URL, '/');
$currentPath = rtrim(str_replace($basePath, '', parse_url($currentUri, PHP_URL_PATH)), '/');
if ($currentPath === '') $currentPath = '/';

function isActive($path, $currentPath) {
    $path = rtrim($path, '/');
    if ($path === '') $path = '/';
    return ($currentPath === $path) ? 'active' : '';
}
?>
<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h5 class="text-white mb-0"><i class="fas fa-bars"></i> Menu</h5>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= BASE_URL ?>" class="nav-link <?= isActive('/', $currentPath) ?>">
            <i class="fas fa-chart-line"></i>
            <span>Tableau de Bord</span>
        </a>
        <a href="<?= BASE_URL ?>dons/saisie" class="nav-link <?= isActive('/dons/saisie', $currentPath) ?>">
            <i class="fas fa-plus-circle"></i>
            <span>Ajouter un Don</span>
        </a>
        <a href="<?= BASE_URL ?>dons/liste" class="nav-link <?= isActive('/dons/liste', $currentPath) ?>">
            <i class="fas fa-gift"></i>
            <span>Liste des Dons</span>
        </a>
        <a href="<?= BASE_URL ?>besoins" class="nav-link <?= isActive('/besoins', $currentPath) ?>">
            <i class="fas fa-city"></i>
            <span>Liste des Besoins</span>
        </a>
        <a href="<?= BASE_URL ?>besoins-restants" class="nav-link <?= isActive('/besoins-restants', $currentPath) ?>">
            <i class="fas fa-exclamation-circle"></i>
            <span>Besoins Restants</span>
        </a>
        <a href="<?= BASE_URL ?>distributions" class="nav-link <?= isActive('/distributions', $currentPath) ?>">
            <i class="fas fa-dolly"></i>
            <span>Dispatch/Distribution</span>
        </a>
        <a href="<?= BASE_URL ?>recap" class="nav-link <?= isActive('/recap', $currentPath) ?>">
            <i class="fas fa-chart-pie"></i>
            <span>Récapitulation</span>
        </a>
        <hr class="my-3">
    </nav> 
</aside>
