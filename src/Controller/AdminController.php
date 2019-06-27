<?php

namespace App\Controller;

use App\Entity\EtatActe;
use App\Entity\FamilleMatiere;
use App\Entity\Matiere;
use App\Entity\NatureActe;
use App\Entity\Service;
use App\Entity\TypeAction;
use App\Entity\Utilisateur;
use App\Form\EtatActeType;
use App\Form\FamilleMatiereType;
use App\Form\MatiereType;
use App\Form\NatureActeType;
use App\Form\ServiceType;
use App\Form\TypeActionType;
use App\Service\AdminHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/*

Ce controleur est en charge de la gestion des données et procédures relatives au fonctionnement de DasDoc.
Il est en charge des groupes d'utilisateurs mais n'a rien à voir avec la sécurité.

*/

/*

Définition "une entité fonctionnelle" :
    Entité (une table niveau modèle) possédant des enregistrements précis permettant à la plateforme de fonctionner.
        
Ex: NatureActe contient toutes les natures d'actes => on ne peut pas retirer
une nature d'acte une fois qu'elle est utilisée pour définir au moins un acte.

Définition "une instance" :
    Une ligne de la table, un enregistrement (bien faire la différence avec avec "Entité).

Ex: Client est une entité, Monsieur X est une instance de Client.

*/

class AdminController extends AbstractController
{
    /**
     * Affiche un tableau de bord contenant :
     * - la liste des comptes/utilisateurs de la plateforme et leur rôle.
     *
     * @Route("admin/dashboard", name="admin_dashboard")
     */
    public function afficherDashboard(AdminHelper $helper)
    {
        $utilisateurs = $helper->getAllInstances("Utilisateur");

        return $this->render('admin/dashboard.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }



    /**
     * Affiche la liste des enregistrements d'une entité fonctionnelle donnée ($entity).
     * Affiche également des liens pour les renommer ou en ajouter (pas de suppression).
     * 
     * @Route("admin/lister/{entity}", name="admin_lister", requirements={
     *     "entity"="EtatActe|TypeAction|NatureActe|FamilleMatiere|Matiere|Service"
     * })
     */
    public function lister(string $entity, AdminHelper $helper)
    {
        $instances = $helper->getAllInstances($entity);

        return $this->render('admin/liste.html.twig', [
            'entites' => $instances
        ]);
    }



    /**
     * Affiche et traite un formulaire d'ajout d'un enregistrement pour une entité fonctionnelle donnée ($entity).
     *
     * @Route("admin/ajout/{entity}", name="admin_ajouter", requirements={
     *     "entity"="EtatActe|TypeAction|NatureActe|FamilleMatiere|Matiere|Service"
     * })
     */
    public function ajouter(Request $request, string $entity, AdminHelper $helper)
    {
        // Création d'un formulaire lié à une instance vide
        $formClass = $helper->getFormClass($entity);
        $entityClass = $helper->getEntityClass($entity);
        $newInstance = new $entityClass();
        $form = $this->createForm($formClass, $newInstance);
        
        // Traitement du formulaire (test validité, m-à-j DB, redirection)
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newInstance);
            $manager->flush();
            return $this->redirectToRoute('admin_lister', [
                'entity' => $entity
            ]);

        }

        // Affichage du formulaire
        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'creation' => true
        ]);
    }



    /**
     * Affiche et traite un formulaire de modification d'un enregistrement pour une entité fonctionnelle donnée ($entity).
     * (similaire à l'ajout si ce n'est qu'on donne un enregistrement existant au lieu d'en créer un)
     *
     * @Route("admin/renommage/{entity}/{id}", name="admin_renommer", requirements={
     *     "entity"="EtatActe|TypeAction|NatureActe|FamilleMatiere|Matiere|Service","id"="\d+"
     * })
     */
    public function renommer(Request $request, string $entity, int $id)
    {
        // Demande une instance, si elle existe (sinon redirection "consultation")
        $instance = $this->getInstance($entity,$id);
        if (null === $instance)
            return $this->redirectToRoute('consultation');

        // Création d'un formulaire prérempli lié à l'instance
        $formClass = $helper->getFormClass($entity);
        $form = $this->createForm($formClass, $instance);

        // Traitement du formulaire (test validité, m-à-j DB, redirection)
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($instance);
            $manager->flush();
            return $this->redirectToRoute('admin_lister', [
                'entity' => $instance
            ]);

        }
        
        // Affichage du formulaire
        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'creation' => false
        ]);
    }



    /**
     * Supprime un enregistrement.
     * Non-utilisé actuellment mais je la garde car on ne sait jamais.
     *
     * @Route("admin/suppression/{entity}/{id}", name="admin_supprimer", requirements={
     *     "entity"="EtatActe|TypeAction|NatureActe|FamilleMatiere|Matiere|Service","id"="\d+"
     * })
     */
    public function supprimer(string $entity, int $id)
    {
        // Demande une instance, si elle existe (sinon redirection "consultation")
        $instance = $this->getInstance($entity,$id);
        if (null === $instance)
            return $this->redirectToRoute('consultation');

        // Supprime l'instance (sur la DB), redirige
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($suppr);
        $manager->flush();
        return $this->redirectToRoute('admin_lister', [
            'entity' => $entity
        ]);
    }


    /**
     * Permet d'initialiser les enregistrements nécessaires au fonctionnement de la plateforme :
     * EtatActe, TypeAction, NatureActe, FamilleMatiere, Matiere, Service
     *
     * N.B. : 
     * - faire attention au lien entre FamilleMatiere et Matiere.
     * - vérifier la correspondance entre les paramètres globaux dans "config/services.yaml" et les ids des instances !!!
     *
     * Cette fonction ne doit se lancer qu'une fois après création des tables encore vides pour les remplir.
     * pour créer tables vides -> "php bin/console doctrine:migrations:migrate"
     *
     * @Route("admin/initialisation", name="admin_initialiser")
     */
    public function initialiser(AdminHelper $helper)
    {

        // On récupère les objets dont on va avoir besoin
        $manager = $this->getDoctrine()->getManager();
        $entites = $helper->getEnregistrementsInit();

        // On ajoute au manager les enregistrements sauf les matières (car elles ont besoin que leur famille soit dans la DB)
        foreach ($entites as $key => $entite) {

            if ($key !== "Matiere") {
                foreach($entite as $libelle) {
                    $entityClass = $helper->getEntityClass($key);
                    $instance = new $entityClass();
                    $instance->setLibelle($libelle);
                    $manager->persist($instance);
                }
            }

        }

        // On ajoute les enregistrements à la DB (sauf les matières)
        $manager->flush();

        // On rajoute les matières
        foreach($entites['Matiere'] as $infos) {
            $instance = new Matiere();
            $instance->setLibelle($infos['libelle']);
            $instance->setFkFamille($manager->getRepository(FamilleMatiere::class)->findOneById($infos['familleId']));
            $manager->persist($instance);
        }

        // On ajoute les matières à la DB
        $manager->flush();

        // On redirige vers "security_register" pour ajouter un utilisateur après l'initialisation
        return $this->redirectToRoute('security_register');

    }
}
