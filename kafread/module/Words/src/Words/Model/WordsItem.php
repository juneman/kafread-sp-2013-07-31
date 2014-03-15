<?php

namespace Words\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class WordsItem implements InputFilterAwareInterface
{
    public $id;
    public $user_id;
    public $word;
    public $ts_add;
    public $ts_next;
    public $category;
    public $degree;

    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->id        = (isset($data['id'])) ? $data['id'] : null;
        $this->user_id   = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->word      = (isset($data['word'])) ? $data['word'] : null;
        $this->ts_add    = (isset($data['ts_add'])) ? $data['ts_add'] : null;
        $this->ts_next   = (isset($data['ts_next'])) ? $data['ts_next'] : null;
        $this->category  = (isset($data['category'])) ? $data['category'] : null;
        $this->degree    = (isset($data['degree'])) ? $data['degree'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'user_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'word',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 64,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'ts_add',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 12,
                            'max'      => 32,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'ts_next',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 12,
                            'max'      => 32,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'category',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            
						$inputFilter->add($factory->createInput(array(
                'name'     => 'degree',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));



            $this->inputFilter = $inputFilter;        
        }

        return $this->inputFilter;
    }
}
