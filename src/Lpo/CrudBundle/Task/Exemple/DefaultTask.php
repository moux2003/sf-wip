<?php

namespace Lpo\CrudBundle\Task\Exemple;

use Lpo\CrudBundle\Task\BaseTask;

class DefaultTask extends BaseTask
{

    public function getType()
    {
        return 'chambre';
    }
}
