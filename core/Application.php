<?php

abstract class Application
    $debug = false;
    $request;
    $response;
    $session;
    $db_manager;
    $login_action = [];

{
    public function __construct($debug = false) 
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set("display_errors", 1);
            error_reporting(-1);
        } else {
            ini_set("display_errors", 0);
        }
    }

    protected function initialize()
    {
        $this->request    = new Request();
        $this->response   = new Response();
        $this->session    = new Session();
        $this->db_manager = new DbManager();
        $this->router     = new Router($this->registerRoutes());
    }
    
    protected function configure()
    {
    }

    abstract function getRootDir();
    
    abstract protected function registerRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }
    
    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }
    
    public function getDbManager()
    {
        return $this->DbManager;
    }

    public function getCotrollerDir()
    {
        return $this->getRootDir() . "/controller";
    }

    public function getViewDir()
    {
        return $this->getRootDir() . "/views";
    }
    
    public getModelDir()
    {
        return $this->getRootDir() . "/models";
    }

    public function getWebDir()
    {
        return $this->getRootDir() . "/web";
    }

    public function run()
    {
        try {
            $param = $this->router->resolve($this->request->getPathInfo());
            
            if ($param === false) {
                throw new HttpNotFoundException("No route found for:" . $this->request->getPathInfo());
            } 

            $controller = $param["controller"];
            $action     = $param["action"];

            $this->runAction($controller, $action, $param);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }
        $this->response->send();
    }
    
    public function runAction($controller_name, $action, $params = []) 
    {
        $controller_class = ucfirst($controller_name) . "Controller";
        
        $controller = $this->findController($controller_class);
        
        if ($controller === false) {
            throw new HttpNotFoundException($controller_class . " controller is not found");
        }
        
        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }
    
    protected function findControlle($controller_class)
    {
        if(!class_exists($controller_class)) {
            $controller_file = this->getCotrollerDir() . '/' . $controller_class;
            if ( !is_readable($controller_file) ) {
                return false;
            } else {
                require_once $controller_file;
                    
                if ( !class_exists($controller_class) ) {
                    return false;
                }
            }
        }
        return new $controller_class($this);
    }

    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, "Not Found");
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page Not Found';
        $message = htmlspecialchars($message, ENT_QUOTES, "UTF-8");
        
        $this->response->setContent(<<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404</title>
</head>
<body>
   {$message} 
</body>
</html>
EOF
        );
    }
}
