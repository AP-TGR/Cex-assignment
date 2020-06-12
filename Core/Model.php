<?php

namespace Core;

use App\Config;
use Medoo\Medoo;

/**
 * Base model
 */
abstract class Model
{
    /**
     * The database connection
     *
     * @var null|Medoo
     */
    protected $_database = null;

    /**
     * Get the Medoo database connection
     *
     * @return mixed
     */
    public function __construct()
    {
        // Initialize
        $this->_database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => Config::DB_NAME,
            'server' => Config::DB_HOST,
            'username' => Config::DB_USER,
            'password' => Config::DB_PASSWORD
        ]);
    }
}
