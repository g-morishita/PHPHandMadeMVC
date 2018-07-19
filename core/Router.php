<?php

class Router
{
    protected $routes;

    public function __construct($defenitions)
    {
        $this->routes = $this->compileRoutes($defenitions); 
    }
    
    public function compileRoutes($defenitions)
    {
        $routes = [];
    
        foreach ( $defenitions as $url => $params ) {
            $tokens = explode('/', ltrim($url, '/')); 
            foreach ( $tokens as $i => $token ) {
                if ( 0 === strpos(':', $token )) {
                    $name = substr($token, 1);
                    $token = "?P<" . $name . ">[^/]+";
                }
                $tokens[$i] = $token;
            }
            $patterns = '/' . implode('/', $tokens);
            $routes[$pattern] = $param;
        }
        return $routes;
    }
    
    public function resolve($path_info)
    {
        if ( '/' !== substr($path_info, 0, 1) ) {
            $path_info = '/' . $path_info;
        }
        
        foreach ( $this->routes as $pattern => $params ) {
            if ( preg_match('#^' . $pattern . '?$', $path_info, $matches) {
                $param = array_merge($param, $matches);
            }
            return $param; 
        }
        return false;
    }
}
