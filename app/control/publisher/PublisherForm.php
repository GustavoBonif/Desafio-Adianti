<?php

class PublisherForm extends TStandardForm
{
    protected $form; 
    
    public function __construct( $param )
    {
        parent::__construct();
        parent::setDatabase('livraria-adianti');
        parent::setActiveRecord('Publisher');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Publisher');
        $this->form->setFormTitle('Editoras');
        $id   = new TEntry('id');
        $name = new TEntry('name');
        $countryOfOrigin = new TEntry('country_of_origin');
        $foundedDate = new TEntry('founded_date');
        $name->addValidation('Nome', new TRequiredValidator()); 

        $foundedDate->setMask('99/99/9999');
                
        $this->form->addFields([new TLabel('Id:')],[$id]);
        $this->form->addFields([new TLabel('Nome:')],[$name]);
        $this->form->addFields([new TLabel('País de Origem:')],[$countryOfOrigin]);
        $this->form->addFields([new TLabel('Data de Criação:')],[$foundedDate]);
        
        $id->setSize(100);
        $name->setSize('70%');
        $countryOfOrigin->setSize('70%');
        $foundedDate->setSize('70%');
        $id->setEditable(FALSE);

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o')->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        $this->form->addActionLink( _t('Back'), new TAction(array('PublisherList','onReload')),  'far:arrow-alt-circle-left blue' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(new TXMLBreadCrumb('menu.xml', 'PublisherList'));
        $container->add($this->form);
        parent::add($container);
    }
}