<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Article\Controller\Article' => 'Article\Controller\ArticleController',
            'Article\Controller\HistoryArticle' => 'Article\Controller\HistoryarticleController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
						
            'article' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/article[/:action][/:url]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'url' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Article\Controller\Article',
                        'action'     => 'index',
                    ),
                ),
            ),

            'historyarticle' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/historyarticle[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Article\Controller\HistoryArticle',
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
