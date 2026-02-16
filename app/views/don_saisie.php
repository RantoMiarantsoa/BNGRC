<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saisie d'un don</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h5 mb-0">Saisie d'un don</h1>
                </div>
                <div class="card-body">
                    <form method="post" action="/dons/saisie">
                        <div class="mb-3">
                            <label class="form-label" for="type_besoin_id">Type de don</label>
                            <select class="form-select" id="type_besoin_id" name="type_besoin_id" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($types as $type) : ?>
                                    <option value="<?php echo (int) $type['id']; ?>">
                                        <?php echo htmlspecialchars($type['nom'], ENT_QUOTES, 'UTF-8'); ?>
                                        (<?php echo htmlspecialchars($type['categorie'], ENT_QUOTES, 'UTF-8'); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="quantite">Quantité</label>
                            <input type="number" class="form-control" id="quantite" name="quantite" min="1" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <a class="btn btn-outline-secondary" href="/">Retour</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
