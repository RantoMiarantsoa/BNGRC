<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row">
        <div class="col-lg-11 mx-auto">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-info shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total besoins</h6>
                            <p class="display-6 mb-0"><?php echo number_format((int) $indicateurs['total_besoins'], 0, ',', ' '); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="card-title">Total dons</h6>
                            <p class="display-6 mb-0"><?php echo number_format((int) $indicateurs['total_dons'], 0, ',', ' '); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="card-title">Taux de couverture</h6>
                            <p class="display-6 mb-0"><?php echo number_format((float) $indicateurs['taux_couverture'], 2, ',', ' '); ?> %</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h5 mb-0">Tableau par ville</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Ville</th>
                                    <th class="text-end">Besoins totaux</th>
                                    <th class="text-end">Reçus</th>
                                    <th class="text-end">Reste</th>
                                    <th class="text-end">Valeur monétaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Aucune donnée.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($rows as $row) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['ville'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-end"><?php echo (int) $row['total_besoin']; ?></td>
                                            <td class="text-end"><?php echo (int) $row['total_recu']; ?></td>
                                            <td class="text-end"><?php echo (int) $row['total_reste']; ?></td>
                                            <td class="text-end">
                                                <?php echo number_format((float) $row['valeur_monetaire'], 2, ',', ' '); ?>
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
