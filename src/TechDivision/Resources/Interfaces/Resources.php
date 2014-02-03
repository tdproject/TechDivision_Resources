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

require_once 'TechDivision/Util/SystemLocale.php';
require_once 'TechDivision/Collections/ArrayList.php';

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
interface TechDivision_Resources_Interfaces_Resources {

	/**
	 * Create (if necessary) and return a Resources instance for
	 * the specified logical name, with a configuration based on
	 * the specified configuration String.
	 *
	 * @throws TechDivision_Resources_Exceptions_ResourcesException
	 * 		Is thrown if an exception occurs when the Resources are initialized
	 */
	public function initialize();

	/**
	 * Release any internal references to Resources instances that
	 * have been returned previously, after calling the destroy()
	 * method on each such instance.
	 *
	 * @return void
	 */
	public function destroy();

	/**
	 * Return TRUE if resource getter methods will return null instead
	 * of throwing an exception on invalid key values.
	 *
	 * @return boolean TRUE if null is returned for invalid key values.
	 */
	public function isReturnNull();

	/**
	 * Set a flag determining whether resource getter methods should return
	 * null instead of throwing an exception on invalid key values.
	 *
	 * @param boolean $returnNull The new flag value
	 * @return void
	 */
	public function setReturnNull($returnNull);

	/**
	 * Return the logical name of this Resources instance.
	 *
	 * @return TechDivision_Lang_String
	 * 		Holds the logical name of this Resources instance
	 */
	public function getName();

	/**
	 * Returns the keys of this Resources instance
	 * as an ArrayList.
	 *
	 * @return TechDivision_Collections_ArrayList
	 * 		Holds the keys of this Resources instance
	 */
	public function getKeys();

	/**
     * This method searches in the container for the
     * resource with the key passed as parameter.
     *
     * @param string $name Holds the key of the requested resource
     * @param TechDivision_Util_SystemLocale $systemLocale
     * 		Holds the SystemLocale with which to localize retrieval, or null
     * 		for the default SystemLocale
     * @param TechDivision_Collections_ArrayList $parameter
     * 		Holds an ArrayList with parameters with replacements for the
     * 		placeholders in the resource string
     * @return string Holds the requested resource value
     * @throws TechDivision_Resources_Exceptions_ResourcesException
     * 		Is thrown if an error occurs retrieving or returning the
     * 		requested content
	 * @throws TechDivision_Resources_Exceptions_ResourcesKeyException
	 * 		Is thrown if the no value for the specified key was found, and
	 * 		isReturnNull() returns false
     */
    public function find(
        $name,
        TechDivision_Util_SystemLocale $systemLocale = null,
        TechDivision_Collections_ArrayList $parameter = null);
}