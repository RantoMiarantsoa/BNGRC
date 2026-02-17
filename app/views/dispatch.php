<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Distribution Automatique</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/dashboard.css">
    <style>
        .btn-dispatch {
            transition: all 0.3s ease;
        }
        .btn-dispatch:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }
    </style>
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- SIDEBAR -->
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="d-flex flex-column flex-grow-1">
            <!-- HEADER -->
            <?php include __DIR__ . '/includes/header.php'; ?>

            <!-- PAGE CONTENT -->
            <main class="main-content flex-grow-1">
                <?php if (!isset($summary)) $summary = []; if (!isset($leftDons)) $leftDons = []; if (!isset($leftBesoins)) $leftBesoins = []; if (!isset($debug)) $debug = []; if (!isset($error)) $error = null; if (!isset($attributions)) $attributions = []; ?>

<div class="container py-4">
    <!-- Section Démarrage - Toujours visible -->
    <div class="card shadow-lg border-0 mb-4" id="dispatchStart">
        <div class="card-body text-center py-5">
            <h2 class="card-title mb-3">
                <i class="bi bi-distribute-vertical text-danger"></i> Distribution Automatique
            </h2>
            <p class="text-muted fs-5 mb-4">Attribuer les dons aux besoins de manière optimale</p>
            
            <?php $currentStrategy = $strategy ?? 'oldest'; ?>
            
            <div class="mb-4">
                <p class="text-muted mb-2"><i class="bi bi-sort-down me-1"></i> Choisissez la stratégie de distribution :</p>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-lg <?= $currentStrategy === 'oldest' ? 'btn-primary' : 'btn-outline-primary' ?>" 
                            onclick="selectStrategy('oldest')" id="btnOldest">
                        <i class="bi bi-clock-history me-2"></i>
                        Les plus anciens d'abord
                    </button>
                    <button type="button" class="btn btn-lg <?= $currentStrategy === 'smallest' ? 'btn-info' : 'btn-outline-info' ?>"
                            onclick="selectStrategy('smallest')" id="btnSmallest">
                        <i class="bi bi-sort-numeric-down me-2"></i>
                        Les plus petits d'abord
                    </button>
                    <button type="button" class="btn btn-lg <?= $currentStrategy === 'proportional' ? 'btn-success' : 'btn-outline-success' ?>"
                            onclick="selectStrategy('proportional')" id="btnProportional">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribution proportionnelle
                    </button>
                </div>
                <input type="hidden" id="strategyInput" value="<?= $currentStrategy ?>">
                <script>
                function selectStrategy(strategy) {
                    document.getElementById('strategyInput').value = strategy;
                    var btnOldest = document.getElementById('btnOldest');
                    var btnSmallest = document.getElementById('btnSmallest');
                    var btnProportional = document.getElementById('btnProportional');
                    btnOldest.className = 'btn btn-lg ' + (strategy === 'oldest' ? 'btn-primary' : 'btn-outline-primary');
                    btnSmallest.className = 'btn btn-lg ' + (strategy === 'smallest' ? 'btn-info' : 'btn-outline-info');
                    btnProportional.className = 'btn btn-lg ' + (strategy === 'proportional' ? 'btn-success' : 'btn-outline-success');
                }
                </script>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <a href="#" onclick="window.location.href='<?= BASE_URL ?>dispatch/simulate?strategy='+document.getElementById('strategyInput').value; return false;" 
                   class="btn btn-warning btn-lg px-5 py-3 btn-dispatch">
                    <i class="bi bi-eye me-2"></i>
                    Simuler le Dispatch
                </a>
                <?php if (isset($is_simulation) && $is_simulation): ?>
                    <a href="#" onclick="window.location.href='<?= BASE_URL ?>dispatch/validate?strategy='+document.getElementById('strategyInput').value; return false;" 
                       class="btn btn-success btn-lg px-5 py-3 btn-dispatch">
                        <i class="bi bi-check-circle me-2"></i>
                        Valider les Attributions
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Attributions existantes - Toujours visible -->
    <div class="card shadow-sm border-0 border-top border-primary border-4 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check text-primary me-2"></i>Attributions existantes</h5>
            <span class="badge bg-primary"><?= count($attributions) ?> attribution(s)</span>
        </div>
        <div class="card-body">
            <?php if (empty($attributions)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                    Aucune attribution enregistrée. Cliquez sur "Simuler" puis "Valider" pour créer des attributions.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Ville</th>
                                <th>Type</th>
                                <th>Don</th>
                                <th>Quantité attribuée</th>
                                <th>Date dispatch</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($attributions as $attr): ?>
                            <tr>
                                <td><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($attr['ville_nom'] ?? 'N/A') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($attr['type_nom']) ?></span></td>
                                <td><small><?= htmlspecialchars($attr['don_nom']) ?> (<?= (int)$attr['don_quantite'] ?> unités)</small></td>
                                <td><strong class="text-success"><?= (int)$attr['quantite_attribuee'] ?></strong></td>
                                <td><small><?= date('d/m/Y H:i', strtotime($attr['date_dispatch'])) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section Résultats de simulation - Visible seulement après simulation -->
    <?php 
    // Afficher les résultats seulement si on a cliqué sur simuler
    $show_results = isset($is_simulation) && $is_simulation;
    ?>
    <?php if ($show_results): ?>
        
        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div><strong>Simulation terminée</strong> - Voici les nouvelles attributions qui seraient créées</div>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <!-- Nouvelles Attributions (simulation) -->
        <div class="card shadow-sm border-0 border-top border-warning border-4 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-lightning-fill text-warning me-2"></i>Nouvelles attributions (simulation)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($summary)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                        Aucune nouvelle attribution à créer.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ville</th>
                                    <th>Catégorie/Type</th>
                                    <th>Date Besoin</th>
                                    <th>Don</th>
                                    <th>Besoin</th>
                                    <th>Quantité attribuée</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($summary as $row): ?>
                                <tr>
                                    <td><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($row['ville_nom'] ?? 'N/A') ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['type']) ?></span></td>
                                    <td><small><?= date('d/m/Y H:i', strtotime($row['besoin_date'])) ?></small></td>
                                    <td><small><?= htmlspecialchars($row['don_description']) ?></small></td>
                                    <td><small><?= htmlspecialchars($row['besoin_description']) ?></small></td>
                                    <td><strong class="text-success"><?= (int)$row['quantite'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reste des dons -->
        <div class="card shadow-sm border-0 border-top border-primary border-4 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-gift-fill text-primary me-2"></i>Dons disponibles et simulation</h5>
            </div>
            <div class="card-body">
                <?php if (empty($leftDons)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-all fs-1 d-block mb-2 opacity-50"></i>
                        Tous les dons ont été attribués.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nom</th>
                                    <th>Disponible</th>
                                    <th>Attribué (simulation)</th>
                                    <th>Reste après simulation</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($leftDons as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['nom'] ?? '') ?></td>
                                    <td><?= (int)$d['quantite'] ?></td>
                                    <td class="<?= (int)$d['attrib'] > 0 ? 'text-success fw-bold' : 'text-muted' ?>"><?= (int)$d['attrib'] ?></td>
                                    <td><strong class="text-primary"><?= (int)$d['quantite'] - (int)$d['attrib'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>" class="btn btn-secondary px-4">
            <i class="bi bi-arrow-left me-2"></i>
            Retour à l'accueil
        </a>
        <?php if (!empty($attributions)): ?>
        <a href="<?= BASE_URL ?>dispatch/reset" class="btn btn-danger px-4 ms-2">
            <i class="bi bi-arrow-clockwise me-2"></i>
            Réinitialiser les attributions
        </a>
        <?php endif; ?>
    </div>
</div>

            </main>
            <!-- FOOTER -->
            <?php include __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/layout.js"></script>
</body>
</html>