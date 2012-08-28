<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Metadata\Cache;

use Opensoft\SimpleSerializer\Metadata\ClassMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
interface CacheInterface
{
    /**
     * Loads a class metadata instance from the cache
     *
     * @param string $className
     *
     * @return ClassMetadata
     */
    function loadClassMetadataFromCache($className);

    /**
     * Puts a class metadata instance into the cache
     *
     * @param ClassMetadata $metadata
     */
    function putClassMetadataInCache(ClassMetadata $metadata);

    /**
     * Evicts the class metadata for the given class from the cache.
     *
     * @param string $className
     */
    function removeClassMetadataFromCache($className);
}
