<?php

/**
 * This file was originally received from metadata library; since then,
 * it has been modified, and does only in parts resemble the original source code.
 *
 * To the original source code, the following license applies:
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * To all other portions of code, the following license applies:
 *
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
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
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
