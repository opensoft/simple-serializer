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
     * @param $arrayAdapter
     * @param $serializerAdapter
     */
    public function __construct($arrayAdapter, $serializerAdapter)
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
     * @param object $object
     * @return object string
     */
    public function unserialize($data, $object)
    {
        $array = $this->serializerAdapter->unserialize($data);

        return $this->arrayAdapter->toObject($array, $object);
    }
}
