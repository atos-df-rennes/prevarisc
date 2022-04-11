<?php
class Form_FormChampFromParent extends Zend_Form
{
    /**
     * {@inheritdoc}
     */

    protected $champParentID;
    protected $rubriqueID;

    public function setChampParentID($myParameters)
    {
        $this->champParentID = $myParameters;
    }

    public function setRubriqueID($myParameters)
    {
        $this->rubriqueID = $myParameters;
    }

    public function init()
    {

        $dbType = new Model_DbTable_ListeTypeChampRubrique();
        $this->setMethod('post');
        $this->setAttrib('class', 'form-inline');

        $this->addElement('text', 'nom_champ', [
            'label' => 'Nom du champ',
            'required' => true,
            'filters' => [new Zend_Filter_HtmlEntities(), new Zend_Filter_StripTags()],
            'validators' => [new Zend_Validate_StringLength(1, 255)],
        ]);

        $this->addElement('hidden', 'ID_CHAMP_PARENT', [
            'required'  => true,
            'value'     => $this->champParentID
        ]);
        $this->addElement('hidden', 'rubrique', [
            'required'  => true,
            'value'     => $this->rubriqueID
        ]);

        $this->addElement('select','type_champ',
        array(
                'label'         => 'Type de champ',
                'required'      => true,
                'empty_option'  => 'Choisir un type',
                'multiOptions'  => $dbType->getTypeWithoutParentToSelectForm()
                ,
            )
        );
        $submit = new Zend_Form_Element_Button('save');
        $submit->id = 'add-champ';
        $submit->class = 'marginBottom btn btn-success pull-left add-champ';
        $submit->setLabel('Ajouter le champ');
        $this->addElement($submit);
        

        $this->setDecorators([
            'FormElements',
            'Form',
        ]);

        $this->setElementDecorators(
            [
                'ViewHelper',
                'Label',
            ],
            [
                'save',
            ],
            false
        );
    }
}
