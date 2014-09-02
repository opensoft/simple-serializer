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

use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class HandlerProcessor
{
    /**
     * @param ArrayNormalizer $normalizer
     * @param mixed $value
     * @param PropertyMetadata $property
     * @return array|bool|float|int|null|string
     * @throws InvalidArgumentException
     */
    public function normalizeProcess(ArrayNormalizer $normalizer, $value, $property)
    {
        if ($value === null) {
            return null;
        }

        $type = $property->getType();

        if ($type === 'string') {
            $value = (string)$value;
        } elseif ($type === 'boolean') {
            $value = (boolean)$value;
        } elseif ($type === 'integer') {
            $value = (integer)$value;
        } elseif ($type === 'double') {
            $value = (double)$value;
        } elseif ($type === 'DateTime' || ($type[0] === 'D' && strpos($type, 'DateTime<') === 0)) {
            $dateHandler = new DateTimeHandler();
            $value= $dateHandler->normalizationHandle($value, $property);
            unset($dateHandler);
        } elseif ($type === 'array' || ($type[0] === 'a' && strpos($type, 'array<') === 0)) {
            $arrayHandler = new ArrayHandler($normalizer, $this);
            $value = $arrayHandler->normalizationHandle($value, $property);
            unset($arrayHandler);
        } elseif (is_object($value)) {
            $innerObjectHandler = new InnerObjectHandler($normalizer);
            $value = $innerObjectHandler->normalizationHandle($value, $property);
            unset($innerObjectHandler);
        } elseif ($type !== null) {
            throw new InvalidArgumentException(sprintf('Unsupported type: %s', $type));
        }

        return $value;
    }

    /**
     * @param ArrayNormalizer $normalizer
     * @param mixed $value
     * @param PropertyMetadata $property
     * @param mixed $object
     * @param bool $inner
     * @return array|bool|\DateTime|float|int|null|string
     * @throws InvalidArgumentException
     */
    public function denormalizeProcess(ArrayNormalizer $normalizer, $value, $property, $object, $inner = false)
    {
        if ($value === null) {
            return null;
        }

        $type = $property->getType();

        if ($type === 'string') {
            $value = (string)$value;
        } elseif ($type === 'boolean') {
            $value = (boolean)$value;
        } elseif ($type === 'integer') {
            $value = (integer)$value;
        } elseif ($type === 'double') {
            $value = (double)$value;
        } elseif ($type === 'DateTime' || ($type[0] === 'D' && strpos($type, 'DateTime<') === 0)) {
            $dateHandler = new DateTimeHandler();
            $value = $dateHandler->denormalizationHandle($value, $property, $object);
            unset($dateHandler);
        } elseif ($type === 'array' || ($type[0] === 'a' && strpos($type, 'array<') === 0)) {
            $arrayHandler = new ArrayHandler($normalizer, $this);
            $value = $arrayHandler->denormalizationHandle($value, $property, $object);
            unset($arrayHandler);
        } elseif (is_array($value)) {
            $innerObjectHandler = new InnerObjectHandler($normalizer, $inner);
            $value = $innerObjectHandler->denormalizationHandle($value, $property, $object);
            unset($innerObjectHandler);
        } elseif ($type !== null) {
            throw new InvalidArgumentException(sprintf('Unsupported type: %s', $type));
        }

        return $value;
    }
}
