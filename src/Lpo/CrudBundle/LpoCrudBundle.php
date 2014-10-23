<?php

namespace Lpo\CrudBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Lpo\CrudBundle\DependencyInjection\LpoCrudExtension;
use Lpo\CrudBundle\DependencyInjection\Compiler\OrdererPass;

class LpoCrudBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new LpoCrudExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OrdererPass());
    }
}
