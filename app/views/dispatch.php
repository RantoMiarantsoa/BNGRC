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
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
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
                <?php if (!isset($summary)) $summary = []; if (!isset($leftDons)) $leftDons = []; if (!isset($leftBesoins)) $leftBesoins = []; ?>

<div class="container py-4">
    <!-- Section Démarrage - Toujours visible -->
    <div class="card shadow-lg border-0 mb-4" id="dispatchStart">
        <div class="card-body text-center py-5">
            <h2 class="card-title mb-3">
                <i class="bi bi-distribute-vertical text-danger"></i> Distribution Automatique
            </h2>
            <p class="text-muted fs-5 mb-4">Algorithme FIFO pour attribuer les dons aux besoins de manière optimale</p>
            <a href="/dispatch/run" class="btn btn-danger btn-lg px-5 py-3">
                <i class="bi bi-play-fill me-2"></i>
                Démarrer l'Auto Dispatch
            </a>
        </div>
    </div>

    <!-- Section Résultats - Visible uniquement après exécution -->
    <?php if (!empty($summary) || !empty($error) || !empty($leftDons) || !empty($leftBesoins)): ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($debug)): ?>
            <div class="alert alert-info" role="alert">
                <strong>Debug Info:</strong>
                <ul style="margin-bottom: 0;">
                <?php foreach ($debug as $msg): ?>
                    <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Attributions créées -->
        <div class="card shadow-sm border-0 border-top border-success border-4 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Attributions créées</h5>
            </div>
            <div class="card-body">
                <?php if (empty($summary)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                        Aucune attribution n'a été créée.
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
                <h5 class="mb-0"><i class="bi bi-gift-fill text-primary me-2"></i>Reste des dons disponibles</h5>
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
                                    <th>Don ID</th>
                                    <th>Type</th>
                                    <th>Quantité totale</th>
                                    <th>Quantité attribuée</th>
                                    <th>Reste</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($leftDons as $d): ?>
                                <tr>
                                    <td>#<?= (int)$d['id'] ?></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($d['type_nom'] ?? '') ?></span></td>
                                    <td><?= (int)$d['quantite'] ?></td>
                                    <td><?= (int)$d['attrib'] ?></td>
                                    <td><strong class="text-primary"><?= (int)$d['quantite'] - (int)$d['attrib'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Besoins non satisfaits -->
        <div class="card shadow-sm border-0 border-top border-warning border-4 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-circle-fill text-warning me-2"></i>Besoins non satisfaits</h5>
            </div>
            <div class="card-body">
                <?php if (empty($leftBesoins)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-emoji-smile fs-1 d-block mb-2 opacity-50"></i>
                        Tous les besoins sont satisfaits !
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Besoin ID</th>
                                    <th>Ville</th>
                                    <th>Type</th>
                                    <th>Quantité demandée</th>
                                    <th>Quantité reçue</th>
                                    <th>Manquant</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($leftBesoins as $b): ?>
                                <tr>
                                    <td>#<?= (int)$b['id'] ?></td>
                                    <td><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($b['type_nom'] ?? '') ?></span></td>
                                    <td><?= (int)$b['quantite'] ?></td>
                                    <td><?= (int)$b['recu'] ?></td>
                                    <td><strong class="text-danger"><?= (int)$b['quantite'] - (int)$b['recu'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/" class="btn btn-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>
                Retour à l'accueil
            </a>
            <a href="/dispatch/reset" class="btn btn-warning px-4 ms-2">
                <i class="bi bi-arrow-clockwise me-2"></i>
                Réinitialiser pour un nouveau test
            </a>
        </div>
    <?php endif; ?>
</div>

            </main>
            <!-- FOOTER -->
            <?php include __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/layout.js"></script>
</body>
</html>