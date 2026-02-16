<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Liste des Besoins</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/besoin-liste.css">
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- SIDEBAR -->
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <div class="d-flex flex-column flex-grow-1">
            <!-- HEADER -->
            <?php include __DIR__ . '/../includes/header.php'; ?>

            <!-- PAGE CONTENT -->
            <main class="main-content flex-grow-1">
                <div class="container-fluid py-4">
                    <div class="header-dashboard">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1><i class="fas fa-list-check"></i> Liste des Besoins par Type</h1>
                                <p>Détail complet des besoins identifiés par ville et catégorie</p>
                            </div>
                            <a href="/besoins/ajouter" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter un Besoin
                            </a>
                        </div>
                    </div>

                    <!-- Tableau des Besoins -->
                    <div class="card p-5 mb-5">
                        <h3 class="mb-4">
                            <i class="fas fa-table me-2" style="color: #e74c3c;"></i>
                            Besoins Détaillés par Type
                        </h3>

                        <?php if (empty($besoins)): ?>
                            <div class="besoin-empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Aucun besoin enregistré</p>
                            </div>
                        <?php else: ?>
                            <div class="besoin-table-container">
                                <table class="besoin-table">
                                    <thead>
                                        <tr>
                                            <th>Ville</th>
                                            <th>Type de Besoin</th>
                                            <th>Catégorie</th>
                                            <?php 
                                            $hasArgents = array_filter($besoins, fn($b) => $b['categorie'] === 'ARGENTS');
                                            $hasOthers = array_filter($besoins, fn($b) => $b['categorie'] !== 'ARGENTS');
                                            ?>
                                            <?php if ($hasOthers): ?>
                                                <th>Quantité</th>
                                                <th>Prix Unitaire</th>
                                            <?php endif; ?>
                                            <th><?php echo count($hasArgents) > 0 && count($hasOthers) == 0 ? 'Montant' : 'Valeur Totale'; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($besoins as $besoin): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo htmlspecialchars($besoin['nom_ville'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($besoin['type_besoin'] ?? 'N/A'); ?></td>
                                                <td class="text-center">
                                                    <span class="besoin-badge-categorie">
                                                        <?php echo htmlspecialchars($besoin['categorie'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <?php if ($besoin['categorie'] === 'ARGENTS'): ?>
                                                    <!-- Pour ARGENTS: afficher directement le montant -->
                                                    <td style="color: #e74c3c;" class="fw-bold"><?php echo number_format($besoin['valeur_totale'] ?? 0, 0) . ' Ar'; ?></td>
                                                <?php else: ?>
                                                    <!-- Pour autres: afficher quantité, prix, total -->
                                                    <td><?php echo number_format($besoin['quantite_totale'] ?? 0); ?>kg</td>
                                                    <td><?php echo number_format($besoin['prix_unitaire'] ?? 0, 2); ?> Ar</td>
                                                    <td style="color: #e74c3c;"><?php echo number_format($besoin['valeur_totale'] ?? 0, 2); ?> Ar</td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>

            <!-- FOOTER -->
            <?php include __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Layout JS -->
    <script src="/assets/js/layout.js"></script>
</body>
</html>