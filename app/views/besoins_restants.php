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
                <?php if (!isset($donsArgent)) $donsArgent = []; ?>
                <?php if (!isset($tauxFrais)) $tauxFrais = 0; ?>

                <div class="container py-4">
                    <!-- Message d'erreur/succès -->
                    <?php if (isset($erreur)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($erreur) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($succes)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($succes) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Info taux de frais -->
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Taux de frais d'achat:</strong> <?= number_format($tauxFrais, 2) ?>%
                    </div>

                    <!-- Besoins non satisfaits -->
                    <div class="card shadow-sm border-0 border-top border-warning border-4 mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-exclamation-circle-fill text-warning me-2"></i>Besoins Restants (<?= count($leftBesoins) ?>)</h5>
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
                                                <th>Ville</th>
                                                <th>Type</th>
                                                <th>Catégorie</th>
                                                <th>Prix Unit.</th>
                                                <th>Demandé</th>
                                                <th>Reçu</th>
                                                <th>Manquant</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($leftBesoins as $b): 
                                            $manquant = (int)$b['quantite'] - (int)$b['recu'];
                                            $prixUnitaire = isset($b['prix_unitaire']) ? (float)$b['prix_unitaire'] : 0;
                                            $peutAcheter = $prixUnitaire > 0 && in_array($b['categorie_id'] ?? 0, [1, 2]);
                                        ?>
                                            <tr>
                                                <td><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                                                <td><span class="badge bg-secondary"><?= htmlspecialchars($b['type_nom'] ?? '') ?></span></td>
                                                <td><span class="badge bg-<?= ($b['categorie_id'] ?? 0) == 1 ? 'success' : (($b['categorie_id'] ?? 0) == 2 ? 'primary' : 'warning') ?>"><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></span></td>
                                                <td><?= $prixUnitaire > 0 ? number_format($prixUnitaire, 0, ',', ' ') . ' Ar' : '-' ?></td>
                                                <td><?= (int)$b['quantite'] ?></td>
                                                <td><strong class="text-info"><?= (int)$b['recu'] ?></strong></td>
                                                <td><strong class="text-danger"><?= $manquant ?></strong></td>
                                                <td>
                                                    <?php if ($peutAcheter && !empty($donsArgent)): ?>
                                                        <button type="button" class="btn btn-sm btn-success" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalAchat"
                                                                data-besoin-id="<?= (int)$b['id'] ?>"
                                                                data-type-nom="<?= htmlspecialchars($b['type_nom'] ?? '') ?>"
                                                                data-ville-nom="<?= htmlspecialchars($b['ville_nom'] ?? '') ?>"
                                                                data-prix="<?= $prixUnitaire ?>"
                                                                data-manquant="<?= $manquant ?>">
                                                            <i class="bi bi-cart-plus me-1"></i>Acheter
                                                        </button>
                                                    <?php elseif ($peutAcheter && empty($donsArgent)): ?>
                                                        <span class="text-muted small"><i class="bi bi-exclamation-circle"></i> Pas de fonds</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Achats en cours -->
                    <?php if (!isset($achatsEnCours)) $achatsEnCours = []; ?>
                    <?php if (!empty($achatsEnCours)): ?>
                    <div class="card shadow-sm border-0 border-top border-info border-4 mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-cart-check text-info me-2"></i>Achats en cours (<?= count($achatsEnCours) ?>)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Ville</th>
                                            <th>Quantité</th>
                                            <th>Montant</th>
                                            <th>Frais</th>
                                            <th>Total</th>
                                            <th>Source</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($achatsEnCours as $achat): ?>
                                        <tr>
                                            <td><strong>#<?= (int)$achat['id'] ?></strong></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($achat['type_nom'] ?? '') ?></span></td>
                                            <td><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($achat['ville_nom'] ?? '') ?></td>
                                            <td><?= (int)$achat['quantite'] ?></td>
                                            <td><?= number_format($achat['montant_achat'], 0, ',', ' ') ?> Ar</td>
                                            <td><?= number_format($achat['frais_achat'], 0, ',', ' ') ?> Ar</td>
                                            <td><strong class="text-success"><?= number_format($achat['montant_total'], 0, ',', ' ') ?> Ar</strong></td>
                                            <td><small><?= htmlspecialchars($achat['don_nom'] ?? '') ?></small></td>
                                            <td><small><?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?></small></td>
                                            <td>
                                                <form action="/achats/finaliser/<?= (int)$achat['id'] ?>" method="POST" class="d-inline">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Finaliser">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <form action="/achats/annuler/<?= (int)$achat['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Annuler cet achat ?')">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Annuler">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

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

    <!-- Modal Achat -->
    <div class="modal fade" id="modalAchat" tabindex="-1" aria-labelledby="modalAchatLabel" aria-hidden="true" data-taux-frais="<?= $tauxFrais ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAchatLabel"><i class="bi bi-cart-plus me-2"></i>Nouvel Achat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/achats/creer" method="POST" id="formAchat">
                    <div class="modal-body">
                        <input type="hidden" name="besoin_id" id="achat_besoin_id">
                        
                        <!-- Info besoin -->
                        <div class="alert alert-light border mb-3">
                            <strong>Besoin:</strong> <span id="achat_type_nom"></span><br>
                            <strong>Ville:</strong> <span id="achat_ville_nom"></span><br>
                            <strong>Prix unitaire:</strong> <span id="achat_prix_unitaire"></span> Ar<br>
                            <strong>Quantité manquante:</strong> <span id="achat_manquant"></span>
                        </div>
                        
                        <!-- Don argent -->
                        <div class="mb-3">
                            <label for="don_argent_id" class="form-label">Source de financement (Don Argent)</label>
                            <select class="form-select" name="don_argent_id" id="don_argent_id" required>
                                <option value="">-- Sélectionner un don --</option>
                                <?php foreach ($donsArgent as $don): ?>
                                    <option value="<?= (int)$don['id'] ?>" data-disponible="<?= (float)$don['disponible'] ?>">
                                        <?= htmlspecialchars($don['nom']) ?> - Disponible: <?= number_format($don['disponible'], 0, ',', ' ') ?> Ar
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Quantité -->
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité à acheter</label>
                            <input type="number" class="form-control" name="quantite" id="achat_quantite" min="1" required>
                            <small class="text-muted">Max: <span id="achat_max_quantite"></span></small>
                        </div>
                        
                        <!-- Calcul automatique -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Montant achat:</small>
                                        <div id="calcul_montant">0 Ar</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Frais (<?= number_format($tauxFrais, 2) ?>%):</small>
                                        <div id="calcul_frais">0 Ar</div>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <strong>Total:</strong> <span id="calcul_total" class="fs-5 text-success">0 Ar</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Message d'erreur -->
                        <div class="alert alert-danger mt-3 d-none" id="achat_erreur"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success" id="btn_valider_achat">
                            <i class="bi bi-check-lg me-1"></i>Valider l'achat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/assets/js/besoins-restants.js"></script>
</body>
</html>
