<?php

namespace DM\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DMMenuExtension extends Extension
{
    const NODE_FACTORY_SERVICE_ID_PREFIX = 'dm_menu.node_factory.custom.';
    const TREE_BUILDER_SERVICE_ID_PREFIX = 'dm_menu.tree_builder.custom.';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        //doing it here because somehow ->defaultValue() does not work within prototype node?!
        $defaultValues = array(
            'node_factory' => 'dm_menu.node_factory',
            'twig_template' => 'DMMenuBundle::menu.html.twig'
        );

        if(isset($configs[0]['menues'])) {
            $menuDefHolder = $container->getDefinition('dm_menu.menu_definition_holder');

            foreach($configs[0]['menues'] as $name => &$menuConfig) {

                $menuConfig = array_merge($defaultValues, $menuConfig);

                $menuConfig['tree_builder'] = $this->getReferenceFromConfigValue(
                    $container,
                    self::TREE_BUILDER_SERVICE_ID_PREFIX,
                    $menuConfig['tree_builder']
                );

                $menuConfig['node_factory'] = $this->getReferenceFromConfigValue(
                    $container,
                    self::NODE_FACTORY_SERVICE_ID_PREFIX,
                    $menuConfig['node_factory']
                );

                $menuDefHolder->addMethodCall('addMenuDefinition', array($name, $menuConfig));
            }
        }
    }

    /**
     * @param $value
     * @return Reference
     */
    protected function getReferenceFromConfigValue(ContainerBuilder $container, $prefix, $value)
    {
        if(class_exists($value)) {
            $id = $prefix.md5($value);
            $container->register($id, $value);
            return new Reference($id);
        }
        else{
            return new Reference($value);
        }
    }
}
