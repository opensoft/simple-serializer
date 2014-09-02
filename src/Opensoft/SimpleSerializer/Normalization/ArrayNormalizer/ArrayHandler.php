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
class ArrayHandler
{
    /**
     * @var HandlerProcessor
     */
    private $processor;


    public function __construct(HandlerProcessor $processor)
    {
        $this->processor = $processor;
    }
    /**
     * @param ArrayNormalizer $normalizer
     * @param mixed $value
     * @param string $type
     * @param PropertyMetadata $property
     * @param int $direct
     * @param null|mixed $object
     * @throws InvalidArgumentException
     * @return array|bool|float|int|string|null
     */
    public function handle(ArrayNormalizer $normalizer, $value, $type, $property, $direct, $object = null)
    {
        $tmpResult = array();
        $tmpType = new PropertyMetadata($property->getName());
        $tmpType->setExpose(true)->setSerializedName($property->getSerializedName());
        if (preg_match('/array<(?<type>[a-zA-Z\\\]+)>/', $type, $matches)) {
            $tmpType->setType($matches['type']);
        }
        if ($direct == ArrayNormalizer::DIRECTION_UNSERIALIZE) {
            $existsData = ObjectHandler::serializationHandle($object, $property);
        }
        $inner = false;

        foreach ($value as $subKey => $subValue) {
            $tmpObject = $object;
            if ($direct == ArrayNormalizer::DIRECTION_UNSERIALIZE && isset($existsData[$subKey]) && is_object($existsData[$subKey])) {
                $tmpObject = $existsData[$subKey];
                $inner = true;
            }
            $subValue = $this->processor->process($normalizer, $subValue, $tmpType, $direct, $tmpObject, $inner);
            $tmpResult[$subKey] = $subValue;
            unset($tmpObject);
        }

        return $tmpResult;
    }
}
