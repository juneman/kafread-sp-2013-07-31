<?php

namespace Words\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class HFDictCategoryTable extends AbstractTableGateway
{
	protected $table = 'hf_category_tb';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new HydratingResultSet();
		$this->resultSetPrototype->setObjectPrototype(new HFDictCategory());
		$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

		$this->initialize();
	}

	public function queryWord($word)
	{
		$rowset = $this->select(array('word' => $word));

		if ($rowset == null || $rowset->count() < 1) 
		{
			return null;
		}

		return $rowset;
	}

	public function getItem($word, $category)
	{
		$rowset = $this->select(array('word' => $word, 'category' => $category));

		if ($rowset == null || $rowset->count() < 1) 
		{
			return null;
		}

		return $rowset->current();
	}

	public function fetch($category)
	{
		$rowset = $this->select(array('category' => $category));

		if ($rowset == null || $rowset->count() < 1 ) 
		{
			return null;
		}

		return $rowset;
	}

	public function addItem($word, $category)
	{
		$row = $this->getItem($word, $category);
		if ($row != null)
		{
			return "exist";
		}

		$data = array(
				'word' => $word,
				'category' => $category,
				);
		$this->insert($data);

		return "ok";
	}
}

