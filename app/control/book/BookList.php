<?php

class BookList extends TStandardList
{
    protected $form;     
    protected $datagrid; 
    protected $pageNavigation;
    protected $formgrid;
    protected $loaded;
    protected $deleteButton;
    protected $transformCallback;
 
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('livraria-adianti');  
        parent::setActiveRecord('book'); 
        parent::setDefaultOrder('id', 'asc');
        parent::addFilterField('id', '=', 'id'); 
        parent::addFilterField('name', 'like', 'name');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Book');
        $this->form->setFormTitle('Livros');
        
        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $price = new TEntry('price');
        $published_year = new TEntry('published_year');
        $author_id = new TDBCombo('author_id', 'livraria-adianti', 'Author', 'id', 'name');
        $publisher_id = new TDBCombo('publisher_id', 'livraria-adianti', 'Publisher', 'id', 'name');

        $price->setMask('99,99');
        $published_year->setMask('9999');
        
        // add the fields
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Nome')], [$name] );
        $this->form->addFields( [new TLabel('Preço')], [$price] );
        $this->form->addFields( [new TLabel('Ano de Publicação')], [$published_year] );
        $this->form->addFields( [new TLabel('Autor')], [$author_id] );
        $this->form->addFields( [new TLabel('Editora')], [$publisher_id] );

        $id->setSize('20%');
        $name->setSize('70%');
        $price->setSize('70%');
        $published_year->setSize('70%');
        $author_id->setSize('70%');
        $publisher_id->setSize('70%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Book_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Novo',  new TAction(array('BookForm', 'onEdit')), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_name = new TDataGridColumn('name', 'Nome', 'left');
        $column_price = new TDataGridColumn('price', 'Preço', 'left');
        $column_published_year = new TDataGridColumn('published_year', 'Ano de Publicação', 'left');
        $column_author_id = new TDataGridColumn('author->name', 'Autor', 'left');
        $column_publisher_id = new TDataGridColumn('publisher->name', 'Editora', 'left');

        $column_name->setTransformer( function($value, $object, $row) {
            return strtoupper($value);
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_price);
        $this->datagrid->addColumn($column_published_year);
        $this->datagrid->addColumn($column_author_id);
        $this->datagrid->addColumn($column_publisher_id);

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);

        // create EDIT action
        $action_edit = new TDataGridAction(array('BookForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Editar');
        $action_edit->setImage('far:edit blue');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel('Delete');
        $action_del->setImage('far:trash-alt red');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }

    public function onSearch($param = NULL)
    {
        $data = $this->form->getData();

        TSession::setValue('BookList_filter_id', NULL);
        TSession::setValue('BookList_filter_name', NULL);
        TSession::setValue('BookList_filter_price', NULL);
        TSession::setValue('BookList_filter_published_year', NULL);
        TSession::setValue('BookList_filter_author_id', NULL);
        TSession::setValue('BookList_filter_publisher_id', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id");
            TSession::setValue('BookList_filter_id', $filter);
        }

        if (isset($data->name) AND ($data->name)) {
            $filter = new TFilter('name', 'like', "%{$data->name}%");
            TSession::setValue('BookList_filter_name', $filter);
        }

        if (isset($data->price) AND ($data->price)) {
            $filter = new TFilter('price', 'like', "%{$data->price}%");
            TSession::setValue('BookList_filter_price', $filter);
        }
        
        if (isset($data->published_year) AND ($data->published_year)) {
            $filter = new TFilter('published_year', 'like', "%{$data->published_year}%");
            TSession::setValue('BookList_filter_published_year', $filter);
        }

        if (isset($data->author_id) AND ($data->author_id)) {
            $filter = new TFilter('author_id', 'like', "$data->author_id");
            TSession::setValue('BookList_filter_author_id', $filter);
        }

        if (isset($data->publisher_id) AND ($data->publisher_id)) {
            $filter = new TFilter('publisher_id', 'like', "%{$data->publisher_id}%");
            TSession::setValue('BookList_filter_publisher_id', $filter);
        }

        $this->form->setData($data);

        TSession::setValue('Book_filter_data', $data);

        $param=array();
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }

    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('livraria-adianti');

            $repository = new TRepository('Book');
            $limit = 10;
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('BookList_filter_id')) {
                $criteria->add(TSession::getValue('BookList_filter_id'));
            }

            if (TSession::getValue('BookList_filter_name')) {
                $criteria->add(TSession::getValue('BookList_filter_name'));
            }

            if (TSession::getValue('BookList_filter_price')) {
                $criteria->add(TSession::getValue('BookList_filter_price'));
            }
           
            if (TSession::getValue('BookList_filter_published_year')) {
                $criteria->add(TSession::getValue('BookList_filter_published_year'));
            }

            if (TSession::getValue('BookList_filter_author_id')) {
                $criteria->add(TSession::getValue('BookList_filter_author_id'));
            }
            
            if (TSession::getValue('BookList_filter_publisher_id')) {
                $criteria->add(TSession::getValue('BookList_filter_publisher_id'));
            }

            $objects = $repository->load($criteria, FALSE);

            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }

            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param);

        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }

    public function Delete($param)
    {
        try
        {
            $key=$param['key'];
            TTransaction::open('livraria-adianti');
            $object = new Book($key, FALSE);
            $object->delete();
            TTransaction::close();
            $this->onReload( $param );
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
