<?php

namespace Lpo\CrudBundle\Orderer;

use Lpo\CrudBundle\Task\TaskInterface;

class Orderer
{
    protected $handlers;

    public function __construct($handlers = array())
    {
        $this->handlers = $handlers;
    }

    public function order(TaskInterface $task)
    {
        $handler = $this->findTaskHandler($task);
        $action  = $this->findTask($task);
        $handler->$action($task);
    }

    protected function findTaskHandler(TaskInterface $task)
    {
        return $this->handlers[$task->getType()];
    }

    protected function findTask(TaskInterface $task)
    {
        $parts = explode("\\", get_class($task));

        return strtolower(str_replace('Task', '', end($parts)));
    }
}
