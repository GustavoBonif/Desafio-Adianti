<?php
class Publisher extends TRecord
{
    const TABLENAME = 'publisher';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('country_of_origin');
        parent::addAttribute('founded_date');
    }
}