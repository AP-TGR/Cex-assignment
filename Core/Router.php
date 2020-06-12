<?php

namespace Core;

use AltoRouter;

/**
 * Router
 */
class Router extends AltoRouter
{
    /**
     * Parameters from the matched route
     * @var array
     */
    protected $_params = [];

    /**
     * Dispatch the route, creating the controller object and running the
     * action method
     *
     * @return void
     */
    public function dispatch()
    {
        $match = $this->match();
        if (!$match) {
            throw new \Exception('No route matched.', 404);
        }

        list( $controller, $action ) = explode( '#', $match['target'] );
        $this->_params = $match['params'];
        $controller = $this->convertToStudlyCaps($controller);
        $controller = $this->getNamespace() . $controller;
        if (!class_exists($controller)) {
            throw new \Exception("Controller class $controller not found");
        }

        $controller_object = new $controller($this->_params);
        $action = $this->convertToCamelCase($action);

        if (preg_match('/action$/i', $action) == 0) {
            $controller_object->$action($this->_params);
        } else {
            throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
        }
    }

    /**
     * Convert the string with hyphens to camelCase,
     * e.g. add-new => addNew
     *
     * @param string $string The string to convert
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Get the namespace for the controller class. The namespace defined in the
     * route parameters is added if present.
     *
     * @return string The request URL
     */
    protected function getNamespace()
    {
        return 'App\Controllers\\';
    }
}
