<?php
namespace Article\Form;

use Zend\Form\Form;

class ArticleAddForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('article_add');

        $this->setAttribute('method', 'post');
				$this->setAttribute('class', 'form-article-editor');

				$this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
								'class' =>  'input-block-level',
								'placeholder' => 'Title',
            ),
        ));

        $this->add(array(
            'name' => 'author',
            'attributes' => array(
                'type'  => 'text',
								'class' =>  'input-block-level',
								'placeholder' => 'Article',
            ),
        ));

        $this->add(array(
            'name' => 'from_url',
            'attributes' => array(
                'type'  => 'text',
								'class' =>  'input-block-level',
								'placeholder' => 'From URL',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Add',
								'class' => 'btn btn-large btn-primary btn-block',
            ),
        ));

    }
}
