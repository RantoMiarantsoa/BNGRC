<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Ajouter un Besoin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/don-saisie.css">
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
                        <h1><i class="fas fa-plus-circle"></i> Ajouter un Besoin</h1>
                        <p>Saisissez un nouveau besoin pour une ville</p>
                    </div>

                    <!-- Formulaire Ajouter Besoin -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="don-form-container">
                                <h3>
                                    <i class="fas fa-city"></i>
                                    Enregistrer un Besoin
                                </h3>

                                <form method="POST" action="/besoins/ajouter">
                                    <div class="form-group">
                                        <label for="ville_id">Ville <span style="color: #e74c3c;">*</span></label>
                                        <select class="form-select" id="ville_id" name="ville_id" required>
                                            <option value="">-- Choisir une ville --</option>
                                            <?php foreach ($villes as $ville): ?>
                                                <option value="<?php echo (int) $ville['id']; ?>">
                                                    <?php echo htmlspecialchars($ville['nom']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="categorie_id">Catégorie <span style="color: #e74c3c;">*</span></label>
                                        <select class="form-select" id="categorie_id" name="categorie_id" required>
                                            <option value="">-- Choisir une catégorie --</option>
                                            <option value="1">NATURE</option>
                                            <option value="2">MATERIELS</option>
                                            <option value="3">ARGENTS</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="type_besoin_nom">Type de Besoin <span style="color: #e74c3c;">*</span></label>
                                        <input type="text" class="form-control" id="type_besoin_nom" 
                                               name="type_besoin_nom" placeholder="Ex: Riz, Tente, Aide financière" required>
                                    </div>

                                    <div class="form-group" id="prix-group" style="display: none;">
                                        <label for="prix_unitaire">Prix Unitaire <span style="color: #e74c3c;">*</span></label>
                                        <input type="number" class="form-control" id="prix_unitaire" 
                                               name="prix_unitaire" placeholder="Entrez le prix unitaire" step="0.01" min="0">
                                    </div>

                                    <div class="form-group">
                                        <label for="quantite">Quantité <span style="color: #e74c3c;">*</span></label>
                                        <input type="number" class="form-control" id="quantite" name="quantite" 
                                               min="1" placeholder="Entrez la quantité" required>
                                        <small class="form-text" id="quantite-help" style="color: #7f8c8d;">
                                            Quantité requise pour ce besoin
                                        </small>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn-submit">
                                            <i class="fas fa-save me-2"></i>Enregistrer
                                        </button>
                                        <a href="/besoins" class="btn-cancel">
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
            <?php include __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Layout JS -->
    <script src="/assets/js/layout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorieSelect = document.getElementById('categorie_id');
            const prixGroup = document.getElementById('prix-group');
            const quantiteHelp = document.getElementById('quantite-help');

            if (categorieSelect) {
                categorieSelect.addEventListener('change', function() {
                    const categorie = this.value;

                    // Afficher/cacher le champ prix selon la catégorie
                    if (categorie === '3') { // ARGENTS
                        prixGroup.style.display = 'none';
                        document.getElementById('prix_unitaire').required = false;
                        quantiteHelp.textContent = 'Montant en Ariary (ex: 1000000 pour 1 million Ar)';
                    } else {
                        prixGroup.style.display = 'block';
                        document.getElementById('prix_unitaire').required = true;
                        if (categorie === '1') { // NATURE
                            quantiteHelp.textContent = 'Quantité en unités (kg, L, boîtes, etc.)';
                        } else if (categorie === '2') { // MATERIELS
                            quantiteHelp.textContent = 'Nombre d\'unités requises';
                        } else {
                            quantiteHelp.textContent = 'Quantité requise pour ce besoin';
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>