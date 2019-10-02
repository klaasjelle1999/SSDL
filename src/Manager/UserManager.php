<?php


namespace App\Manager;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createUser($data, $password, User $user)
    {
        $user
            ->setEmail($data->email)
            ->setPassword($password)
            ->setName($data->name)
            ->setGender($data->gender)
            ->setCreatedAt(new \DateTime('now'))
        ;
        if ($data->roles === "ROLE_ADMIN") {
            $user->setRoles(["ROLE_ADMIN"]);
        } else {
            $user->setRoles(["ROLE_USER"]);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    public function deleteUser(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function updateUser($data, User $user)
    {
        $user
            ->setName($data->name)
            ->setGender($data->gender)
            ->setEmail($data->email)
        ;
        if ($data->roles === "ROLE_ADMIN") {
            $user->setRoles(["ROLE_ADMIN"]);
        } else {
            $user->setRoles(["ROLE_USER"]);
        }

        $this->em->merge($user);
        $this->em->flush();
    }
}