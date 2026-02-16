// Données pour le calcul
let prixUnitaire = 0;
let maxQuantite = 0;
let disponibleDon = 0;

// Récupère le taux de frais depuis l'attribut data
const tauxFrais = parseFloat(document.getElementById('modalAchat').getAttribute('data-taux-frais')) || 0;

// Quand on ouvre le modal
document.getElementById('modalAchat').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    
    // Récupère les données du bouton
    const besoinId = button.getAttribute('data-besoin-id');
    const typeNom = button.getAttribute('data-type-nom');
    const villeNom = button.getAttribute('data-ville-nom');
    prixUnitaire = parseFloat(button.getAttribute('data-prix'));
    maxQuantite = parseInt(button.getAttribute('data-manquant'));
    
    // Met à jour le modal
    document.getElementById('achat_besoin_id').value = besoinId;
    document.getElementById('achat_type_nom').textContent = typeNom;
    document.getElementById('achat_ville_nom').textContent = villeNom;
    document.getElementById('achat_prix_unitaire').textContent = prixUnitaire.toLocaleString('fr-FR');
    document.getElementById('achat_manquant').textContent = maxQuantite;
    document.getElementById('achat_max_quantite').textContent = maxQuantite;
    document.getElementById('achat_quantite').max = maxQuantite;
    document.getElementById('achat_quantite').value = '';
    document.getElementById('achat_erreur').classList.add('d-none');
    
    // Reset calculs
    document.getElementById('calcul_montant').textContent = '0 Ar';
    document.getElementById('calcul_frais').textContent = '0 Ar';
    document.getElementById('calcul_total').textContent = '0 Ar';
});

// Quand on change le don sélectionné
document.getElementById('don_argent_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    disponibleDon = parseFloat(option.getAttribute('data-disponible') || 0);
    calculerMontants();
});

// Quand on change la quantité
document.getElementById('achat_quantite').addEventListener('input', calculerMontants);

function calculerMontants() {
    const quantite = parseInt(document.getElementById('achat_quantite').value) || 0;
    const montant = quantite * prixUnitaire;
    const frais = montant * (tauxFrais / 100);
    const total = montant + frais;
    
    document.getElementById('calcul_montant').textContent = montant.toLocaleString('fr-FR') + ' Ar';
    document.getElementById('calcul_frais').textContent = frais.toLocaleString('fr-FR') + ' Ar';
    document.getElementById('calcul_total').textContent = total.toLocaleString('fr-FR') + ' Ar';
    
    // Vérification
    const erreur = document.getElementById('achat_erreur');
    const btnValider = document.getElementById('btn_valider_achat');
    
    if (quantite > maxQuantite) {
        erreur.textContent = 'Quantité supérieure au manquant!';
        erreur.classList.remove('d-none');
        btnValider.disabled = true;
    } else if (total > disponibleDon && disponibleDon > 0) {
        erreur.textContent = 'Montant insuffisant dans le don sélectionné! (Disponible: ' + disponibleDon.toLocaleString('fr-FR') + ' Ar)';
        erreur.classList.remove('d-none');
        btnValider.disabled = true;
    } else {
        erreur.classList.add('d-none');
        btnValider.disabled = false;
    }
}
