<?php
class Book extends TRecord
{
    const TABLENAME = 'book';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';

    private $publisher;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('published_date');
        parent::addAttribute('publisher_id');
    }

    public function set_publisher(Publisher $object)
    {
        $this->publisher = $object;
        $this->publisher_id = $object->id;
    }

    public function get_publisher()
    {
        if(empty($this->publisher))
            $this->publisher = new Publisher($this->publisher_id);

        return $this->publisher;
    }
}