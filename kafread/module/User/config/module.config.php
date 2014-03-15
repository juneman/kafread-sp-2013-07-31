<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
            'User\Controller\Account' => 'User\Controller\AccountController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
						
            'account' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/account[/:action][/:do]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z]*',
                        'do' => '[a-zA-Z][a-zA-Z]*',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Controller\Account',
                        'action'     => 'auth',
												'do'         => 'login',
                    ),
                ),
            ),
            
						'wizard' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/wizard',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'wizard',
                    ),
                ),
            ),

						'profile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/profile',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'profile',
                    ),
                ),
            ),

						'rsconfig' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rsconfig',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'rsconfig',
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
