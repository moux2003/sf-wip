<?php

namespace Lpo\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Lpo\SecurityBundle\DependencyInjection\LpoSecurityExtension;

class LpoSecurityBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new LpoSecurityExtension();
    }
}
