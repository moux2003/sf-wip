<?php

namespace Lpo\InfraBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;

class DefaultManager
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRepository($class)
    {
        return $this->em->getRepository($class);
    }

    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }
}
