<?php

namespace App\Service;

use App\Entity\Acte;
use App\Entity\Action;
use App\Entity\EtatActe;
use App\Entity\FamilleMatiere;
use App\Entity\Motcle;
use App\Entity\TypeAction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Security;

class GestionnaireActeHelper {

	private $manager;
	private $security;

	public function __construct(EntityManagerInterface $manager, Security $security)
	{
		$this->manager = $manager;
		$this->security = $security;
	}



	// l'action crée sera toujours faites par l'utilisateur de la session (on ne crée pas d'action pr qq d'autre)
	public function creerAction(int $typeAction, Acte $acte = null)
    {
        $action = new Action();
        $action->setFkType($this->getTypeAction($typeAction));
        $action->setFkUtilisateur($this->security->getUser());
        $action->setFkActe($acte);
        $action->setDate(new \DateTime('now'));

        $this->manager->persist($action);
    }



    public function ajouterMotcles(Acte $acte, array $libelleMotcles)
    {
        $this->lierMotclesActe($libelleMotcles, $acte);
    }



    public function majMotcles(Acte $acte, array $libelleMotcles)
    {
        $oldMotcles = $acte->getMotcles()->toArray();

        $this->delierMotclesActe($oldMotcles, $acte);
        $this->lierMotclesActe($libelleMotcles, $acte);
        $this->nettoyerMotcles($oldMotcles);
    }



    public function supprimerMotcles(Acte $acte)
    {
        $motcles = $acte->getMotCles()->toArray();

        $this->delierMotclesActe($motcles, $acte);
        $this->nettoyerMotcles($motcles);
    }



    // Permet d'uploader le fichier sur le disque en lui donnant un répertoire, un fichier et un nom de fichier.
    public function uploaderFichierSurDisque(string $directory, File $file, string $nomPDF)
    {
    	/** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file->move($directory, $nomPDF);
    }



    public function renommerFichierSurDisque(string $directory, string $ancienNom, string $nouveauNom)
    {
    	if( $nouveauNom !== $ancienNom ) {
            $fs = new Filesystem();
            $fs->rename($directory.$ancienNom, $directory.$nouveauNom);
        }
    }



    public function supprimerFichierSurDisque(string $directory, string $nom)
    {
        $fs = new Filesystem();
        $fs->remove($directory.$nom);
    }



    public function getEtat(int $id): EtatActe
    {
    	return $this->manager->getRepository(EtatActe::class)->findOneById($id);
    }



    public function getTypeAction(int $id): TypeAction
    {
    	return $this->manager->getRepository(TypeAction::class)->findOneById($id);
    }



    public function getMotclesString(Acte $acte): string
    {
        $libellesMotcles;
        foreach ($acte->getMotcles()->toArray() as $motcle) {
            $libellesMotcles[] = $motcle->getLibelle();
        }

        return implode(",",$acte->getMotcles()->toArray());
    }



    /*
     * Créer des motcles (s'ils n'existent pas) et les lie à l'acte
     */
    private function lierMotclesActe(array $libelleMotcles, Acte $acte)
    {
        // Si le 1er élément de $libelleMotcles est une chaîne vide (la faute à explode() quand on lui donne une chaîne est vide)
        if ("" !== $libelleMotcles[0]) {
            foreach ($libelleMotcles as $libelle) {

                if (null === $this->manager->getRepository(Motcle::class)->findOneByLibelle($libelle)) {
                    $motcle = new Motcle();
                    $motcle->setLibelle($libelle);
                }
                else {
                    $motcle = $this->manager->getRepository(Motcle::class)->findOneByLibelle($libelle);
                }

                // On ajoute le motclé à l'acte et vice-versa
                $motcle->addActe($acte);
                $acte->addMotcle($motcle);

                // On enregistre les changements
                $this->manager->persist($motcle);
                $this->manager->persist($acte);
            }
        }
    }



    private function delierMotclesActe(array $motcles, Acte $acte)
    {
        foreach ($motcles as $motcle) {
            $acte->removeMotcle($motcle);
            $motcle->removeActe($acte);

            $this->manager->persist($motcle);
            $this->manager->persist($acte);
        }
    }



    private function nettoyerMotcles(array $motcles)
    {
        foreach ($motcles as $motcle) {
            if ([] === $motcle->getActes()->toArray())
                $this->manager->remove($motcle);
        }
    }
}