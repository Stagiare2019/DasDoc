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
    public function ajout(Request $request, GestionnaireActeHelper $helper)
    {
        // Création d'un formulaire lié à une instance vide de Acte
        $form = $this->createForm(ActeType::class, $acte = new Acte());

        // Traitement du formulaire (test validité, complétion des données, création d'une action, m-à-j DB et disque, upload du fichier, redirection)
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            // Préparation données : selon si c'est un brouillon ou pas

                $idEtat = (isset($_POST['brouillon']) ? 
                    $this->getParameter('id_etat_brouillon') : $this->getParameter('id_etat_enAttenteValidation'));
                $idTypeAction = (isset($_POST['brouillon']) ? 
                    $this->getParameter('id_typeAction_enregistrerBrouillon') : $this->getParameter('id_typeAction_envoyerValidation'));

            // Complétion données

                $acte->setFkEtat($helper->getEtatActe($idEtat));
                $acte->setNomPDF($helper->generateNomPDF($form->get('numero')->getData(),$form->get('objet')->getData()));

            // maj DB et disque

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($acte);
                $helper->creerAction($idTypeAction,$acte);
                $helper->majAnnexes($acte,$form);
                $helper->majMotcles($acte,$form);
                $manager->flush();

            // Upload fichier

                $helper->uploaderFichier($form->get('file')->getData(),$this->getParameter('pdf_directory'),$acte->getNomPDF());

            return $this->redirectToRoute('consultation');

        }

        // Affichage du formulaire
        return $this->render('gestionnaire_acte/form.html.twig', [
            'form' => $form->createView(),
            'creation' => true
        ]);
    }



    /**
     * Affiche et traite un formulaire de modification d'acte
     *
     * @Route("/acte/modification/{id}", name="gestionnaire_modification")
     */
    public function modification(Request $request, Acte $acte, GestionnaireActeHelper $helper)
    {
        // Création d'un formulaire prérempli lié à l'acte à modifier
        $form = $this->createForm(ActeType::class, $acte);

        // On recharge le fichier dans $acte et on stocke l'ancien nom du PDF pour le retouver
        $acte->setFile(new File($this->getParameter('pdf_directory').$acte->getNomPDF()));
        $oldNomPDF = $acte->getNomPDF();

        // On recharge les PieceJointes (non-relatives à l'acte) dans le formulaire
        $pieceJointes = $acte->getPieceJointes()->toArray();
        $i = 1;
        foreach ($pieceJointes as $pj) {
            $form->get('objetAnnexe'.$i)->setData($pj->getObjet());
            $form->get('hiddenPathAnnexe'.$i)->setData($pj->getNomPDF());
            $form->get('hiddenSupprAnnexe'.$i)->setData("false");
            $i++;
        }

        // Traitement du formulaire (test validité, m-à-j disque, m-à-j DB, redirection)
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            // Préparation données : selon si c'est un brouillon ou pas

                $idEtat = (isset($_POST['brouillon']) ? 
                    $this->getParameter('id_etat_brouillon') : $this->getParameter('id_etat_enAttenteValidation'));
                $idTypeAction = (isset($_POST['brouillon']) ? 
                    $this->getParameter('id_typeAction_reprendreBrouillon') : $this->getParameter('id_typeAction_envoyerValidation'));
            
            // Complétion données

                $acte->setFkEtat($helper->getEtatActe($idEtat));
                $acte->setNomPDF($helper->generateNomPDF($form->get('numero')->getData(),$form->get('objet')->getData()));

            // maj DB et disque

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($acte);
                $helper->creerAction($idTypeAction,$acte);
                $helper->majAnnexes($acte,$form);
                $helper->majMotcles($acte,$form);
                $manager->flush();

            // Renommage du fichier

                $helper->renommerFichier($this->getParameter('pdf_directory'),$oldNomPDF,$acte->getNomPDF());

            return $this->redirectToRoute('consultation');

        }

        // Affichage du formulaire
        return $this->render('gestionnaire_acte/form.html.twig', [
            'form' => $form->createView(),
            'creation' => false,
            'motcles' => $helper->getStringConcatenatedMotcles($acte),
            'pieceJointeChemins' => $helper->getArrayNomPDFAnnexes($acte)
        ]);
    }



    /**
     * Affiche une page (avec les infos de l'acte) demandant confirmation à l'utilisateur pour la suppression d'un acte.
     * N.B: Ne crée pas d'action de suppression
     *
     * @Route("/acte/suppression/{id}", name="gestionnaire_suppression")
     */
    public function suppression(Request $request, Acte $acte, GestionnaireActeHelper $helper)
    {
        // Si bouton = annuler
        if ( isset($_POST['annul']) )
            return $this->redirectToRoute('consultation');

        // Si bouton = supprimer
        if ( isset($_POST['suppr']) ) {

            // Récupèration des actions liées à $acte

                $actionsDeActe = $this->getDoctrine()->getRepository(Action::class)->findByFkActe($acte);

            // "Nullification" des attributs fkActe des actions (les actions ne sont pas supprimées)

                foreach ($actionsDeActe as $action)
                    $action->setFkActe(null);
            
            // maj DB et disque

                $manager = $this->getDoctrine()->getManager();
                $helper->majAnnexes($acte);
                $helper->majMotcles($acte);
                $manager->remove($acte);
                $manager->flush();

            // Suppression du fichier

                $helper->supprimerFichier($this->getParameter('pdf_directory'),$acte->getNomPDF());

            return $this->redirectToRoute('consultation');

        }

        // Affiche la page de confirmation de suppression
        return $this->render('gestionnaire_acte/confirmation_suppression.html.twig', [
            'acte' => $acte
        ]);
    }

}