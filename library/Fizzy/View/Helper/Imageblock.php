<?php
/**
 * Class Fizzy_View_Helper_Image
 * @package Fizzy
 * @subpackage View
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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

class Fizzy_View_Helper_Imageblock extends Zend_View_Helper_FormElement
{
    public function imageblock($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        
        $prependBase = true;
        if (isset($attribs['prependBase'])) {
            $prependBase = (boolean) $attribs['prependBase'];
            unset($attribs['prependBase']);
        }

        if ($prependBase) {
            if (null == $this->view) {
                $this->view = new Zend_View();
            }
            $value = $this->view->baseUrl($value);
        }

        $xhtml = <<<XHTML
<div class="image-block">
    <img id="{$this->view->escape($id)}" src="$value" />
</div>
<script type="text/javascript">
    fizzy.image.register("{$this->view->escape($id)}");
</script>
XHTML;
        return $xhtml;
    }
}