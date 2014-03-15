<?php

namespace Job\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class JobTable extends AbstractTableGateway
{
    protected $table = 'job_tb';

    public function __construct(Adapter $adapter)
		{
			$this->adapter = $adapter;

			$this->resultSetPrototype = new HydratingResultSet();
			$this->resultSetPrototype->setObjectPrototype(new Job());
			$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

			$this->initialize();
		}

		public function getJob($type, $job)
		{
			$rowset = $this->select(array('type' => $type, 'job' => $job));
			if ($rowset == null || $rowset->count() <= 0)
			{
				return null;
			}

			return $rowset->current();
		}

		public function getJobsByType($type, $deleted = 0)
		{
			$rowset = $this->select(array('type' => $type, 'deleted' => $deleted));
			if ($rowset == null || $rowset->count() <= 0)
			{
				return null;
			}

			return $rowset;
		}

		public function addJob($type, $job, $argv = null)
		{
			$row = $this->getJob($type, $job);

			if ($row == null) {
				$data = array(
						'type' => $type,
						'job' => $job,
						'argv' => $argv,
						'ts' => date('Y-m-d H:i:s'),
						'deleted' => 0,
						);
				$this->insert($data);
			}
		}

		public function deleteItem($type, $job) 
		{
			$row = $this->getJob($type, $job);
			if ($row != null) 
			{
				$this->update(array('deleted' => 1 ), array('id' => $row->id) );
//				$this->delete(array('type' => $type, 'job' => $job));
			}
		}
}

