<?php

namespace App\Service;

use App\Entity\Acte;
use App\Entity\Action;
use App\Entity\EtatActe;
use App\Entity\FamilleMatiere;
use App\Entity\Motcle;
use App\Entity\PieceJointe;
use App\Entity\TypeAction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Security;

/*

Ce service contient beaucoup de fonctions très courte qui vise uniquement à faciliter la lecture du code dans le controller.
ex : "$this->manager->getRepository(EtatActe::class)->findOneById(3);" devient "getEtatActe(3)"

*/

class GestionnaireActeHelper {

    private $manager;
    private $pdfDirectory;
    private $security;
    private $fileSystem;

    public function __construct($pdfDirectory, EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->pdfDirectory = $pdfDirectory;
        $this->security = $security;
        $this->fileSystem = new Filesystem();
    }

    // GETTERS

    /* Recherche et retourne une instance EtatActe selon son id */
    public function getEtatActe(int $id): EtatActe
    {
        return $this->manager->getRepository(EtatActe::class)->findOneById($id);
    }


    /* Recherche et retourne une instance TypeAction selon son id */
    public function getTypeAction(int $id): TypeAction
    {
        return $this->manager->getRepository(TypeAction::class)->findOneById($id);
    }

    /* Retourne une concaténation des libelles des Motcles lié à un acte
        Ex de retour : "Stade,Flobert ROMANO,Tournoi de tennis" */
    public function getStringConcatenatedMotcles(Acte $acte): string
    {
        $libellesMotcles;
        foreach ($acte->getMotcles()->toArray() as $motcle)
            $libellesMotcles[] = $motcle->getLibelle();

        return implode(",",$acte->getMotcles()->toArray());
    }

    // FACTORY (avec persistence des instances crées => pas de retour)

    /* Crée une instance de Action et la fait persister
        NB : L'auteur de l'action crée sera toujours l'utilisateur de la session
        NB : La correspondance id-TypeAction se fait dans "services.yaml" */
	public function creerAction(int $idTypeAction, Acte $acte = null)
    {
        $action = new Action();
        $action->setFkType($this->getTypeAction($idTypeAction));
        $action->setFkUtilisateur($this->security->getUser());
        $action->setFkActe($acte);
        $action->setDate(new \DateTime('now'));

        $this->manager->persist($action);
    }

    /* Crée une instance de PieceJointe et la fait persister */
    public function creerPieceJointe(Acte $acte, String $nomPDF, String $objet)
    {
        $pj = new PieceJointe();
        $pj->setObjet($objet);
        $pj->setNomPDF($nomPDF);
        $pj->setFkActe($acte);
        $this->manager->persist($pj);
    }

    // MAJ DB (enlever les liens, en remettre de nouveaux, nettoyer si besoin)

    /* Retire les PieceJointes liés à l'acte et ajoute éventuellement les nouvelles PieceJointes */
    public function majPieceJointes(Acte $acte, Form $form = null)
    {
        $this->supprimerPieceJointes($acte);
        if (null !== $form)
            $this->ajouterPieceJointes($acte,$form);
    }

    /* Délie les Motcles liés à un Acte et ajoute éventuellement les nouveaux Motcle puis vérifie et enlève les motclé lié à aucun Acte */
    public function majMotcles(Acte $acte, Form $form = null)
    {
        $acteMotcles = $acte->getMotcles()->toArray();

        $this->delierMotclesActe($acteMotcles, $acte);
        if (null !== $form) {
            $libelleMotcles = explode(',',$form->get('motcles')->getData());
            $this->lierMotclesActe($libelleMotcles, $acte);
        }
        $this->nettoyerMotcles($acteMotcles);
    }

    // RETIRER LIENS

    /* Supprime les PieceJointes lié à un Acte */
    public function supprimerPieceJointes(Acte $acte)
    {
        $pieceJointes = $acte->getPieceJointes()->toArray();
        foreach ($pieceJointes as $pj) {
            $this->manager->remove($pj);
            $this->supprimerFichier($this->pdfDirectory,$pj->getNomPDF());
        }
    }

    /* Retire le lien entre des Motcles et un Acte */
    private function delierMotclesActe(array $motcles, Acte $acte)
    {
        foreach ($motcles as $motcle) {
            $acte->removeMotcle($motcle);
            $motcle->removeActe($acte);

            $this->manager->persist($motcle);
            $this->manager->persist($acte);
        }
    }

    // CREER LIENS

    /* Vérifie les données du formulaire : Si annexe fournie -> ajoute une PieceJointe à la DB et upload sur le disque */
    public function ajouterPieceJointes(Acte $acte, Form $form)
    {
        if (null !== $annexe1 = $form->get('annexe1')->getData()) {
            $nomAnnexe1 = $acte->getNumero()." - Annexe - ".$form->get('objetAnnexe1')->getData().".pdf";
            $this->creerPieceJointe($acte, $nomAnnexe1, $form->get('objetAnnexe1')->getData());
            $this->uploaderFichier($annexe1, $this->pdfDirectory, $nomAnnexe1);
        }

        if (null !== $annexe2 = $form->get('annexe2')->getData()) {
            $nomAnnexe2 = $acte->getNumero()." - Annexe - ".$form->get('objetAnnexe2')->getData().".pdf";
            $this->creerPieceJointe($acte, $nomAnnexe2, $form->get('objetAnnexe2')->getData());
            $this->uploaderFichier($annexe2, $this->pdfDirectory, $nomAnnexe2);
        }
            
        if (null !== $annexe3 = $form->get('annexe3')->getData()) {
            $nomAnnexe3 = $acte->getNumero()." - Annexe - ".$form->get('objetAnnexe3')->getData().".pdf";
            $this->creerPieceJointe($acte, $nomAnnexe3, $form->get('objetAnnexe3')->getData());
            $this->uploaderFichier($annexe3, $this->pdfDirectory, $nomAnnexe3);
        }
    }

    /* Crée des motcles (s'ils n'existent pas) et les lie à l'acte */
    private function lierMotclesActe(array $libelleMotcles, Acte $acte)
    {
        foreach ($libelleMotcles as $libelle) {

            if ("" === $libelle)
                break;

            // Si le motcle n'existe pas => crée nouveau
            if (null === $motcle = $this->manager->getRepository(Motcle::class)->findOneByLibelle($libelle)) {
                $motcle = new Motcle();
                $motcle->setLibelle($libelle);
            }

            // On ajoute le motclé à l'acte et vice-versa
            $motcle->addActe($acte);
            $acte->addMotcle($motcle);

            // On enregistre les changements
            $this->manager->persist($motcle);
            $this->manager->persist($acte);
            
        }
    }

    // NETTOYER

    /* Vérifie si les motcles sont liés à au moins un acte : si non => suppression */
    private function nettoyerMotcles(array $motcles)
    {
        foreach ($motcles as $motcle) {
            // Si un motcle n'a pas d'actes
            if ([] === $motcle->getActes()->toArray())
                $this->manager->remove($motcle);
        }
    }

    // MANIPULATION FICHIER

    /* Upload un fichier dans un répertoire */
    public function uploaderFichier(File $file, string $directory, string $nomFichier)
    {
    	/** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file->move($directory,$nomFichier);
    }

    /* Renomme un fichier dans un répertoire */
    public function renommerFichier(string $directory, string $ancienNom, string $nouveauNom)
    {
        $this->fileSystem->rename($directory.$ancienNom, $directory.$nouveauNom);
    }

    /* Supprime un fichier dans un répertoire */
    public function supprimerFichier(string $directory, string $nom)
    {
        $this->fileSystem->remove($directory.$nom);
    }
}