<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2020 Xavier Perseguers <xavier@causal.ch>
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

namespace Causal\Staffdirectory\Domain\Model;

/**
 * Base class for entities.
 *
 * @category    Model
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
abstract class AbstractEntity {

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
	 * @return AbstractEntity
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
		$reflect = new \ReflectionClass($this);
		$properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

		$ret = array();
		foreach ($properties as $property) {
            $property->setAccessible(TRUE);
            $value = $property->getValue($this);
			if (!is_array($value)) {
				$ret[$property->getName()] = (string) $value;
			}
			if ($value instanceof \Tx_StaffDirectory_Domain_Model_AbstractEntity) {
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
