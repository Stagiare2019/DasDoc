<?php

namespace App\Form;

use App\Entity\Acte;
use App\Entity\Matiere;
use App\Entity\NatureActe;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $acte = $event->getData();
                $form = $event->getForm();

                // Vérifie si l'acte est nouveau (si la var $acte est nulle) => champs file
                if (!$acte || null === $acte->getId()) {
                    $form->add('file', FileType::class);
                }
                //sinon c'est qu'on le modifie => champs nomPDF
                else {
                    $form->add('nomPDF', TextType::class);
                }
            })

            //options
            ->add('pieceJointes', CollectionType::class, [
                'required' => false,
                'entry_type' => FileType::class,
                'entry_options' => [
                    'attr' => ['label' => "Pièce jointe"],
                ]
            ])
            ->add('fkService', EntityType::class, [
                'required' => false,
                'class' => Service::class,
                'choice_label' => 'libelle'
            ])
            ->add('motcles', CollectionType::class, [
                'required' => false,
                'entry_type' => TextType::class
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
