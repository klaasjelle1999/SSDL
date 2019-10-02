<?php


namespace App\Manager;


use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;

class PageManager
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createPage($data)
    {
        $page = new Page();

        $page
            ->setName($data->name)
            ->setVisibleTo($data->visibleTo)
            ->setCreatedAt(new \DateTime('now'))
            ->setText($data->text)
        ;

        $this->em->persist($page);
        $this->em->flush();
    }

    public function deletePage(Page $page)
    {
        $this->em->remove($page);
        $this->em->flush();
    }

    public function updatePage(Page $page, $data)
    {
        $page
            ->setText($data->text)
            ->setVisibleTo($data->visibleTo)
            ->setName($data->name)
        ;

        $this->em->merge($page);
        $this->em->flush();
    }
}