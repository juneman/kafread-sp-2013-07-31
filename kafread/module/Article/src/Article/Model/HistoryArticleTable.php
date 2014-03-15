<?php

namespace Article\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class HistoryArticleTable extends AbstractTableGateway
{
    protected $table = 'history_articles_tb';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new HydratingResultSet();
				$this->resultSetPrototype->setObjectPrototype(new HistoryArticle());
				$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

        $this->initialize();
    }
		
		public function fetchAll($uid)
		{
			$uid = (int) $uid;
			$rowset = $this->select(array('user_id' => uid));
			return $rowset;
		}

		public function getArticle($uid, $aid)
		{
			$uid = (int) $uid;
			$aid = (int) $aid;

			$rowset = $this->select(array(
						'user_id' => $uid,
						'article_id' => $aid,
						));
			if ($rowset == null && $rowset->count() < 1 )
			{
				return null;
			}

			return $rowset->current();
		}
		
		public function addHistory($uid, $aid, $ts, $next = 0)
		{
			$rowset = $this->getArticle($uid, $aid);
			if ($rowset != null)
			{
				return 'exist';
			}

			$data = array(
					'user_id' => $uid,
					'article_id' => $aid,
					'ts' => $ts,
					'next' => $next,
					);
			
			$this->insert($data);

			return 'ok';
		}
}

