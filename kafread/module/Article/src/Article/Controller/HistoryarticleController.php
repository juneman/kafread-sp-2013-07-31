<?php

namespace Article\Controller;

use Zend\Authentication;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

use Article\Model\Article;
use Article\Model\ArticleTable;

use Article\Model\HistoryArticle;
use Article\Model\HistoryArticleTable;


class HistoryarticleController extends AbstractActionController
{
	protected $history_article_table;

	public function addAction()
	{
		$uid = $this->getCurrentUserId();
		if ($uid == 0)
		{
			return $this->newJM(array('code' => 'please-login-first'));
		}

		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newVM(array(
						'code' => 'please-use-post-method',
						));
		}
		
		$url = $request->getPost()->get('url');
		$next = $request->getPost()->get('next');
		
		if (strlen($url) == 0 || $next < 0 || $next > 1)
		{
			return $this->newJM(array(
						'code' => 'post-data-invalid',
						));
		}
		
		$rowset = $this->getArticleTable()->getArticleBy('url', $url);
		if ($rowset == null)
		{
			return $this->newJM(array(
						'code' => 'no-exist',
						));
		}
		$row = $rowset->current();
		$aid = $row->id;
		$ts = date('Y-m-d');
		
		$ret = $this->getHistoryArticleTable()->addHistory($uid, $aid, $ts, $next);
		return $this->newJM(array(
					'code' => $ret,
					));
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
		$jm = new JsonModel(array('code' => $var_array));
		$jm->setTerminal(true);
		return $jm;
	}

	public function getCurrentUserId()
	{
		$auth = new \Zend\Authentication\AuthenticationService();
		if ($auth->hasIdentity())
		{
			$identity = $auth->getIdentity();
			return (int) $identity->user_id;
		}

		return 0;
	}
	
	public function getArticleTable()
	{
		if (!$this->article_table) {
			$sm = $this->getServiceLocator();
			$this->article_table = $sm->get('Article\Model\ArticleTable');
		}
		return $this->article_table;
	}

	public function getHistoryArticleTable()
	{
		if (!$this->history_article_table) {
			$sm = $this->getServiceLocator();
			$this->history_article_table = $sm->get('Article\Model\HistoryArticleTable');
		}
		return $this->history_article_table;
	}

}
