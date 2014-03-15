<?php

namespace Words\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class WordsTable extends AbstractTableGateway
{
    protected $table = 'new_words_tb';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new HydratingResultSet();
				$this->resultSetPrototype->setObjectPrototype(new WordsItem());
				$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

        $this->initialize();
    }
		
		public function getWordsItemBy($user_id, $word = null)
		{
			$uid = (int) $user_id;
			
			$opt = array('user_id' => $uid);
			if ($word != null)
			{
				$opt['word'] = $word; 
			}

			$rowset = $this->select($opt);

			if ($rowset == null || $rowset->count() <= 0)
			{return null;}

			return $rowset;
		}
		
		public function addNewWord(WordsItem $item)
		{
			$rowset = $this->getWordsItemBy($item->user_id, $item->word);
			if ($rowset != null && $rowset->count() > 0 )
			{
				return array('code' => 'exist',
										'value' => $item->word);
			}
			
			$data = array (
						'user_id' => $item->user_id,
						'word'    => $item->word,
						'ts_add'  => $item->ts_add,
						'ts_next' => $item->ts_next,
						'degree'  => $item->degree,
					);
			
			$this->insert($data);

			return array('code' => 'ok', 
									'value' => $item->word);
		}
		
		public function updateWordsItem($user_id, $word, $ts_next, $degree)
		{
			$rowset = $this->getWordsItemBy($user_id, $word);
			if ($rowset == null || $rowset->count() <= 0 )
			{
				return array('code' => 'not-exist');
			}
			
			$id = $rowset->current()->id;

			$this->update(array('ts_next' => $ts_next, 'degree' => $degree),
								array('id' => $id));

			return array('code' => 'ok', 
									'value' => $word);
			
		}
		
		public function clearWordsListByUserID($user_id)
		{
				$this->delete(array('user_id' => $user_id));
		}
		
		public function deleteWordsItem($id) 
		{
				$this->delete(array('id' => $id));
		}
}

