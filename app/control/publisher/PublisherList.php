<?php

class PublisherList extends TStandardList
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
        parent::setActiveRecord('Publisher');  
        parent::setDefaultOrder('id', 'asc');         
        parent::addFilterField('id', '=', 'id'); 
        parent::addFilterField('name', 'like', 'name');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Publisher');
        $this->form->setFormTitle('Editoras');
        
        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $countryOfOrigin = new TEntry('country_of_origin');
        $foundedDate = new TEntry('founded_date');
        
        // add the fields
        $this->form->addFields([new TLabel('Id:')],[$id]);
        $this->form->addFields([new TLabel('Nome:')],[$name]);
        $this->form->addFields([new TLabel('País de Origem:')],[$countryOfOrigin]);
        $this->form->addFields([new TLabel('Data de Criação:')],[$foundedDate]);
        
        $id->setSize('20%');
        $name->setSize('70%');
        $countryOfOrigin->setSize('70%');
        $foundedDate->setSize('70%'); 

        $foundedDate->setMask('99/99/9999');
         
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Publisher_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Novo',  new TAction(array('PublisherForm', 'onEdit')), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_name = new TDataGridColumn('name', 'Nome', 'left');
        $column_country_of_origin = new TDataGridColumn('country_of_origin', 'Origem', 'left');
        $column_founded_date = new TDataGridColumn('founded_date', 'Dt. de Criação', 'left');

        $column_name->setTransformer( function($value, $object, $row) {
            return strtoupper($value);
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_country_of_origin);
        $this->datagrid->addColumn($column_founded_date);

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('PublisherForm', 'onEdit'));
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

    public function onInlineEdit($param)
    {
        try
        {
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];

            TTransaction::open('samples');
            $object = new Publisher($key);
            $object->{$field} = $value;
            $object->store();
            TTransaction::close();

            $this->onReloaded($param);
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSearch($param = NULL)
    {
        $data = $this->form->getData();

        TSession::setValue('PublisherList_filter_id', NULL);
        TSession::setValue('PublisherList_filter_name', NULL);
        TSession::setValue('PublisherList_filter_country_of_origin', NULL);
        TSession::setValue('PublisherList_filter_founded_date', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id");
            TSession::setValue('PublisherList_filter_id', $filter);
        }

        if (isset($data->name) AND ($data->name)) {
            $filter = new TFilter('name', 'like', "%{$data->name}%");
            TSession::setValue('PublisherList_filter_name', $filter);
        }

        if (isset($data->country_of_origin) AND ($data->country_of_origin)) {
            $filter = new TFilter('country_of_origin', 'like', "%{$data->country_of_origin}%");
            TSession::setValue('PublisherList_filter_country_of_origin', $filter);
        }

        if (isset($data->founded_date) AND ($data->founded_date)) {
            $filter = new TFilter('founded_date', 'like', "%{$data->founded_date}%");
            TSession::setValue('PublisherList_filter_founded_date', $filter);
        }

        $this->form->setData($data);

        TSession::setValue('Publisher_filter_data', $data);

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

            $repository = new TRepository('Publisher');
            $limit = 10;
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('PublisherList_filter_id')) {
                $criteria->add(TSession::getValue('PublisherList_filter_id'));
            }

            if (TSession::getValue('PublisherList_filter_name')) {
                $criteria->add(TSession::getValue('PublisherList_filter_name'));
            }

            if (TSession::getValue('PublisherList_filter_country_of_origin')) {
                $criteria->add(TSession::getValue('PublisherList_filter_country_of_origin'));
            }

            if (TSession::getValue('PublisherList_filter_founded_date')) {
                $criteria->add(TSession::getValue('PublisherList_filter_founded_date'));
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
            $object = new Publisher($key, FALSE);
            $object->delete();
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
