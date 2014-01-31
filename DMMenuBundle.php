<?php

namespace DM\MenuBundle;

use DM\MenuBundle\DependencyInjection\CompilerPass\NodeVisitorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DMMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new NodeVisitorCompilerPass());
    }
}
