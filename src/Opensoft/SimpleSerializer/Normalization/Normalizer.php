<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Normalization;

use Opensoft\SimpleSerializer\Exclusion\Specification;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
interface Normalizer
{
    /**
     * Convert object to array
     *
     * @abstract
     * @param object $object
     * @return mixed
     */
    public function normalize($object);

    /**
     * Convert array to object
     *
     * @abstract
     * @param mixed $data
     * @param object $object
     */
    public function denormalize(array $data, $object);

    /**
     * @param Specification $exclusionStrategy
     * @return Normalizer
     */
    public function addExclusionSpecification(Specification $exclusionStrategy);

    public function cleanUpExclusionSpecifications();

    /**
     * Sets Unserialized mode
     *
     * @param boolean $unserializeMode
     * @return Normalizer
     */
    public function setUnserializeMode($unserializeMode);

    /**
     * @param mixed $object
     * @return bool
     */
    public function supportNormalization($object);
}
