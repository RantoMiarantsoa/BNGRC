<?php

class DonController
{
    private DonRepository $donRepository;
    private CategorieRepository $categorieRepository;

    public function __construct()
    {
        try {
            $db = Flight::db();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Database connection is not available.', 0, $e);
        }

        if ($db === null) {
            throw new \RuntimeException('Database connection is not available.');
        }

        $this->donRepository = new DonRepository($db);
        $this->categorieRepository = new CategorieRepository($db);
    }

    public function showForm(): void
    {
        $categories = $this->categorieRepository->getAll();
        Flight::render('don_saisie', [
            'categories' => $categories
        ]);
    }

    public function store(): void
    {
        $nom = trim((string) Flight::request()->data->nom);
        $quantite = (int) Flight::request()->data->quantite;
        $idTypeCategorie = Flight::request()->data->id_type_categorie;
        $idTypeCategorie = ($idTypeCategorie !== '' && $idTypeCategorie !== null) ? (int) $idTypeCategorie : null;

        $this->donRepository->create($nom, $quantite, $idTypeCategorie);

        $categories = $this->categorieRepository->getAll();
        Flight::render('don_saisie', [
            'categories' => $categories,
            'succes' => 'Don enregistré avec succès'
        ]);
    }

    public function list(): void
    {
        $dons = $this->donRepository->getDisponiblesParType();

        Flight::render('don_liste', [
            'dons' => $dons
        ]);
    }
}
