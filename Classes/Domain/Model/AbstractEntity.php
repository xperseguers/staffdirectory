<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Xavier Perseguers <xavier@causal.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Base class for entities.
 *
 * @category    Model
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
abstract class Tx_StaffDirectory_Domain_Model_AbstractEntity {

	/**
	 * @var integer
	 */
	protected $uid;

	/**
	 * @var integer
	 */
	protected $pid;

	/**
	 * Default constructor.
	 *
	 * @param integer $uid
	 */
	public function __construct($uid) {
		$this->uid = intval($uid);
	}

	/**
	 * Gets the uid.
	 *
	 * @return integer
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * @return integer
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * @param integer $pid
	 * @return tx_StaffDirectory_Domain_Model_AbstractEntity
	 */
	public function setPid($pid) {
		$this->pid = intval($pid);
		return $this;
	}

	/**
	 * Converts this entity as an array of its properties.
	 *
	 * @return array
	 */
	public function toArray() {
		$reflect = new ReflectionClass($this);
		$properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

		$ret = array();
		foreach ($properties as $property) {
			if (version_compare(phpversion(), '5.3.0', '<')) {
				$propertyName = $property->getName();
				$name = t3lib_div::underscoredToUpperCamelCase($propertyName);
				$prefixes = array('get', 'is', 'has');
				$getter = '';
				$value = '__COULD_NOT_BE_RETRIEVED__';
				foreach ($prefixes as $prefix) {
					$getter = $prefix . $name;
					if (method_exists($this, $getter)) {
						break;
					} else {
						$getter = '';
					}
				}
				if ($getter) {
					$value = call_user_func(array($this, $getter));
				}
			} else {
				$property->setAccessible(TRUE);
				$value = $property->getValue($this);
			}
			if (!is_array($value)) {
				$ret[$property->getName()] = (string) $value;
			}
			if ($value instanceof tx_StaffDirectory_Domain_Model_AbstractEntity) {
				$ret[$property->getName() . '_uid'] = $value->getUid();
			}
		}

		unset($properties, $reflect);
		return $ret;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return strtoupper(get_class($this));
	}

}

?>