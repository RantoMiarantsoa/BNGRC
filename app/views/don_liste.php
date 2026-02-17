<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Liste des Dons</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/don-liste.css">
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
                <div class="container-fluid py-4">
                    <div class="header-dashboard">
                        <h1><i class="fas fa-gift"></i> Liste des Dons Disponibles</h1>
                        <p>Suivi des collectes de dons par type et catégorie</p>
                    </div>

                    <!-- Tableau des Dons -->
                    <div class="card p-5 mb-5">
                        <h3 class="mb-4">
                            <i class="fas fa-table me-2" style="color: #e74c3c;"></i>
                            Dons par Type
                        </h3>

                        <?php if (empty($dons)): ?>
                            <div class="don-empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Aucun don disponible</p>
                            </div>
                        <?php else: ?>
                            <div class="don-table-container">
                                <table class="don-table">
                                    <thead>
                                        <tr>
                                            <th>Nom du Don</th>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                            <th>Date de saisie</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dons as $don): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo htmlspecialchars($don['nom'] ?? 'N/A'); ?></td>
                                                <td class="text-center">
                                                    <span class="don-badge-categorie">
                                                        <?php echo htmlspecialchars($don['categorie'] ?? 'Sans catégorie'); ?>
                                                    </span>
                                                </td>
                                                <td style="color: #e74c3c;" class="fw-bold"><?php echo number_format($don['quantite'] ?? 0); ?></td>
                                                <td><?php echo htmlspecialchars($don['date_saisie'] ?? ''); ?></td>
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
            <?php include __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Layout JS -->
    <script src="<?= BASE_URL ?>assets/js/layout.js"></script>
</body>
</html>
