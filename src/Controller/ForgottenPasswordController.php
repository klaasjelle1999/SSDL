<?php

namespace App\Controller;

use App\Entity\ForgottenPassword;
use App\Entity\Page;
use App\Entity\User;
use App\FormType\ForgottenPasswordFormType;
use phpDocumentor\Reflection\Types\This;
use App\FormType\RecoverPasswordFormType;
use App\Manager\ForgottenPasswordManager;
use App\Repository\ForgottenPasswordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ForgottenPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @Route("/password-forget", name="forgotten_password")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, Swift_Mailer $mailer)
    {
        $pages = $this->getDoctrine()->getRepository(Page::class)->findAll();
        $form = $this->createForm(ForgottenPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getDoctrine()->getRepository(User::class)->findByEmail($data->email);
            if ($user === null) {
                $this->addFlash('danger', 'This user has not been found!');
                return $this->redirectToRoute('forgotten_password');
            } else {
                $forgottenPassword = new ForgottenPassword();
                $forgottenPassword
                    ->setUser($user)
                    ->setToken($this->generateRandomString())
                ;
                $this->em->persist($forgottenPassword);
                $this->em->flush();

                $message = (new Swift_Message('Password recovery'))
                    ->setFrom('recovery@ssdl.nl')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'email/forgot_password.html.twig', [
                                'name' => $user->getName(),
                                'token' => $forgottenPassword->getToken(),
                            ]
                        ),
                        'text/html'
                    );
                $mailer->send($message);
                $this->addFlash('success', 'There will be an email send to u if your email exists in our database.');
                return $this->redirectToRoute('forgotten_password');
            }
        }

        return $this->render('forgotten_password/index.html.twig', [
            'controller_name' => 'ForgottenPasswordController',
            'pages' => $pages,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/recover-password/{token}", name="recover")
     * @param string $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function recover(string $token, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findByRecoverToken($token);
        $forgottenPassword = $this->getDoctrine()->getRepository(ForgottenPassword::class)->findByToken($token);

        $form = $this->createForm(RecoverPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $encoder->encodePassword($user, $data->password);
            $user->setPassword($password);
            $this->em->merge($user);
            $this->em->remove($forgottenPassword);
            $this->em->flush();
            $this->addFlash('success', 'Your password has been updated, you can now log in.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('forgotten_password/recover_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
