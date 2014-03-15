<?php

namespace Rsarticle\Controller;

use Zend\Authentication;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\AuthenticationService;

use Rsarticle\Model\ArticleStatics;
use Rsarticle\Model\ArticleStaticsTable;

use Words\Model\HFDictItem;
use Words\Model\HFDictTable;

use Words\Model\WordsItem;
use Words\Model\WordsTable;

use Rsarticle\Model\WordArticleMap;
use Rsarticle\Model\WordArticleMapTable;

use Rsarticle\Model\Rsarticle;
use Rsarticle\Model\RsarticleTable;

use Article\Model\Article;
use Article\Model\ArticleTable;

use Words\Model\HFDictCategory;
use Words\Model\HFDictCategoryTable;

use Job\Model\Job;
use Job\Model\JobTable;

use User\Model\UserProfile;
use User\Model\UserProfileTable;

class RsarticleController extends AbstractActionController
{
	protected $rsarticle_table;
	protected $article_table;
	protected $hf_cat_table;
	protected $job_table;
	protected $statics_table;
	protected $wamap_table;
	protected $profile_table;	
	protected $hfdict_table;	

	public function fetchjobAction()
	{
		//1. check job
		$rowset = $this->getJobTable()->getJobsByType("recommend");
		if ($rowset == null)
		{
			return $this->newJM(array('code' => 'no', 'result' => "type:recommend:no-job."));
		}
		
		return $this->newJM(array('code' => 'ok', 'result' => $rowset));

	}

	public function fetchcontentAction() 
	{
		$request = $this->getRequest();

		if (!$request->isPost()) 
		{
			return $this->newJM(array('code' =>  'no'));
		}

		$aid = (int) $request->getPost()->get('aid');

		$article = $this->getArticleTable()->getArticle($aid);
		if ($article == null)
		{
			return $this->newJM(array('code' =>  'no'));
		}
		
		$contents = $article->contents;
		
		return $this->newJM(array('code' => 'ok', 'result'=> $contents));

	}
	
	public function compareAction()
	{
		$request = $this->getRequest();

		if (!$request->isPost()) 
		{
			return $this->newJM(array('code' =>  'no'));
		}
		
		$word = $request->getPost()->get('word');
		
		$rowset = $this->getHFCateTable()->queryWord($word);
		if ($rowset == null)
		{
			return $this->newJM(array('code' =>  'no'));
		}
		
		return $this->newJM(array('code' =>  'ok', 'result' => $rowset));

	}

	public function savemapAction()
	{
		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newJM(array('code' => 'post'));
		}
		
		$word = $request->getPost()->get('word');
		$aid  = (int)$request->getPost()->get('aid');
		$times  = (int)$request->getPost()->get('times');
		$cat  = (int)$request->getPost()->get('cat');
		
		if (strlen($word) == 0 || $aid <= 0 || $times <= 0 || $cat <=0 || $cat > 5)
		{
			return $this->newJM(array('code' => 'params'));
		}

		$ret=$this->getWordArticleMapTable()->addItem($word, $aid, $times, $cat);
		if ($ret == 'ok')
		{	return $this->newJM(array('code' => 'ok'));}
		else
		{	return $this->newJM(array('code' => 'exist')); }

	}
	public function savestaAction()
	{
		$request = $this->getRequest();
		if (!$request->isPost())
		{
			return $this->newJM(array('code' => 'post'));
		}
		
		$statics = new ArticleStatics();
		
		$statics->aid  = (int)$request->getPost()->get('aid');
		$statics->total = $request->getPost()->get('total');

		$statics->cet4  = (int)$request->getPost()->get('cet4');
		$statics->cet6  = (int)$request->getPost()->get('cet6');
		$statics->ge    = (int)$request->getPost()->get('ge');
		$statics->gre   = (int)$request->getPost()->get('gre');
		$statics->toefl = (int)$request->getPost()->get('toefl');

		if ($statics->aid <= 0 || $statics->total <= 0 
				|| ($statics->cet4 + $statics->cet6 + $statics->ge + $statics->gre + $statics->toefl) <= 0 )
		{
			return $this->newJM(array('code' => 'params'));
		}
		
		$P=10000;

		$statics->cet4_p = (int)($statics->cet4 * $P / $statics->total);
		$statics->cet6_p = (int)($statics->cet6 * $P / $statics->total);
		$statics->ge_p   = (int)($statics->ge   * $P / $statics->total);
		$statics->gre_p   = (int)($statics->gre_p * $P / $statics->total);
		$statics->toefl_p = (int)($statics->toefl * $P / $statics->total);
		
		$ret=$this->getArticleStaticsTable()->addItem($statics);
		
		if ($ret == 'ok')
		{
			$job = (int)$statics->aid;
			$this->getJobTable()->deleteItem('recommend', $job);
		}

		if ($ret == "ok")
		{	return $this->newJM(array('code' => 'ok'));}
		else
		{
			return $this->newJM(array('code' => 'exist'));
		}
	}
	
	public function recommendAction()
	{
		$uid = (int)$this->getCurrentUserId();
		if ($uid <= 0 )
		{
			return $this->newJM(array('code' => 'no', 'value'=>'please login first' )); 
		}
		
		$request = $this->getRequest();
		if(!$request->isPost())
		{
			return $this->newJM(array('code' => 'no', 'value' => '43445')); 
		}
		
		$limit = $request->getPost()->get('limit');
		$page  = $request->getPost()->get('page');

		$profile = new UserProfile($this->getProfileTable()->getAdapter());
		$profile->load($uid);
		
		$vocabulary = $profile->getProfile('vocabulary');
		if ($vocabulary <= 0 || $vocabulary > 5)
		{
			return $this->newJM(array('code' => 'no', 'value'=>'invaid vocabulary')); 
		}
		$key = 'cet4_p';
		switch ($vocabulary)
		{
			case 1: $key = 'cet4_p';break;
			case 2: $key = 'cet6_p';break;
			case 3: $key = 'ge_p' ; break;
			case 4: $key = 'gre_p'; break;
			case 5: $key = 'toefl_p';break;
		}

		$ret = $this->recommend_go($uid, $key, $page, $limit);
		if ($ret == null)
		{
			return $this->newJM(array('code' => 'no', 'value'=>'query failed. no recommend articles')); 
		}
		
		return $this->newJM(array('code' => 'ok', 'value'=>$ret )); 
	}

	private function recommend_go($uid, $key, $page, $limit)
	{
		if ((int)$uid < 0 )
		{
			return null;
		}

		// 1, 从 ArticleStaticsTable 里找出 和该用户 词库匹配的AID.
		$articles_set = $this->getArticleStaticsTable()->searchArticleTop($uid, $key, $page, $limit);

		return $articles_set;
	}

	public function newJM($var_array)
	{
		$jm = new JsonModel(array('code'=> $var_array));
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
	
	public function getJobTable()
	{
		if (!$this->job_table) {
			$sm = $this->getServiceLocator();
			$this->job_table = $sm->get('Job\Model\JobTable');
		}
		return $this->job_table;
	}
	
	public function getHFCateTable()
	{
		if (!$this->hf_cat_table) {
			$sm = $this->getServiceLocator();
			$this->hf_cat_table = $sm->get('Words\Model\HFDictCategoryTable');
		}
		return $this->hf_cat_table;
	}

	public function getWordArticleMapTable()
	{
		if (!$this->wamap_table) {
			$sm = $this->getServiceLocator();
			$this->wamap_table = $sm->get('Rsarticle\Model\WordArticleMapTable');
		}
		return $this->wamap_table;
	}

	public function getArticleStaticsTable()
	{
		if (!$this->statics_table) {
			$sm = $this->getServiceLocator();
			$this->statics_table = $sm->get('Rsarticle\Model\ArticleStaticsTable');
		}
		return $this->statics_table;
	}

	public function getArticleTable()
	{
		if (!$this->article_table) {
			$sm = $this->getServiceLocator();
			$this->article_table = $sm->get('Article\Model\ArticleTable');
		}
		return $this->article_table;
	}

	public function getRsarticleTable()
	{
		if (!$this->rsarticle_table) {
			$sm = $this->getServiceLocator();
			$this->rsarticle_table = $sm->get('Rsarticle\Model\RsarticleTable');
		}
		return $this->rsarticle_table;
	}
	
	public function getHFDictTable()
	{
		if (!$this->hfdict_table) {
			$sm = $this->getServiceLocator();
			$this->hfdict_table = $sm->get('Words\Model\HFDictTable');
		}
		return $this->hfdict_table;
	}

	public function getProfileTable()
	{
		if (!$this->profile_table) {
			$sm = $this->getServiceLocator();
			$this->profile_table = $sm->get('User\Model\UserProfileTable');
		}
		return $this->profile_table;
	}
	


}
