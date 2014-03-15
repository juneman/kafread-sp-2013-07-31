<?php

namespace Words\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class DictTable extends AbstractTableGateway
{
    protected $table = 'dictionary_tb';

    public function __construct(Adapter $adapter)
		{
			$this->adapter = $adapter;

			$this->resultSetPrototype = new HydratingResultSet();
			$this->resultSetPrototype->setObjectPrototype(new DictItem());
			$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

			$this->initialize();
		}

		public function getItem($word)
		{
			$rowset = $this->select(array('word' => $word));
			if ($rowset == null || $rowset->count() <= 0)
			{
				return null;
			}

			return $rowset->current();
		}

		public function addItem(DictItem $item, $check = false)
		{
			$row = $check==true ? $this->getItem($item->word) : null;

			if ($row == null) {
				$data = array(
						'word' => $item->word,
						'type' => $item->type,
						'value' => $item->value,
						);
				$this->insert($data);
			}
		}

		public function updateItem(DictItem $item)
		{
			$row = $this->getItem($item->word);

			if ($row) {
				$this->update(array('value' => $item->value, 'type' => $item->type), 
						array('id' => $row->id));
			} else {
				throw new \Exception("Could not update word,id:$id, word:$word");
			}
		}

		public function deleteItem($word) 
		{
			$this->delete(array('word' => $word));
		}
}

