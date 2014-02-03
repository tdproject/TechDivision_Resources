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

require_once 'TechDivision/Util/SystemLocale.php';
require_once 'TechDivision/Lang/String.php';
require_once 'TechDivision/Resources/DBResourceBundle.php';
require_once 'TechDivision/Resources/AbstractResources.php';
require_once 'TechDivision/Resources/Exceptions/ResourcesException.php';
require_once 'TechDivision/Resources/Exceptions/ResourcesKeyException.php';

/**
 * This class acts as a container resources stored
 * in a database.
 *
 * Properties for the database connection are:
 *
 *	 db.connect.driver               = mysqli
 *	 db.connect.user                 = resourcesUser
 *	 db.connect.password             = resourcesPassword
 * 	 db.connect.database             = resources
 * 	 db.connect.host                 = localhost
 * 	 db.connect.port                 = 3306
 * 	 db.connect.options              =
 * 	 db.sql.table                    = resources
 * 	 db.sql.locale.column            = locale
 * 	 db.sql.key.column               = msgKey
 * 	 db.sql.val.column               = val
 * 	 resource.cache 				 = true
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_Resources_DBResources
    extends TechDivision_Resources_AbstractResources {

	/**
	 * Holds the data directory with the path to the resource files to export.
	 * @var TechDivision_Lang_String
	 */
	private $_config = null;

	/**
	 * The constructor initializes the resources with the
	 * database connection to load the resources from.
	 *
	 * @param TechDivision_Lang_String $name
	 * 		Holds the logical name of the Resources to create
	 * @param TechDivision_Lang_String $config
	 * 		Holds the optional string to the configuration
	 * @return void
	 */
	public function __construct(
	    TechDivision_Lang_String $name,
	    TechDivision_Lang_String $config = null) {
		// initialize the name
		TechDivision_Resources_AbstractResources::__construct($name);
		// initialize the members with the passed values
		$this->_config = $config;
	}

    /**
     * @see Resources::find(
     * 		$name,
     * 		TechDivision_Util_SystemLocale $systemLocale = null,
     * 		TechDivision_Collections_ArrayList $parameter = null)
     */
    public function find(
        $name,
        TechDivision_Util_SystemLocale $systemLocale = null,
        TechDivision_Collections_ArrayList $parameter = null) {
    	// if no system locale is passed, use the default one
   		if($systemLocale == null) {
   			$systemLocale = $this->getDefaultSystemLocale();
   		}
   		// check if the property resources bundle has already been loaded
   		if(!$this->_exists($systemLocale)) {
       		// load the resource bundle and return the value
       		$this->_add(
       		    TechDivision_Resources_DBResourceBundle::getBundle(
       		        $this->_config,
       		        $systemLocale
       		    )
       		);
   		}
   		// return the requested resource value
   		$value = $this->_get($systemLocale)->find($name, $parameter);
   		// check if an exception should be thrown if the requested value is null
   		if (($value == null) && ($this->isReturnNull() == false)) {
   			throw new TechDivision_Resources_Exceptions_ResourcesKeyException(
   				'Found no value for requested resource ' . $name
   		    );
   		}
   		// return the requested value
   		return $value;
    }

    /**
     * This method returns the first key found for
     * the passed value.
     *
     * @param string $value Holds the resource value to return the key for
     * @return string Holds the resource key for the passed value
     */
    public function findKeyByValue($value) {
    	// @TODO Still to implement
    }

    /**
     * This method returns the number of resources in the container.
     *
     * @return integer Number of resources in the container
     */
    public function count() {
    	// @TODO Still to implement
    }
}