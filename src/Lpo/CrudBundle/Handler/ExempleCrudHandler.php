<?php

namespace Lpo\CrudBundle\Handler;

use Lpo\InfraBundle\Manager\DefaultManager;
use Lpo\CrudBundle\Task\TaskInterface;
use Lpo\Domain\Exemple;

class ExempleCrudHandler implements HandlerCrudInterface
{
    protected $manager;
    private static $classPath = 'Lpo\InfraBundle\Entity\Exemple';

    public function __construct(DefaultManager $manager)
    {
        $this->manager = $manager;
    }

    public function create(TaskInterface $task)
    {
        $exemple = new self::$classPath($task->nom);
        $exemple->setDescription($task->description);

        $this->manager->save($exemple);
    }

    public function update(TaskInterface $task)
    {
        $repo = $this->manager->getRepository(self::$classPath);

        $exemple = $repo->find($task->id)
            ->changeNom($task->nom)
            ->setDescription($task->description);

        $this->manager->save($exemple);
    }

    public function delete(TaskInterface $task)
    {
        $repo = $this->manager->getRepository(self::$classPath);

        $exemple = $repo->find($task->id);

        $this->manager->delete($exemple);
    }
}
