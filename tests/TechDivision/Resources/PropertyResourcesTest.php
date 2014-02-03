<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * faett.net is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * faett.net is distributed in the hope that it will be useful,
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

require_once "TechDivision/Util/SystemLocale.php";
require_once "TechDivision/Resources/PropertyResourcesFactory.php";

/**
 * This is the test for the property resources.
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_Resources_PropertyResourcesTest
    extends PHPUnit_Framework_TestCase {

	/**
	 * This tests the find() method of the
	 * PropertyResources instance.
	 *
	 * @return void
	 */
	function testPropertyResourcesFind() {
		// initialize the factory
		$factory = new TechDivision_Resources_PropertyResourcesFactory();
		// load the resources
		$propertyResources = $factory->getResources(
		    new TechDivision_Lang_String('TestResources'),
		    new TechDivision_Lang_String(
		    	'TechDivision/Resources/data/testresources'
		    )
		);
		// check the correct values to load
		$this->assertEquals(
			"Testwert",
		    $propertyResources->find(
		    	"test.key",
		        TechDivision_Util_SystemLocale::create(
		            TechDivision_Util_SystemLocale::GERMANY)
		        )
		);
		$this->assertEquals(
			"Testvalue",
		    $propertyResources->find(
		    	"test.key",
		        TechDivision_Util_SystemLocale::create(
		            TechDivision_Util_SystemLocale::US
		        )
		    )
		);
		// release the resources
		$factory->release();
	}

	/**
	 * This tests the getKeys() method to return
	 * all keys for a PropertyResources instance.
	 *
	 * @return void
	 */
	function testPropertyResourcesGetKeys() {
		// initialize the factory
		$factory = new TechDivision_Resources_PropertyResourcesFactory();
		// load the resources
		$propertyResources = $factory->getResources(
		    new TechDivision_Lang_String("TestResources"),
		    new TechDivision_Lang_String("resources/data/testresources")
		);
		// iterate over the keys and check the values
		foreach ($propertyResources->getKeys() as $key) {
			// check the correct values to load
			$this->assertEquals("test.key", $key);
		}
		// release the resources
		$factory->release();
	}

	/**
	 * This tests the attach() method of the
	 * PropertyResourceBundle instance.
	 *
	 * @return void
	 */
	function testPropertyResourcesAttach() {
		// load the ResourceBundle
		$resourceBundle = TechDivision_Resources_PropertyResourceBundle
		    ::getBundle(
		        new TechDivision_Lang_String(
		        	'TechDivision/Resources/data/testresources'
		        ),
		        TechDivision_Util_SystemLocale::create(
		            TechDivision_Util_SystemLocale::GERMANY
		        )
		    );
		// iterate over the keys and check the values
		$resourceBundle->attach("test.key.new", "neuer Testeintrag");
		// save the ResourceBundle
		$resourceBundle->save();
		// check the correct values to load
		$this->assertEquals(
			"neuer Testeintrag",
		    $resourceBundle->find("test.key.new")
		);
	}

	/**
	 * This tests the replace() method of the
	 * PropertyResourceBundle instance.
	 *
	 * @return void
	 */
	function testPropertyResourcesReplace() {
		// load the ResourceBundle
		$resourceBundle = TechDivision_Resources_PropertyResourceBundle
		    ::getBundle(
		        new TechDivision_Lang_String(
		        	'TechDivision/Resources/data/testresources'
		        ),
		        TechDivision_Util_SystemLocale::create(
		            TechDivision_Util_SystemLocale::GERMANY
		        )
		    );
		// iterate over the keys and check the values
		$resourceBundle->replace("test.key.new", "neuester Testeintrag");
		// save the ResourceBundle
		$resourceBundle->save();
		// check the correct values to load
		$this->assertEquals(
			"neuester Testeintrag",
		    $resourceBundle->find("test.key.new")
		);
	}
}