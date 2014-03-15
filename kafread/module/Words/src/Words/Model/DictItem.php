<?php

namespace  Words\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class DictItem implements InputFilterAwareInterface
{
    public $id;
    public $word;
		public $type;
		public $value;
/****
		当type = 0; 
			value = {
             	b_pr: ,
             	a_pr: ,
             	sm: ,
             	synonym: ,
             	antonym: ,
             	usage: ,
             	tense: ,
				}
		当type = 1, 表示该单词是 value的 第三人称单数
		       = 2，过去式
					 = 3，过去分词
					 = 4，现在分词

	*/


	/*	
    public $b_pr; // 英式发音
    public $a_pr; // 美式发音
		public $sm; // 简单释义
		public $synonym; //同义词
		public $antonym; // 反义词
		public $usage;
		public $tense;  // 时态
		public $plural; // 复数形式
*/
    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->id    = (isset($data['id'])) ? $data['id'] : null;
        $this->word  = (isset($data['word'])) ? $data['word'] : null;
        $this->type  = (isset($data['type'])) ? $data['type'] : null;
        $this->value = (isset($data['value'])) ? $data['value'] : null;
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
                'name'     => 'type',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'value',
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
                            'max'      => 2048,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;        
        }

        return $this->inputFilter;
    }
}
