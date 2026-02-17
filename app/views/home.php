
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Tableau de Bord</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/dashboard.css">
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
                        <h1>📊 Tableau de Bord</h1>
                        <p>Suivi des collectes et distributions de dons pour les sinistrés</p>
                    </div>

        <div class="dashboard">
            <!-- Card 1: Dons Disponibles -->
            <div class="card">
                <div class="card-icon">🎁</div>
                <div class="card-title">Dons Disponibles</div>
                <div class="card-value"><?php echo number_format($totalDons ?? 0); ?></div>
                <div class="card-description">
                    Quantité totale de dons en attente de distribution
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-label">Articles disponible</span>
                        <span class="stat-value"><?php echo number_format($quantiteDons ?? 0); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Argent disponible</span>
                        <span class="stat-value"><?php echo number_format($argentDisponible ?? 0); ?> Ar</span>
                    </div>

            <!-- Card 2: Villes et Besoins -->
            <div class="card">

                <div class="card-icon">🏙️</div>
                <div class="card-title">Villes Affectées</div>
                <div class="card-value"><?php echo number_format($villesAffectees ?? 0); ?></div>
                <div class="card-description">
                    Nombre total de villes avec des besoins urgent
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-label">Besoins totaux</span>
                        <span class="stat-value"><?php echo number_format($totalBesoins ?? 0); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Personnes affectées</span>
                        <span class="stat-value"><?php echo number_format($totalBesoins ?? 0); ?></span>
                    </div>
              

            <!-- Card 3: Attributions Réussies -->
            <div class="card">
                <div class="card-icon">✅</div>
                <div class="card-title">Attributions Réussies</div>
                <div class="card-value"><?php echo number_format($attributionsReussies ?? 0); ?></div>
                <div class="card-description">
                    Total des distributions effectuées avec succès
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-label">Bénéficiaires</span>
                        <span class="stat-value"><?php echo number_format($beneficiaires ?? 0); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Taux de succès</span>
                        <span class="stat-value"><?php echo number_format($tauxReussite['taux_reussite'] ?? 0, 1); ?>%</span>
                    </div>
               
                </div>
            </main>
            <!-- FOOTER -->
            <?php include __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>assets/js/script.js"></script>
</body>
</html>

</html>