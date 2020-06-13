<?php

namespace Core;

use App\Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

/**
 * Base controller
 */
abstract class Controller
{
    /**
     * The request object
     *
     * @var HttpFoundationRequest
     */
    protected $_request;

    public function __construct()
    {
        $this->_request = HttpFoundationRequest::createFromGlobals();
        if (!$this->isValidRequest()) {
            $this->sendResponse(
                Config::HTTP_UNAUTHORIZED,
                'Unauthorize',
                null,
                ['You are not allowed to make this request.']
            );
        }

        if (0 === strpos($this->_request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($this->_request->getContent(), true);
            $this->_request->request->replace(is_array($data) ? $data : []);
        }
    }

    /**
     * Whethre the request is valid or not
     *
     * @return boolean
     */
    public function isValidRequest()
    {
        if (in_array($this->_request->getPathInfo(), Config::$excludeRoutesSecurity)) {
            return true;
        }

        return (new JWT())->isValid($this->_request->headers->get('Authorization'));
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], array_shift($args));
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($code, $message, $result = [], $errorList = null)
    {
        $response = new JsonResponse([
            'success' => $code,
            'message' => $message,
            'response' => $result,
            'errorList' => $errorList,
        ], $code);

        $response->send();
        exit;
    }
}
