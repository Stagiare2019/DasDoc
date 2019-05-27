<?php

namespace App\Controller;

use App\Entity\Acte;
use App\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    /**
     * @Route("/", name="recherche")
     */
    public function recherche(Request $request)
    {
    	$form = $this->createForm(RechercheType::class);


    	$form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

        	//On récupère les informations du form
        	$data = $form->getData();

        	//On ajoutes des filtres à la recherche de la fonction findBy() en fonction du form
        	$filtres = array();
        	if(null !== $data['nature'])
        		$filtres['fkNature'] = $data['nature'];
        	if(null !== $data['numero'])
        		$filtres['numero'] = $data['numero'];
        	if(null !== $data['matiere'])
        		$filtres['fkMatiere'] = $data['matiere'];
        	
        	$resultats = $this->getDoctrine()->getRepository(Acte::class)->findBy($filtres);

        	//On affiche de nouveau la page avec les résultats.
        	return $this->render('recherche/recherche.html.twig', [
        		'form' => $form->createView(),
        		'resultats' => $resultats
        	]);
        }

        return $this->render('recherche/recherche.html.twig', [
        	'form' => $form->createView(),
        	'resultats' => null
        ]);
    }
}
