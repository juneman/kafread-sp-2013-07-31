<?php

namespace Words\Controller;

use Zend\Authentication;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Words\Model\DictItem;

class DictController extends AbstractActionController
{
	protected $dict_table;

	public function indexAction()
	{
		return $this->newVM(array('code' => 'ok'));
	}

	public function searchAction()
	{
		$word = $this->params('word');

		if (strlen($word) == 0)
		{
			return $this->newVM(array('code' => 'invalid'));
		}

		$dict_item = $this->getDictTable()->getItem($word);
		if ($dict_item == null)
		{
			return $this->newVM(array(
						'code' => 'not-exist',
						'word' => $word,
						));
		}
		
		if ($dict_item->type == 0) 
		{
			return $this->newVM(array(
						'code' => 'ok',
						'word' => $word,
						'type' => $dict_item->type,
						'value' => json_decode($dict_item->value),
						));
		}

		$other_word = $dict_item->value;
		$other_item = $this->getDictTable()->getItem($other_word);
		if ($other_item == null)
		{
			return $this->newVM(array(
						'code' => 'not-exist',
						'word' => $word,
						));
		}

		return $this->newVM(array(
					'code' => 'ok',
					'word' => $word,
					'prototype' => $other_word,
					'type' => $dict_item->type,
					'value' => json_decode($other_item->value),
					));
	}
	
	public function quicksearchAction()
	{
		$word = $this->params('word');

		if (strlen($word) == 0)
		{
			return $this->newJM(array(
							'code' => 'invalid'
							));
		}

		$dict_item = $this->getDictTable()->getItem($word);
		if ($dict_item == null)
		{
			return $this->newJM(array(
						'code' => 'not-exist',
						'word' => $word,
						));
		}
		
		if ($dict_item->type == 0) 
		{
			return $this->newJM(array(
						'code' => 'ok',
						'word' => $word,
						'type' => $dict_item->type,
						'value' => $dict_item->value,
						));
		}

		$other_word = $dict_item->value;
		$other_item = $this->getDictTable()->getItem($other_word);
		if ($other_item == null)
		{
			return $this->newJM(array(
						'code' => 'not-exist',
						'word' => $word,
						));
		}

		return $this->newJM(array(
					'code' => 'ok',
					'word' => $word,
					'prototype' => $other_word,
					'type' => $dict_item->type,
					'value' => $other_item->value,
					));
	}

	public function addAction()
	{
		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newJM(array('code' => 'please-use-post-method.'));
		}

		$word    = $request->getPost()->get('word');
		$b_pr    = $request->getPost()->get('b_pr');
		$a_pr    = $request->getPost()->get('a_pr');
		$sm      = $request->getPost()->get('sm');
		$synonym = $request->getPost()->get('synonym');
		$antonym = $request->getPost()->get('antonym');
		$usage   = $request->getPost()->get('usage');
		$tense_1 = $request->getPost()->get('tense_1');
		$tense_2 = $request->getPost()->get('tense_2');
		$tense_3 = $request->getPost()->get('tense_3');
		$tense_4 = $request->getPost()->get('tense_4');
		$tense_5 = $request->getPost()->get('tense_5');
			
		// check the $word exist.
		$row = $this->getDictTable()->getItem($word);
		if ($row != null)
		{
			return $this->newJM(array(
						'code' => 'exist',
						'word' => $word,
						));
		}

		// insert it
		$value = array(
				'b_pr' => $b_pr,
				'a_pr' => $a_pr,
				'sm'   => $sm,
				'synonym' => $synonym,
				'antonym' => $antonym,
				'usage'   => $usage,
				'tense_1' => $tense_1,
				'tense_2' => $tense_2,
				'tense_3' => $tense_3,
				'tense_4' => $tense_4,
				'tense_5' => $tense_5,
				);
		
		$dict_item = new DictItem();
		$dict_item->word = $word;
		$dict_item->type = 0;
		$dict_item->value = json_encode($value);

		$this->getDictTable()->addItem($dict_item);
		
		// insert tense
		$tenses = array($tense_1, $tense_2, $tense_3, $tense_4, $tense_5);
		$tmp_dict = new DictItem();
		$cont = 0;
		foreach ($tenses as $t)
		{
			$cont = (int)$cont + 1;
			if (strlen($t) > 0)
			{
				$tmp_dict->word = $t;
				$tmp_dict->type = $cont; 
				$tmp_dict->value = $word;
				$this->getDictTable()->addItem($tmp_dict, true);
			}
			else
			{
			}
		}
		
		return $this->newJM(array(
					'code' => 'ok',
					'word' => $word,
					));
	}

	public function getDictTable()
	{
		if (!$this->dict_table) {
			$sm = $this->getServiceLocator();
			$this->dict_table = $sm->get('Words\Model\DictTable');
		}
		return $this->dict_table;
	}

	public function newVM($var_array)
	{
		$identity = null;

		$auth = new \Zend\Authentication\AuthenticationService();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getIdentity();
		}

		$view = new ViewModel($var_array);

		$nav = new ViewModel(array('identity' => $identity));
		$nav->setTemplate("layout/navigation");

		$view->addChild($nav, "navigation");
		return $view;
	}

	public function newJM($var_array)
	{
		$vm = new JsonModel(array('code' => $var_array));
		$vm->setTerminal(true);
		return $vm; 
	}

}
