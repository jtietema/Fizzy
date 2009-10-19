<?php
/**
 * Class PagesController
 */

/** Fizzy_Controller */
require_once 'Fizzy/Controller.php';

/** Fizzy_Storage */
require_once 'Fizzy/Storage.php';

/** Page **/
require_once 'Page.php';

/**
 * Pages controller.
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class PageController extends Fizzy_Controller
{
    /**
     * Pages storage object.
     * @var Fizzy_Storage
     */
    protected $_storage = null;

    /** **/

    protected function _init()
    {
        $config = Fizzy_Config::getInstance();
        $storageOptions = $config->getConfiguration('storage');
        $this->_storage = new Fizzy_Storage($storageOptions);
    }

    public function defaultAction()
    {
        echo "<p>This is PagesController::defaultAction().</p>";
        
        echo "<p>Parameters:<br />";
        echo "<pre>";
        print_r($this->_getParams());
        echo "</pre></p>";
    }

    public function homepageAction()
    {
        $model = $this->_storage->fetchOne('page', '1');
        var_dump($model);
    }

    public function showAction()
    {
        $slug = $this->_getParam('slug');
        $model = $this->_storage->fetchColumn('page', 'slug', $slug);

        if(null !== $model) {
            var_dump($model);
            var_dump($model->getId());
            $this->getView()->disable();
        }
        else {
            $this->getView()->setScript('page/notfound.phtml');
        }
    }

    public function listAction()
    {
        echo "<p>This is PagesController::listAction().</p>";

        echo "<p>Parameters:<br />";
        echo "<pre>";
        print_r($this->_getParams());
        echo "</pre></p>";
    }

    public function slugAction()
    {
        echo "<p>This is PagesController::slugAction().</p>";
        
        echo "<p>Parameters:<br />";
        echo "<pre>";
        print_r($this->_getParams());
        echo "</pre></p>";
    }

}