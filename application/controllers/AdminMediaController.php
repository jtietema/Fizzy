<?php
/**
 * Class AdminMediaController
 * @package Fizzy
 * @subpackage Controller
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.voidwalkers.nl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@voidwalkers.nl so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/** SecureController */
require_once 'SecureController.php';

/**
 * Controller for media inside Fizzy. Enables basic upload and deletion of files.
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class AdminMediaController extends SecureController
{
    
    public function defaultAction()
    {
        $uploadFolder = Fizzy_Config::getInstance()->getPath('uploads');
        
        if($this->getRequest()->getMethod() === Fizzy_Request::METHOD_POST)
        {
            if(isset($_FILES) && !empty($_FILES['upload'])) {
                $overwrite = ((boolean) isset($_POST['overwrite']) && !empty($_POST['overwrite']));
                $messages = $this->_handleUpload('upload', $uploadFolder, $overwrite);
                $this->getView()->messages = $messages;
            }
        
        }
        
        // Parse all files in the upload directory
        $files = array();
        foreach(new DirectoryIterator($uploadFolder) as $file) 
        {
            if($file->isFile()) 
            {
                $fileInfo = array(
                    'type' => substr(strrchr($file->getBaseName(), '.'), 1),
                    'basename' => $file->getBaseName(),
                    'path' => $file->getPath(),
                    'size' => $file->getSize(),
s                );
                $files[] = (object) $fileInfo;
            }
        }
        
        // Render the view
        $this->getView()->files = $files;
        $this->getView()->setScript('/admin/media/list.phtml');
    }
    
    /**
     * Deletes an uploaded file.
     */
    public function deleteAction()
    {
        $name = $this->_getParam('name');
        
        if(null !== $name) 
        {
            $name = basename(urldecode($name));
            $uploadFolder = Fizzy_Config::getInstance()->getPath('uploads');
            $file = $uploadFolder . DIRECTORY_SEPARATOR . $name;
            if(is_file($file))
            {
                unlink($file);
            }
        }
        
        $this->_redirect('/admin/media');
    }
    
    
    public function uploadAction()
    {
        $this->_redirect('/admin/media');
    }
    
    /**
     * Handles a file upload.
     * @param string $name
     * @param string $uploadFolder
     * @return array
     */
    protected function _handleUpload($name, $uploadFolder, $overwrite = false)
    {
        $file = $_FILES[$name];
        if ($file['error'] == UPLOAD_ERR_OK) {
            $target = $file["tmp_name"];
            $destination = $uploadFolder . DIRECTORY_SEPARATOR . $file["name"];
            if(!file_exists($destination) || $overwrite)
            {
                move_uploaded_file($target, $destination);
            }
            else 
            {
                unlink($target);
            }
            return array();
        }
        else {
            switch($file['error'])
            {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return array('The uploaded file exceeds the Maximum upload filesize.');
                break;
                case UPLOAD_ERR_PARTIAL:
                    return array('The uploaded file was only partially uploaded.');
                break;
                case UPLOAD_ERR_NO_FILE:
                    return array('No file was uploaded.');
                break;
                default:
                    return array('An internal server error occurred.');
            }
        }
        
    }
    
}
