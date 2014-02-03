<?php

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
require_once 'TechDivision/Collections/HashMap.php';
require_once 'TechDivision/Resources/Interfaces/ResourcesFactory.php';
require_once 'TechDivision/Resources/Exceptions/ResourcesException.php';

/**
 * This class bundles several resource files to a so
 * called resource bundle.
 *
 * It also provides several functions to handle resource
 * files, like an import/export functionality.
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
abstract class TechDivision_Resources_AbstractResourcesFactory
    implements TechDivision_Resources_Interfaces_ResourcesFactory {

	/**
	 * Return the returnNull property value that will be configured on
	 * Resources instances created by this factory.
	 * @var boolean
	 */
	private $_returnNull = true;

	/**
	 * This HashMap holds the initialized Resources instances.
	 * @var TechDivision_Collections_HashMap
	 */
	private $_resources = null;

	/**
	 * The constructor initializes the internal members
	 * necessary for handling the initialized resources.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_resources = new TechDivision_Collections_HashMap();
	}

	/**
	 * Create and return a new resources instance with the specified
	 * logical name, after calling its init() method and delegating
	 * the relevant properties. Concrete subclasses MUST implement
	 * this method.
	 *
	 * @param TechDivision_Lang_String $name
	 * 		Holds the logical name of the Resources to create
	 * @param TechDivision_Lang_String $config
	 * 		Holds the optional string to the configuration
	 * @return TechDivision_Resources_Interfaces_Resources
	 * 		Holds the initialized resources
	 */
	protected abstract function createResources(
	    TechDivision_Lang_String $name,
	    TechDivision_Lang_String $config = null);

	/**
	 * @see TechDivision_Resources_Interfaces_ResourcesFactory::getResources(
	 * 		TechDivision_Lang_String $name,
	 *		TechDivision_Lang_String $config = null)
	 */
	public function getResources(
	    TechDivision_Lang_String $name,
	    TechDivision_Lang_String $config = null) {
		// check if the resources already exists
		if(!$this->_resources->exists($name)) {
		    // if not, initialize them
			$this->_resources->add(
			    $name,
			    $this->createResources($name, $config));
		}
		// return the requested resources
		return $this->_resources->get($name);
	}

	/**
	 * @see TechDivision_Resources_Interfaces_ResourcesFactory::isReturnNull()
	 */
	public function isReturnNull() {
		return $this->_returnNull;
	}

	/**
	 * @see TechDivision_Resources_Interfaces_ResourcesFactory::setReturnNull(
	 * 		$returnNull)
	 */
	public function setReturnNull($returnNull) {
		$this->_returnNull = $returnNull;
	}

	/**
	 * @see TechDivision_Resources_Interfaces_ResourcesFactory::release()
	 */
	public function release() {
		// invoke the destroy method on each instace and delete
		// it from the internal array
		foreach($this->_resources as $name => $resources) {
			// invoke the destroy method of the resources
			$resources->destroy();
			// remove the resources from the internal HashMap
			$this->_resources->remove($name);
		}
	}
}