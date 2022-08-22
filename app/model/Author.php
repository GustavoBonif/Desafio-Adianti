<?php
class Author extends TRecord
{
    const TABLENAME = 'author';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('birthday');
        parent::addAttribute('gender');
    }
}