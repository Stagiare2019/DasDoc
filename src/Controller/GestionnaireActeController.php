<?php

namespace App\Controller;

use App\Entity\Acte;
use App\Entity\Action;
use App\Entity\EtatActe;
use App\Entity\Motcle;
use App\Entity\PieceJointe;
use App\Entity\TypeAction;
use App\Form\ActeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class GestionnaireActeController extends AbstractController
{
    /**
     * @Route("/acte/ajout", name="gestionnaire_ajout")
     */
    public function ajout(Request $request, UserInterface $user)
    {
        $acte = new Acte();
        $form = $this->createForm(ActeType::class, $acte);

        dump($acte);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            //On prépare des données
            $file = $form->get('file')->getData();
            if ( isset($_POST['brouillon']) ) {
                $etat = "Brouillon";
                $libelleTypeAction = "Enregistrement d'acte en brouillon";
            }
            else {
                $etat = "Valide";
                $libelleTypeAction = "Ajout d'acte";
            }

            //On ajoute les infos complémentaires à l'acte
            $acte->setNomPDF($acte->getNumero().' - '.$acte->getObjet().'.'.$file->guessExtension());
            $acte->setFkEtat($this->getDoctrine()->getRepository(EtatActe::class)->findOneByLibelle($etat));

            // L'upload du fichier
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file->move($this->getParameter('pdf_directory'), $acte->getNomPDF());

            //On met à jour la DB
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($acte);
            $manager->persist($this->creerAction($user,$acte,$libelleTypeAction));
            $manager->flush();
            return $this->redirectToRoute('consultation');

        }

        return $this->render('gestionnaire_acte/form.html.twig', [
            'form' => $form->createView(),
            'creation' => true
        ]);
    }



    /**
     * @Route("/acte/modification/{id}", name="gestionnaire_modification")
     */
    public function modification(Request $request, Acte $acte, UserInterface $user)
    {
        //On recharge le fichier dans $acte et on stocke l'ancien nom du PDF
        $acte->setFile(new File($this->getParameter('pdf_directory').'/'.$acte->getNomPDF()));
        $oldNomPDF = $acte->getNomPDF();

        $form = $this->createForm(ActeType::class, $acte);

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            //On prépare des données en fonction du type d'enregistrement
            if ( isset($_POST['brouillon']) ) {
                $etat = "Brouillon";
                $libelleTypeAction = "Modification d'acte";
            }
            else {
                $etat = "Valide";
                $libelleTypeAction = "Complétion d'acte";
            }

            // Le renommage du fichier (si changement dans le path)
            if( $acte->getNomPDF() != $oldNomPDF ) {
                $fs = new Filesystem();
                $fs->rename($this->getParameter('pdf_directory').'/'.$oldNomPDF,
                    $this->getParameter('pdf_directory').'/'.$acte->getNomPDF());
            }

            //On ajoute les infos complémentaires à l'acte
            $acte->setFkEtat($this->getDoctrine()->getRepository(EtatActe::class)->findOneByLibelle($etat));

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($acte);
            $manager->persist($this->creerAction($user,$acte,$libelleTypeAction));
            $manager->flush();
            return $this->redirectToRoute('recherche');

        }

        return $this->render('gestionnaire_acte/form.html.twig', [
            'form' => $form->createView(),
            'creation' => false
        ]);
    }



    /**
     * @Route("/acte/suppression/{id}", name="gestionnaire_suppression")
     */
    public function suppression(Request $request, Acte $acte, UserInterface $user)
    {
        if ( isset($_POST['annul']) )
            return $this->redirectToRoute('recherche');

        if ( isset($_POST['suppr']) ) {

            //On récupère les entités liés à Acte
            $pjsDeActe = $this->getDoctrine()->getRepository(PieceJointe::class)->findByFkActe($acte);
            $actionsDeActe = $this->getDoctrine()->getRepository(Action::class)->findByFkActe($acte);
            $motClesDeActe = $acte->getMotCles();

            //On "nullifie" les fk des actions
            foreach ($actionsDeActe as $action) {
                $action->setFkActe(null);
            }

            //Suppression du fichier sur le disque
            $fs = new Filesystem();
            $fs->remove($this->getParameter('pdf_directory').'/'.$acte->getNomPDF());

            $manager = $this->getDoctrine()->getManager();
            //On supprimer les pieces jointes
            foreach ($pjsDeActe as $pj) {
                $fs->remove($this->getParameter('pdf_directory').'/'.$pj->getNomPDF());
                $manager->remove($pj);
            }
            //On supprimer l'acte de ses motclés
            foreach ($motClesDeActe as $motcle) {
                $motcle->removeActe($acte);
            }
            $manager->remove($acte);
            $manager->persist($this->creerAction($user,null,"Suppression d'acte"));
            $manager->flush();
            return $this->redirectToRoute('recherche');

        }

        return $this->render('gestionnaire_acte/confirmation_suppression.html.twig', [
            'acte' => $acte
        ]);
    }



    private function creerAction(UserInterface $user, Acte $acte = null, String $libelleTypeAction): Action
    {
        $action = new Action();
        $action->setDate(new \DateTime('now'));
        $action->setFkUtilisateur($user);
        $action->setFkActe($acte);
        $action->setFkType($this->getDoctrine()->getRepository(TypeAction::class)->findOneByLibelle($libelleTypeAction));

        return $action;
    }
}