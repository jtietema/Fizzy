<?php
/**
 * Class Fizzy_Route_Simple
 * @package Fizzy
 * @subpackage Route
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

/** Fizzy_Route_Abstract */
require_once 'Fizzy/Route/Abstract.php';

/**
 * Simple route class. Subsitutes named parameters and wildcards.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Route_Simple extends Fizzy_Route_Abstract
{
    /**
     * Map for the named parameters.
     * @var array
     */
    protected $_parameterMap = array();

    /** **/

    /**
     * Override of {@see Fizzy_Route_Abstract::__construct}.
     * @param Fizzy_Router $router
     * @param array $config
     */
    public function __construct(Fizzy_Router $router, array $config)
    {
        parent::__construct($router, $config);
        $this->_assemble();
    }

    /**
     * Match the route against the request.
     * @param Fizzy_Request $request
     * @return boolean
     */
    public function match(Fizzy_Request $request)
    {
        $rule = $this->getRule();
        $path = $request->getPath();

        // See if the route matches the path
        $matchCount = preg_match_all("#{$rule}#i", $path, $routeMatches, PREG_SET_ORDER);
        if(0 >= $matchCount) {
            return false;
        }
        
        // Slice the complete match of the matches array
        $parameters = array_slice($routeMatches[0], 1);

        // Add named parameters
        $parameters = array_merge($parameters, array_combine($this->_parameterMap, $parameters));

        // Inject the request object
        $request->setController($this->_camelCase($this->getController()));
        $request->setAction($this->_camelCase($this->getAction()));
        $request->addParameters($parameters);

        return true;
    }

    /**
     * Assemble the route by repacing named parameters and wildcards.
     * @return string
     */
    protected function _assemble()
    {
        //$namedParameterPattern   = "(?:/([^\/]*))?";
        $namedParameterPattern   = "([^\/]*)";

        $namedParameters = array();
        $parsedParts = array();

        $ruleParts = preg_split('/\//', $this->getRule(), -1, PREG_SPLIT_NO_EMPTY);
        foreach($ruleParts as $part) {
            // Named variables
            if(0 === strpos($part, ':')) {
                if(preg_match('/^:([^\:]+)$/', $part, $matches)) {
                    $parsedParts[] = $namedParameterPattern;
                    $namedParameters[] = $matches[1];
                };
            }
            else {
                $parsedParts[] = $part;
            }
        }

        $this->_parameterMap = $namedParameters;
        $this->_rule = implode('/', $parsedParts);

        return $this->_rule;
    }

}
