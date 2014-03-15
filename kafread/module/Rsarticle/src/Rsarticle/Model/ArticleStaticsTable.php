<?php

namespace Rsarticle\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class ArticleStaticsTable extends AbstractTableGateway
{
	protected $table = 'article_statics_tb';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new HydratingResultSet();
		$this->resultSetPrototype->setObjectPrototype(new ArticleStatics());
		$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

		$this->initialize();
	}

	public function getItem($aid)
	{
		$aid = (int) $aid;
		$rowset = $this->select(array('article_id' => $aid));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset->current();
	}
	
	// $key: cet4, cet6, ge , gre, toefl. 
	public function getItemBy($key, $value)
	{
		$rowset = $this->select(array($key => $value));
		if ($rowset == null || $rowset->count() < 1)
		{
			return null;
		}

		return $rowset;
	}

	private function qi($name)
	{
		return $this->adapter->platform->quoteIdentifier($name);
	}

	private function qv($name)
	{
		return $this->adapter->platform->quoteValue($name);
	}

	private function fp($name)
	{
		return $this->adapter->driver->formatParameterName($name);
	}

	public function searchArticleTop($uid, $key, $page = null, $limit = null)
	{
/*
		$subSql='SELECT history_articles_tb.article_id FROM  history_articles_tb where history_articles_tb.user_id= ' 
			. $uid
			. ' and history_articles_tb.next != 0';

		$sql= 'SELECT article_id FROM  article_statics_tb WHERE article_id != '
				. '\'' . $subSql . '\' '  
				. ' order by '
				. $key . ' DESC ';

	 */
		
		$subSql='SELECT history_articles_tb.article_id FROM  history_articles_tb where history_articles_tb.user_id= ' 
			. $uid
			. ' and history_articles_tb.next != 0';

		$subSql2= 'SELECT article_id FROM  article_statics_tb WHERE article_id != '
				. '\'' . $subSql . '\' ' 
				. 'order by ' . $key . ' DESC ';
				
		$sql = 'SELECT * FROM article_tb WHERE id in('
			  . $subSql2 . ') ';
		
		if ($page !=null && $page > 0 && $limit != null && $limit > 0)
		{
			$start = $page * $limit;	
			$sql = $sql . ' limit ' . $start . '  '  . $limit;
		}

		$statement = $this->adapter->query($sql);
		$rowset = $statement->execute();
		if ($rowset == null || $rowset->count() < 1)
		{return null;}
		return $rowset;
	}

	public function addItem(ArticleStatics $statics)
	{
		$rowset = $this->getItem($statics->aid);
		if ($rowset != null)
		{
			return 'exist';
		}

		$data = array(
				'article_id' => $statics->aid,
				'total' 		 => $statics->total,
				'cet4' 			 => $statics->cet4,
				'cet6'       => $statics->cet6,
				'ge'         => $statics->ge,
				'gre'        => $statics->gre,
				'toefl'      => $statics->toefl,
				'cet4_p' 		 => $statics->cet4_p,
				'cet6_p'     => $statics->cet6_p,
				'ge_p'       => $statics->ge_p,
				'gre_p'      => $statics->gre_p,
				'toefl_p'    => $statics->toefl_p,
				);

		$this->insert($data);

		return 'ok';
	}

}

