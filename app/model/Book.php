<?php
class Book extends TRecord
{
    const TABLENAME = 'book';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';

    private $publisher;
    private $author;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('price');
        parent::addAttribute('published_year');
        parent::addAttribute('publisher_id');
        parent::addAttribute('author_id');
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

    public function set_author(Author $object)
    {
        $this->author = $object;
        $this->author_id = $object->id;
    }

    public function get_author()
    {
        if(empty($this->author))
            $this->author = new Author($this->author_id);

        return $this->author;
    }
}