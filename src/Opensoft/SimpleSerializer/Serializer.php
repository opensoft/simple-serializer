<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer;

use Opensoft\SimpleSerializer\Adapter\ArrayAdapterInterface;
use Opensoft\SimpleSerializer\Adapter\SerializerAdapterInterface;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Exclusion\ExclusionStrategyInterface;
use Opensoft\SimpleSerializer\Exclusion\VersionExclusionStrategy;
use Opensoft\SimpleSerializer\Exclusion\GroupsExclusionStrategy;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class Serializer
{
    /**
     * @var ArrayAdapterInterface
     */
    private $arrayAdapter;

    /**
     * @var SerializerAdapterInterface;
     */
    private $serializerAdapter;

    /**
     * @var boolean
     */
    private $level = 0;

    /**
     * @param ArrayAdapterInterface $arrayAdapter
     * @param SerializerAdapterInterface $serializerAdapter
     */
    public function __construct(ArrayAdapterInterface $arrayAdapter, SerializerAdapterInterface $serializerAdapter)
    {
        $this->arrayAdapter = $arrayAdapter;
        $this->serializerAdapter = $serializerAdapter;
    }

    /**
     * @param object|array $data
     * @return string
     */
    public function serialize($data, $className = null)
    {
        $this->level++;
        $dataAsArray = null;
        if (is_array($data)) {
            $dataAsArray = array();
            foreach ($data as $key => $object) {
                $dataAsArray[$key] = $this->serialize($object);
            }
        } else if (is_object($data)) {
            $dataAsArray = $this->arrayAdapter->toArray($data, $className);
        } else {
            $dataAsArray = $data;
        }
        $this->level--;
        if ($this->level === 0) {
            return $this->serializerAdapter->serialize($dataAsArray);
        } else {
            return $dataAsArray;
        }
    }

    /**
     * @param mixed $data
     * @param mixed $targetData
     * @throws InvalidArgumentException
     * @return object|array
     */
    public function unserialize($data, $targetData = null)
    {
        if ($this->level === 0) {
            $unserializedData = $this->serializerAdapter->unserialize($data);
        } else {
            $unserializedData = $data;
        }
        if (is_object($targetData)) {
            return $this->arrayAdapter->toObject($unserializedData, $targetData);
        } else if (is_array($targetData)) {
            $this->level++;
            $result = array();
            foreach ($unserializedData as $key => $item) {
                if (array_key_exists($key, $targetData)) {
                    $value = $targetData[$key];
                } else {
                    $value = null;
                }
                $result[$key] = $this->unserialize($item, $value);
            }
            $this->level--;

            return $result;
        }

        return $unserializedData;
    }

    /**
     * @deprecated Deprecated since version 1.1 to be removed in 2
     * @param string $version
     * @return bool
     */
    public function setVersion($version)
    {
        if (null === $version) {
            $this->arrayAdapter->setExclusionStrategy(null);

            return false;
        }

        $this->arrayAdapter->setExclusionStrategy(new VersionExclusionStrategy($version));
    }

    /**
     * @deprecated Deprecated since version 1.1 to be removed in 2
     * @param array $groups
     * @return bool
     */
    public function setGroups(array $groups)
    {
        if (!$groups) {
            $this->arrayAdapter->setExclusionStrategy(null);

            return false;
        }

        $this->arrayAdapter->setExclusionStrategy(new GroupsExclusionStrategy($groups));
    }

    /**
     * @param ExclusionStrategyInterface $exclusionStrategy
     */
    public function addExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy)
    {
        $this->arrayAdapter->addExclusionStrategy($exclusionStrategy);
    }

    public function cleanUpExclusionStrategies()
    {
       $this->arrayAdapter->cleanUpExclusionStrategies();
    }

    /**
     * @param integer $unserializeMode
     */
    public function setUnserializeMode($unserializeMode)
    {
        $this->arrayAdapter->setUnserializeMode($unserializeMode);
    }
}
