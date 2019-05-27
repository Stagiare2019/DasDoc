<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    private $requestStack;

    //utilisation du service requestStack pour savoir si la route est 'security_register'
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    } 

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email')
        ->add('password', PasswordType::class)
        ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {

            if ($this->requestStack->getCurrentRequest()->attributes->get('_route') === 'security_register')
                $event->getForm()->add('confirm_password', PasswordType::class);

        })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class
        ]);
    }
}
