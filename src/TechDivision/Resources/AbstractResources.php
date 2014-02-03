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

require_once 'TechDivision/Lang/String.php';
require_once 'TechDivision/Util/SystemLocale.php';
require_once 'TechDivision/Resources/Interfaces/Resources.php';
require_once
	'TechDivision/Resources/Exceptions/SystemLocaleNotExistsException.php';

/**
 * Abstract class of all resources.
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
abstract class TechDivision_Resources_AbstractResources
    implements TechDivision_Resources_Interfaces_Resources {

	/**
	 * Holds the flag to return null if a requested propert does not exists.
	 * @var boolean
	 */
	private $_returnNull = true;

	/**
	 * Holds the default system locale of this resources.
	 * @var TechDivision_Util_SystemLocale
	 */
	private $_defaultSystemLocale = null;

	/**
	 * Holds the name of the resource bundle
	 * @var TechDivision_Lang_String
	 */
	private $_name = null;

	/**
	 * Holds the HashMap with the initialized ResourceBundle instances.
	 * @var TechDivision_Collections_HashMap
	 */
	private $_bundles = null;

	/**
	 * The constructor initializes the resources with the
	 * database connection to load the resources from.
	 *
	 * @param TechDivision_Lang_String $name
	 * 		Holds the logical name of the Resources to create
	 * @return void
	 */
	public function __construct(TechDivision_Lang_String $name)
	{
		// initialize the name
		$this->setName($name);
	}

	/**
	 * This method sets the name of the resource bundle.
	 *
	 * @param TechDivision_Lang_String $name The name of the resource bundle
	 */
	public function setName(TechDivision_Lang_String $name)
	{
		$this->_name = $name;
	}

	/**
	 * This method returns the name of the resource bundle.
	 *
	 * @return TechDivision_Lang_StringString The name of the resource bundle
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @see Resources::isReturnNull()
	 */
	public function isReturnNull()
	{
    	return $this->_returnNull;
	}

	/**
	 * @see Resources::setReturnNull($returnNull)
	 */
	public function setReturnNull($returnNull)
	{
    	$this->_returnNull = $returnNull;
	}

	/**
	 * Sets the default system locale for this resources.
	 *
	 * @param TechDivision_Util_SystemLocale $systemLocale
	 * 		The default system locale for this resources
	 */
	public function setDefaultSystemLocale(
	    TechDivision_Util_SystemLocale $systemLocale) {
		$this->_defaultSystemLocale = $systemLocale;
	}

	/**
	 * Returns the default system locale for this resources.
	 *
	 * @return TechDivision_Util_SystemLocale
	 * 		The default system locale for this resources
	 */
	public function getDefaultSystemLocale()
	{
		return $this->_defaultSystemLocale;
	}

	/**
	 * @see TechDivision_Resources_Interfaces_Resources::initialize()
	 */
	public function initialize()
	{
		$this->_bundles = new TechDivision_Collections_HashMap();
	}

	/**
	 * @see TechDivision_Resources_Interfaces_Resources::destroy()
	 */
	public function destroy()
	{
		// invoke the destroy method of the resource bundle instances
		foreach ($this->_bundles as $bundle) {
			$bundle->destroy();
		}
		// reset the HashMap
		$this->_bundles = new TechDivision_Collections_HashMap();
	}

	/**
	 * This method saves all resource files back
	 * to the file system.
	 *
	 * @return void
	 */
	public function save()
	{
		// iterate over all resources and save them
		foreach ($this->_bundles as $resourcesBundle) {
			$resourcesBundle->save();
		}
	}

    /**
     * @see TechDivision_Resources_Interfaces_Resources::getKeys()
     */
    public function getKeys()
    {
    	// initialize the array with the keys
    	$keys = array();
    	// iterate over all bundles and the keys of the bundles
    	foreach($this->_bundles as $bundle) {
    		foreach($bundle->getKeys() as $key) {
    			if (!in_array($key, $keys)) {
    				$keys[] = $key;
    			}
    		}
    	}
    	// return the ArrayList with the unique keys
    	return new TechDivision_Collections_ArrayList($keys);
    }

	/**
	 * This method adds the passed resource bundle instance
	 * to the HashMap with the initialized bundles.
	 *
	 * If resources with the same system locale as the system
	 * locale of the passed resource bundle instance exists,
	 * they will be replaced by the passed one.
	 *
	 * @param TechDivision_Resources_Interfaces_ResourceBundle
	 * 		$newBundle Holds the resource bundle to add
	 * @return void
	 */
	protected function _add(
	    TechDivision_Resources_Interfaces_ResourceBundle $newBundle) {
		$this->_bundles->add(
		    $newBundle->getSystemLocale()->__toString(),
		    $newBundle
		);
	}

	/**
	 * This method returns the resources for the passed
	 * system locale.
	 *
	 * If the requested resources does not exist an exception
	 * is thrown.
	 *
	 * @param TechDivision_Util_SystemLocale $sytemLocale
	 * 		Holds the system locale to return the resources for
	 * @return TechDivision_Resources_Interfaces_ResourceBundle
	 * 		Holds the initialized resource bundle instance
	 * @throws TechDivision_Resources_Exceptions_SystemLocaleNotExistsException
	 * 		Is throw if the requested resources is not available
	 */
	protected function _get(TechDivision_Util_SystemLocale $systemLocale)
	{
		if (!$this->_exists($systemLocale)) {
		    // check if resources for the passed system locale exists
			throw new TechDivision_Resources_Exceptions_SystemLocaleNotExistsException(
				'ResourceBundle for system locale ' .
			    $systemLocale->__toString() .
			    ' is not available'
			);
		}
		// returns the requested resources
		return $this->_bundles->get($systemLocale->__toString());
	}

	/**
	 * This method checks if resources for the passed
	 * system locale already exists in the bundle or not.
	 *
	 * @param TechDivision_Util_SystemLocale $systemLocale
	 * 		Holds the system locale to check for
	 * @return boolean
	 *		TRUE if resources for the passed system locale exists, else FALSE
	 */
	protected function _exists(TechDivision_Util_SystemLocale $systemLocale)
	{
		return $this->_bundles->exists($systemLocale->__toString());
	}
}