<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\User;
use App\FormData\UserUpdateFormData;
use App\FormType\UserCreateFormType;
use App\FormType\UserUpdateFormType;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $manager;

    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/user/create", name="create_user")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function create(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $pages = $this->getDoctrine()->getRepository(Page::class)->findAll();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $userForm = $this->createForm(UserCreateFormType::class);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $data = $userForm->getData();
            foreach ($users as $user) {
                if ($data->email === $user->getEmail()) {
                    $this->addFlash('danger', 'Er bestaat al een gebruiker met deze email');
                    return $this->redirectToRoute('admin');
                }
            }
            $user = new User;
            $password = $encoder->encodePassword($user, $data->password);
            $this->manager->createUser($data, $password, $user);

            $this->addFlash('success', 'De gebruiker is met success aangemaakt!');
            return $this->redirectToRoute('admin');
        }

        return $this->render('user/index.html.twig', [
            'pages' => $pages,
            'userForm' => $userForm->createView(),
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @param User $user
     * @Route(path="/user/delete/{user}", name="delete_user")
     * @return Response
     */
    public function delete(User $user)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($user === $this->getUser()) {
                $this->addFlash('danger', 'U kan uzelf niet verwijderen');
                return $this->redirectToRoute('admin');
            }
            $this->manager->deleteUser($user);
            $this->addFlash('danger', 'De gebruiker is met success verwijderd!');
            return $this->redirectToRoute('admin');
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }

    /**
     * @Route(path="/user/update/{user}", name="update_user")
     * @param User $user
     * @param Request $request
     * @return Response
     * @ParamConverter("user", options={"mapping": {"user": "email"}})
     */
    public function update(User $user, Request $request)
    {
        $pages = $this->getDoctrine()->getRepository(Page::class)->findAll();
        if ($this->getUser() === $user || $this->isGranted('ROLE_ADMIN')) {
            $updateForm = $this->createForm(UserUpdateFormType::class);
            $updateForm->handleRequest($request);
            if ($updateForm->isSubmitted() && $updateForm->isValid()) {
                $data = $updateForm->getData();
                $this->manager->updateUser($data, $user);
                $this->addFlash('success', 'De gebruiker is met success bewerkt');

                return $this->redirectToRoute('admin');
            }

            return $this->render('user/update.html.twig', [
                'pages' => $pages,
                'updateForm' => $updateForm->createView(),
                'user' => $user,
            ]);
        }
    }
}
