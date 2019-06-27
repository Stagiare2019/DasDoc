<?php

namespace App\Controller;

use App\Entity\Acte;
use App\Entity\Action;
use App\Entity\EtatActe;
use App\Entity\TypeAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/*

Ce controleur est en charge de la gestion pour un utilisateur de tous les actes qui sont en lien avec lui (brouillons, attente de validation, suivi, etc...)

*/

class ConsultationController extends AbstractController
{
    /**
     * @Route("/consultation", name="consultation")
     */
    public function consultation(UserInterface $user)
    {
        $nbActionsAffiches = 10;
        
        //On récupère l'historique de l'utilisateur
        $historique = $this->getDoctrine()->getRepository(Action::class)->findBy(
            array('fkUtilisateur' => $user),
            array('date' => 'DESC'), 
            $nbActionsAffiches
        );

        //On récupère les actions qui sont des créations de brouillons par l'utilisateur
    	$brouillon_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_etat_brouillon')),
            'fkUtilisateur' => $user
        ));

        //On récupères les actes de ces brouillons (sauf si supprimé)
        $brouillons = array();
        foreach ($brouillon_actions as $brouillon_action) {
            if (null !== $brouillon_action->getFkActe())
                $brouillons[] = $brouillon_action->getFkActe();
        }

        return $this->render('consultation/consultation.html.twig', [
            'historique' => $historique,
            'brouillons' => $brouillons
        ]);

    }
}
