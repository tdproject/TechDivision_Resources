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

require_once 'MDB2.php';
require_once 'TechDivision/Util/SystemLocale.php';
require_once 'TechDivision/Util/DataSource.php';
require_once 'TechDivision/Lang/String.php';
require_once 'TechDivision/Properties/Properties.php';
require_once 'TechDivision/Resources/AbstractResourceBundle.php';

/**
 * This class is a container for the resources and
 * provides methods for handling them.
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
class TechDivision_Resources_DBResourceBundle
    extends TechDivision_Resources_AbstractResourceBundle {

    /**
	 * Holds the the name of the key with the database charset to use.
	 * @var string
     */
    const DB_CHARSET = 'db.charset';

	/**
	 * Holds the name of the database table where the properties are stored.
	 * @var string
	 */
	const DB_SQL_TABLE = "db.sql.table";

	/**
	 * Holds the name of the column with the locale stored.
	 * @var string
	 */
	const DB_SQL_LOCALE_COLUMN = "db.sql.locale.column";

	/**
	 * Holds the name of the column with the key stored.
	 * @var string
	 */
	const DB_SQL_KEY_COLUMN = "db.sql.key.column";

	/**
	 * Holds the name of the column with the value stored.
	 * @var string
	 */
	const DB_SQL_VAL_COLUMN = "db.sql.val.column";

	/**
	 * Holds the cache flag to cache the alread loaded properties.
	 * @var string
	 */
	const RESOURCE_CACHE = "resource.cache";

	/**
	 * Holds the Properties necessary to initialize the database connection.
	 * @var Properties
	 */
	private $_properties = null;

	/**
	 * Holds the database connection to request the resources from.
	 * @var MDB2
	 */
	private $_db = null;

	/**
	 * Holds the flag that resources should be cached or not.
	 * @var boolean
	 */
	private $_cacheResources = true;

	/**
	 * Holds the path to the database configuration file.
	 * @var TechDivision_Lang_String
	 */
	private $_config = null;

	/**
	 * Initializes the resource bundle with the
	 * configuration and the locale to use.
	 *
	 * @param TechDivision_Lang_String $config
	 * 		The configuration for the database connection
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
	 * the propertees with the passed configuration file name
	 * and locale.
	 *
	 * @param TechDivision_Lang_String $config
	 * 		Holds the name of the property file to load the configuration from
	 * @param TechDivision_Util_SystemLocale $systemLocale
	 * 		Holds the system locale of the property resource bundle to load
	 * @return TechDivision_Resources_PropertyResourceBundle
	 * 		Holds the initialized property resource bundle
	 */
	public static function getBundle(
	    TechDivision_Lang_String $config,
	    TechDivision_Util_SystemLocale $systemLocale = null) {
		if ($systemLocale == null) {
			$systemLocale = TechDivision_Util_SystemLocale::getDefault();
		}
		$bundle = new TechDivision_Resources_DBResourceBundle(
		    $config,
		    $systemLocale
		);
		$bundle->initialize();
		return $bundle;
	}

	/**

	/**
	 * This method initializes the database connection
	 * and returns id.
	 *
	 * @return MDB2 Holds the initialized database connection
	 * @throws TechDivision_Resources_Exceptions_ResourcesException
	 * 		Is thrown if an error while conneting to the database occurs
	 */
	public function initialize()
	{
		// load the properties
		$this->_properties = new TechDivision_Properties_Properties();
		$this->_properties->load($this->_config . ".properties");
        // initialize the datasource
		$cn = TechDivision_Util_DataSource::create($this->_properties);
		// initialize the flag to cache the resources
		$this->_cacheResources = (boolean) $this->_properties->getProperty(
		    TechDivision_Resources_DBResourceBundle::RESOURCE_CACHE
		);
		// initialize the database connection
		$this->_db = MDB2::factory($cn->getConnectionString());
		$this->_db->setCharset(
		    $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_CHARSET
		    )
		);
		// check if an error occurs
		if (PEAR::isError($this->_db)) {
			throw new TechDivision_Resources_Exceptions_ResourcesException(
			    $this->_db->getMessage()
			);
		}
	}

	/**
	 * This method disconnects from the
	 * database and frees the memory.
	 *
	 * @return void
	 */
	public function destroy()
	{
		$this->_db->disconnect();
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
        // prepare and execute the statement to load the requested resource
        // string from the database
		$statement = $this->_db->prepare(
			"SELECT `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_VAL_COLUMN
		    ) . "` FROM `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_TABLE
		    ) . "` WHERE `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
		    ) . "` = ? AND `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_LOCALE_COLUMN
		    ) . "` = ?",
		    array("text", "text"),
		    array("text", "text")
		);
		// check if statement has been prepared successfully
		if (PEAR::isError($statement)) {
    		throw new Exception($statement->getMessage());
		}
		// get the result with the values
    	$result = $statement->execute(
    	    array(
    	        $name,
    	        TechDivision_Resources_AbstractResourceBundle::getSystemLocale()
    	            ->__toString()
    	    )
    	);
    	// check if the query has been successfully executed
		if (PEAR::isError($result)) {
    		throw new Exception($result->getMessage());
    	}
		while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    $resource = $row[$this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_VAL_COLUMN
		    )];
		}
        // check if parameter for replacement are passed
        if ($parameter != null) {
			// replace the placeholders with the passed parameter
			foreach ($parameter as $key => $value) {
				$resource = str_replace($key . "?", $value, $resource);
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
    public function replace($name, $value)
    {
    	// prepare and execute the statement to load the keys
    	$statement = $this->_db->prepare(
    		"UPDATE `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_TABLE
    	    ) . "` SET `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_VAL_COLUMN
    	    ) . "` = ?, `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_LOCALE_COLUMN
    	    ) . "` = ? WHERE `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
    	    ) . "` = ?",
    	    array("text", "text", "text")
    	);
		// check if statement has been prepared successfully
		if (PEAR::isError($statement)) {
    		throw new Exception($statement->getMessage());
		}
		return $statement->execute(
		    array(
		        $value,
		        TechDivision_Resources_AbstractResourceBundle::getSystemLocale()
		            ->__toString(),
		        $name
		    )
		);
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
    public function attach($name, $value)
    {
    	// prepare and execute the statement to attach the resources
    	$statement = $this->_db->prepare(
    		"INSERT INTO `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_TABLE
    	    ) . "` (`" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
    	    ) . "`, `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_VAL_COLUMN
    	    ) . "`, `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_LOCALE_COLUMN
    	    ) . "`) VALUES (?, ?, ?)",
    	    array("text", "text", "text")
    	);
		// check if statement has been prepared successfully
		if (PEAR::isError($statement)) {
    		throw new Exception($statement->getMessage());
		}
    	// excecute the statement
    	$result = $statement->execute(
		    array(
		        $name,
		        $value,
		        TechDivision_Resources_AbstractResourceBundle::getSystemLocale()
		            ->__toString()
		    )
		);
		// check the result
		if (PEAR::isError($result)) {
		    // a system error occured
    		throw new Exception($result->getMessage());
		} elseif ($result->rowCount() > 1) {
		    // more than exactly one row has been attached
    		throw new Exception($result->getMessage());
		} else {
			// if the row was successfully attached, return TRUE
		    return true;
		}
		// else return FALSE
		return false;
    }

    /**
     * This method returns the first key found for
     * the passed value.
     *
     * @param string $value Holds the resource value to return the key for
     * @return string Holds the resource key for the passed value
     */
    public function findKeyByValue($value)
    {
		// prepare the statement to load the requested
		// resource key from the database
		$statement = $this->_db->prepare(
			"SELECT `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
		    ) . "` FROM `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_TABLE
		    ) . "` WHERE `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_VAL_COLUMN
		    ) . "` = ? AND `" . $this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_LOCALE_COLUMN
		    ) . "` = ?",
		    array("text", "text"),
		    array("text", "text")
		);
		// check if statement has been prepared successfully
		if (PEAR::isError($statement)) {
    		throw new Exception($statement->getMessage());
		}
        // execute the statement
		$result = $statement->execute(
		    array(
		        $value,
		        TechDivision_Resources_AbstractResourceBundle::getSystemLocale()
		            ->__toString()
		    )
		);
		// check if statement has been executed successfully
		if (PEAR::isError($result)) {
    		throw new Exception($result->getMessage());
		}
        // iterate over the result
    	while ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    $resource = $row[$this->_properties->getProperty(
		        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
		    )];
		}
		// return the resource string
		return $resource;
    }

    /**
     * This method returns the number of resources in the container.
     *
     * @return integer Number of resources in the container
     */
    public function count()
    {
    	// prepare the statement
    	$statement = $this->_db->prepare(
    		"SELECT COUNT(`" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
    	    ) . "`) AS size FROM `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_TABLE
    	    ) . "` WHERE `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_LOCALE_COLUMN
    	    ) . "` = ? GROUP BY `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
    	    ) . "`",
    	    array("text")
    	);
		// check if statement has been prepared successfully
		if (PEAR::isError($statement)) {
    		throw new Exception($statement->getMessage());
		}
        // execute the statement count the resources
		$result = $statement->execute(
		    array(
		        TechDivision_Resources_AbstractResourceBundle::getSystemLocale()
		            ->__toString()
		    )
		);
		// check if statement has been executed successfully
		if (PEAR::isError($result)) {
    		throw new Exception($result->getMessage());
		}
		// return the size of the found resources
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    return $row["size"];
		}
    }

    /**
     * This method saves all resources back to
     * the database.
     *
     * @return void
     */
    public function save()
    {
		// Nothing to do here, because all values are saved immediately
    }

	/**
	 * Returns the keys of this ResourceBundle instance
	 * as an ArrayList.
	 *
	 * @return TechDivision_Collections_ArrayList
	 * 		Holds the keys of this ResourceBundle instance
	 */
    public function getKeys()
    {
    	// initialize the ArrayList with the keys
    	$list = new TechDivision_Collections_ArrayList();
    	// prepare the statement to load the keys
    	$statement = $this->_db->prepare(
    		"SELECT `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
    	    ) . "` FROM `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_TABLE
    	    ) . "` WHERE `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_LOCALE_COLUMN
    	    ) . "` = ? GROUP BY `" . $this->_properties->getProperty(
    	        TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
    	    ) . "`",
    	    array("text")
    	);
		// check if statement has been prepared successfully
		if (PEAR::isError($statement)) {
    		throw new Exception($statement->getMessage());
		}
        // execute the statement
		$result = $statement->execute(
		    array(
		        TechDivision_Resources_AbstractResourceBundle::getSystemLocale()
		            ->__toString()
		    )
		);
		// check if statement has been executed successfully
		if (PEAR::isError($result)) {
    		throw new Exception($result->getUserInfo());
		}
		// build the ArrayList
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		    $list->add(
		        $row[$this->_properties->getProperty(
		            TechDivision_Resources_DBResourceBundle::DB_SQL_KEY_COLUMN
		        )]
		    );
		}
		// return the ArrayList with the keys
		return $list;
    }
}