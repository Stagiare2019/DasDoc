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
                "Valide"
            ),

            // Types d'action
            "TypeAction" => array(
                "Brouillon",
                "Ajout",
                "Suppression",
                "Modification"
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
                "1. COMMANDE PUBLIQUE",
                "2. URBANISME",
                "3. DOMAINE et PATRIMOINE",
                "4. FONCTION PUBLIQUE",
                "5. INSTITUTIONS et VIE POLITIQUE",
                "6. LIBERTES PUBLIQUES et POUVOIRS DE POLICE",
                "7. FINANCES LOCALES",
                "8. DOMAINES DE COMPETENCES PAR THEMES",
                "9. AUTRES DOMAINES DE COMPETENCES"
            ),

            // Services
            "Service" => array(
                "Urbanisme",
                "Ressources humaines"
            ),
            
            // Matières
            "Matiere" => array(
                array('libelle' => "1.1 Marchés publics",'familleId' => "1"),
                array('libelle' => "1.2 Délégations de service public",'familleId' => "1"),
                array('libelle' => "1.3 Conventions de mandat",'familleId' => "1"),
                array('libelle' => "1.4 Autres contrats",'familleId' => "1"),
                array('libelle' => "1.5 Transactions (protocole d’accord transactionnel)",'familleId' => "1"),
                array('libelle' => "1.6 Maîtrise d’oeuvre",'familleId' => "1"),
                array('libelle' => "1.7 Actes spéciaux et divers",'familleId' => "1"),
                array('libelle' => "2.1 Documents d’urbanisme",'familleId' => "2"),
                array('libelle' => "2.2 Actes relatifs au droit d’occupation et d’utilisation des sols",'familleId' => "2"),
                array('libelle' => "2.3 Droit de préemption urbain",'familleId' => "2"),
                array('libelle' => "3.1 Acquisitions",'familleId' => "3"),
                array('libelle' => "3.2 Aliénations",'familleId' => "3"),
                array('libelle' => "3.3 Locations",'familleId' => "3"),
                array('libelle' => "3.4 Limites territoriales",'familleId' => "3"),
                array('libelle' => "3.5 Actes de gestion du domaine public",'familleId' => "3"),
                array('libelle' => "3.6 Actes de gestion du domaine privé",'familleId' => "3"),
                array('libelle' => "4.1 Personnels titulaires et stagiaires de la F.P.T",'familleId' => "4"),
                array('libelle' => "4.2 Personnels contractuels",'familleId' => "4"),
                array('libelle' => "4.3 Fonction publique hospitalière",'familleId' => "4"),
                array('libelle' => "4.4 Autres catégories de personnels",'familleId' => "4"),
                array('libelle' => "4.5 Régime indemnitaire",'familleId' => "4"),
                array('libelle' => "5.1 Election Exécutif",'familleId' => "5"),
                array('libelle' => "5.2 Fonctionnement des assemblées",'familleId' => "5"),
                array('libelle' => "5.3 Désignation des représentants",'familleId' => "5"),
                array('libelle' => "5.4 Délégation de fonctions",'familleId' => "5"),
                array('libelle' => "5.5 Délégation de signature",'familleId' => "5"),
                array('libelle' => "5.6 Exercice des mandats locaux",'familleId' => "5"),
                array('libelle' => "5.7 Intercommunalité",'familleId' => "5"),
                array('libelle' => "5.8 Décision d’ester en justice",'familleId' => "5"),
                array('libelle' => "6.1 Police municipale",'familleId' => "6"),
                array('libelle' => "6.2 Pouvoirs du président du conseil général",'familleId' => "6"),
                array('libelle' => "6.3 Pouvoirs du président du conseil régional",'familleId' => "6"),
                array('libelle' => "6.4 Autres actes réglementaires",'familleId' => "6"),
                array('libelle' => "6.5 Actes pris au nom de l’Etat",'familleId' => "6"),
                array('libelle' => "7.1 Décisions budgétaires (B.P., D.M., C.A. …)",'familleId' => "7"),
                array('libelle' => "7.2 Fiscalité",'familleId' => "7"),
                array('libelle' => "7.3 Emprunts",'familleId' => "7"),
                array('libelle' => "7.4 Interventions économiques en faveur des entreprises",'familleId' => "7"),
                array('libelle' => "7.5 Subventions",'familleId' => "7"),
                array('libelle' => "7.6 Contributions budgétaires",'familleId' => "7"),
                array('libelle' => "7.7 Avances",'familleId' => "7"),
                array('libelle' => "7.8 Fonds de concours",'familleId' => "7"),
                array('libelle' => "7.9 Prise de participation (SEM, etc.)",'familleId' => "7"),
                array('libelle' => "7.10 Divers",'familleId' => "7"),
                array('libelle' => "8.1 Enseignement",'familleId' => "8"),
                array('libelle' => "8.2 Aide sociale",'familleId' => "8"),
                array('libelle' => "8.3 Voirie",'familleId' => "8"),
                array('libelle' => "8.4 Aménagement du territoire",'familleId' => "8"),
                array('libelle' => "8.5 Politique de la ville, habitat, logement",'familleId' => "8"),
                array('libelle' => "8.6 Emploi, formation professionnelle",'familleId' => "8"),
                array('libelle' => "8.7 Transports",'familleId' => "8"),
                array('libelle' => "8.8 Environnement",'familleId' => "8"),
                array('libelle' => "8.9 Culture",'familleId' => "8"),
                array('libelle' => "9.1 Autres domaines de compétences des communes",'familleId' => "9"),
                array('libelle' => "9.2 Autres domaines de compétences des départements",'familleId' => "9"),
                array('libelle' => "9.3 Autres domaines de compétences des régions",'familleId' => "9"),
                array('libelle' => "9.4 Voeux et motions",'familleId' => "9")
            )
        );

        return $entites;

    }

}