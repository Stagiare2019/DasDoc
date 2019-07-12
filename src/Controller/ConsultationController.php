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
     * 
     */
    public function consultation(UserInterface $user)
    {
        
    // REMPLISSAGE DU TABLEAU BROUILLON

        //On récupère les actions qui sont des créations de brouillons par l'utilisateur
    	$brouillon_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_typeAction_enregistrerBrouillon')),
            'fkUtilisateur' => $user
        ));

        //On récupères les actes de ces brouillons qui ont un etat brouillon

        $brouillons=array();
       
       
        foreach ($brouillon_actions as  $brouillon_action) {
            if ($brouillon_action->getFkActe()->getFkEtat()=='Brouillon')
                $brouillons[] = array(
                    'acte'=>$brouillon_action->getFkActe(),
                    'date'=>$brouillon_action->getDate());
        }

        

    // REMPLISSAGE DU TABLEAU ACTES E ATTENTE DE VALIDATION
        //partie1: reservée à l'agent
        $envoyerVal_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_typeAction_envoyerValidation')),
            'fkUtilisateur' => $user
        ));

         //On récupères les actes envoyés en validations qui ont un etat en attente de validation

        $attenteVals=array();

        foreach ($envoyerVal_actions as  $envoyerVal_action ) {
            if ($envoyerVal_action->getFkActe()->getFkEtat()=='En attente de validation')
                $attenteVals[] = array(
                    'acte'=>$envoyerVal_action->getFkActe(),
                    'date'=>$envoyerVal_action->getDate());
        }

        //partie2: reservée au valideur
       
        $attVal_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_typeAction_envoyerValidation'))
        ));

        $attVals=array();

        foreach ($attVal_actions as  $attVal_action ) {
            if ($attVal_action->getFkActe()->getFkEtat()=='En attente de validation')
                $attVals[] = array(
                    'acte'=>$attVal_action->getFkActe(),
                    'date'=>$attVal_action->getDate());
        }
    // REMPLISSAGE DU TABLEAU ACTES VISES
        //on recupère les actes envoyés en validations qui ont un etat validé ou refusé

        $vises=array();
        foreach ($envoyerVal_actions as  $envoyerVal_action ) {
            if (($envoyerVal_action->getFkActe()->getFkEtat()=='Validé')|| ($envoyerVal_action->getFkActe()->getFkEtat()=='Refusé'))
                    $vises[] = array(
                    'acte'=>$envoyerVal_action->getFkActe(),
                    'date'=>$envoyerVal_action->getDate(),
                    'etat'=>$envoyerVal_action->getFkActe()->getFkEtat()
                );
        }


    //REMPLISSAGE DU TABLEAU ACTES A TRANSMETTRE

        $envoyerTrans_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_typeAction_valider'))
            
        ));

        //On récupère les actes validés qui ont encore un etat validé

        $Atransmettres=array();

        foreach ($envoyerTrans_actions as  $envoyerTrans_action ) {
            if ($envoyerTrans_action->getFkActe()->getFkEtat()=='Validé')
            $Atransmettres[] = array(
                    'acte'=> $envoyerTrans_action->getFkActe(),
                    'date'=> $envoyerTrans_action->getDate());
        }

    //REMPLISSAGE DU TABLEAU ACTES A ARCHIVES

        //on part de l'action et de on envoie à archiver ceux datant de + de 30j
        //etape1 je recupere les brouillons à archiver

        $BrouillonAachives=array();
        foreach( $brouillon_actions as $brouillon_action)
        {
            //j'envoie à archiver ceux avec date > 30j
            $delai=(int) $brouillon_action->getDate()->diff( new \DateTime('now'))->format('%a');
            if(( $brouillon_action->getFkActe()->getFkEtat()=='Brouillon') && ($delai >= 30))
            {
                $BrouillonAachives[]=array(
                    'acte'=> $brouillon_action->getFkActe(),
                    'date'=>  $brouillon_action->getDate(),
                    'etat'=>$brouillon_action->getFkActe()->getFkEtat()) ;
            }
        }

        //etape2 je recupere les actes validés à archiver
        

        $ValidesAachives=array();
        foreach(   $envoyerTrans_action as   $envoyerTrans_action)
        {
            //j'envoie à archiver ceux avec date > 30j
            $delai=(int)  $envoyerTrans_action->getDate()->diff( new \DateTime('now'))->format('%a');
            if(( $envoyerTrans_action->getFkActe()->getFkEtat()=='Validé') && ($delai >= 30))
            {
                $ValidesAachives[]=array(
                    'acte'=> $envoyerTrans_action->getFkActe(),
                    'date'=> $envoyerTrans_action->getDate(),
                    'etat'=> $envoyerTrans_action->getFkActe()->getFkEtat());
            }
        }

        // etape3 je recupere les actes refusé

        $RefusesAachives_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_typeAction_refuser'))
        
        ));

        $RefusesAachives=array();
        foreach(  $RefusesAachives_actions as  $RefusesAachives_action)
        {
            if( $RefusesAachives_action->getFkActe()->getFkEtat()=='Refusé')
            {
                $RefusesAachives[]=array(
                    'acte'=>  $RefusesAachives_action->getFkActe(),
                    'date'=>   $RefusesAachives_action->getDate(),
                    'etat'=>  $RefusesAachives_action->getFkActe()->getFkEtat()
                    ) ;
            }
        }
        
        //etape4 je recupere les actes transmis

        
        $TransmisAachives_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_typeAction_transmettre'))
        
        ));

        $TransmisAachives=array();
        foreach( $TransmisAachives_actions  as  $TransmisAachives_action )
        {
            if($TransmisAachives_action->getFkActe()->getFkEtat()=='Transmis')
            {
                $TransmisAachives[]=array(
                    'acte'=> $TransmisAachives_action->getFkActe(),
                    'date'=>   $TransmisAachives_action->getDate(),
                    'etat'=>  $TransmisAachives_action->getFkActe()->getFkEtat()
                    
                    );
            }
        }

        //etape5 je recupere les actes annulés
        $AnnulesAachives_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(array(
            'fkType' => $this->getDoctrine()->getRepository(TypeAction::class)->findById($this->getParameter('id_typeAction_annuler'))
        
        ));

        $AnnulesAachives=array();
        foreach(  $AnnulesAachives_actions   as  $AnnulesAachives_action  )
        {
            if( $AnnulesAachives_action->getFkActe()->getFkEtat()=='Annulé')
            {
                $AnnulesAachives[]=array(
                    'acte'=>  $AnnulesAachives_action->getFkActe(),
                    'date'=> $AnnulesAachives_action->getDate(),
                    'etat'=>  $AnnulesAachives_action->getFkActe()->getFkEtat()
                    );
            }
        }

    

    //REMPLISSAGE DU TABLEAU HISTORIQUE DES ACTIONS
        
        //On récupère les actions de l'utilisateur
        $historique_actions = $this->getDoctrine()->getRepository(Action::class)->findBy(
            array('fkUtilisateur' => $user)
        );
        //on recupere les actes correspondant à ces actions
        $historiques=array();
        foreach ($historique_actions as  $historique_action ) {
                    $historiques[] = array(
                    'acte'=>$historique_action->getFkActe(),
                    'date'=>$historique_action->getDate(),
                    'type'=>$historique_action->getFkType()
                    
                );
        }

    
        return $this->render('consultation/consultation.html.twig', [
            'brouillons' => $brouillons,
            'attenteVals'=>$attenteVals,
            'attVals'=> $attVals,
            'vises'=>$vises,
            'historiques'=>$historiques,
            'Atransmettres'=>$Atransmettres,
            'BrouillonAachives'=> $BrouillonAachives,
            'ValidesAachives'=>  $ValidesAachives,
            'RefusesAachives'=> $RefusesAachives,
            'TransmisAachives'=>$TransmisAachives,
            'AnnulesAachives'=> $AnnulesAachives


           

        
        ]);

    }

    
    
}
