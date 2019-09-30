<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExtraInformationController extends AbstractController
{
    /**
     * @Route("/extra", name="extra_information")
     */
    public function index()
    {
        if ($this->getUser()) {
            return $this->render('extra_information/index.html.twig', [
                'controller_name' => 'ExtraInformationController',
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error403.html.twig');
        }
    }
}
