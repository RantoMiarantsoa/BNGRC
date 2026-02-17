<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Saisie de Don</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/don-saisie.css">
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
                        <h1><i class="fas fa-plus-circle"></i> Saisie de Don</h1>
                        <p>Enregistrer un nouveau don pour les collectes</p>
                    </div>

                    <!-- Formulaire Saisie Don -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="don-form-container">
                                <h3>
                                    <i class="fas fa-gift"></i>
                                    Enregistrer un Don
                                </h3>

                                <form method="post" action="<?= BASE_URL ?>dons/saisie">
                                    <div class="form-group">
                                        <label for="nom">Nom du don <span style="color: #e74c3c;">*</span></label>
                                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Sac de riz, Tente, etc." required>
                                    </div>

                                    <div class="form-group">
                                        <label for="id_type_categorie">Catégorie <small class="text-muted">(optionnel)</small></label>
                                        <select class="form-select" id="id_type_categorie" name="id_type_categorie">
                                            <option value="">-- Aucune catégorie --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo (int) $cat['id']; ?>">
                                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="quantite">Quantité <span style="color: #e74c3c;">*</span></label>
                                        <input type="number" class="form-control" id="quantite" name="quantite" min="1" placeholder="Entrez la quantité" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="date_saisie">Date de saisie</label>
                                        <input type="date" class="form-control" id="date_saisie" name="date_saisie" value="<?= date('Y-m-d') ?>" required>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn-submit">
                                            <i class="fas fa-save me-2"></i>Enregistrer
                                        </button>
                                        <a href="<?= BASE_URL ?>dons/liste" class="btn-cancel">
                                            <i class="fas fa-times me-2"></i>Annuler
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
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
