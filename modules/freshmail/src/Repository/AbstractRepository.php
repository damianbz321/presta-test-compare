<?php

namespace FreshMail\Repository;

use Db;

abstract class AbstractRepository
{
    /**
     * @var Db
     */
    protected $db;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->db = Db::getInstance();
    }
}