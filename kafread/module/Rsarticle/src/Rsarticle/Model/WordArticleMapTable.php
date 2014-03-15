<?php

namespace Rsarticle\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class WordArticleMapTable extends AbstractTableGateway
{
	protected $table = 'word_article_map_tb';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new HydratingResultSet();
		$this->resultSetPrototype->setObjectPrototype(new WordArticleMap());
		$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

		$this->initialize();
	}

	public function getItem($id)
	{
		$id = (int) $id;
		$rowset = $this->select(array('id' => $id));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset->current();
	}

	public function getItemsByWord($word)
	{
		$rowset = $this->select(array('word' => $word));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset;
	}
	
	public function getItemsByAID($aid)
	{
		$rowset = $this->select(array('article_id' => $aid));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset;
	}
	
	public function getItemsBy($word, $aid)
	{
		$rowset = $this->select(array('word'=>$aid, 'article_id' => $aid));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset;
	}

	public function addItem($word, $aid, $times, $category)
	{
		//$rowset = $this->getItemsBy($word, $aid);
		$rowset = $this->select(array('word'=>$word, 'article_id' => $aid, 'category'=>$category));
		if ($rowset != null && $rowset->count() > 0 )
		{
			return 'exist';
		}

		$data = array(
				'word' => $word,
				'article_id' => $aid,
				'times' => $times,
				'category' => $category,
				);

		$this->insert($data);

		return 'ok';
	}

}

