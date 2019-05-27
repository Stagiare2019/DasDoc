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

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Crée un Utilisateur inutile qui ne sert qu'à la création du form
        $form = $this->createForm(UtilisateurType::class, new Utilisateur());

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }



    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
         #Fonction vide qui n'a "pas le temps" d'être atteinte (le firewall l'intercepte avant et redirige)
    }



    /**
     * @Route("/register", name="security_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        //création du form lié à une var $user vide
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);

        //teste si le form a été rempli, puis encode le password de $user et le migre vers la DB
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            //login automatique après registration
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('admin_lister', [
                'entity' => $entity
            ]);
        }

        //else : afficher le form de registration
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
