<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

/*

Ce controleur est en charge de l'accès à la plateforme (sécurisée par des utilisateurs compte/mdp)

*/
class SecurityController extends AbstractController
{
    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    } 
    /**
     * Affiche et traite un formulaire de login
     *
     * @Route("/", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user=new Utilisateur();
        // Création d'un formulaire vide non-lié à une instance
        $form = $this->createFormBuilder($user)
                    ->add('email')
                    ->add('password', PasswordType::class)
                    ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {

                        if ($this->requestStack->getCurrentRequest()->attributes->get('_route') === 'security_register')
                            $event->getForm()->add('confirm_password', PasswordType::class);})
                    ->getForm();
            

        // Affichage du formulaire
        return $this->render('security/login.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * Déconnecte l'utilisateur courant. Cette fonction est vide car elle n'a "pas le temps" d'être atteinte :
     * Le firewall intercepte la requête -> déconnecte l'utilisateur courant -> redirige sur l'ancienne page.
     *
     * Dans notre cas, l'utilisateur est automatiquement redirigé vers la page de login après déconnexion.
     *
     * @Route("/logout", name="security_logout")
     */
    public function logout() {}



    /**
     * @Route("/register", name="security_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // Création d'un formulaire lié à une instance vide
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);

        // Traitement du formulaire (test validité, complétion de l'objet Utilisateur, m-à-j DB, redirection)
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            // Complétion : Hashage du mdp de l'utilisateur
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            // Log automatiquement sur la session de l'utilisateur crée
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('admin_dashboard');
        }

        // Affichage du formulaire
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
