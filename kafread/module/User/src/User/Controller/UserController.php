<?php

namespace User\Controller;

use Zend\Authentication;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use User\Model\UserProfile;
use User\Model\UserProfileTable;

class UserController extends AbstractActionController
{
	protected $user_profile_table;
	
	public function profileAction()
	{
		$this->layout("layout/personal");
		$auth = new \Zend\Authentication\AuthenticationService();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getIdentity();
		
			return new ViewModel(array('identity' => $identity ));
		}
		
		return $this->redirect()->toRoute("account");
	}
	
	public function rsconfigAction() 
	{
		$this->layout('layout/personal');
		$auth = new \Zend\Authentication\AuthenticationService();
		$identity = null;
		if (!$auth->hasIdentity())
		{
			return $this->redirect()->toRoute("account");
		}
		
		$identity = $auth->getIdentity();
		$uid = $identity->user_id;
		
		$request = $this->getRequest();
		if ($request->isPost())
		{
			if ($identity == null)
			{
				return $this->newJM(array('code' =>'1', 'message' => 'please-login-first.'));
			}
			$id =(int) $request->getPost()->get('id');
			if ($id < 1 || $id > 5) 
			{
				return $this->newJM(array('code' => '2' , 'message' => "param invalid: id($id)"));
			}
		
			$this->getUserProfileTable()->saveProfile($uid, 'vocabulary', $id);
			$this->getUserProfileTable()->deleteProfile($uid, 'wizarded');
		
			return $this->newJM(array('code' => '0'));
		}

		$profile = new UserProfile($this->getUserProfileTable()->getAdapter());
		$profile->load($uid);

		return new ViewModel(array('method'=>'get', 'vocabulary' => $profile->getProfile('vocabulary')));
		
	}

	public function wizardAction()
	{
		$this->layout("layout/wizard");
		
		$auth = new \Zend\Authentication\AuthenticationService();
		if (!$auth->hasIdentity())
		{
			return $this->redirect()->toRoute("account");
		}
		
		$identity = $auth->getIdentity();
		$uid = $identity->user_id;

		$profile = new UserProfile($this->getUserProfileTable()->getAdapter());
		$profile->load($uid);
		
		// 已经完成了 wizard
		if ($profile->getProfile('wizarded') == null ||   
				$profile->getProfile('wizarded') == "1")
		{
			return $this->redirect()->toRoute("profile");
		}
		
		$request = $this->getRequest();
		if (!$request->isPost())
		{
			if (isset($identity->wizarded) && $identity->wizarded == '0')
			{
				return new ViewModel(array('code' => 'again'));
			}
			return new ViewModel(array('code' => null));
		}
		
		// Handle Post 
		$nick_name = $request->getPost()->get('nick_name');
		$vocabulary = (int)$request->getPost()->get('vocabulary');

		// nick name : 0~9 , a-z A-Z.
		if (strlen($nick_name) < 6 || strlen($nick_name) > 32)
		{
			return new ViewModel(array(
						'code' => 'failed' , 
						'message' => 'nick name too short or too long.must be 6~32.',
						));
		}

		// vocabulary : 1~5
		if ($vocabulary < 1 || $vocabulary > 5)
		{
			return new ViewModel(array(
						'code' => 'failed' , 
						'message' => 'please select one vocabulary.',
						));
		}

		$this->getUserProfileTable()->saveProfile($uid, 'nick_name',  $nick_name);	
		$this->getUserProfileTable()->saveProfile($uid, 'vocabulary', $vocabulary);	
		$this->getUserProfileTable()->deleteProfile($uid, 'wizarded');
		
		$auth->getStorage()->write((object) 
				array('user_id' => $identity->user_id,
							'login_email' => $identity->login_email,
							'nick_name' => $nick_name,
							'ts_last_login' => $identity->ts_last_login,
				));

		if (isset($identity->wizarded) && $identity->wizarded == '0')
		{
			return $this->redirect()->toRoute('profile');
		}
		else
		{
			return $this->redirect()->toRoute('home');
		}
	}

	private function newJM($var_array)
	{
		$jm = new JsonModel(array('method' => 'post', 'code' => $var_array));
		$jm->setTerminal(true);
		return $jm;
	}

	public function getUserProfileTable()
	{
		if (!$this->user_profile_table) {
			$sm = $this->getServiceLocator();
			$this->user_profile_table = $sm->get('User\Model\UserProfileTable');
		}
		return $this->user_profile_table;
	}

}
