<?php

/**
 * @author Igor Banchikov
 * @todo This is the main controller class,
 * which manages all requests to the server
 * @use Singleton pattern
 */

class FrontController {
    /**
     * @var string $_controller needs to select the controller class to use
     * @var string $_action needs to select the method of using controller class
     * @var array() $_params is used to assign the values of this array to the parameters of selected method 
     * @var string $_body is used for the showing content
     * @var object $_instance is used in other controllers for the giving reference to the instance of this class
     */
    public $_controller, $_action, $_params, $_body;
    static $_instance;

    /**
     * @return object $_instance - the instance of the FrontController
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * @todo split the request uri to the array $split & put the values to the same vars
     * for example: uri = "petshop.loc/api/table" in this case
     * the _controller var = 'Api'
     * the _action var = 'table'
     * the _params get from $_POST array
     */
    private function __construct() {
        $request = $_SERVER['REQUEST_URI'];
        $splits = explode('/', trim($request, '/'));
        //Choose the controller
        $this->_controller = !empty($splits[0]) ? ucfirst($splits[0]) : 'Api';
        //Какой action использовать?
        $this->_action = !empty($splits[1]) ? $splits[1] : 'table';
        if (!empty($_POST)) {
            foreach ($_POST as $k => $v) {
                $this->_params[$k] = $v;
            }
        } else {
            $this->_params[] = null;
        }
    }

    /**
     * Very simple routing method
     * @throws Exception on unsearch the controller class, action method or if the conrtoller class does not implements IController interface
     */
    public function route() {
        if (class_exists('db\\'.$this->getController())) {
            $rc = new ReflectionClass('db\\'.$this->getController());
                if ($rc->hasMethod($this->getAction())) {
                    $controller = $rc->newInstance();
                    $method = $rc->getMethod($this->getAction());
                    if (!empty($this->_params)) {
//                        var_dump(json_decode($method->invokeArgs($controller, $this->_params)));
                        echo $method->invokeArgs($controller, $this->_params);
                    } else {
//                        var_dump(json_decode($method->invoke($controller)));
                        echo $method->invoke($controller);
                    }
                } else {
                  //TODO add the 404 redirection
                    throw new Exception("Action");
                }
        } else {
          //TODO add the 404 redirection
            throw new \Exception("Controller");
        }
    }

    public function getParams() {
        return $this->_params;
    }

    public function getController() {
        return $this->_controller;
    }

    public function getAction() {
        return $this->_action;
    }

    public function getBody() {
        return $this->_body;
    }

    /**
     * @todo output the body string to the body of page
     * @param string $body
     */
    public function setBody($body) {
        $this->_body = $body;
    }

}
