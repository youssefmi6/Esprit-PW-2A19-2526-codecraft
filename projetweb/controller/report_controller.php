<?php
require_once __DIR__ . '/../model/rapport.php';
require_once __DIR__ . '/../model/statistiques.php';

class ReportController {
    private $rapport;
    private $statistiques;
    
    public function __construct() {
        $this->rapport = new Rapport();
        $this->statistiques = new Statistiques();
    }
    
    // Générer un rapport mensuel
    public function genererRapportMensuel($mois = null, $annee = null) {
        if (!$mois) $mois = date('m');
        if (!$annee) $annee = date('Y');
        
        $date_debut = $annee . '-' . $mois . '-01';
        $date_fin = date('Y-m-t', strtotime($date_debut));
        
        return $this->genererRapport($date_debut, $date_fin, 'Mensuel', 'Rapport mensuel - ' . $mois . '/' . $annee);
    }
    
    // Générer un rapport trimestriel
    public function genererRapportTrimestriel($trimestre = null, $annee = null) {
        if (!$trimestre) $trimestre = ceil(date('m') / 3);
        if (!$annee) $annee = date('Y');
        
        $mois_debut = (($trimestre - 1) * 3) + 1;
        $mois_fin = $mois_debut + 2;
        
        $date_debut = $annee . '-' . str_pad($mois_debut, 2, '0', STR_PAD_LEFT) . '-01';
        $date_fin = $annee . '-' . str_pad($mois_fin, 2, '0', STR_PAD_LEFT) . '-01';
        $date_fin = date('Y-m-t', strtotime($date_fin));
        
        return $this->genererRapport($date_debut, $date_fin, 'Trimestriel', 'Rapport trimestriel Q' . $trimestre . ' ' . $annee);
    }
    
    // Générer un rapport annuel
    public function genererRapportAnnuel($annee = null) {
        if (!$annee) $annee = date('Y');
        
        $date_debut = $annee . '-01-01';
        $date_fin = $annee . '-12-31';
        
        return $this->genererRapport($date_debut, $date_fin, 'Annuel', 'Rapport annuel ' . $annee);
    }
    
    // Générer un rapport personnalisé
    public function genererRapportPersonnalise($date_debut, $date_fin, $titre = 'Rapport personnalisé') {
        return $this->genererRapport($date_debut, $date_fin, 'Custom', $titre);
    }
    
    // Générer un rapport (fonction principale)
    private function genererRapport($date_debut, $date_fin, $type, $titre) {
        // Récupérer les statistiques
        $stats_periode = $this->statistiques->obtenirStatsPeriode($date_debut, $date_fin);
        $temps_resolution_cat = $this->statistiques->obtenirTempsResolutionParCategorie();
        $satisfaction_agent = $this->statistiques->obtenirSatisfactionParAgent();
        
        // Compiler les statistiques
        $statistiques = array(
            'total' => $stats_periode['total'] ?? 0,
            'reglees' => $stats_periode['reglees'] ?? 0,
            'en_cours' => $stats_periode['en_cours'] ?? 0,
            'en_attente' => $stats_periode['en_attente'] ?? 0,
            'rejetees' => $stats_periode['rejetees'] ?? 0,
            'satisfaction_moyenne' => $stats_periode['satisfaction_moyenne'] ?? 0,
            'temps_moyen' => $this->statistiques->obtenirTempsResolutionMoyen()['heures'] ?? 0,
            'cout_total' => $stats_periode['cout_total'] ?? 0,
            'temps_resolution_par_categorie' => $temps_resolution_cat,
            'satisfaction_par_agent' => $satisfaction_agent
        );
        
        // Créer l'enregistrement du rapport
        $this->rapport->titre = $titre;
        $this->rapport->type = $type;
        $this->rapport->date_debut = $date_debut;
        $this->rapport->date_fin = $date_fin;
        $this->rapport->statistiques = $statistiques;
        $this->rapport->date_generation = date('Y-m-d H:i:s');
        $this->rapport->cree_par = 1; // À remplacer par l'ID utilisateur réel
        
        $id_rapport = $this->rapport->creer();
        
        if (!$id_rapport) {
            return array('success' => false, 'message' => 'Erreur lors de la création du rapport');
        }
        
        return array(
            'success' => true,
            'id_rapport' => $id_rapport,
            'titre' => $titre,
            'statistiques' => $statistiques
        );
    }
    
    // Obtenir les rapports
    public function obtenirRapports($type = null) {
        if ($type) {
            return $this->rapport->obtenirParType($type);
        } else {
            return $this->rapport->obtenirTous();
        }
    }
    
    // Exporter un rapport en CSV
    public function exporterCSV($id_rapport) {
        $filename = $this->rapport->genererCSV($id_rapport);
        
        if (!$filename) {
            return array('success' => false, 'message' => 'Erreur lors de la génération du fichier CSV');
        }
        
        return array(
            'success' => true,
            'filename' => $filename,
            'url' => 'exports/' . $filename
        );
    }
    
    // Exporter un rapport en PDF
    public function exporterPDF($id_rapport) {
        // Vérifier si mPDF est installé
        if (!file_exists('../libs/mpdf/vendor/autoload.php')) {
            return array('success' => false, 'message' => 'mPDF n\'est pas installé. Utilisez CSV à la place.');
        }
        
        $filename = $this->rapport->genererPDF($id_rapport);
        
        if (!$filename) {
            return array('success' => false, 'message' => 'Erreur lors de la génération du PDF');
        }
        
        return array(
            'success' => true,
            'filename' => $filename,
            'url' => 'exports/' . $filename
        );
    }
    
    // Supprimer un rapport
    public function supprimerRapport($id_rapport) {
        if ($this->rapport->supprimer($id_rapport)) {
            return array('success' => true, 'message' => 'Rapport supprimé avec succès');
        } else {
            return array('success' => false, 'message' => 'Erreur lors de la suppression');
        }
    }
}
?>
