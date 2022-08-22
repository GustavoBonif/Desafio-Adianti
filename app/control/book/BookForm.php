<?php

class BookForm extends TStandardForm
{
    protected $form; 
    
    public function __construct( $param )
    {
        parent::__construct();
        parent::setDatabase('livraria-adianti');
        parent::setActiveRecord('Book');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Book');
        $this->form->setFormTitle('Livros');
        $id = new TEntry('id');
        $name = new TEntry('name');
        $price = new TEntry('price');
        $published_year = new TEntry('published_year');
        $author_id = new TDBCombo('author_id', 'livraria-adianti', 'Author', 'id', 'name');
        $publisher_id = new TDBCombo('publisher_id', 'livraria-adianti', 'Publisher', 'id', 'name');
        
        $name->addValidation('Nome', new TRequiredValidator()); 
        $author_id->addValidation('Autor', new TRequiredValidator()); 
        $publisher_id->addValidation('Editora', new TRequiredValidator()); 

        $price->setMask('99,99');
        $published_year->setMask('9999');

        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Nome')], [$name] );
        $this->form->addFields( [new TLabel('Preço')], [$price] );
        $this->form->addFields( [new TLabel('Ano de Publicação')], [$published_year] );
        $this->form->addFields( [new TLabel('Autor')], [$author_id] );
        $this->form->addFields( [new TLabel('Editora')], [$publisher_id] );

        // $id->setEditable(false);
        $id->setSize(100);
        $name->setSize('70%');
        $price->setSize('70%');
        $author_id->setSize('70%');
        $publisher_id->setSize('70%');
        $id->setEditable(FALSE);

        // create the form actions
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o')->addStyleClass('btn-primary');
        $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        $this->form->addActionLink( _t('Back'), new TAction(array('BookList','onReload')),  'far:arrow-alt-circle-left blue' );

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(new TXMLBreadCrumb('menu.xml', 'BookList'));
        $container->add($this->form);
        parent::add($container);
    }
}