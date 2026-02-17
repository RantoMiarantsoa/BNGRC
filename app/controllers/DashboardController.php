<?php

class DashboardController
{
    private DashboardRepository $dashboardRepository;

    public function __construct($db = null)
    {
        if ($db === null) {
            $db = Flight::db();
        }
        $this->dashboardRepository = new DashboardRepository($db);
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
