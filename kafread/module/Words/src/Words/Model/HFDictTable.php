<?php

namespace Words\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class HFDictTable extends AbstractTableGateway
{
    protected $table = 'high_frequency_words_tb';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new HydratingResultSet();
				$this->resultSetPrototype->setObjectPrototype(new HFDictItem());
				$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

        $this->initialize();
    }
		
		public function getItem($word)
		{
			$rowset = $this->select(array('word' => $word));

			if ($rowset == null || $rowset->count() < 1) 
			{
				return null;
			}
			
			return $rowset->current();
		}
			
		public function addItem($word, $a_pr, $b_pr, $sm)
		{
			$row = $this->getItem($word);
			if ($row != null)
			{
				return "exist";
			}

			$data = array(
					'word' => $word,
					'a_pr' => $a_pr,
					'b_pr' => $b_pr,
					'sm' => $sm,
					'hot_point' => 0,
					);
			$this->insert($data);
			
			return "ok";
		}
		
		public function updateHotpoint($word, $hotpoint)
		{
			$row = $this->getItem($word);
			if ($row == null)
			{
				return "not-exist";
			}
			
			$id = (int) $row->id;

			$this->update(array('hot_point' => $hotpoint), 
					array('id' => $id));

			return "ok";

		}
}

