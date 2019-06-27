<?php

namespace App\Controller;

use App\Entity\Acte;
use App\Entity\Action;
use App\Entity\EtatActe;
use App\Entity\Motcle;
use App\Entity\PieceJointe;
use App\Entity\TypeAction;
use App\Form\ActeType;
use App\Service\GestionnaireActeHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/*

Ce controleur est en charge de la manipulation des actes dans la base de données et sur le disque.

*/

class GestionnaireActeController extends AbstractController
{
    /**
     * Affiche et traite un formulaire d'ajout d'acte
     *
     * @Route("/acte/ajout", name="gestionnaire_ajout")
     */
    public function ajout(Request $request, UserInterface $user, GestionnaireActeHelper $helper)
    {
        // Création d'un formulaire lié à une instance vide de Acte
        $form = $this->createForm(ActeType::class, $acte = new Acte());

        // Traitement du formulaire (test validité, complétion des données, upload des fichiers, création d'une action, m-à-j DB, redirection)
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            // Préparation données : selon si c'est un brouillon ou pas

                $idEtat = (isset($_POST['brouillon']) ? 
                    $this->getParameter('id_etat_brouillon') : $this->getParameter('id_etat_valide'));
                $idTypeAction = (isset($_POST['brouillon']) ? 
                    $this->getParameter('id_typeAction_brouillon') : $this->getParameter('id_typeAction_ajout'));

            // Complétion données

                $acte->setFkEtat($helper->getEtatActe($idEtat));

            // Upload fichier

                $helper->uploaderFichier($form->get('file')->getData(),$this->getParameter('pdf_directory'),$acte->getNomPDF());

            // maj DB et disque

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($acte);
                $helper->creerAction($idTypeAction,$acte);
                $helper->majPieceJointes($acte,$form);
                $helper->majMotcles($acte,$form);
                $manager->flush();

            return $this->redirectToRoute('consultation');

        }

        // Affichage du formulaire
        return $this->render('gestionnaire_acte/form.html.twig', [
            'form' => $form->createView(),
            'creation' => true
        ]);
    }



    /**
     * Affiche et traite un formulaire de modification d'un acte
     * (même formulaire que l'ajout si ce n'est qu'on donne une instance déjà existante au lieu d'en créer une)
     * NB : Symfony gère si l'id ne correspond à aucun acte
     *
     * @Route("/acte/modification/{id}", name="gestionnaire_modification")
     */
    public function modification(Request $request, Acte $acte, UserInterface $user, GestionnaireActeHelper $helper)
    {
        // Création d'un formulaire prérempli lié à l'acte à modifier
        $form = $this->createForm(ActeType::class, $acte);

        // On recharge le fichier dans $acte et on stocke l'ancien nom du PDF pour le retouver
        $acte->setFile(new File($this->getParameter('pdf_directory').$acte->getNomPDF()));
        $oldNomPDF = $acte->getNomPDF();

        // Traitement du formulaire (test validité, m-à-j disque, m-à-j DB, redirection)
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            // Renommage du fichier

                $helper->renommerFichier($this->getParameter('pdf_directory'),$oldNomPDF,$acte->getNomPDF());
            
            // maj DB et disque

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($acte);
                $helper->creerAction($this->getParameter('id_typeAction_modification'),$acte);
                $helper->majPieceJointes($acte,$form);
                $helper->majMotcles($acte,$form);
                $manager->flush();

            return $this->redirectToRoute('consultation');

        }

        // Affichage du formulaire
        return $this->render('gestionnaire_acte/form.html.twig', [
            'form' => $form->createView(),
            'creation' => false,
            'motcles' => $helper->getStringConcatenatedMotcles($acte)
        ]);
    }



    /**
     * Affiche une page (avec les infos de l'acte) demandant confirmation à l'utilisateur pour la suppression d'un acte.
     *
     * @Route("/acte/suppression/{id}", name="gestionnaire_suppression")
     */
    public function suppression(Request $request, Acte $acte, UserInterface $user, GestionnaireActeHelper $helper)
    {
        // Si bouton = annuler
        if ( isset($_POST['annul']) )
            return $this->redirectToRoute('consultation');

        // Si suppression confirmée
        if ( isset($_POST['suppr']) ) {

            // Récupèration des instances liées à $acte

                $actionsDeActe = $this->getDoctrine()->getRepository(Action::class)->findByFkActe($acte);

            // "Nullification" des attributs fkActe des actions (les actions ne sont pas supprimées)

                foreach ($actionsDeActe as $action)
                    $action->setFkActe(null);

            // Suppression du fichier

                $helper->supprimerFichier($this->getParameter('pdf_directory'),$acte->getNomPDF());
            

            // maj DB et disque

                $manager = $this->getDoctrine()->getManager();
                $manager->remove($acte);
                $helper->creerAction($this->getParameter('id_typeAction_suppression'),null);
                $helper->majPieceJointes($acte);
                $helper->majMotcles($acte);
                $manager->flush();

            return $this->redirectToRoute('consultation');

        }

        // Affiche confirmation suppression
        return $this->render('gestionnaire_acte/confirmation_suppression.html.twig', [
            'acte' => $acte
        ]);
    }

}