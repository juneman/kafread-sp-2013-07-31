<?php

namespace Rsarticle\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ArticleStatics implements InputFilterAwareInterface
{
    public $article_id;
    public $total;
		public $cet4;
    public $cet6;
    public $ge;
		public $gre;
		public $toefl;

		public $cet4_p;
    public $cet6_p;
    public $ge_p;
		public $gre_p;
		public $toefl_p;

    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->article_id  = (isset($data['article_id'])) ? $data['article_id'] : null;
        $this->total        = (isset($data['total'])) ? $data['total'] : null;
        $this->cet4        = (isset($data['cet4'])) ? $data['cet4'] : null;
        $this->cet6        = (isset($data['cet6'])) ? $data['cet6'] : null;
        $this->ge          = (isset($data['ge'])) ? $data['ge'] : null;
        $this->gre         = (isset($data['gre'])) ? $data['gre'] : null;
        $this->toefl       = (isset($data['toefl'])) ? $data['toefl'] : null;

        $this->cet4_p        = (isset($data['cet4_p'])) ? $data['cet4_p'] : null;
        $this->cet6_p        = (isset($data['cet6_p'])) ? $data['cet6_p'] : null;
        $this->ge_p          = (isset($data['ge_p'])) ? $data['ge_p'] : null;
        $this->gre_         = (isset($data['gre_p'])) ? $data['gre_p'] : null;
        $this->toefl_p       = (isset($data['toefl_p'])) ? $data['toefl_p'] : null;
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
                'name'     => 'article_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'total',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'cet4',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

						$inputFilter->add($factory->createInput(array(
                'name'     => 'cet6',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'ge',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

						$inputFilter->add($factory->createInput(array(
                'name'     => 'gre',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'toefl',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

						/////
            $inputFilter->add($factory->createInput(array(
                'name'     => 'cet4_p',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

						$inputFilter->add($factory->createInput(array(
                'name'     => 'cet6_p',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'ge_p',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

						$inputFilter->add($factory->createInput(array(
                'name'     => 'gre_p',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'toefl_p',
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
