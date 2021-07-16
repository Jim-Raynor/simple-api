<?php

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ValidatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $validator = $container->getDefinition('validator');

        foreach ($container->findTaggedServiceIds('app.validation') as $id => $tags) {
            $container->getDefinition($id)->addMethodCall('setValidator', [$validator]);
        }
    }
}
