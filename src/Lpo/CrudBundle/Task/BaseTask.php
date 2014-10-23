<?php

namespace Lpo\CrudBundle\Task;

abstract class BaseTask implements TaskInterface
{
    public function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                $parts = explode("\\", get_class($this));
                $task = end($parts);

                throw new \RuntimeException(sprintf('Property "%s" is not a valid property for task "%s".', $key, $task));
            }

            $this->$key = $value;
        }
    }
}
