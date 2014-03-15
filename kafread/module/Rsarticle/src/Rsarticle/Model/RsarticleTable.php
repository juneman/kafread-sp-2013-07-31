<?php

namespace Rsarticle\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class RsarticleTable extends AbstractTableGateway
{
    protected $table = 'rsarticle_tb';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new HydratingResultSet();
				$this->resultSetPrototype->setObjectPrototype(new Rsarticle());
				$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

        $this->initialize();
    }
		
		public function recommend($uid, $category)
		{
			$uid = (int) $uid;
			$rowset = $this->select(array('user_id' => $uid, 'category' => $category));
			if ($rowset == null || $rowset->count() < 1)
			{
				return null;
			}

			return $rowset;
		}
		
		private function is_exist($uid, $aid, $category)
		{
			$rowset = $this->select(array('user_id' => $uid, 'article_id' => $aid, 'category' => $category));
			if ($rowset == null || $rowset->count() < 1)
			{
				return false;
			}

			return true;
		}
		
		public function addRsarticle(Rsarticle $rsarticle)
		{
			$uid        = $rsarticle->uid;
			$aid 				= $rsarticle->article_id;
			$category   = $rsarticle->category;

			$flag = $this->is_exist($uid, $aid, $category);
			if ($flag == true)
			{
				return 'exist';
			}

			$data = array(
					'user_id' => $uid,
					'article_id' => $aid,
					'category' => $category,
					'ts_rs' => $rsarticle->ts_rs,
					);
			
			$this->insert($data);

			return 'ok';
		}

		public function deleteRsarticle($uid, $aid, $category) 
		{
				$this->delete(array(
							'user_id' => $uid,
							'article_id' => $aid,
							'category' => $category,
							));
		}
}

