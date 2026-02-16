# Todo List - Fonctionnalités Complémentaires BNGRC

---

## Miaro

| ID | Module | Tâche | Description |
|----|--------|-------|-------------|
| 1 | Configuration | Constante ACHAT_FRAIS_PERCENT | Ajouter define('ACHAT_FRAIS_PERCENT', X); dans config.php |
| 2 | Repository | SimulationRepository | Créer avec calculatePossibleAchats() |
| 3 | Vue | recap.php | Affiche totaux/satisfaits/reste + bouton actualiser AJAX |
| 4 | Routes | Route /recap | GET + AJAX /recap/get |
| 5 | Routes | Route /achats/liste | GET - affiche liste, filtre via query param ville |

---

## Miarantsoa

| ID | Module | Tâche | Description |
|----|--------|-------|-------------|
| 1 | Repository | RecapRepository | Créer avec getRecapGlobal() et getRecapDetaille() |
| 2 | Controller | DonAchatController | Créer avec showForm(), store(), list() |
| 3 | Controller | RecapController | Créer avec index() et getRecapAjax() |
| 4 | Vue | achat_liste.php | Tableau avec filtre ville en dropdown, montants calculés |
| 5 | Routes | Route /achats/saisie | GET (form) + POST (store) |

---

## Olivier

| ID | Module | Tâche | Description |
|----|--------|-------|-------------|
| 1 | Repository | DonAchatRepository | Créer avec create(), getAll(), getParVille(), getTotalParVille() |
| 2 | Controller | SimulationController | Créer avec index(), simulate(), validate() |
| 3 | Vue | achat_saisie.php | Formulaire standalone (type, quantité, ville, frais affichés) |
| 4 | Vue | simulation.php | 2 boutons "Simuler" et "Validation" |
| 5 | Routes | Route /simulation | GET + POST simulate + POST validate |
