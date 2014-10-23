<?php

namespace Lpo\InfraBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Lpo\InfraBundle\DependencyInjection\LpoInfraExtension;

class LpoInfraBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new LpoInfraExtension();
    }
}
