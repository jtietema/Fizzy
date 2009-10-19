<?php

require_once 'SecureController.php';

/**
 * Description of AdminPagesController
 *
 * @author jeroen
 */
class AdminPagesController extends SecureController
{
    function defaultAction()
    {
        echo "default";
    }
}
