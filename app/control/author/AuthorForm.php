<?php

class AuthorForm extends TStandardForm
{
    protected $form; 
    
    public function __construct( $param )
    {
        parent::__construct();
        parent::setDatabase('livraria-adianti');
        parent::setActiveRecord('Author');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Author');
        $this->form->setFormTitle('Autores');
        $id   = new TEntry('id');
        $name = new TEntry('name');
        $age = new TEntry('age');
        $gender = new TRadioGroup('gender');
        $name->addValidation('Nome', new TRequiredValidator()); 

        $gender->addItems( [ 'M' => 'Masculino', 'F' => 'Feminino',  'O' => 'Outro' ] );
        $gender->setUseButton();
        $gender->setLayout('horizontal');
        
        $this->form->addFields([new TLabel('Id:')],[$id]);
        $this->form->addFields([new TLabel('Nome:')],[$name]);
        $this->form->addFields([new TLabel('Idade:')],[$age]);
        $this->form->addFields([new TLabel('Gênero:')],[$gender]);
        
        // $id->setEditable(false);
        $id->setSize(100);
        $name->setSize('70%');
        $age->setSize('70%');
        $gender->setSize('70%');
        $id->setEditable(FALSE);

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o')->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(new TXMLBreadCrumb('menu.xml', 'AuthorList'));
        $container->add($this->form);
        parent::add($container);
    }
}