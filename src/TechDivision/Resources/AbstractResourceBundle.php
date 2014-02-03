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
require_once 'TechDivision/Resources/Interfaces/ResourceBundle.php';

/**
 * Abstract class of all resource bundles.
 *
 * @package TechDivision_Resources
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
abstract class TechDivision_Resources_AbstractResourceBundle
    implements TechDivision_Resources_Interfaces_ResourceBundle {

	/**
	 * Holds the system locale to use
	 * @var TechDivision_Util_SystemLocale
	 */
	private $_systemLocale = null;

	/**
	 * The constructor initializes the Resources with the
	 * system locale to use.
	 *
	 * @param TechDivision_Util_SystemLocale $systemLocale
	 * 		Holds the system locale instance to load the resources for
	 * @return void
	 */
	protected function __construct(TechDivision_Util_SystemLocale $systemLocale)
	{
		// initialize the name
		$this->setSystemLocale($systemLocale);
	}

	/**
	 * This method returns the system locale instance.
	 *
	 * @return TechDivision_Util_SystemLocale The system locale
	 * @see TechDivision_Resources_Interfaces_ResourceBundle::getSystemLocale()
	 */
	public function getSystemLocale()
	{
		return $this->_systemLocale;
	}

	/**
	 * This sets the passed system locale.
	 *
	 * @param TechDivision_Util_SystemLocale The new system locale
	 */
	public function setSystemLocale(TechDivision_Util_SystemLocale $newLocale)
	{
		$this->_systemLocale = $newLocale;
	}
}