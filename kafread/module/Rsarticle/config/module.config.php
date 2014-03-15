<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Rsarticle\Controller\Rsarticle' => 'Rsarticle\Controller\RsarticleController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
						
            'rsystem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rsystem[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Rsarticle\Controller\Rsarticle',
                        'action'     => 'index',
                    ),
                ),
            ),


        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),

	 ),
);
