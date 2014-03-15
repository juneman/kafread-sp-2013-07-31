<?php

namespace Words;

use Words\Model\WordsTable;
use Words\Model\DictTable;
use Words\Model\HFDictTable;
use Words\Model\HFDictCategoryTable;

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
                'Words\Model\WordsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \Words\Model\WordsTable($dbAdapter);
                    return $table;
                },

               	'Words\Model\DictTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \Words\Model\DictTable($dbAdapter);
                    return $table;
                },

               	'Words\Model\HFDictTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \Words\Model\HFDictTable($dbAdapter);
                    return $table;
                },

               	'Words\Model\HFDictCategoryTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new \Words\Model\HFDictCategoryTable($dbAdapter);
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
