<?php

class RecapController
{
    private RecapRepository $recapRepository;

    public function __construct()
    {
        $this->recapRepository = new RecapRepository(Flight::db());
    }

    public function index(): void
    {
        $recap = $this->recapRepository->getRecap();

        Flight::render('recap', [
            'recap' => $recap
        ]);
    }

    public function getRecapAjax(): void
    {
        $recap = $this->recapRepository->getRecap();

        Flight::json($recap);
    }
}
