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

use Opensoft\SimpleSerializer\Adapter\SerializerAdapterInterface;
use Opensoft\SimpleSerializer\Adapter\ArrayAdapterInterface;
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
     * @param ArrayAdapterInterface $arrayAdapter
     * @param SerializerAdapterInterface $serializerAdapter
     */
    public function __construct(ArrayAdapterInterface $arrayAdapter, SerializerAdapterInterface $serializerAdapter)
    {
        $this->arrayAdapter = $arrayAdapter;
        $this->serializerAdapter = $serializerAdapter;
    }

    /**
     * @param object|array $objects
     * @return string
     */
    public function serialize($objects)
    {
        $dataAsArray = array();
        if (is_array($objects)) {
            foreach ($objects as $object) {
                $dataAsArray[] = $this->arrayAdapter->toArray($object);
            }
        } else {
            $dataAsArray = $this->arrayAdapter->toArray($objects);
        }


        return $this->serializerAdapter->serialize($dataAsArray);
    }

    /**
     * @param string $data
     * @param object|array $object
     * @return object|array
     */
    public function unserialize($data, $object)
    {
        $array = $this->serializerAdapter->unserialize($data);
        if (is_array($object)) {
            $result = array();
            foreach ($array as $key => $item) {
                $result[] = $this->arrayAdapter->toObject($item, $object[$key]);
            }

            return $result;
        }

        return $this->arrayAdapter->toObject($array, $object);
    }

    /**
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
     * @param integer $unserializeMode
     */
    public function setUnserializeMode($unserializeMode)
    {
        $this->arrayAdapter->setUnserializeMode($unserializeMode);
    }
}
