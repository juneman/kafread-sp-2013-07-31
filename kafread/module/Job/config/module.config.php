<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Job\Controller\Job' => 'Job\Controller\JobController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
						
            'job' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/job[/:action][/:word]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z]*',
                        'word' => '[a-zA-Z][a-zA-Z-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Job\Controller\Job',
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
