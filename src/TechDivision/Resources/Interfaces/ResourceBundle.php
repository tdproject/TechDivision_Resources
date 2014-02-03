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

/**
 * This is the interface for all resource bundles.
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
interface TechDivision_Resources_Interfaces_ResourceBundle
{

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
	 * This method returns the system locale instance.
	 *
	 * @return TechDivision_Util_SystemLocale The system locale
	 */
	public function getSystemLocale();

	/**
	 * Returns the keys of this resource bundle instance
	 * as an ArrayList.
	 *
	 * @return TechDivision_Collections_ArrayList
	 * 		Holds the keys of this resource bundle instance
	 */
	public function getKeys();

	/**
     * This method searches in the container for the
     * resource with the key passed as parameter.
     *
     * @param string $name Holds the key of the requested resource
     * @param TechDivision_Collections_ArrayList $parameter
     * 		Holds an ArrayList with parameters with replacements for the
     * 		placeholders in the resource string
     * @return string Holds the requested resource value
     * @throws TechDivision_Resources_Exceptions_ResourcesException
     * 		Is thrown if an error occurs retrieving or returning the
     * 		requested content
	 * @throws TechDivision_Resources_Exceptions_ResourcesKeyException
	 * 		Is thrown if the no value for the specified key was found, and
	 * 		isReturnNull() returns FALSE
     */
    public function find(
        $name,
        TechDivision_Collections_ArrayList $parameter = null);
}