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

require_once "Spreadsheet/Excel/Writer.php";
require_once "TechDivision/Collections/HashMap.php";
require_once "TechDivision/Resources/AbstractResources.php";
require_once "TechDivision/Resources/PropertyResourceBundle.php";
require_once "TechDivision/Resources/Exceptions/ResourcesException.php";
require_once "TechDivision/Resources/Exceptions/ResourcesKeyException.php";

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
class TechDivision_Resources_PropertyResources
    extends TechDivision_Resources_AbstractResources {

	/**
	 * Holds the data directory with the path to the resource files to export.
	 * @var TechDivision_Lang_String
	 */
	private $_config = null;

	/**
	 * Create and return a new resources instance with the specified
	 * logical name, after calling its init() method and delegating
	 * the relevant properties. Concrete subclasses MUST implement
	 * this method.
	 *
	 * @param TechDivision_Lang_String $name
	 * 		Holds the logical name of the resources to create
	 * @param TechDivision_Lang_String $config
	 * 		Holds the optional string to the configuration
	 * @return TechDivision_Property_Resources The initialized resources
	 */
 	public function __construct(
 	    TechDivision_Lang_String $name,
 	    TechDivision_Lang_String $config = null) {
 		// initialize the superclass
		TechDivision_Resources_AbstractResources::__construct($name);
		// initialize the members with the passed values
		$this->_config = $config;
 	}

    /**
     * @see TechDivision_Resources_AbstractResources::find(
     * 		$name,
     * 		TechDivision_Util_SystemLocale $systemLocale = null,
     * 		TechDivision_Collections_ArrayList $parameter = null)
     */
    public function find(
        $name,
        TechDivision_Util_SystemLocale $systemLocale = null,
        TechDivision_Collections_ArrayList $parameter = null) {
    	// if no Locale is passed, use the property resources default one
   		if($systemLocale == null) {
   			$systemLocale = $this->getDefaultSystemLocale();
   		}
   		// check if the property resources bundle has already been loaded
   		if(!$this->_exists($systemLocale)) {
       		$this->_add(
       		    TechDivision_Resources_PropertyResourceBundle::getBundle(
       		        $this->_config,
       		        $systemLocale
       		    )
       		);
   		}
   		// return the requested resource value
   		$value = $this->_get($systemLocale)->find($name, $parameter);
   		// check if an exception should be thrown if the requested value is null
   		if(($value == null) && ($this->isReturnNull() == false)) {
   			throw new TechDivision_Resources_Exceptions_ResourcesKeyException(
   				'Found no value for requested resource ' . $name
   		    );
   		}
   		// return the requested value
   		return $value;
    }

    /**
     * Initializes the resource bundles for all installed locales.
     *
     * @return void
     */
    protected function _read()
    {
        // get all installed locales
        $locales = TechDivision_Util_SystemLocale::getAvailableLocles();
        // load the default system locale
        $systemLocale = $this->getDefaultSystemLocale();
        // iterate over the installed locales and instanciate
        // the resource bundles therefore
        for ($i = 0; $i < $locales->size(); $i++) {
       		$this->_add(
       		    TechDivision_ResourcesPropertyResourceBundle::getBundle(
       		        $this->_config,
       		        $systemLocale
       		    )
       		);
        }
    }

	/**
	 * This method creates an Excelsheet and adds for each locale a column
	 * with the values for it's key.
	 *
	 * After adding all resource strings it sends a header with the download
	 * information of the generated Excelsheet.
	 *
	 * @return void
	 */
	public function export() {
		// read and initialize the resource files
		$this->_read();
		// get the default bundle
		$defaultBundle = $this->_bundles->get(
		    AbstractResources::getDefaultSystemLocale()->__toString()
		);
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer(
		    $this->dataDir . DIRECTORY_SEPARATOR .
		    $defaultBundle->getName()->__toString() .
		    ".xls"
		);
		// Creating a worksheet
		$worksheet = $workbook->addWorksheet("resources");
		$worksheet->write(0, 0, "keys");
		// get the locale keys from the default resource bundle
		$line = 1;
		foreach($defaultBundle as $key => $value) {
			// add the keys to the key array
			$keys[] = $key;
			// write the keys
			$worksheet->write($line++, 0, $key);
		}
		// initialize the column counter
		$column = 1;
		// iterate over the resource bundles and add the values
		foreach($this->_bundles as $systemLocale => $resources) {
			// write the
			$worksheet->write(0, $column, $systemLocale);
			// iterate over the the keys and add the values
			// from the resource bundle
			$line = 1;
			foreach($keys as $key) {
				// get the value for the key
				$value = $resources->find($key);
				// The actual data
				$worksheet->write($line, $column, $value);
				$line++;
			}
			$column++;
		}
		// let's send the file
		$workbook->close();
	}

	/**
	 * This method imports the resource string from the
	 * file with the name specified as parameter.
	 *
	 * @param string $fileToImport
	 * 		Holds the name of the file to import the resource strings from
	 * @return void
	 */
	public function import($fileToImport) {
		// read and initialize the resource files
		$this->_read();
		// initialize the array with the locales
		$systemLocales = array();
		// open the file to import the resource strings from
		$handle = @fopen($fileToImport, "r");
		// throw an exception if the file with the resources to import
		// can't be opened
		if (!$handle) {
		    throw new ResourcesException(
		        'Can\'t open ' . $fileToImport . ' with resources to import'
		    );
		}
		// separate the content in lines
		$lines = 0;
		while($row = fgetcsv($handle, 4096)) {
			$key = "";
			for($i = 0; $i <= $this->_bundles->size(); $i++) {
				if($i == 0) {
					$key = $row[$i];
				} elseif($lines == 0 && $i > 0) {
					$systemLocales[$i] = $row[$i];
				} else {
					$resources = $this->_bundles->get($systemLocales[$i]);
					$resources->replace($key, $row[$i]);
				}
			}
			$lines++;
		}
	}
}