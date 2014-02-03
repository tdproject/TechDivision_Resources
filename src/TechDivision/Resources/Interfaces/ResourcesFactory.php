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
interface TechDivision_Resources_Interfaces_ResourcesFactory
{

	/**
	 * Create (if necessary) and return a Resources instance for
	 * the specified logical name, with a configuration based on
	 * the specified configuration String.
	 *
	 * @param TechDivision_Lang_String $name
	 * @param TechDivision_Lang_String $config
	 * @throws TechDivision_Resources_Exceptions_ResourcesException
	 * 		Is thrown if an exception occurs when the Resources are initialized
	 */
	public function getResources(
	    TechDivision_Lang_String $name,
	    TechDivision_Lang_String $config = null);

	/**
	 * Return the returnNull property value that will be
	 * configured on Resources instances created by this
	 * factory.
	 *
	 * @return boolean TRUE if null is returned for invalid key values.
	 */
	public function isReturnNull();

	/**
	 * Set the returnNull property value that will be configured
	 * on Resources instances created by this factory.
	 *
	 * @param boolean $returnNull The new value to delegate
	 * @return void
	 */
	public function setReturnNull($returnNull);

	/**
	 * Release any internal references to Resources instances that
	 * have been returned previously, after calling the destroy()
	 * method on each such instance.
	 *
	 * @return void
	 */
	public function release();
}