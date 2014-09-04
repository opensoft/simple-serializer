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
class DataProcessor
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

        if (SimpleTypeTransformer::isSimpleType($type)) {
            $simpleTypeTransformer = new SimpleTypeTransformer();
            $value = $simpleTypeTransformer->normalize($value, $property);
            unset($simpleTypeTransformer);
        } elseif ($type === 'DateTime' || ($type[0] === 'D' && strpos($type, 'DateTime<') === 0)) {
            $dateTimeTransformer = new DateTimeTransformer();
            $value= $dateTimeTransformer->normalize($value, $property);
            unset($dateTimeTransformer);
        } elseif ($type === 'array' || ($type[0] === 'a' && strpos($type, 'array<') === 0)) {
            $arrayTransformer = new ArrayTransformer($normalizer, $this);
            $value = $arrayTransformer->normalize($value, $property);
            unset($arrayTransformer);
        } elseif (is_object($value)) {
            $innerObjectTransformer = new InnerObjectTransformer($normalizer);
            $value = $innerObjectTransformer->normalize($value, $property);
            unset($innerObjectTransformer);
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

        if (SimpleTypeTransformer::isSimpleType($type)) {
            $simpleTypeTransformer = new SimpleTypeTransformer();
            $value = $simpleTypeTransformer->denormalize($value, $property, $object);
            unset($simpleTypeTransformer);
        } elseif ($type === 'DateTime' || ($type[0] === 'D' && strpos($type, 'DateTime<') === 0)) {
            $dateTimeTransformer = new DateTimeTransformer();
            $value = $dateTimeTransformer->denormalize($value, $property, $object);
            unset($dateTimeTransformer);
        } elseif ($type === 'array' || ($type[0] === 'a' && strpos($type, 'array<') === 0)) {
            $arrayTransformer = new ArrayTransformer($normalizer, $this);
            $value = $arrayTransformer->denormalize($value, $property, $object);
            unset($arrayTransformer);
        } elseif (is_array($value)) {
            if (!$inner) {
                $object = ObjectHelper::expose($object, $property);;
            }
            $innerObjectTransformer = new InnerObjectTransformer($normalizer);
            $value = $innerObjectTransformer->denormalize($value, $property, $object);
            unset($innerObjectTransformer);
        } elseif ($type !== null) {
            throw new InvalidArgumentException(sprintf('Unsupported type: %s', $type));
        }

        return $value;
    }
}
