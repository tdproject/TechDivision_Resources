<?PHP

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Resources is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Resources is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Resources
 */

require_once 'TechDivision/Collections/HashMap.php';
require_once 'TechDivision/Collections/Interfaces/Collection.php';
require_once 'TechDivision/Util/SystemLocale.php';
require_once 'TechDivision/Lang/String.php';
require_once 'TechDivision/Properties/Properties.php';
require_once 'TechDivision/Resources/AbstractResourceBundle.php';

/**
 * This class is a container for the resources and
 * provides methods for handling them.
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_Resources_PropertyResourceBundle
    extends TechDivision_Resources_AbstractResourceBundle {

	/**
	 * Holds the optional separator for the filename and the locale key.
	 * @var string
	 */
	const SEPARATOR = '_';

	/**
	 * Path to the property file with the resource properties.
	 * @var TechDivision_Lang_String
	 */
	private $config = null;

    /**
     * The initialized resource properties,
     * @var TechDivision_Properties_Properties
     */
	private $properties = null;

	/**
	 * Initializes the resource bundle with the
	 * property resources from the passed file
	 * and the locale to use.
	 *
	 * @param TechDivision_Lang_String $config
	 * 		The path to the property file with the resources
	 * @param TechDivision_Util_SystemLocale $systemLocale
	 * 		The system locale to use
	 * @return void
	 */
	protected function __construct(
	    TechDivision_Lang_String $config,
	    TechDivision_Util_SystemLocale $systemLocale) {
		TechDivision_Resources_AbstractResourceBundle::__construct(
		    $systemLocale
		);
		$this->_config = $config;
	}

	/**
	 * This method initializes the property resource bundle for
	 * the properties with the passed name and locale.
	 *
	 * @param TechDivision_Lang_String $config
	 * 		Holds the name of the property file to load the resources from
	 * @param TechDivision_Util_SystemLocale $systemLocale
	 * 		Holds the system locale of the property resource bundle to load
	 * @return TechDivision_Resources_PropertyResourceBundle
	 * 		Holds the initialized property resource bundle
	 */
	public static function getBundle(
	    TechDivision_Lang_String $config,
	    TechDivision_Util_SystemLocale $systemLocale = null) {
		if($systemLocale == null) {
			$systemLocale = TechDivision_Util_SystemLocale::getDefault();
		}
		$bundle = new TechDivision_Resources_PropertyResourceBundle(
		    $config,
		    $systemLocale
		);
		$bundle->initialize();
		return $bundle;
	}

	/**
	 * This method parses the resource file depending on
	 * the actual locale and sets the values in the internal
	 * array.
	 *
	 * The separator default value is . but can be changed
	 * to some other value by passing it as paramter to this
	 * method.
	 *
	 * For example the filename for an application could be
	 * 'applicationresources.en_US.properties'.
	 *
	 * @return void
	 */
	public function initialize() {
		// add the Locale as string
		$this->_config .=
		    TechDivision_Resources_PropertyResourceBundle::SEPARATOR .
		    TechDivision_Resources_AbstractResourceBundle::getSystemLocale()
		        ->__toString() .
		    ".properties";
		// initialize and load the Properties
		$this->properties = new TechDivision_Properties_Properties();
		$this->properties->load($this->_config);
	}

	/**
	 * This method disconnects from the
	 * database and frees the memory.
	 *
	 * @return void
	 */
	public function destroy() {
		// @TODO Still to implement
	}

    /**
     * This method searches in the container
     * for the resource with the key passed
     * as parameter.
     *
     * @param string $name Holds the key of the requested resource
     * @param TechDivision_Collections_ArrayList $parameter
     * 		Holds an ArrayList with parameters with replacements for the
     * 		placeholders in the resource string
     * @return string Holds the requested resource value
     */
    public function find(
        $name,
        TechDivision_Collections_ArrayList $parameter = null) {
		// initialize an find the resource string
		$resource = "";
		// get the value for the property with the passed key
        if(($property = $this->properties->getProperty($name)) != null) {
            $resource = $property;
        }
        // check if parameter for replacement are passed
        if($parameter != null) {
			// replace the placeholders with the passed parameter
			foreach($parameter as $key => $value) {
				$resource = str_replace('{' . $key . '}', $value, $resource);
			}
        }
		// return the resource string
		return $resource;
    }

    /**
     * This method replaces the resource string with the
     * passed key in the resource file.
     *
     * If the resource string does not yes exist, the
     * value will be attached
     *
     * @param string $name Holds the name of the resource string to replace
     * @param string $value Holds the value to replace the original one with
     * @return void
     */
    public function replace($name, $value) {
		$this->properties->add($name, $value);
    }

    /**
     * This method attaches the resource string with the
     * passed key in the resource file.
     *
     * If the resource string already exists, the old one
     * will be kept and the function returns FALSE, else
     * it returns TRUE
     *
     * @param string $name Holds the name of the resource string to replace
     * @param string $value Holds the value to replace the original one with
     * @return boolean
     * 		FALSE if a resource string with the passed name already exists
     */
    public function attach($name, $value) {
		if(!$this->properties->exists($name)) {
			$this->properties->add($name, $value);
			return true;
		}
		// return FALSE if a resource string with the passed name already exists
		return false;
    }

    /**
     * This method returns the first key found for
     * the passed value.
     *
     * @param string $value Holds the resource value to return the key for
     * @return string Holds the resource key for the passed value
     */
    public function findKeyByValue($value) {
		// check if the value exists in the resource bundle
		$key = array_search($value, $this->properties->toIndexedArray());
        if($key === false) {
			return;
		}
		// return the found key
		return $key;
    }

    /**
     * This method returns the number of resources in the container.
     *
     * @return integer Number of resources in the container
     */
    public function count() {
        return $this->properties->size();
    }

    /**
     * This method saves the resource string back to
     * the resource file.
     *
     * @return void
     */
    public function save() {
		$this->properties->store($this->_config);
    }

	/**
	 * Returns the keys of this resource bundle instance
	 * as an ArrayList.
	 *
	 * @return TechDivision_Collections_ArrayList
	 * 		Holds the keys of this resource bundle instance
	 */
    public function getKeys() {
    	// initialize the ArrayList with the keys of the property file
    	return new TechDivision_Collections_ArrayList(
    	    $this->properties->getKeys()
    	);
    }
}