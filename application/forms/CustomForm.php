<?php

class Form_CustomForm extends Zend_Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('class', 'form-inline rubrique-form');

        $this->addElement('text', 'nom_rubrique', array(
            'label' => 'Nom de la rubrique',
            'required' => true,
            'filters' => array(new Zend_Filter_HtmlEntities(), new Zend_Filter_StripTags()),
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
        ));

        $this->addElement('checkbox', 'afficher_rubrique', array(
            'label' => 'Afficher la rubrique par défaut',
        ));

        $submit = new Zend_Form_Element_Button('save');
        $submit->class = 'btn btn-success pull-right';
        $submit->setLabel('Ajouter la rubrique');
        $this->addElement($submit);

        $this->setDecorators(array(
            'FormElements',
            'Form',
        ));

        $this->setElementDecorators(
            array(
                'ViewHelper',
                'Label',
            ),
            array(
                'save',
            ),
            false
        );
    }
}