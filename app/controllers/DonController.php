<?php

class DonController
{
    private DonRepository $donRepository;
    private TypeBesoinRepository $typeBesoinRepository;

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
        $this->typeBesoinRepository = new TypeBesoinRepository($db);
    }

    public function showForm(): void
    {
        $types = $this->typeBesoinRepository->getAll();
        Flight::render('don_saisie', [
            'types' => $types
        ]);
    }

    public function store(): void
    {
        $typeBesoinId = (int) Flight::request()->data->type_besoin_id;
        $quantite = (int) Flight::request()->data->quantite;

        $this->donRepository->create($typeBesoinId, $quantite);

        Flight::redirect('/dons/saisie');
    }

    public function list(): void
    {
        $dons = $this->donRepository->getDisponiblesParType();

        Flight::render('don_liste', [
            'dons' => $dons
        ]);
    }
}
