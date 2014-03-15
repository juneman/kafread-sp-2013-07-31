<?php

namespace Article\Controller;

use Zend\Authentication;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

use Article\Model\Article;
use Article\Model\ArticleTable;

use Article\Model\HistoryArticle;
use Article\Model\HistoryArticleTable;

use Job\Model\Job;
use Job\Model\JobTable;

class ArticleController extends AbstractActionController
{
	protected $article_table;
	protected $history_article_table;
	protected $job_table;

	public function indexAction()
	{
		$articles = $this->getArticleTable()->fetchAll();

		return $this->newVM(array(
					'articles' => $articles,
					));
	}

	public function addAction()
	{
		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newVM(array(
						'code' => 'none',
						));
		}
		
		$title = $request->getPost()->get('title');
		$author = $request->getPost()->get('author');
		$from_url = $request->getPost()->get('from_url');
		$summary  = $request->getPost()->get('summary');
		$contents = $request->getPost()->get('contents');
		
		$article = new Article();
		$article->title = trim($title);
		$article->summary = $summary;
		$article->author = trim($author);
		$article->from_url = trim($from_url);
		$article->contents = $contents;
		$article->user_id = $this->getCurrentUserId(); 
		
		$pattern = "/[\s_:?,，.'\"<>\\%&\(\)]/";
		$url_tmp = preg_replace($pattern, "-", strtolower($article->title));
		$pattern = "/[-]+/";
		$url_tmp = preg_replace($pattern, "-", $url_tmp);

		$article->url = $url_tmp;

		$ret = $this->getArticleTable()->addArticle($article);
	
		// Add job....
		if ($ret == 'ok')
		{
			$rowset = $this->getArticleTable()->getArticleBy('url', $article->url);
			if ($rowset == null ) 
			{
				return $this->newVM(array(
						'code' => 'none',
						));
			}

			$row = $rowset->current();
			$aid = $row->id;
		
			$this->getJobTable()->addJob('recommend', $aid);
		}

		if ($ret == 'ok')
		{	
			return $this->newVM(array(
						'code' => 'ok',
						'title' => $article->title,
						'url' => $article->url,
						));
		}
		else if ($ret == 'exist')
		{
			return $this->newVM(array(
						'code' => 'exist',
						'title' => $article->title,
						'url' => $article->url,
						));
		}
		else
		{
			return $this->newVM(array(
						'code' => 'none',
						));
		}
	}

	public function editAction()
	{
	}

	public function deleteAction()
	{
	}

	public function readAction()
	{
		$uid = $this->getCurrentUserId();
		if ($uid == 0)
		{
			$this->redirect()->toRoute('account');
		}

		$article_url = $this->params('url');
		
		if (strlen($article_url) == 0)
		{
			$this->redirect()->toRoute('article');
		}

		$rowset = $this->getArticleTable()->getArticleBy('url', $article_url);
		if ($rowset == null ) 
		{
			return $this->newVM(array(
						'code' => 'not-found',
						)); 
		}
		$row = $rowset->current();
		
		$aid = $row->id;
		$history = $this->getHistoryArticleTable()->getArticle($uid, $aid);
		
		return $this->newVM(array(
					'code' => 'ok',
					'history' => $history,
					'article' => $row,
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

		$nav = new ViewModel(array('identity' => $identity, 'section' => 'articles'));
		$nav->setTemplate("layout/navigation");

		$view->addChild($nav, "navigation");
		return $view;
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
	
	public function getJobTable()
	{
		if (!$this->job_table) {
			$sm = $this->getServiceLocator();
			$this->job_table = $sm->get('Job\Model\JobTable');
		}
		return $this->job_table;
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
