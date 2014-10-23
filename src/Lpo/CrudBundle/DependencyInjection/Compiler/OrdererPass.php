<?php

namespace MRi\CrudBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OrdererPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // On recupere tous les handlers taggés
        $handlers = [];
        foreach ($container->findTaggedServiceIds('lpo_crud.handler') as $id => $tags) {
            $index = explode('.', $id);
            $handlers[end($index)] = new Reference($id);
        }
        // Declaration du nouveau service "orderer"
        $container
                ->register('lpo_crud.orderer', 'MRi\CrudBundle\Orderer\Orderer')
                ->addArgument($handlers);
    }
}
