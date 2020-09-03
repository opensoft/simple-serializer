<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Adapter;

use Opensoft\SimpleSerializer\Exclusion\ExclusionStrategyInterface;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
interface ArrayAdapterInterface
{
    /**
     * Convert object to array
     *
     * @abstract
     * @param object $object
     * @return mixed
     */
    public function toArray($object);

    /**
     * Convert array to object
     *
     * @abstract
     * @param mixed $data
     * @param object $object
     */
    public function toObject(array $data, $object);

    /**
     * Sets ExclusionStrategy
     * @deprecated Deprecated since version 1.1 to be removed in 2
     *
     * @param ExclusionStrategyInterface|null $exclusionStrategy
     * @return ArrayAdapterInterface
     */
    public function setExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy = null);

    /**
     * @param ExclusionStrategyInterface $exclusionStrategy
     * @return ArrayAdapterInterface
     */
    public function addExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy);

    public function cleanUpExclusionStrategies();

    /**
     * Sets Unserialized mode
     *
     * @param boolean $unserializeMode
     * @return ArrayAdapterInterface
     */
    public function setUnserializeMode($unserializeMode);

    /**
     * Convert array to object of given classname
     * This method implies that class constructor is public and does not require any parameters
     *
     * @abstract
     * @param mixed $data
     * @param string $className
     */
    public function toObjectOfClass(array $data, $className);
}
