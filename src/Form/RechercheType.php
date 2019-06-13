<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\NatureActe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nature', EntityType::class, [
                'required' => false,
                'class' => NatureActe::class,
                'choice_label' => 'libelle'
            ])
            ->add('numero', TextType::class, [
                'required' => false
            ])
            ->add('motcles', TextType::class, [
                'required' => false
            ])
            ->add('matiere', EntityType::class, [
                'required' => false,
                'class' => Matiere::class,
                'choice_label' => 'libelle'
            ])
            ->add('dateDecisionDebut', DateType::class, [
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('dateDecisionFin', DateType::class, [
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
