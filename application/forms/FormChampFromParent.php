<?php
class Form_FormChampFromParent extends Zend_Form
{
    /**
     * {@inheritdoc}
     */
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

        $this->addElement('select','selectType',
        array(
                'label'         => 'Type de champ',
                'required'      => true,
                'empty_option'  => 'Choisir un type',
                'multiOptions'  => $dbType->getTypeWithoutParentToSelectForm()
                ,
            )
        );

        $submit = new Zend_Form_Element_Button('save');
        $submit->class = 'btn btn-success pull-right add-champ';
        $submit->setLabel('Ajouter le champ');
        $this->addElement($submit);
        
        
        $monInputSubmit = new Zend_Form_Element_Submit('save2');
        $monInputSubmit->class = 'btn btn-success pull-right add-champ';
        $monInputSubmit->setLabel('Mon Ajouter le champ');
        $this->addElement($monInputSubmit);

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
