<?php

namespace App\Form;

use App\Entity\Acte;
use App\Entity\Matiere;
use App\Entity\NatureActe;
use App\Entity\PieceJointe;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            // CHAMPS OBLIGATOIRES : (fichier à uploader -> si creation), nature, objet, matière, dateDécision, numero, nomPDF

            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {

                // Si l'acte est nouveau (var $acte est nulle) => on ajoute le champs 'file'
                $acte = $event->getData();
                if (!$acte || null === $acte->getId())
                    $event->getForm()->add('file', FileType::class);
    
            })
            ->add('fkNature', EntityType::class, [
                'class' => NatureActe::class,
                'choice_label' => 'libelle'
            ])
            ->add('objet', TextType::class)
            ->add('fkMatiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'libelle'
            ])
            ->add('dateDecision', DateType::class, [
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('numero', TextType::class)

            // CHAMPS OPTIONNELS
            
            ->add('annexe1', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('objetAnnexe1', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('hiddenPathAnnexe1', HiddenType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('hiddenSupprAnnexe1', HiddenType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('annexe2', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('objetAnnexe2', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('hiddenPathAnnexe2', HiddenType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('hiddenSupprAnnexe2', HiddenType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('annexe3', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('objetAnnexe3', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('hiddenPathAnnexe3', HiddenType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('hiddenSupprAnnexe3', HiddenType::class, [
                'mapped' => false,
                'required' => false
            ])

            ->add('fkService', EntityType::class, [
                'required' => false,
                'class' => Service::class,
                'choice_label' => 'libelle'
            ])
            ->add('motcles', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('dateEffectiviteDebut', DateType::class, [
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('dateEffectiviteFin', DateType::class, [
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Acte::class,
        ]);
    }
}
