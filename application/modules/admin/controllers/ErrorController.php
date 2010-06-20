<?php

class Admin_ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');

                $this->renderScript('error/404.phtml');
                break;
            default:
                // application error; display error page, but don't change
                // status code
                //
                // Mail the exception:
                $exception = $errors->exception;
                $body = "Exception log \n"
                        . $exception->getMessage(). "\n"
                        . "in file " . $exception->getFile() . " op regel " . $exception->getLine() . "\n\n"
                        . "Stacktrace: \n"
                        . $exception->getTraceAsString() . "\n\n"
                        . "GET:\n";
                foreach($_GET as $key => $value) {
                    $body .= $key . ': ' . $value ."\n";
                }
                $body .= "POST:\n";
                foreach($_POST as $key => $value) {
                    $body .= $key . ': ' . $value ."\n";
                }
                $body .= "SERVER:\n";
                foreach($_SERVER as $key => $value) {
                    $body .= $key . ': ' . $value ."\n";
                }
                mail("info@voidwalkers.nl", "Fizzy Exception", $body);

                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 500 Server error');
                
                $this->renderScript('error/500.phtml');
                break;
        }
    }
}