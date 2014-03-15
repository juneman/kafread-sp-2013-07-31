<?php

namespace Album\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;

class AlbumTable extends AbstractTableGateway
{
    protected $table = 'album';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        //$this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype = new HydratingResultSet();
//        $this->resultSetPrototype->setArrayObjectPrototype(new Album());
				$this->resultSetPrototype->setObjectPrototype(new Album());
				$this->resultSetPrototype->setHydrator(new ReflectionHydrator());

        $this->initialize();
    }

    public function fetchAll()
    {
				$resultSet =  $this->getAlbumByTitle('one');
				return $resultSet;
    }


    public function getAlbumByTitle($title)
    {
        $id  = (int) $id;

        $rowset = $this->select(array('title' => $title));
				
				

        return $rowset;

    }

    public function getAlbum($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveAlbum(Album $album)
    {
        $data = array(
            'artist' => $album->artist,
            'title'  => $album->title,
        );

        $id = (int)$album->id;
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getAlbum($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteAlbum($id)
    {
        $this->delete(array('id' => $id));
    }

}
