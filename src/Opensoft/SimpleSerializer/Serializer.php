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

use Opensoft\SimpleSerializer\Normalization\Normalizer;
use Opensoft\SimpleSerializer\Encoder\SerializerEncoder;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Exclusion\Specification;
use Opensoft\SimpleSerializer\Exclusion\VersionSpecification;
use Opensoft\SimpleSerializer\Exclusion\GroupsSpecification;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class Serializer
{
    /**
     * @var Normalizer
     */
    private $normalizer;

    /**
     * @var SerializerEncoder;
     */
    private $serializerEncoder;

    /**
     * @var boolean
     */
    private $level = 0;

    /**
     * @param Normalizer $normalizer
     * @param SerializerEncoder $serializerEncoder
     */
    public function __construct(Normalizer $normalizer, SerializerEncoder $serializerEncoder)
    {
        $this->normalizer = $normalizer;
        $this->serializerEncoder = $serializerEncoder;
    }

    /**
     * @param object|array $data
     * @return string
     */
    public function serialize($data)
    {
        $this->level++;
        $dataAsArray = null;
        if (is_array($data)) {
            $dataAsArray = array();
            foreach ($data as $key => $object) {
                $dataAsArray[$key] = $this->serialize($object);
            }
        } else if (is_object($data)) {
            $dataAsArray = $this->normalizer->normalize($data);
        } else {
            $dataAsArray = $data;
        }
        $this->level--;
        if ($this->level === 0) {
            return $this->serializerEncoder->encode($dataAsArray);
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
            $unserializedData = $this->serializerEncoder->decode($data);
        } else {
            $unserializedData = $data;
        }
        if ($this->normalizer->supportNormalization($targetData)) {
            return $this->normalizer->denormalize($unserializedData, $targetData);
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
        $this->normalizer->cleanUpExclusionSpecifications();
        if (null === $version) {
            return false;
        }

        $this->normalizer->addExclusionSpecification(new VersionSpecification($version));
    }

    /**
     * @deprecated Deprecated since version 1.1 to be removed in 2
     * @param array $groups
     * @return bool
     */
    public function setGroups(array $groups)
    {
        $this->cleanUpExclusionSpecifications();
        if (!$groups) {
            return false;
        }

        $this->normalizer->addExclusionSpecification(new GroupsSpecification($groups));
    }

    /**
     * @param Specification $exclusionSpecification
     */
    public function addExclusionSpecification(Specification $exclusionSpecification)
    {
        $this->normalizer->addExclusionSpecification($exclusionSpecification);
    }

    public function cleanUpExclusionSpecifications()
    {
       $this->normalizer->cleanUpExclusionSpecifications();
    }

    /**
     * @param integer $unserializeMode
     */
    public function setUnserializeMode($unserializeMode)
    {
        $this->normalizer->setUnserializeMode($unserializeMode);
    }
}
