<?php

namespace Rsarticle;

use Rsarticle\Model\RsarticleTable;
use Rsarticle\Model\ArticleStaticsTable;
use Rsarticle\Model\WordArticleMapTable;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Rsarticle\Model\RsarticleTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \Rsarticle\Model\RsarticleTable($dbAdapter);
                    return $table;
                },

                'Rsarticle\Model\ArticleStaticsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \Rsarticle\Model\ArticleStaticsTable($dbAdapter);
                    return $table;
                },

                'Rsarticle\Model\WordArticleMapTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \Rsarticle\Model\WordArticleMapTable($dbAdapter);
                    return $table;
                },



            ),
        );
    }    

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
