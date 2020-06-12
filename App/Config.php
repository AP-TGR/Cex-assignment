<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{
    /**
     * List of routes excluded to check for JWT authentication
     *
     * @var array
     */
    public static $excludeRoutesSecurity = [
        '/users/register',
        '/users/login',
    ];

    /**
     * Domain host
     * 
     * @var string
     */
    const BASE_URL = 'http://localhost/Cex-assignment/';

    /**
     * Database host
     * 
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * 
     * @var string
     */
    const DB_NAME = 'cex';

    /**
     * Database user
     * 
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * 
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * 
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * The key for JWT configuration
     * 
     * @var string
     */
    const JWT_KEY = 'iTqXHI0zbAnJCKDaobfhkM1f-6rmSpTfyZMRp_2tKI8';

    /**
     * Const for http response code
     */
    const HTTP_SUCCESS = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
}
