<?php

class AuthorList extends TStandardList
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
        parent::setActiveRecord('author'); 
        parent::setDefaultOrder('id', 'asc'); 
        parent::addFilterField('id', '=', 'id'); 
        parent::addFilterField('name', 'like', 'name'); 
        
        $this->form = new BootstrapFormBuilder('form_Author');
        $this->form->setFormTitle('Autores');
        
        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $birthday = new TEntry('birthday');
        $gender = new TEntry('gender');

        $birthday->setMask('99/99/9999');
        
        // add the fields
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Nome')], [$name] );
        $this->form->addFields( [new TLabel('Nascido em')], [$birthday] );
        $this->form->addFields( [new TLabel('Gênero')], [$gender] );

        $id->setSize('20%');
        $name->setSize('70%');
        $birthday->setSize('70%');
        $gender->setSize('70%');
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Author_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Novo',  new TAction(array('AuthorForm', 'onEdit')), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center', 50);
        $column_name = new TDataGridColumn('name', 'Nome', 'left');
        $column_birthday = new TDataGridColumn('birthday', 'Idade', 'left');
        $column_gender = new TDataGridColumn('gender', 'Gênero', 'left');

        $column_name->setTransformer( function($value, $object, $row) {
            return strtoupper($value);
        });

        $column_gender->setTransformer( function($value, $object, $row)
        {
            switch ($value) {
                case 'F':
                    $mascara = 'Feminino';
                    break;
                case 'M':
                    $mascara = 'Masculino';
                    break;
                
                default:
                    $mascara = 'Outro';
                    break;
            }

            return $mascara;
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_birthday);
        $this->datagrid->addColumn($column_gender);

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);

        // create EDIT action
        $action_edit = new TDataGridAction(array('AuthorForm', 'onEdit'));
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

    // public function onInlineEdit($param)
    // {
    //     try
    //     {
    //         $field = $param['field'];
    //         $key   = $param['key'];
    //         $value = $param['value'];

    //         TTransaction::open('samples');
    //         $object = new Author($key);
    //         $object->{$field} = $value;
    //         $object->store();
    //         TTransaction::close();

    //         $this->onReloaded($param);
    //         new TMessage('info', "Record Updated");
    //     }
    //     catch (Exception $e)
    //     {
    //         new TMessage('error', $e->getMessage());
    //         TTransaction::rollback();
    //     }
    // }

    public function onSearch($param = NULL)
    {
        $data = $this->form->getData();

        TSession::setValue('AuthorList_filter_id', NULL);
        TSession::setValue('AuthorList_filter_name', NULL);
        TSession::setValue('AuthorList_filter_birthday', NULL);
        TSession::setValue('AuthorList_filter_gender', NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id");
            TSession::setValue('AuthorList_filter_id', $filter);
        }

        if (isset($data->name) AND ($data->name)) {
            $filter = new TFilter('name', 'like', "%{$data->name}%");
            TSession::setValue('AuthorList_filter_name', $filter);
        }

        if (isset($data->birthday) AND ($data->birthday)) {
            $filter = new TFilter('birthday', '=', "$data->birthday");
            TSession::setValue('AuthorList_filter_birthday', $filter);
        }

        if (isset($data->gender) AND ($data->gender)) {
            $filter = new TFilter('gender', 'like', "%{$data->gender}%");
            TSession::setValue('AuthorList_filter_gender', $filter);
        }

        $this->form->setData($data);

        TSession::setValue('Author_filter_data', $data);

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

            $repository = new TRepository('Author');
            $limit = 10;
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('AuthorList_filter_id')) {
                $criteria->add(TSession::getValue('AuthorList_filter_id'));
            }

            if (TSession::getValue('AuthorList_filter_name')) {
                $criteria->add(TSession::getValue('AuthorList_filter_name'));
            }

            if (TSession::getValue('AuthorList_filter_birthday')) {
                $criteria->add(TSession::getValue('AuthorList_filter_birthday'));
            }

            if (TSession::getValue('AuthorList_filter_gender')) {
                $criteria->add(TSession::getValue('AuthorList_filter_gender'));
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
            $object = new Author($key, FALSE);
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
