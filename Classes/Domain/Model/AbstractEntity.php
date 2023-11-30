<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2023 Xavier Perseguers <xavier@causal.ch>
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
abstract class AbstractEntity
{

    /**
     * @var int
     */
    protected $uid;

    /**
     * @var int
     */
    protected $pid;

    /**
     * Default constructor.
     *
     * @param int $uid
     */
    public function __construct(int $uid)
    {
        $this->uid = $uid;
    }

    /**
     * Gets the uid.
     *
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     * @return AbstractEntity
     */
    public function setPid(int $pid): AbstractEntity
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * Converts this entity as an array of its properties.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        $reflect = new \ReflectionClass($this);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

        $ret = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            if (!is_array($value)) {
                $ret[$property->getName()] = (string)$value;
            }
            if ($value instanceof AbstractEntity) {
                $ret[$property->getName() . '_uid'] = $value->getUid();
            }
        }

        unset($properties, $reflect);
        return $ret;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return strtoupper(get_class($this));
    }

}
