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
require_once 'TechDivision/Resources/PropertyResources.php';
require_once 'TechDivision/Resources/AbstractResourcesFactory.php';

/**
 * Factory for all property based resources.
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_Resources_PropertyResourcesFactory
    extends TechDivision_Resources_AbstractResourcesFactory {

	/**
	 * @see TechDivision_Resources_AbstractResourcesFactory::createResources(
	 * 		TechDivision_Lang_String $name,
	 * 	 	TechDivision_Lang_String $config = null);
	 */
	protected function createResources(
	    TechDivision_Lang_String $name,
	    TechDivision_Lang_String $config = null) {
		// initialize the new Resources
		$resources = new TechDivision_Resources_PropertyResources(
		    $name, $config
		);
		$resources->initialize();
		$resources->setReturnNull($this->isReturnNull());
		// return the resources
		return $resources;
	}
}