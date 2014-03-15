<?php

namespace Words\Controller;

use Zend\Authentication;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

use Words\Model\HFDictItem;
use Words\Model\HFDictTable;

use Words\Model\HFDictCategory;
use Words\Model\HFDictCategoryTable;

use Words\Model\WordsItem;
use Words\Model\WordsTable;

class HfdictController extends AbstractActionController
{
	protected $hfdict_table;
	protected $hfdict_cate_table;
	protected $words_table;

	public function indexAction()
	{
		$identity = null;
		$test_auth = new \Zend\Authentication\AuthenticationService();
		if ($test_auth->hasIdentity())
		{
			$identity = $test_auth->getIdentity();
		}
		
		if ($identity == null)
		{
			$this->redirect()->toRoute("account");
		}

		if ($identity->user_id != 1 )
		{
			$this->redirect()->toRoute("account");
		}

		return new ViewModel();
	}

	public function addAction()
	{
		// turn off
		return $this->newJM(array('code' => 'this-function-not-exist'));
		
		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newJM(array('code' => 'please-use-post-method'));
		}
		
		$word = $request->getPost()->get('word');
		$category   = $request->getPost()->get('category');
		$a_pr = $request->getPost()->get('a_pr');
		$b_pr = $request->getPost()->get('b_pr');
		$sm   = $request->getPost()->get('sm');
			
		if ($word == "" || $a_pr == "" || $b_pr == "" || $sm == "")
		{
			return $this->newJM(array('code' => "post-data-invalid"));
		}
		
		$result = $this->getHFDictCategoryTable()->addItem($word, $category);
		if ($result != "ok")
		{
			return $this->newJM(array('code' => "cate: $result"));
		}

		$result = $this->getHFDictTable()->addItem($word, $a_pr, $b_pr, $sm);
		
		return $this->newJM(array('code' => "sm:$result"));

	}
	
	public function searchAction()
	{
		$word = $this->params('word');
		if (strlen($word) == 0)
		{
			return $this->newJM(array('code' => 'param-null'));
		}
		
		$item = $this->getHFDictTable()->getItem($word);
		if ($item == null)
		{
			return $this->newJM(array(
						'code' => 'not-exist',
						'word' => $word,
						));
		}

		return $this->newJM(array(
					'code' => 'ok',
					'word' => $word,
					'a_pr' => $item->a_pr,
					'b_pr' => $item->b_pr,
					'sm'  => nl2br($item->sm),
					));	

	}
	
	public function pullAction()
	{
		$uid = $this->getCurrentUserID();
		if ($uid == 0)
		{
			return $this->newJM(array('code' => 'please-login-first'));
		}

		$word = $this->params('word');
		if (strlen($word) == 0)
		{
			return $this->newJM(array('code' => 'param-null'));
		}
		
		$isfaved = false;
		$rowset = $this->getWordsTable()->getWordsItemBy($uid, $word);
		if ($rowset != null)
		{
			$isfaved = true;
		}

		$item = $this->getHFDictTable()->getItem($word);
		if ($item == null)
		{
			return $this->newJM(array(
						'code' => 'not-exist',
						'word' => $word,
						));
		}

		return $this->newJM(array(
					'code' => 'ok',
					'word' => $word,
					'a_pr' => $item->a_pr,
					'b_pr' => $item->b_pr,
					'sm'  => nl2br($item->sm),
					'isfaved' => $isfaved,
					));	
	}

	public function editAction()
	{
	}

	public function deleteAction()
	{
	}

	public function getHFDictTable()
	{
		if (!$this->hfdict_table) {
			$sm = $this->getServiceLocator();
			$this->hfdict_table = $sm->get('Words\Model\HFDictTable');
		}
		return $this->hfdict_table;
	}

	public function getHFDictCategoryTable()
	{
		if (!$this->hfdict_cate_table) {
			$sm = $this->getServiceLocator();
			$this->hfdict_cate_table = $sm->get('Words\Model\HFDictCategoryTable');
		}
		return $this->hfdict_cate_table;
	}

	public function getWordsTable()
	{
		if (!$this->words_table) {
			$sm = $this->getServiceLocator();
			$this->words_table = $sm->get('Words\Model\WordsTable');
		}
		return $this->words_table;
	}

	public function newJM($var_array)
	{
		$vm = new JsonModel(array('code' => $var_array));
		$vm->setTerminal(true);
		return $vm; 
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
}
