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

    /* Retourne un tableau des noms des pdf des annexes d'un Acte */
    public function getArrayNomPDFAnnexes(Acte $acte)
    {
        $pathsPieceJointes = array();
        foreach ($acte->getPieceJointes()->toArray() as $pj)
            $pathsPieceJointes[] = $pj->getNomPDF();

        return $pathsPieceJointes;
    }

    // FACTORIES (avec persistence des instances crées => pas de retour)

    /* Crée une instance de Action et la fait persister
        NB : L'auteur de l'action crée sera toujours l'utilisateur de la session
        NB : La correspondance id-TypeAction se fait dans "services.yaml" */
	public function creerAction(int $idTypeAction, Acte $acte)
    {
        $action = new Action();
        $action->setFkType($this->getTypeAction($idTypeAction));
        $action->setFkUtilisateur($this->security->getUser());
        $action->setFkActe($acte);
        $action->setDate(new \DateTime('now'));

        $this->manager->persist($action);
    }

    /* Crée une instance de PieceJointe et la fait persister */
    public function creerAnnexe(Acte $acte, String $nomPDF, String $objet)
    {
        $pj = new PieceJointe();
        $pj->setObjet($objet);
        $pj->setNomPDF($nomPDF);
        $pj->setFkActe($acte);
        $this->manager->persist($pj);
    }

    // GENERATORS

    /* Génère le nom du pdf d'une annexe via son numéro et son objet */
    public function generateNomPDFAnnexe(string $numero, string $objet)
    {
        return $numero." - Annexe - ".$objet.".pdf";
    }

    /* Génère le nom du pdf d'un acte via son numéro et son objet */
    public function generateNomPDF(string $numero, string $objet)
    {
        return $numero." - ".$objet.".pdf";
    }

    // MAJ DB (enlever les liens, en remettre de nouveaux, nettoyer si besoin)

    /* Retire les PieceJointes liés à l'acte et ajoute éventuellement les nouvelles PieceJointes */
    public function majAnnexes(Acte $acte, Form $form = null)
    {
        // Crée un tableau des nomsPDF vers les annexes à supprimer et un autre tableau vers les annexes à renommer

            $nomsPDFAvecObjetARenommer = array();
            $nomsPDFASupprimer = array();

            // Si suppression : toutes les annexes sont à supprimer et aucunes à renommer
            if (null === $form) {
                $nomsPDFASupprimer = $this->getArrayNomPDFAnnexes($acte);
            }
            else {
                for ($i=1; $i < 3; $i++) {
                    if ("true" === $form->get('hiddenSupprAnnexe'.$i)->getData()) {
                        $nomsPDFASupprimer[] = $form->get('hiddenPathAnnexe'.$i)->getData();
                    }
                    else if (null !== $form->get('hiddenPathAnnexe'.$i)->getData()) {
                        $nomsPDFAvecObjetARenommer[] = ["path" => $form->get('hiddenPathAnnexe'.$i)->getData(),
                                            "objet" => $form->get('objetAnnexe'.$i)->getData()];
                    }
                }
            }
                

        // Renomme les annexes à renommer

            foreach ($nomsPDFAvecObjetARenommer as $pjInfos) {
                
                $pj = $this->manager->getRepository(PieceJointe::class)->findOneByNomPDF($pjInfos["path"]);
                $newName = $this->generateNomPDFAnnexe($acte->getNumero(), $pjInfos["objet"]);
                $this->renommerFichier($this->pdfDirectory,$pjInfos['path'],$newName);
                $pj->setNomPDF($newName);
                $pj->setObjet($pjInfos["objet"]);
                $this->manager->persist($pj);
            }

        // Supprime les annexes à supprimer et ajoute d'enventuelles nouvelles annexes

            $this->supprimerAnnexes($nomsPDFASupprimer);
            if (null !== $form)
                $this->ajouterAnnexes($acte,$form);
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

    /* Supprime les PieceJointes du tableau de chemins donné */
    public function supprimerAnnexes(array $nomsPDFASupprimer)
    {
        foreach ($nomsPDFASupprimer as $nomPDF) {
            $this->manager->remove($this->manager->getRepository(PieceJointe::class)->findOneByNomPDF($nomPDF));
            $this->supprimerFichier($this->pdfDirectory,$nomPDF);
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
    public function ajouterAnnexes(Acte $acte, Form $form)
    {
        if (null !== ($annexe1 = $form->get('annexe1')->getData())) {
            $nomAnnexe1 = $this->generateNomPDFAnnexe($acte->getNumero(), $form->get('objetAnnexe1')->getData());
            $objetAnnexe1 = $form->get('objetAnnexe1')->getData();
            $this->creerAnnexe($acte, $nomAnnexe1, $objetAnnexe1);
            $this->uploaderFichier($annexe1, $this->pdfDirectory, $nomAnnexe1);
        }

        if (null !== $annexe2 = $form->get('annexe2')->getData()) {
            $nomAnnexe2 = $this->generateNomPDFAnnexe($acte->getNumero(), $form->get('objetAnnexe2')->getData());
            $objetAnnexe2 = $form->get('objetAnnexe2')->getData();
            $this->creerAnnexe($acte, $nomAnnexe2, $objetAnnexe2);
            $this->uploaderFichier($annexe2, $this->pdfDirectory, $nomAnnexe2);
        }
            
        if (null !== $annexe3 = $form->get('annexe3')->getData()) {
            $nomAnnexe3 = $this->generateNomPDFAnnexe($acte->getNumero(), $form->get('objetAnnexe3')->getData());
            $objetAnnexe3 = $form->get('objetAnnexe3')->getData();
            $this->creerAnnexe($acte, $nomAnnexe3, $objetAnnexe3);
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
        if ($ancienNom !== $nouveauNom)
            $this->fileSystem->rename($directory.$ancienNom, $directory.$nouveauNom);
    }

    /* Supprime un fichier dans un répertoire */
    public function supprimerFichier(string $directory, string $nom)
    {
        $this->fileSystem->remove($directory.$nom);
    }
}