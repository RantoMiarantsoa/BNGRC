<?php

class DashboardController
{
    private DashboardRepository $dashboardRepository;

    public function __construct()
    {
        $this->dashboardRepository = new DashboardRepository(Flight::db());
    }

    public function index(): void
    {
        $rows = $this->dashboardRepository->getTableauParVille();
        $indicateurs = $this->dashboardRepository->getIndicateursGlobaux();

        Flight::render('dashboard', [
            'rows' => $rows,
            'indicateurs' => $indicateurs
        ]);
    }
}
