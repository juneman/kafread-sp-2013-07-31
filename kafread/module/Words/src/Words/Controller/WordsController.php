<?php

namespace Words\Controller;

use Zend\Authentication\Adapter, 
		Zend\Authentication,
		Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

use Words\Model\WordsItem;
use Words\Model\WordsTable;

use Words\Model\HFDictItem;
use Words\Model\HFDictTable;

class WordsController extends AbstractActionController
{
	protected $words_table;
	protected $hfdict_table;
	
	public function indexAction()
	{
		$uid = $this->getCurrentUserID();
		if ($uid == 0)
		{
			$this->redirect()->toRoute('account');
		}
		
		$rowset = $this->getWordsTable()->getWordsItemBy($uid);
	
		return new ViewModel(array('code' => 'ok',
					'lists' => $rowset,
					));
	}

	public function addAction()
	{
		$uid = $this->getCurrentUserID();
		if ($uid == 0)
		{
			return $this->newJM(array(
						'code' => 'failed',
						'value' => 'please-login-first.',
						));
		}

		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newJM(array(
						'code' => 'failed',
						'value' => 'please-use-post-method.',
						));
		}

		$word    = $request->getPost()->get('word');
		
		// 如果该单词已经在生词本中，更改degree = 0, ts_next 重新计算-
		$rowset = $this->getWordsTable()->getWordsItemBy($uid, $word);
		if ($rowset == null)
		{
			$word_item = new WordsItem();

			$word_item->word    = $word;
			$word_item->user_id = $uid;
			$word_item->ts_add  = date('Y-m-d'); 
			$word_item->ts_next = date('Y-m-d');
			$word_item->degree  = 0;

			$ret = $this->getWordsTable()->addNewWord($word_item);
			return $this->newJM($ret);
		}
		
		// 重新计算ts_next
		$ts_next = date('Y-m-d');
		$degree = $rowset->degree ==0 ? 0 : ($rowset->degree - 1);
		$ret =  $this->getWordsTable()->updateWordsItem($uid, $word, $ts_next, $degree);
		
		return $this->newJM($ret);
	}

	public function updateAction()
	{
		$uid = $this->getCurrentUserID();
		if ($uid == 0)
		{
			return $this->newJM(array(
						'code' => 'failed',
						'value' => 'please-login-first.',
						));
		}

		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newJM(array(
						'code' => 'failed',
						'value' => 'please-use-post-method.',
						));
		}

		$word    = $request->getPost()->get('word');
		$ts_next = $request->getPost()->get('ts_next');
		$degree = $request->getPost()->get('degree');
		
		$ret = $this->getWordsTable()->updateWordsItem($uid, $word, $ts_next,$degree);

		return $this->newJM($ret);
	}

	public function queryAction()
	{
		$uid = $this->getCurrentUserID();
		if ($uid == 0)
		{
			return $this->newJM(array(
						'code' => 'failed',
						'value' => 'please-login-first.',
						));
		}
		
		$word = $this->params('word');
		if (strlen($word) == 0)
		{
			return $this->newJM(array('code' => 'param-null'));
		}
		
		$rowset = $this->getWordsTable()->getWordsItemBy($uid, $word);
		if ($rowset == null)
		{
			return $this->newJM(array(
						'code' => 'no-exist',
						)); 
		}

		return $this->newJM(array(
					'code' => 'exist',
					)); 
	}

	public function fetchallAction()
	{
		$uid = $this->getCurrentUserID();
		if ($uid == 0)
		{
			return $this->newJM(array(
						'code' => 'no',
						'value' => 'please-login-first.',
						));
		}
		
		$words = 
			$this->getWordsTable()->getWordsItemBy($uid);

		if ($words != null)
		{
			$words_list = array();
			$nums = 0;
			foreach ($words as $w)
			{
				$word_item = $this->getHFDictTable()->getItem($w->word);
				if ($word_item != null)
				{
					$words_list["$nums"] = $word_item;
					$nums = $nums + 1;
				}
			}

			return $this->newJM(array('code' => 'ok', 'nums'=>$nums, 'value' => $words_list));
		}
		
		return $this->newJM(array('code' => 'null', 'value' => 'no new words'));
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

	public function getHFDictTable()
	{
		if (!$this->hfdict_table) {
			$sm = $this->getServiceLocator();
			$this->hfdict_table = $sm->get('Words\Model\HFDictTable');
		}
		return $this->hfdict_table;
	}
	public function getWordsTable()
	{
		if (!$this->words_table) {
			$sm = $this->getServiceLocator();
			$this->words_table = $sm->get('Words\Model\WordsTable');
		}
		return $this->words_table;
	}
}
