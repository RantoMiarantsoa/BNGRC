<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des dons</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h5 mb-0">Liste des dons disponibles par type</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Catégorie</th>
                                    <th class="text-end">Prix unitaire</th>
                                    <th class="text-end">Quantité disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dons)) : ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucun don disponible.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($dons as $don) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($don['nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($don['categorie'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-end">
                                                <?php echo number_format((float) $don['prix_unitaire'], 2, ',', ' '); ?>
                                            </td>
                                            <td class="text-end">
                                                <?php echo (int) $don['quantite_totale']; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <a class="btn btn-outline-secondary" href="/">Retour</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
