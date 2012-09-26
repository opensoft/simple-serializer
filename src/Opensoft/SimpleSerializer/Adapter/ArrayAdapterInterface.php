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
     *
     * @param ExclusionStrategyInterface|null $exclusionStrategy
     * @return ArrayAdapterInterface
     */
    public function setExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy = null);

    /**
     * Sets Unserialized mode
     *
     * @param boolean $unserializeMode
     * @return ArrayAdapterInterface
     */
    public function setUnserializeMode($unserializeMode);
}
