<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Récapitulation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <style>
        .recap-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .recap-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        .recap-card .card-body {
            padding: 30px;
        }
        .recap-card .recap-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .recap-card .recap-montant {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        .recap-card .recap-label {
            font-size: 0.95rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .bg-besoins { background: linear-gradient(135deg, #3498db, #2980b9); }
        .bg-satisfaits { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .bg-restants { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .btn-actualiser {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            padding: 12px 30px;
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-actualiser:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }
        .btn-actualiser:disabled {
            opacity: 0.7;
        }
        .spinner-refresh {
            display: none;
        }
        .loading .spinner-refresh {
            display: inline-block;
        }
        .loading .refresh-icon {
            display: none;
        }
    </style>
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="d-flex flex-column flex-grow-1">
            <?php include __DIR__ . '/includes/header.php'; ?>

            <main class="main-content flex-grow-1">
                <div class="container-fluid py-4">
                    <div class="header-dashboard d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1><i class="fas fa-chart-pie"></i> Récapitulation</h1>
                            <p>Montants globaux des besoins, satisfaits et restants</p>
                        </div>
                        <button id="btn-refresh" class="btn btn-actualiser text-white" onclick="actualiser()">
                            <i class="fas fa-sync-alt refresh-icon me-2"></i>
                            <span class="spinner-border spinner-border-sm spinner-refresh me-2" role="status"></span>
                            Actualiser
                        </button>
                    </div>

                    <div class="row g-4" id="recap-cards">
                        <div class="col-md-4">
                            <div class="card recap-card bg-besoins text-white shadow">
                                <div class="card-body text-center">
                                    <div class="recap-icon"><i class="fas fa-clipboard-list"></i></div>
                                    <div class="recap-montant" id="montant-besoins">
                                        <?php echo number_format((float) $recap['montant_besoins_totaux'], 2, ',', ' '); ?> Ar
                                    </div>
                                    <div class="recap-label">Besoins totaux</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card recap-card bg-satisfaits text-white shadow">
                                <div class="card-body text-center">
                                    <div class="recap-icon"><i class="fas fa-check-circle"></i></div>
                                    <div class="recap-montant" id="montant-satisfaits">
                                        <?php echo number_format((float) $recap['montant_besoins_satisfaits'], 2, ',', ' '); ?> Ar
                                    </div>
                                    <div class="recap-label">Besoins satisfaits</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card recap-card bg-restants text-white shadow">
                                <div class="card-body text-center">
                                    <div class="recap-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div class="recap-montant" id="montant-restants">
                                        <?php echo number_format((float) $recap['montant_besoins_restants'], 2, ',', ' '); ?> Ar
                                    </div>
                                    <div class="recap-label">Besoins restants</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php include __DIR__ . '/includes/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/layout.js"></script>
    <script>
        function formatMontant(n) {
            return parseFloat(n).toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' Ar';
        }

        function actualiser() {
            var btn = document.getElementById('btn-refresh');
            btn.classList.add('loading');
            btn.disabled = true;

            fetch('/recap/data')
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    document.getElementById('montant-besoins').textContent = formatMontant(data.montant_besoins_totaux);
                    document.getElementById('montant-satisfaits').textContent = formatMontant(data.montant_besoins_satisfaits);
                    document.getElementById('montant-restants').textContent = formatMontant(data.montant_besoins_restants);
                })
                .catch(function(err) {
                    console.error('Erreur:', err);
                })
                .finally(function() {
                    btn.classList.remove('loading');
                    btn.disabled = false;
                });
        }
    </script>
</body>
</html>
