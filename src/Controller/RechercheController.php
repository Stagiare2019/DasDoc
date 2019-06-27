<?php

namespace App\Controller;

use App\Entity\Acte;
use App\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/*

Ce controleur est en charge de la recherche d'actes (les filtres et la manières dont ils sont utilisés)

*/

class RechercheController extends AbstractController
{
    /**
     * @Route("/", name="recherche")
     * La fonction de recherche affiche le form, le traite et affiche les résultats.
     * Le filtrage des résultats se fait en 3 étapes à cause de la nature des données de filtrage.
     */
    public function recherche(Request $request)
    {
    	$form = $this->createForm(RechercheType::class);

    	$form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

        	// On récupère les informations du form
        	$data = $form->getData();

        	// Si les champs nature/numero/matière sont précisés, on ajoutes leurs données à $filtres pour la fonction findBy()
            // Ce sont les seuls du form qui peuvent être utilisés par findBy()
        	$filtres = array();
        	if(null !== $data['nature'])
        		$filtres['fkNature'] = $data['nature'];
        	if(null !== $data['numero'])
        		$filtres['numero'] = $data['numero'];
        	if(null !== $data['matiere'])
        		$filtres['fkMatiere'] = $data['matiere'];
        	
            // On filtre une première fois via findBy()
        	$resultatsFindBy = $this->getDoctrine()->getRepository(Acte::class)->findBy($filtres);

            // Si on a precisé au moins une date, on filtre une deuxième fois
            if (null !== $data['dateDecisionDebut'] || null !== $data['dateDecisionFin'])
            {
                $intervalleBas = (null !== $data['dateDecisionDebut']) ?
                    $data['dateDecisionDebut'] : new \DateTime('2000-01-01');

                $intervalleHaut = (null !== $data['dateDecisionFin']) ?
                    $data['dateDecisionFin'] : new \DateTime('now');
                $intervalleHaut->modify('+1 day');

                $resultatsDate = array();
                foreach ($resultatsFindBy as $resultat) {
                    $date = $resultat->getDateDecision();
                    if ($intervalleBas <= $date && $date <= $intervalleHaut)
                        $resultatsDate[] = $resultat;
                }
            }
            else
            {
                $resultatsDate = $resultatsFindBy;
            }

            // Si on a precisé au moins un motclé, on filtre une 3eme fois
            if (null !== $data['motcles'])
            {
                $libelleMotcles = explode(",",$data['motcles']);

                $resultatsMotcles = array();
                foreach ($resultatsDate as $resultat) {
                    foreach ($libelleMotcles as $libelle) {
                        if (in_array($libelle,$resultat->getMotcles()->toArray()))
                        $resultatsMotcles[] = $resultat;
                    }
                }
            }
            else
            {
                $resultatsMotcles = $resultatsDate;
            }

        	//On affiche de nouveau la page avec les résultats.
        	return $this->render('recherche/recherche.html.twig', [
        		'form' => $form->createView(),
        		'resultats' => $resultatsMotcles
        	]);
        }

        return $this->render('recherche/recherche.html.twig', [
        	'form' => $form->createView(),
        	'resultats' => null
        ]);
    }
}
