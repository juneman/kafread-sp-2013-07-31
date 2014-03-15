<?php

namespace Job\Controller;

use Zend\Authentication\Adapter, 
		Zend\Authentication,
		Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

use Job\Model\Job;
use Job\Model\JobTable;

class JobController extends AbstractActionController
{
	protected $job_table;
	
	public function indexAction()
	{
		$uid = $this->getCurrentUserID();
		if ($uid == 0)
		{
			$this->redirect()->toRoute('account');
		}
		
		$this->redirect()->toRoute('account');
	}

	public function newJM($var_array)
	{
		$jm = new JsonModel(array('code' => $var_array));
		$jm->setTerminal(true);
		return $jm;
	}

	private function getCurrentUserID()
	{
		$identity = null;

		$auth = new \Zend\Authentication\AuthenticationService();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getIdentity();
			return $identity->user_id;
		}

		return 0;
	}

	public function getJobTable()
	{
		if (!$this->job_table) {
			$sm = $this->getServiceLocator();
			$this->job_table = $sm->get('Job\Model\JobTable');
		}
		return $this->job_table;
	}
}
