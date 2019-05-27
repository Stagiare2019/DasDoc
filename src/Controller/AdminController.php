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
     * @Route("admin/ajout/{entity}", name="admin_ajouter", requirements={
     *     "entity"="natureActe|etatActe|matiere|familleMatiere|service|typeMotcle|typeAction"
     * })
     */
    public function ajouter(Request $request, string $entity)
    {
        //voir la fonction "register" du "SecurityController" pour comprendre le fonctionnement
        if ($entity == "natureActe") {
            $ajout = new NatureActe();
            $form = $this->createForm(NatureActeType::class, $ajout);
        }
        else if ($entity == "etatActe") {
            $ajout = new EtatActe();
            $form = $this->createForm(EtatActeType::class, $ajout);
        }
        else if ($entity == "matiere") {
            $ajout = new Matiere();
            $form = $this->createForm(MatiereType::class, $ajout);
        }
        else if ($entity == "familleMatiere") {
            $ajout = new FamilleMatiere();
            $form = $this->createForm(FamilleMatiereType::class, $ajout);
        }
        else if ($entity == "service") {
        	$ajout = new Service();
        	$form = $this->createForm(ServiceType::class, $ajout);
        }
        else if ($entity == "typeMotcle") {
            $ajout = new TypeMotcle();
            $form = $this->createForm(TypeMotcleType::class, $ajout);
        }
        else if ($entity == "typeAction") {
            $ajout = new TypeAction();
            $form = $this->createForm(TypeActionType::class, $ajout);
        }
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ajout);
            $manager->flush();
            return $this->redirectToRoute('admin_lister', [
                'entity' => $entity
            ]);

        }

        return $this->render('admin/form.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("admin/lister/{entity}", name="admin_lister", requirements={
     *     "entity"="utilisateur|natureActe|etatActe|matiere|familleMatiere|service|typeMotcle|typeAction"
     * })
     */
    public function lister(string $entity)
    {
        if ($entity == "utilisateur")
            $entites = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();

        else if ($entity == "natureActe")
            $entites = $this->getDoctrine()->getRepository(NatureActe::class)->findAll();

        else if ($entity == "etatActe")
            $entites = $this->getDoctrine()->getRepository(EtatActe::class)->findAll();

        else if ($entity == "matiere")
            $entites = $this->getDoctrine()->getRepository(Matiere::class)->findAll();

        else if ($entity == "familleMatiere")
            $entites = $this->getDoctrine()->getRepository(FamilleMatiere::class)->findAll();

        else if ($entity == "service")
            $entites = $this->getDoctrine()->getRepository(Service::class)->findAll();

        else if ($entity == "typeMotcle")
            $entites = $this->getDoctrine()->getRepository(TypeMotcle::class)->findAll();

        else if ($entity == "typeAction")
            $entites = $this->getDoctrine()->getRepository(TypeAction::class)->findAll();



        return $this->render('admin/liste.html.twig', [
            'entites' => $entites
        ]);

    }



    /**
     * @Route("admin/suppression/{entity}/{id}", name="admin_supprimer", requirements={
     *     "entity"="utilisateur|natureActe|etatActe|matiere|familleMatiere|service|typeMotcle|typeAction","id"="\d+"
     * })
     */
    public function supprimer(string $entity, int $id)
    {
        //voir la fonction "register" du "SecurityController" pour comprendre le fonctionnement
        if ($entity == "utilisateur")
            $suppr = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneById($id);

        else if ($entity == "natureActe")
            $suppr = $this->getDoctrine()->getRepository(NatureActe::class)->findOneById($id);

        else if ($entity == "etatActe")
            $suppr = $this->getDoctrine()->getRepository(EtatActe::class)->findOneById($id);

        else if ($entity == "matiere")
            $suppr = $this->getDoctrine()->getRepository(Matiere::class)->findOneById($id);

        else if ($entity == "familleMatiere")
            $suppr = $this->getDoctrine()->getRepository(FamilleMatiere::class)->findOneById($id);

        else if ($entity == "service")
            $suppr = $this->getDoctrine()->getRepository(Service::class)->findOneById($id);

        else if ($entity == "typeMotcle")
            $suppr = $this->getDoctrine()->getRepository(TypeMotcle::class)->findOneById($id);

        else if ($entity == "typeAction")
            $suppr = $this->getDoctrine()->getRepository(TypeAction::class)->findOneById($id);



        if (null === $suppr)
            return $this->redirectToRoute('consultation_historique');

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($suppr);
        $manager->flush();
        return $this->redirectToRoute('admin_lister', [
                'entity' => $entity
        ]);
    }
}
