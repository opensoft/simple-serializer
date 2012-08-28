<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Metadata;

use Opensoft\SimpleSerializer\Metadata\Driver\DriverInterface;
use Opensoft\SimpleSerializer\Metadata\Cache\CacheInterface;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
final class MetadataFactory implements MetadataFactoryInterface
{
    private $driver;
    private $cache;
    private $loadedClassMetadata = array();
    private $loadedMetadata = array();
    private $hierarchyMetadataClass;
    private $debug;

    public function __construct(DriverInterface $driver,
                                CacheInterface $cacheDriver = null,
                                $debug = false,
                                $hierarchyMetadataClass = 'Opensoft\\SimpleSerializer\\Metadata\\ClassHierarchyMetadata')
    {
        $this->driver = $driver;
        $this->hierarchyMetadataClass = $hierarchyMetadataClass;
        $this->debug = $debug;
        $this->cache = $cacheDriver;
    }

    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $className
     * @return ClassHierarchyMetadata
     */
    public function getMetadataForClass($className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        /** @var ClassHierarchyMetadata $metadata */
        $metadata = new $this->hierarchyMetadataClass;
        foreach ($this->getClassHierarchy($className) as $classMetadataName) {
            if (isset($this->loadedClassMetadata[$classMetadataName])) {
                $metadata->addClassMetadata($this->loadedClassMetadata[$classMetadataName]);
                continue;
            }

            // check the cache
            if ($this->cache !== null
                && (null !== $classMetadata = $this->cache->loadClassMetadataFromCache($classMetadataName))) {
                if ($this->debug && !$classMetadata->isFresh()) {
                    $this->cache->removeClassMetadataFromCache($classMetadata->getName());
                } else {
                    $this->loadedClassMetadata[$classMetadataName] = $classMetadata;
                    $metadata->addClassMetadata($classMetadata);
                    continue;
                }
            }

            // load from source
            if (null !== $classMetadata = $this->driver->loadMetadataForClass($classMetadataName)) {
                $this->loadedClassMetadata[$classMetadataName] = $classMetadata;
                $metadata->addClassMetadata($classMetadata);

                if ($this->cache !== null) {
                    $this->cache->putClassMetadataInCache($classMetadata);
                }

                continue;
            }
        }

        return $this->loadedMetadata[$className] = $metadata;
    }

    /**
     * @param string $className
     * @return array
     */
    private function getClassHierarchy($className)
    {
        $classes = array();
        $refl = new \ReflectionClass($className);

        do {
            $classes[] = $refl->getName();
        } while (false !== $refl = $refl->getParentClass());

        unset($refl);
        $classes = array_reverse($classes, false);

        return $classes;
    }
}
