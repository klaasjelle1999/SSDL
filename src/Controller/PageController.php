<?php

namespace App\Controller;

use App\Entity\Page;
use App\FormType\PageCreateFormType;
use App\FormType\PageUpdateFormType;
use App\Manager\PageManager;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/{page}/page", name="page")
     * @param Page $page
     * @ParamConverter("page", options={"mapping": {"page": "name"}})
     * @return Response
     */
    public function index(Page $page)
    {
        if (!$this->getUser()) {
            if ($page->getVisibleTo() === 'member' || $page->getVisibleTo() === 'admin') {
                return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
            }
        }

        $pages = $this->getDoctrine()->getRepository(Page::class)->findAll();

        return $this->render('page/index.html.twig', [
            'pages' => $pages,
            'page' => $page,
            'controller_name' => 'PageController',
        ]);
    }

    /**
     * @Route(path="/page/create", name="create_page")
     * @return Response
     */
    public function create(PageManager $pageManager, Request $request)
    {
        $pages = $this->getDoctrine()->getRepository(Page::class)->findAll();
        $createForm = $this->createForm(PageCreateFormType::class);
        $createForm->handleRequest($request);
        if ($createForm->isSubmitted() && $createForm->isValid()) {
            $data = $createForm->getData();
            $pageManager->createPage($data);
            $this->addFlash('success', 'De pagina is met success aangemaakt!');
            return $this->redirectToRoute('admin');
        }

        return $this->render('page/create.html.twig', [
            'pages' => $pages,
            'form' => $createForm->createView()
        ]);
    }

    /**
     * @Route(path="/page/delete/{page}", name="delete_page")
     * @param Page $page
     */
    public function deletePage(Page $page, PageManager $pageManager)
    {
        $pageManager->deletePage($page);
        $this->addFlash('danger', 'De pagina is verwijderd');

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route(path="/page/update/{page}", name="update_page")
     * @param Page $page
     * @param PageManager $pageManager
     * @return Response
     */
    public function updatePage(Page $page, PageManager $pageManager, Request $request)
    {
        $pages = $this->getDoctrine()->getRepository(Page::class)->findAll();
        $form = $this->createForm(PageUpdateFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pageManager->updatePage($page, $data);
            $this->addFlash('success', 'De pagina is met success bewerkt!');
            return $this->redirectToRoute('admin');
        }
        return $this->render('page/update.html.twig', [
            'form' => $form->createView(),
            'pages' => $pages,
            'page' => $page,
        ]);
    }
}
