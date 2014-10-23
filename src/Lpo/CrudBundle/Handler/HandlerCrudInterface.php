<?php

namespace Lpo\CrudBundle\Handler;

use Lpo\CrudBundle\Task\TaskInterface;

interface HandlerCrudInterface
{

    public function create(TaskInterface $task);

    public function update(TaskInterface $task);

    public function delete(TaskInterface $task);
}
