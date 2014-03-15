<?php

namespace Article\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class ArticleTable extends AbstractTableGateway
{
	protected $table = 'article_tb';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new HydratingResultSet();
		$this->resultSetPrototype->setObjectPrototype(new Article());
		$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

		$this->initialize();
	}

	public function fetchAll()
	{
		$rowset = $this->select();
		return $rowset;
	}

	public function getArticle($id)
	{
		$id = (int) $id;
		$rowset = $this->select(array('id' => $id));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset->current();
	}

	public function getArticleBy($key, $value)
	{
		$rowset = $this->select(array($key => $value));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset;
	}

	public function addArticle(Article $article)
	{
		$rowset = $this->getArticleBy('url', $article->url);
		if ($rowset != null)
		{
			return 'exist';
		}

		$data = array(
				'title' => $article->title,
				'url' => $article->url,
				'summary' => $article->summary,
				'author' => $article->author,
				'from_url' => $article->from_url,
				'user_id' => $article->user_id,
				'contents' => $article->contents,
				);

		$this->insert($data);

		return 'ok';
	}

}

