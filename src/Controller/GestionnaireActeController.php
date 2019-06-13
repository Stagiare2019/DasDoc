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


class GestionnaireActeController extends AbstractController
{
    /**
     * @Route("/acte/ajout", name="gestionnaire_ajout")
     */
    public function ajout(Request $request, UserInterface $user, GestionnaireActeHelper $helper)
    {
        // Création d'un object Acte pour utiliser Doctrine
        $acte = new Acte();
        // Création d'un objet formulaire basé sur l'objet Acte (avec les champs et les contraintes adéquates). 
        $form = $this->createForm(ActeType::class, $acte);

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            //On prépare des données selon le bouton de submit utilisé ("Ajouter" ou  "Enregistrer en brouillon") 
            $file = $form->get('file')->getData();
            $idEtat = (isset($_POST['brouillon']) ? 
                $this->getParameter('id_etat_brouillon') : $this->getParameter('id_etat_valide'));
            $idTypeAction = (isset($_POST['brouillon']) ? 
                $this->getParameter('id_typeAction_brouillon') : $this->getParameter('id_typeAction_ajout'));

            //On ajoute des infos complémentaires à l'acte (non-renseignées par le form)
            $acte->setFkEtat($helper->getEtat($idEtat));

            // On uploade le fichier
            $helper->uploaderFichierSurDisque($this->getParameter('pdf_directory'),$file,$acte->getNomPDF());

            // MAJ de la DB (motcles, acte, action)
            $helper->ajouterMotcles($acte,explode(',',$form->get('motcles')->getData()));
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($acte);
            $helper->creerAction($idTypeAction,$acte);
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
    public function modification(Request $request, Acte $acte, UserInterface $user, GestionnaireActeHelper $helper)
    {
        $form = $this->createForm(ActeType::class, $acte);

        //On recharge des données(le file) dans $acte et on stocke l'ancien nom du PDF
        $acte->setFile(new File($this->getParameter('pdf_directory').$acte->getNomPDF()));
        $oldNomPDF = $acte->getNomPDF();

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            // Renommage du fichier
            $helper->renommerFichierSurDisque($this->getParameter('pdf_directory'),$oldNomPDF,$acte->getNomPDF());

            // MAJ de la DB (motcles, acte, action)
            $helper->majMotcles($acte,explode(',',$form->get('motcles')->getData()));
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($acte);
            $helper->creerAction($this->getParameter('id_typeAction_modification'),$acte);
            $manager->flush();

            return $this->redirectToRoute('consultation');

        }

        return $this->render('gestionnaire_acte/form.html.twig', [
            'form' => $form->createView(),
            'creation' => false,
            'motcles' => $helper->getMotclesString($acte)
        ]);
    }



    /**
     * @Route("/acte/suppression/{id}", name="gestionnaire_suppression")
     */
    public function suppression(Request $request, Acte $acte, UserInterface $user, GestionnaireActeHelper $helper)
    {
        // Si suppression annulée
        if ( isset($_POST['annul']) )
            return $this->redirectToRoute('consultation');

        // Si suppression confirmée
        if ( isset($_POST['suppr']) ) {

            // On récupère les entités liées à $acte
            $actionsDeActe = $this->getDoctrine()->getRepository(Action::class)->findByFkActe($acte);

            // On "nullifie" les fkActe des actions (les actions ne sont pas supprimées)
            foreach ($actionsDeActe as $action) {
                $action->setFkActe(null);
            }

            $helper->supprimerFichierSurDisque($this->getParameter('pdf_directory'),$acte->getNomPDF());

            // MAJ de la DB (motcles, PJs, acte, action)
            $manager = $this->getDoctrine()->getManager();
            $helper->supprimerMotcles($acte);
            $manager->remove($acte);
            $helper->creerAction($this->getParameter('id_typeAction_suppression'),null);
            $manager->flush();

            return $this->redirectToRoute('consultation');

        }

        // Affiche confirmation suppression
        return $this->render('gestionnaire_acte/confirmation_suppression.html.twig', [
            'acte' => $acte
        ]);
    }

}