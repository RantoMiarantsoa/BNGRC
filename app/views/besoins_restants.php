<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Besoins Restants</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
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
                <?php if (!isset($leftBesoins)) $leftBesoins = []; ?>

                <div class="container py-4">
                    <!-- Section Header -->

                    <!-- Besoins non satisfaits -->
                    <div class="card shadow-sm border-0 border-top border-warning border-4 mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-exclamation-circle-fill text-warning me-2"></i>Besoins Restant (<?= count($leftBesoins) ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($leftBesoins)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-emoji-smile fs-1 d-block mb-2 opacity-50"></i>
                                    <p>Tous les besoins sont satisfaits !</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <!-- <th>Besoin ID</th> -->
                                                <th>Ville</th>
                                                <th>Type</th>
                                                <th>Date Besoin</th>
                                                <th>Quantité Demandée</th>
                                                <th>Quantité Reçue</th>
                                                <th>Manquant</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($leftBesoins as $b): 
                                            $manquant = (int)$b['quantite'] - (int)$b['recu'];
                                        ?>
                                            <tr>
                                                <!-- <td>#<?= (int)$b['id'] ?></td> -->
                                                <td><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($b['type_nom'] ?? '') ?></span></td>
                                                <td><small><?= date('d/m/Y H:i', strtotime($b['date_saisie'])) ?></small></td>
                                                <td><?= (int)$b['quantite'] ?></td>
                                                <td><strong class="text-info"><?= (int)$b['recu'] ?></strong></td>
                                                <td><strong class="text-danger"><?= $manquant ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Résumé statistique -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-warning"><?= count($leftBesoins) ?></h3>
                                    <p class="text-muted mb-0">Besoins en attente</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-danger bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-danger">
                                        <?php 
                                        $total_manquant = 0;
                                        foreach ($leftBesoins as $b) {
                                            $total_manquant += (int)$b['quantite'] - (int)$b['recu'];
                                        }
                                        echo $total_manquant;
                                        ?>
                                    </h3>
                                    <p class="text-muted mb-0">Unités manquantes</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="text-center mt-4">
                        <a href="/distributions" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>
                            Retour au Dispatch
                        </a>
                    </div>
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
