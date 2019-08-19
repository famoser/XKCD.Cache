<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 23/05/2016
 * Time: 21:15
 */

namespace Famoser\XKCDCache\Entities\Base;


/**
 * the base database entry, has an id, and allows for the overriding class to specify a table name
 * @package Famoser\XKCDCache\Entities\Base
 */
abstract class BaseEntity
{
    /* @var int $id */
    public $id;

    /**
     * get the name of the table from the database
     *
     * @return string
     */
    abstract public function getTableName();
}
