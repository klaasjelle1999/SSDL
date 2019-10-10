<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/beheer", name="admin")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        } else {
            $users = $this->em->getRepository(User::class)->findAll();
            $userPagination = $paginator->paginate($users, $request->query->getInt('page', 1), 10);

            $pages = $this->em->getRepository(Page::class)->findAll();
            $pagePagination = $paginator->paginate($pages, $request->query->getInt('page', 1, 10));

            return $this->render('admin/index.html.twig', [
                'pages' => $pages,
                'userPagination' => $userPagination,
                'pagePagination' => $pagePagination,
                'controller_name' => 'AdminController',
            ]);
        }
    }
}
