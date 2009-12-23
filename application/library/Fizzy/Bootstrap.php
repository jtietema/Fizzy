<?php

/** Zend_Config */
require_once 'Zend/Config.php';

/**
 * Bootstrap class for Fizzy.
 *
 * @todo find a better way to support environments
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Bootstrap
{

    /**
     * The bootstrap stages in order of execution and their return values.
     * @var array
     */
    protected $_bootstrap = array (
        'autoLoader' => null,
        'registry' => null,
#        'logger' => null,
        'session' => null,
        'frontController' => null,
        'router' => null,
        'storage' => null,
        'view' => null,
        'layout' => null,
    );
    
    /**
     * The final configuration for Fizzy created from merging the default 
     * configuration with the custom configuration.
     * @var Zend_Config
     */
    protected $_config = null;

    /**
     * Default Fizzy config
     * @var array
     */
    protected $_defaultConfig = array (
        'application' => array(
            'title' => 'Fizzy',
            'basePath' => '',
            'defaultLayout' => 'fizzy',
            'defaultTemplate' => 'page.phtml',
            'defaultController' => 'index',
            'defaultAction' => 'index',
        ),
        'routes' => array (
            # Catch all for pages slugs
            'page_by_slug' => array (
                'route' => '/:slug',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'slug'
                )
            ),
            # Admin pages control
            'admin_pages' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/pages',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'index',
                    'module' => 'admin'
                )
            ),
            'admin_pages_add' => array(
                'route' => '/fizzy/pages/add',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'add',
                    'module' => 'admin'
                )
            ),
            'admin_pages_edit' => array(
                'route' => '/fizzy/pages/edit/:id',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'edit',
                    'module' => 'admin'
                )
            ),
            'admin_pages_delete' => array(
                'route' => '/fizzy/pages/delete/:id',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'delete',
                    'module' => 'admin'
                )
            ),
            # Admin media
            /*'admin_media_delete' => array(
                'route' => '/admin/media/delete/:name',
                'defaults' => array (
                    'controller' => 'admin_media',
                    'action' => 'delete'
                )
            ),*/
            'admin_media' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/media',
                'defaults' => array (
                    'controller' => 'media',
                    'module' => 'admin'
                )
            ),
            # Admin users
            /*'admin_users' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'rule' => '/admin/users',
                'defaults' => array (
                    'controller' => 'admin_users',
                    'action' => 'index'
                )
            ),*/
            /*'admin_users_add' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'rule' => '/admin/users/add',
                'defaults' => array (
                    'controller' => 'admin_users',
                    'action' => 'add'
                )
            ),*/
            /*'admin_users_edit' => array(
                'rule' => '/admin/users/edit/:id',
                'defaults' => array (
                    'controller' => 'admin_users',
                    'action' => 'edit'
                )
            ),*/
            /*'admin_users_delete' => array(
                'rule' => '/admin/users/delete/:id',
                'defaults' => array (
                    'controller' => 'admin_users',
                    'action' => 'delete'
                )
            ),*/
            # Static admin routes
            /*'admin_configuration' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/admin/configuration',
                'defaults' => array (
                    'controller' => 'admin',
                    'action' => 'configuration'
                )
            ),*/
            /*'admin_logout' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/admin/logout',
                'defaults' => array (
                    'controller' => 'admin',
                    'action' => 'logout'
                )
            ),*/
            /*'admin_login' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/admin/login',
                'defaults' => array (
                    'controller' => 'admin',
                    'action' => 'login'
                )
            ),*/
            'admin' => array (
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy',
                'defaults' => array (
                    'controller' => 'index',
                    'action' => 'index',
                    'module' => 'admin'
                )
            ),
        ),
        'paths' => array(
            'application' => 'application',
            'controllers' => array (
                'default' => 'application/modules/default/controllers',
                'admin' => 'application/modules/admin/controllers',
                'custom' => 'custom/controllers',
            ),
            'models' => 'application/models',
            'views' => array(
                'fizzy' => 'application/views',
                'custom' => 'custom/views'
            ),
            'layouts' => array(
                'fizzy' => 'application/layouts',
                'custom' => 'custom/layouts',
            ),
            'templates' => array (
                'fizzy' => 'application/templates',
                'custom' => 'custom/templates'
            ),
            'assets' => 'application/assets',
            'configs' => 'configs',
            'custom' => 'custom',
            'data' => 'data',
            'log' => 'data/fizzy.log',
            'library' => 'library',
            'public' => 'public',
            'uploads' => 'public/uploads',
        ),
    );

    /**
     * The environment to run the application in.
     * @var string
     */
    protected $_environment = 'production';

    /** **/

    /**
     * Constructs a new bootstrap for Fizzy. A config for Fizzy can be provided
     * to override or complement the default configuration.
     * @param mixed $config
     * @param string $environment
     */
    public function  __construct($config = null, $environment = 'production')
    {
        $finalConfig = new Zend_Config($this->_defaultConfig, true);
        # Set default basePath in case none is available in the custom config
        $finalConfig->application->basePath = realpath(dirname(__FILE__) . '/../../../');

        # Merge the custom configuration with the defaults
        if(null !== $config) {
            if(is_array($config)) {
                $customConfig = new Zend_Config($config);
            }
            else if($config instanceof Zend_Config) {
                $customConfig = $config;
            }
            else if(is_string($config)) {
                $customConfig = $this->_loadConfigFromFile($config);
            }
            $finalConfig = $finalConfig->merge($customConfig);
            unset($customConfig, $config);
        }
        $this->_config = $finalConfig;

        # Correct realtive paths in config
        $paths = $this->_correctPaths($this->_config->paths->toArray());
        $this->_config->paths = new Zend_Config($paths, true);
        unset($paths);

        # Load a clean PHP environment
        $this->_environment = $environment;
        $this->_loadEnvironment();
    }

    /**
     * Loads configuration from a file. INI and XML files are supported through
     * Zend_Config_Ini and Zend_Config_XML.
     * @param string $path
     * @return Zend_Config
     */
    protected function _loadConfigFromFile($path)
    {
        if(!is_file($path) || !is_readable($path)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception('Could not read config from ' . $path);
        }

        # Check extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'ini':
                require_once 'Zend/Config/Ini.php';
                $config = new Zend_Config_Ini($path);
                break;
            case 'xml':
                require_once 'Zend/Config/Xml.php';
                $config = new Zend_Config_Xml($path);
                break;
            default:
                require_once 'Fizzy/Exception.php';
                throw new Fizzy_Exception('Config type ' . $extension . ' not supported.');
        }

        return $config;
    }

    /**
     * Corrects relative paths by prefixing them with the configured base path.
     *
     * @param array|string $value
     * @return array|string
     */
    protected function _correctPaths($value)
    {
        if(is_array($value)) {
            foreach($value as $childKey => $childValue) {
                $value[$childKey] = $this->_correctPaths($childValue);
            }
        }
        else {
            if(0 !== strpos($value, DIRECTORY_SEPARATOR)) {
                $value = $this->_config->application->basePath . DIRECTORY_SEPARATOR . $value;
            }
        }

        return $value;
    }

    /**
     * Returns the configuration for Fizzy.
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * Shortcut to get a path by it's key from the configuration.
     * @param strubg $key
     * @return string|null
     */
    public function getPath($key)
    {
        $section = $this->_config->paths->get($key, null);
        if(null === $section) {
            return null;
        }
        
        if($section instanceof Zend_Config) {
            $section = $section->toArray();
        }
        
        return $section;
    }

    /**
     * Returns the bootstrap environment.
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Initializes the PHP environment.
     */
    protected function _loadEnvironment()
    {
        # Strip all injected slashes by magic_quotes_gpc
        if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()){
            stripslashes_deep($_GET);
            stripslashes_deep($_POST);
            stripslashes_deep($_REQUEST);
            stripslashes_deep($_COOKIE);
        }

        if('development' === $this->_environment)
        {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', true);
        }
        else {
            error_reporting(0);
            ini_set('display_errors', false);
        }
    }

    /**
     * Recursively strip slashes from array values.
     * @param string $value
     * @return array|string
     */
    protected function _stripslashesDeep(&$value)
    {
        $value = is_array($value) ? array_map(array($this, '_stripslashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    /**
     * Initializes the autoloader. The default namespaces registered are Zend_
     * and ZendX_. This stage adds the Fizzy_ namespace to that.
     * An Autoloader is added to the stack for Fizzy models.
     * @see Fizzy_Autoloader
     * @return Zend_Loader_Autoloader
     */
    protected function _initAutoLoader()
    {
        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Fizzy_');

        # Autoloading for models
        $autoloader->pushAutoloader(array('Fizzy_Autoloader', 'models'));

        return $autoloader;
    }

    /**
     * Registers a new Zend_Registry instance.
     * @return Zend_Registry
     */
    protected function _initRegistry()
    {
        $registry = Zend_Registry::getInstance();
        # Add config to the registry
        Zend_Registry::set('config', $this->_config);
        
        return $registry;
    }

    /**
     * Initializes a logger.
     * @todo implement with Zend_Log
     * @return Zend_Log
     */
    protected function _initLogger()
    {
        
    }

    protected function _initSession()
    {
        Zend_Session::start();
        return null;
    }

    /**
     * Initializes the front controller
     * @return Zend_Controller_Front
     */
    protected function _initFrontController()
    {
        $frontController = Zend_Controller_Front::getInstance();

        # Add configuration
        $frontController->setDefaultControllerName($this->_config->application->defaultController)
                        ->setDefaultAction($this->_config->application->defaultAction);
        
        # Add module directory for Fizzy core
        $frontController->addModuleDirectory($this->getPath('application') . DIRECTORY_SEPARATOR . 'modules');
        
        # Add custom folder as module
        $frontController->addControllerDirectory($this->getPath('custom') . DIRECTORY_SEPARATOR . 'controllers', 'custom');
        
        # Set error handling
        if('development' === $this->_environment) {
            # Allow exceptions and unregister the error handler plugin
            $frontController->throwExceptions(true);
            $frontController->unregisterPlugin('ErrorHandler');
        }
        else {
            $errorHandler = new Zend_Controller_Plugin_ErrorHandler(array (
                'controller' => 'error',
                'action' => 'error',
                'module' => 'default'
            ));
        }
        
        return $frontController;
    }

    /**
     * Loads the configured routes into the router.
     * @return Zend_Router
     */
    protected function _initRouter()
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        
        $routes = $this->_config->routes;
        $router->clearParams()
               ->addConfig($routes);

        return $router;
    }

    /**
     * Initializes the Fizzy storage backend
     * @return Fizzy_Storage
     */
    protected function _initStorage()
    {
        $storage = null;
        if(isset($this->_config->storage)) {
            $storage = new Fizzy_Storage($this->_config->storage->toArray());
            Zend_Registry::set('storage', $storage);
        }

        return $storage;
    }

    /**
     * Initializes a new view object and loads the view directories into it.
     * @return Zend_View
     */
    protected function _initView()
    {
        $view = new Zend_View();
        foreach($this->getPath('views') as $path) {
            $view->addBasePath($path);
        }

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        
        return $view;
    }

    /**
     * Starts Zend_Layout in MVC mode.
     * @todo add custom front controller plugin for layout allow multiple layouts
     * @return Zend_Layout
     */
    protected function _initLayout()
    {
        $layoutPaths = $this->getPath('layouts');
        $layout = new Zend_Layout($layoutPaths['fizzy'], true);
        $layout->setLayout($this->_config->application->defaultLayout);

        return $layout;
    }

    /**
     * Run the bootstrap sequence.
     * @param boolean $dispatch Dispatch immediately after bootstrapping is done.
     */
    public function run($dispatch = true)
    {
        # Run the bootstrap sequence
        foreach($this->_bootstrap as $stage => $value) {
            $methodName = '_init' . ucfirst($stage);
            if(is_callable(array($this, $methodName))) {
                $this->_bootstrap[$stage] = $this->$methodName();
            }
        }

        # Dispatch the request
        if($dispatch) {
            $this->_bootstrap['frontController']->dispatch();
        }
    }

    /**
     * Catch getXYZ calls and check if the result of a bootstrap stage is
     * requested.
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name,  $arguments)
    {
        if(0 === strpos($name, 'get')) {
            # Strip get from the method name
            $step = str_replace('get', '', $name);
            # Lowercase the first character
            $step = strtolower(substr($step, 0, 1)) . substr($step, 1);

            # Search for the bootstrap step
            if(array_key_exists($step, $this->_bootstrap)) {
                return $this->_bootstrap[$step];
            }
        }
    }

}
