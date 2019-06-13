<?php

namespace App\Controller;

use App\Entity\EtatActe;
use App\Entity\FamilleMatiere;
use App\Entity\Matiere;
use App\Entity\NatureActe;
use App\Entity\Service;
use App\Entity\TypeAction;
use App\Entity\TypeMotcle;
use App\Entity\Utilisateur;
use App\Form\EtatActeType;
use App\Form\FamilleMatiereType;
use App\Form\MatiereType;
use App\Form\NatureActeType;
use App\Form\ServiceType;
use App\Form\TypeActionType;
use App\Form\TypeMotcleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("admin/dashboard", name="admin_dashboard")
     */
    public function afficherDashboard()
    {
        $utilisateurs = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }



    /**
     * @Route("admin/lister/{entity}", name="admin_lister", requirements={
     *     "entity"="NatureActe|EtatActe|Matiere|FamilleMatiere|Service|TypeAction"
     * })
     */
    public function lister(string $entity)
    {
        $class = $this->getParameter('entity_directory').$entity;
        $entites = $this->getDoctrine()->getRepository($class)->findAll();

        return $this->render('admin/liste.html.twig', [
            'entites' => $entites
        ]);
    }



    /**
     * @Route("admin/ajout/{entity}", name="admin_ajouter", requirements={
     *     "entity"="NatureActe|EtatActe|Matiere|FamilleMatiere|Service|TypeAction"
     * })
     */
    public function ajouter(Request $request, string $entity)
    {
        $formClass = $this->getParameter('form_directory').$entity."Type";
        $entityClass = $this->getParameter('entity_directory').$entity;
        $entiteAjoute = new $entityClass();
        $form = $this->createForm($formClass, $entiteAjoute);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($entiteAjoute);
            $manager->flush();
            return $this->redirectToRoute('admin_lister', [
                'entity' => $entity
            ]);

        }

        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'creation' => true
        ]);
    }



    /**
     * @Route("admin/renommage/{entity}/{id}", name="admin_renommer", requirements={
     *     "entity"="NatureActe|EtatActe|Matiere|FamilleMatiere|Service|TypeAction","id"="\d+"
     * })
     */
    public function renommer(Request $request, string $entity, int $id)
    {
        $class = $this->getParameter('entity_directory').$entity;
        $entite = $this->getDoctrine()->getRepository($class)->findOneById($id);
        $formClass = $this->getParameter('form_directory').$entity."Type";

        if (null === $entite)
            return $this->redirectToRoute('consultation_historique');

        $form = $this->createForm($formClass, $entite);

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($entite);
            $manager->flush();
            return $this->redirectToRoute('admin_lister', [
                'entity' => $entity
            ]);

        }
        
        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'creation' => false
        ]);
    }



    /**
     * @Route("admin/suppression/{entity}/{id}", name="admin_supprimer", requirements={
     *     "entity"="NatureActe|EtatActe|Matiere|FamilleMatiere|Service|TypeAction","id"="\d+"
     * })
     */
    public function supprimer(string $entity, int $id)
    {
        $class = $this->getParameter('entity_directory').$entity;
        $suppr = $this->getDoctrine()->getRepository($class)->findOneById($id);

        if (null === $suppr)
            return $this->redirectToRoute('consultation_historique');

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($suppr);
        $manager->flush();
        return $this->redirectToRoute('admin_lister', [
            'entity' => $entity
        ]);
    }


    /**
     * Enregistrements nécessaires au fonctionnement de la plateforme : EtatActe, TypeAction, NatureActe, FamilleMatiere, Matiere, Service
     * Attention au lien entre FamilleMatiere et Matiere.
     *
     * Attention à la correspondance entre les paramètres globaux "config/services.yaml" et l'insertion des lignes !!!
     *
     * Cette fonction ne doit se lancer qu'une fois après création de la table pour initialiser
     * (je ne parviens pas à reset les id sinon)
     *
     * @Route("admin/initialisation", name="admin_initialiser")
     */
    public function initialiser()
    {
        $manager = $this->getDoctrine()->getManager();

        // Etats d'actes
        $arrayEtatActeInfos = array(
            "Brouillon",
            "Valide"
        );
        foreach ($arrayEtatActeInfos as $etatActeInfos) { 
            $etatActe = new EtatActe();
            $etatActe->setLibelle($etatActeInfos);
            $manager->persist($etatActe);
        }

        // Types d'actions
        $arrayTypeActionInfos = array(
            '1' => "Brouillon",
            '2' => "Ajout",
            '3' => "Suppression",
            '4' => "Modification"
        );
        foreach ($arrayTypeActionInfos as $typeActionInfos) { 
            $typeAction = new TypeAction();
            $typeAction->setLibelle($typeActionInfos);
            $manager->persist($typeAction);
        }

        // Natures d'actes
        $arrayNatureActeInfos = array(
            "Arrêté",
            "Arrêté individuel",
            "Décision municipale",
            "Délibération"
        );
        foreach ($arrayNatureActeInfos as $natureActeInfos) { 
            $natureActe = new NatureActe();
            $natureActe->setLibelle($natureActeInfos);
            $manager->persist($natureActe);
        }

        // Familles de matières
        $arrayFamilleMatiereInfos = array(
            '1' => "1. COMMANDE PUBLIQUE",
            '2' => "2. URBANISME",
            '3' => "3. DOMAINE et PATRIMOINE",
            '4' => "4. FONCTION PUBLIQUE",
            '5' => "5. INSTITUTIONS et VIE POLITIQUE",
            '6' => "6. LIBERTES PUBLIQUES et POUVOIRS DE POLICE",
            '7' => "7. FINANCES LOCALES",
            '8' => "8. DOMAINES DE COMPETENCES PAR THEMES",
            '9' => "9. AUTRES DOMAINES DE COMPETENCES"
        );
        foreach ($arrayFamilleMatiereInfos as $familleMatiereInfos) { 
            $familleMatiere = new FamilleMatiere();
            $familleMatiere->setLibelle($familleMatiereInfos);
            $manager->persist($familleMatiere);
        }

        $manager->flush();
        
        // Matières
        $arrayMatiereInfos = array(
            '1' => array(
                'libelle' => "1.1 Marchés publics",
                'familleId' => "1"
            ),
            '2' => array(
                'libelle' => "1.2 Délégations de service public",
                'familleId' => "1"
            ),
            '3' => array(
                'libelle' => "1.3 Conventions de mandat",
                'familleId' => "1"
            ),
            '4' => array(
                'libelle' => "1.4 Autres contrats",
                'familleId' => "1"
            ),
            '5' => array(
                'libelle' => "1.5 Transactions (protocole d’accord transactionnel)",
                'familleId' => "1"
            ),
            '6' => array(
                'libelle' => "1.6 Maîtrise d’oeuvre",
                'familleId' => "1"
            ),
            '7' => array(
                'libelle' => "1.7 Actes spéciaux et divers",
                'familleId' => "1"
            ),
            '8' => array(
                'libelle' => "2.1 Documents d’urbanisme",
                'familleId' => "2"
            ),
            '9' => array(
                'libelle' => "2.2 Actes relatifs au droit d’occupation et d’utilisation des sols",
                'familleId' => "2"
            ),
            '10' => array(
                'libelle' => "2.3 Droit de préemption urbain",
                'familleId' => "2"
            ),
            '11' => array(
                'libelle' => "3.1 Acquisitions",
                'familleId' => "3"
            ),
            '12' => array(
                'libelle' => "3.2 Aliénations",
                'familleId' => "3"
            ),
            '13' => array(
                'libelle' => "3.3 Locations",
                'familleId' => "3"
            ),
            '14' => array(
                'libelle' => "3.4 Limites territoriales",
                'familleId' => "3"
            ),
            '15' => array(
                'libelle' => "3.5 Actes de gestion du domaine public",
                'familleId' => "3"
            ),
            '16' => array(
                'libelle' => "3.6 Actes de gestion du domaine privé",
                'familleId' => "3"
            ),
            '17' => array(
                'libelle' => "4.1 Personnels titulaires et stagiaires de la F.P.T",
                'familleId' => "4"
            ),
            '18' => array(
                'libelle' => "4.2 Personnels contractuels",
                'familleId' => "4"
            ),
            '19' => array(
                'libelle' => "4.3 Fonction publique hospitalière",
                'familleId' => "4"
            ),
            '20' => array(
                'libelle' => "4.4 Autres catégories de personnels",
                'familleId' => "4"
            ),
            '21' => array(
                'libelle' => "4.5 Régime indemnitaire",
                'familleId' => "4"
            ),
            '22' => array(
                'libelle' => "5.1 Election Exécutif",
                'familleId' => "5"
            ),
            '23' => array(
                'libelle' => "5.2 Fonctionnement des assemblées",
                'familleId' => "5"
            ),
            '24' => array(
                'libelle' => "5.3 Désignation des représentants",
                'familleId' => "5"
            ),
            '25' => array(
                'libelle' => "5.4 Délégation de fonctions",
                'familleId' => "5"
            ),
            '26' => array(
                'libelle' => "5.5 Délégation de signature",
                'familleId' => "5"
            ),
            '27' => array(
                'libelle' => "5.6 Exercice des mandats locaux",
                'familleId' => "5"
            ),
            '28' => array(
                'libelle' => "5.7 Intercommunalité",
                'familleId' => "5"
            ),
            '29' => array(
                'libelle' => "5.8 Décision d’ester en justice",
                'familleId' => "5"
            ),
            '30' => array(
                'libelle' => "6.1 Police municipale",
                'familleId' => "6"
            ),
            '31' => array(
                'libelle' => "6.2 Pouvoirs du président du conseil général",
                'familleId' => "6"
            ),
            '32' => array(
                'libelle' => "6.3 Pouvoirs du président du conseil régional",
                'familleId' => "6"
            ),
            '33' => array(
                'libelle' => "6.4 Autres actes réglementaires",
                'familleId' => "6"
            ),
            '34' => array(
                'libelle' => "6.5 Actes pris au nom de l’Etat",
                'familleId' => "6"
            ),
            '35' => array(
                'libelle' => "7.1 Décisions budgétaires (B.P., D.M., C.A. …)",
                'familleId' => "7"
            ),
            '36' => array(
                'libelle' => "7.2 Fiscalité",
                'familleId' => "7"
            ),
            '37' => array(
                'libelle' => "7.3 Emprunts",
                'familleId' => "7"
            ),
            '38' => array(
                'libelle' => "7.4 Interventions économiques en faveur des entreprises",
                'familleId' => "7"
            ),
            '39' => array(
                'libelle' => "7.5 Subventions",
                'familleId' => "7"
            ),
            '40' => array(
                'libelle' => "7.6 Contributions budgétaires",
                'familleId' => "7"
            ),
            '41' => array(
                'libelle' => "7.7 Avances",
                'familleId' => "7"
            ),
            '42' => array(
                'libelle' => "7.8 Fonds de concours",
                'familleId' => "7"
            ),
            '43' => array(
                'libelle' => "7.9 Prise de participation (SEM, etc.)",
                'familleId' => "7"
            ),
            '44' => array(
                'libelle' => "7.10 Divers",
                'familleId' => "7"
            ),
            '45' => array(
                'libelle' => "8.1 Enseignement",
                'familleId' => "8"
            ),
            '46' => array(
                'libelle' => "8.2 Aide sociale",
                'familleId' => "8"
            ),
            '47' => array(
                'libelle' => "8.3 Voirie",
                'familleId' => "8"
            ),
            '48' => array(
                'libelle' => "8.4 Aménagement du territoire",
                'familleId' => "8"
            ),
            '49' => array(
                'libelle' => "8.5 Politique de la ville, habitat, logement",
                'familleId' => "8"
            ),
            '50' => array(
                'libelle' => "8.6 Emploi, formation professionnelle",
                'familleId' => "8"
            ),
            '51' => array(
                'libelle' => "8.7 Transports",
                'familleId' => "8"
            ),
            '52' => array(
                'libelle' => "8.8 Environnement",
                'familleId' => "8"
            ),
            '53' => array(
                'libelle' => "8.9 Culture",
                'familleId' => "8"
            ),
            '54' => array(
                'libelle' => "9.1 Autres domaines de compétences des communes",
                'familleId' => "9"
            ),
            '55' => array(
                'libelle' => "9.2 Autres domaines de compétences des départements",
                'familleId' => "9"
            ),
            '56' => array(
                'libelle' => "9.3 Autres domaines de compétences des régions",
                'familleId' => "9"
            ),
            '57' => array(
                'libelle' => "9.4 Voeux et motions",
                'familleId' => "9"
            )
        );
        foreach ($arrayMatiereInfos as $matiereInfos) { 
            $matiere = new Matiere();
            $matiere->setLibelle($matiereInfos['libelle']);
            $matiere->setFkFamille($manager->getRepository(FamilleMatiere::class)->findOneById($matiereInfos['familleId']));
            $manager->persist($matiere);
        }

        // Services
        $arrayServiceInfos = array(
            '1' => "Urbanisme",
            '2' => "Ressources humaines"
        );
        foreach ($arrayServiceInfos as $serviceInfos) { 
            $service = new Service();
            $service->setLibelle($serviceInfos);
            $manager->persist($service);
        }
        
        $manager->flush();

        return $this->redirectToRoute('security_register');

    }
}
