<?php
/**
 * Interface Fizzy_Geocode_Adapter_Interface
 * @package Fizzy_Geocode
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

/**
 * Interface to be used by all adapters for Fizzy_Geocode
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
interface Fizzy_Geocode_Adapter_Interface
{
    public function getQuery();
    public function setQuery($query);
    public function location($query = null);
    
}