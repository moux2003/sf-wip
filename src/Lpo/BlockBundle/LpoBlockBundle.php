<?php

namespace Lpo\BlockBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Lpo\BlockBundle\DependencyInjection\LpoBlockExtension;

class LpoBlockBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new LpoBlockExtension();
    }
}
