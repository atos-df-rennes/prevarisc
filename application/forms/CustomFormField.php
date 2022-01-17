<?php

class Form_CustomFormField extends Zend_Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('class', 'form-inline');
        $this->setAttrib('id', 'field-form');

        $this->addElement('text', 'nom_champ', array(
            'label' => 'Nom du champ',
            'required' => true,
            'filters' => array(new Zend_Filter_HtmlEntities(), new Zend_Filter_StripTags()),
            'validators' => array(new Zend_Validate_StringLength(1, 255)),
        ));

        $this->addElement('select', 'type_champ', array(
            'label' => 'Type du champ',
            'required' => true,
            'multiOptions' => $this->getAllListeTypeChampRubrique()
        ));

        $submit = new Zend_Form_Element_Button('save');
        $submit->class = 'btn btn-success pull-right';
        $submit->setLabel('Ajouter le champ');
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

    public function getAllListeTypeChampRubrique(): array
    {
        $selectValues = [];
        $serviceFormulaire = new Service_Formulaire();

        $typesChampRubrique = $serviceFormulaire->getAllListeTypeChampRubrique();
        foreach ($typesChampRubrique as $typeChampRubrique) {
            $selectValues[$typeChampRubrique['ID_TYPECHAMP']] = $typeChampRubrique['TYPE'];
        }

        return $selectValues;
    }
}