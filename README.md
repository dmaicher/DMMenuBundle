dm-menu-bundle
==============

This bundle can be used to build dynamic menus.

Step 1: create MenuTreeBuilder
------------------------------

    class MainMenuTreeBuilder implements MenuTreeBuilderInterface {
    
        public function buildTree(Node $root, NodeFactoryInterface $factory)
        {
            $root->addChild($factory->create('some_label_a', [
                'route' => 'some_route_a',
                'attr' => ['id' => 'some_id_a'],
                'required_roles' => ['ROLE_NECESSARY_A']
            ]))
            ->addChild($factory->create('some_label_b', [
                'route' => 'some_route_b',
                'attr' => ['id' => 'some_id_b'],
                'required_roles' => ['ROLE_NECESSARY_B']
            ]))
            ->addChild($factory->create('some_label_c', [
                'route' => 'some_route_c',
                'additional_active_routes' => ['another_route']
            ]));
        }
    }
    
    
Step 2: add config for your menu
-----------------------

    dm_menu:
        menues:
            your_namespace.main_menu:
                tree_builder: Your\Namespace\MainMenuTreeBuilder
                twig_template: YourNamespace:main_menu.html.twig #optional

    
Step 3: render the menu
-----------------------

    {{ dm_menu_render('your_namespace.main_menu', {'collapse':true, 'nested':false}) }}
