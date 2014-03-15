<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Words\Controller\Words' => 'Words\Controller\WordsController',
            'Words\Controller\Dict'     => 'Words\Controller\DictController',
            'Words\Controller\HFDict'   => 'Words\Controller\HfdictController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
						
            'words' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/words[/:action][/:word]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z]*',
                        'word' => '[a-zA-Z][a-zA-Z-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Words\Controller\Words',
                        'action'     => 'index',
                    ),
                ),
            ),
            
						'dict' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/dict[/:action][/:word]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]+',
												'word'   => '[a-zA-Z][a-zA-Z-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Words\Controller\Dict',
                        'action'     => 'index',
                    ),
                ),
            ),

						'hfdict' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/hfdict[/:action][/:word]',
                    'constraints' => array(
                        'action' => '[a-zA-Z]+',
												'word'   => '[a-zA-Z_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Words\Controller\HFDict',
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
