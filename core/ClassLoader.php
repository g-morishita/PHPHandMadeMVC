<?php
class ClassLoader 
{
    // directories to require classes
    private $dirs;
    
    // Singleton 
    private static $classLoader;

    private function __construct($dirs) 
    {
        $this->dirs = $dirs;
    }

    public function getInstance($dirs) 
    {
        if (!isset(self::$classLoader)) {
            self::$classLoader = new ClassLoader($dirs);
        }
        return self::$classLoader;
    }

    // register a function to be called when you try to use classes you do not include(require)
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    // require $class among the directories that are in $dirs
    private function loadClass($class)
    {
        foreach ( $this->dirs as $dir ) {
            $file = $dir . '/' . $class . '.php';
            if ( is_readable($file) ) {
                require $file; 
                return;
            }
        } 
    }

    // override __clone to ban the use of clone 
    public final function __clone()
    {
        throw new RuntimeException('Clone is not allowed against' . get_class($this));
    }
}
