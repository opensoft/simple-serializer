<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2014 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;

use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class ArrayTransformer implements Transformer
{
    /**
     * @var DataProcessor
     */
    private $processor;

    /**
     * @var ArrayNormalizer
     */
    private $normalizer;

    /**
     * @param ArrayNormalizer $normalizer
     * @param DataProcessor $processor
     */
    public function __construct(ArrayNormalizer $normalizer, DataProcessor $processor)
    {
        $this->normalizer = $normalizer;
        $this->processor = $processor;
    }

    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     *
     * @return array|bool|float|int|string|null
     */
    public function normalize($value, $property)
    {
        $result = array();
        $itemProperty = $this->makeItemProperty($property);

        foreach ($value as $subKey => $subValue) {
            $result[$subKey] = $this->processor->normalizeProcess($this->normalizer, $subValue, $itemProperty);
        }

        return $result;
    }

    /**
     * @param array $value
     * @param PropertyMetadata $property
     * @param mixed $object
     * @return array
     */
    public function denormalize($value, $property, $object)
    {
        $result = array();
        $itemProperty = $this->makeItemProperty($property);
        $existsData = ObjectHelper::expose($object, $property);
        $inner = false;
        foreach ($value as $subKey => $subValue) {
            $tmpObject = $object;
            if (isset($existsData[$subKey]) && is_object($existsData[$subKey])) {
                $tmpObject = $existsData[$subKey];
                $inner = true;
            }
            $result[$subKey] = $this->processor->denormalizeProcess($this->normalizer, $subValue, $itemProperty, $tmpObject, $inner);
            unset($tmpObject);
        }

        return $result;
    }

    /**
     * @param PropertyMetadata $property
     * @return PropertyMetadata
     */
    private function makeItemProperty(PropertyMetadata $property)
    {
        $itemProperty = new PropertyMetadata($property->getName());
        $itemProperty->setExpose(true)->setSerializedName($property->getSerializedName());
        if (preg_match('/array<(?<type>[a-zA-Z\\\]+)>/', $property->getType(), $matches)) {
            $itemProperty->setType($matches['type']);
        }

        return $itemProperty;
    }
}
