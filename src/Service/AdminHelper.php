<?php

namespace App\Service;

use App\Entity\Acte;
use App\Entity\Action;
use App\Entity\EtatActe;
use App\Entity\FamilleMatiere;
use App\Entity\Motcle;
use App\Entity\TypeAction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/*

Ce service contient beaucoup de getters simplifiant la lecture du code de "AdminController" et les instances d'initialisation des entités fonctionnelles

*/
class AdminHelper {

	private $manager;
    private $entityDirectory;
    private $formDirectory;

	public function __construct($entityDirectory, $formDirectory, EntityManagerInterface $manager)
	{
		$this->manager = $manager;
        $this->entityDirectory = $entityDirectory;
        $this->formDirectory = $formDirectory;
	}

    // GET CLASSE

    // Retourne la classe d'une entité
    public function getEntityClass(string $entity): string
    {
        return $this->entityDirectory.$entity;
    }

    // Retourne la classe du formulaire lié à une entité
    public function getFormClass(string $entity): string
    {
        return $this->formDirectory.$entity."Type";
    }

    // GET INSTANCE(S)

    // Retourne toutes les instances d'une entité
    public function getAllInstances(string $entity): array
    {
        return $this->manager->getRepository($this->getEntityClass($entity))->findAll();
    }

    // Retourne une instance d'une entité en focntion de son id
    public function getInstance(string $entity, int $id)
    {
        return $this->getDoctrine()->getRepository($this->getEntityClass($entity))->findOneById($id);
    }

    // GET DONNEES
    
    // Retourne un array contenant les enregistrements fonctionnels : EtatActe,TypeAction,NatureActe,FamilleMatiere,Service,Matiere
    public function getEnregistrementsInit()
    {
        $entites = array(

            // Etats d'acte
            "EtatActe" => array(
                "Brouillon",
                "En attente de validation",
                "Validé",
                "Refusé",
                "Transmis",
                "Annulé",
                "Archivé"
            ),

            // Types d'action
            "TypeAction" => array(
                "Enregistrer comme brouillon",
                "Reprendre un brouillon",
                "Envoyer en validation",
                "Valider",
                "Refuser",
                "Transmettre",
                "Annuler",
                "Archiver",
            ),

            // Natures d'acte
            "NatureActe" => array(
                "Arrêté",
                "Arrêté individuel",
                "Décision municipale",
                "Délibération"
            ),

            // Familles de matières
            "FamilleMatiere" => array(
                array('code' => "1.", 'nom' => "COMMANDE PUBLIQUE"),
                array('code' => "2.", 'nom' => "URBANISME"),
                array('code' => "3.", 'nom' => "DOMAINE et PATRIMOINE"),
                array('code' => "4.", 'nom' => "FONCTION PUBLIQUE"),
                array('code' => "5.", 'nom' => "INSTITUTIONS et VIE POLITIQUE"),
                array('code' => "6.", 'nom' => "LIBERTES PUBLIQUES et POUVOIRS DE POLICE"),
                array('code' => "7.", 'nom' => "FINANCES LOCALES"),
                array('code' => "8.", 'nom' => "DOMAINES DE COMPETENCES PAR THEMES"),
                array('code' => "9.", 'nom' => "AUTRES DOMAINES DE COMPETENCES")
            ),

            // Services
            "Service" => array(
                "Achats marchés - Commande publique",
                "Action sociale - Séniors - Santé",
                "Archives - Documentation",
                "Bâtiments",
                "Bâtiments - Centre Technique Municipal",
                "Cabinet du maire",
                "Cadre de vie",
                "Comité des Oeuvres Sociales",
                "Comptabilité - Finances",
                "Conservatoire Georges-Gershwin",
                "Crèche familiale du Calin-Calinou",
                "Culturel",
                "Direction de la Communication",
                "Direction des Services Techniques",
                "Direction Générale des Services",
                "Enfance (secteur)",
                "Enfance - Scolarité",
                "Espace Informations Retraités",
                "Espace médical Joseph-Boullanger",
                "Etat-civil - Elections",
                "Evènementiel",
                "Foyer du Clos de Rome",
                "Foyer Les Pincerais",
                "Garage- Transports",
                "Guichet Unique",
                "Halte-garderie Tapis-vole",
                "Jeunesse - Sports - Vie associative",
                "Juridique",
                "Magasin Municipal",
                "Médiathèque Blaise-Cendrars",
                "Multi-accueil Arlequin",
                "Multi-accueil Neson Mandela",
                "Multi-accueil Nougatine",
                "Multi-accueil Tournycoti",
                "Musée de la Batellerie et des voies navigables",
                "Petite enfance",
                "Police Municipale",
                "Relais d'Assistantes Maternelles",
                "Ressources Humaines",
                "Scolarité - Restauration - Entretion bâtiments (secteur)",
                "Systèmes d'Information",
                "Théâtre Simone-Signoret",
                "Urbanisme",
                "Vie économique - commerce - fluvial"
            ),
            
            // Matières
            "Matiere" => array(
                array('familleId' => "1", 'code' => "1.1", 'nom' => "Marchés publics"),
                array('familleId' => "1", 'code' => "1.2", 'nom' => "Délégations de service public"),
                array('familleId' => "1", 'code' => "1.3", 'nom' => "Conventions de mandat"),
                array('familleId' => "1", 'code' => "1.4", 'nom' => "Autres contrats"),
                array('familleId' => "1", 'code' => "1.5", 'nom' => "Transactions (protocole d’accord transactionnel)"),
                array('familleId' => "1", 'code' => "1.6", 'nom' => "Maîtrise d’oeuvre"),
                array('familleId' => "1", 'code' => "1.7", 'nom' => "Actes spéciaux et divers"),
                array('familleId' => "2", 'code' => "2.1", 'nom' => "Documents d’urbanisme"),
                array('familleId' => "2", 'code' => "2.2", 'nom' => "Actes relatifs au droit d’occupation et d’utilisation des sols"),
                array('familleId' => "2", 'code' => "2.3", 'nom' => "Droit de préemption urbain"),
                array('familleId' => "3", 'code' => "3.1", 'nom' => "Acquisitions"),
                array('familleId' => "3", 'code' => "3.2", 'nom' => "Aliénations"),
                array('familleId' => "3", 'code' => "3.3", 'nom' => "Locations"),
                array('familleId' => "3", 'code' => "3.4", 'nom' => "Limites territoriales"),
                array('familleId' => "3", 'code' => "3.5", 'nom' => "Actes de gestion du domaine public"),
                array('familleId' => "3", 'code' => "3.6", 'nom' => "Actes de gestion du domaine privé"),
                array('familleId' => "4", 'code' => "4.1", 'nom' => "Personnels titulaires et stagiaires de la F.P.T"),
                array('familleId' => "4", 'code' => "4.2", 'nom' => "Personnels contractuels"),
                array('familleId' => "4", 'code' => "4.3", 'nom' => "Fonction publique hospitalière"),
                array('familleId' => "4", 'code' => "4.4", 'nom' => "Autres catégories de personnels"),
                array('familleId' => "4", 'code' => "4.5", 'nom' => "Régime indemnitaire"),
                array('familleId' => "5", 'code' => "5.1", 'nom' => "Election Exécutif"),
                array('familleId' => "5", 'code' => "5.2", 'nom' => "Fonctionnement des assemblées"),
                array('familleId' => "5", 'code' => "5.3", 'nom' => "Désignation des représentants"),
                array('familleId' => "5", 'code' => "5.4", 'nom' => "Délégation de fonctions"),
                array('familleId' => "5", 'code' => "5.5", 'nom' => "Délégation de signature"),
                array('familleId' => "5", 'code' => "5.6", 'nom' => "Exercice des mandats locaux"),
                array('familleId' => "5", 'code' => "5.7.", 'nom' => "Intercommunalité"),
                array('familleId' => "5", 'code' => "5.8", 'nom' => "Décision d’ester en justice"),
                array('familleId' => "6", 'code' => "6.1", 'nom' => "Police municipale"),
                array('familleId' => "6", 'code' => "6.2", 'nom' => "Pouvoirs du président du conseil général"),
                array('familleId' => "6", 'code' => "6.3", 'nom' => "Pouvoirs du président du conseil régional"),
                array('familleId' => "6", 'code' => "6.4", 'nom' => "Autres actes réglementaires"),
                array('familleId' => "6", 'code' => "6.5", 'nom' => "Actes pris au nom de l’Etat"),
                array('familleId' => "7", 'code' => "7.1", 'nom' => "Décisions budgétaires (B.P., D.M., C.A. …)"),
                array('familleId' => "7", 'code' => "7.2", 'nom' => "Fiscalité"),
                array('familleId' => "7", 'code' => "7.3", 'nom' => "Emprunts"),
                array('familleId' => "7", 'code' => "7.4", 'nom' => "Interventions économiques en faveur des entreprises"),
                array('familleId' => "7", 'code' => "7.5", 'nom' => "Subventions"),
                array('familleId' => "7", 'code' => "7.6", 'nom' => "Contributions budgétaires"),
                array('familleId' => "7", 'code' => "7.7", 'nom' => "Avances"),
                array('familleId' => "7", 'code' => "7.8", 'nom' => "Fonds de concours"),
                array('familleId' => "7", 'code' => "7.9", 'nom' => "Prise de participation (SEM, etc.)"),
                array('familleId' => "7", 'code' => "7.10", 'nom' => "Divers"),
                array('familleId' => "8", 'code' => "8.1", 'nom' => "Enseignement"),
                array('familleId' => "8", 'code' => "8.2", 'nom' => "Aide sociale"),
                array('familleId' => "8", 'code' => "8.3", 'nom' => "Voirie"),
                array('familleId' => "8", 'code' => "8.4", 'nom' => "Aménagement du territoire"),
                array('familleId' => "8", 'code' => "8.5", 'nom' => "Politique de la ville, habitat, logement"),
                array('familleId' => "8", 'code' => "8.6", 'nom' => "Emploi, formation professionnelle"),
                array('familleId' => "8", 'code' => "8.7", 'nom' => "Transports"),
                array('familleId' => "8", 'code' => "8.8", 'nom' => "Environnement"),
                array('familleId' => "8", 'code' => "8.9", 'nom' => "Culture"),
                array('familleId' => "9", 'code' => "9.1", 'nom' => "Autres domaines de compétences des communes"),
                array('familleId' => "9", 'code' => "9.2", 'nom' => "Autres domaines de compétences des départements"),
                array('familleId' => "9", 'code' => "9.3", 'nom' => "Autres domaines de compétences des régions"),
                array('familleId' => "9", 'code' => "9.4", 'nom' => "Voeux et motions")
            )
        );

        return $entites;

    }

}