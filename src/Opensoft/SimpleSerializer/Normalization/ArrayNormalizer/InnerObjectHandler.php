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
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class InnerObjectHandler implements Handler
{
    /**
     * @var ArrayNormalizer
     */
    private $normalizer;

    /**
     * @var bool
     */
    private $inner;

    /**
     * @param ArrayNormalizer $normalizer
     * @param bool $inner
     */
    public function __construct(ArrayNormalizer $normalizer, $inner = false)
    {
        $this->normalizer = $normalizer;
        $this->inner = $inner;
    }

    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @return mixed
     */
    public function normalizationHandle($value, $property)
    {
        return $this->normalizer->normalize($value);
    }

    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @param object $object
     * @return object
     */
    public function denormalizationHandle($value, $property, $object)
    {
        $type = $property->getType();
        if ($this->inner) {
            $innerObject = $object;
        } else {
            $innerObject = ObjectHelper::expose($object, $property);
        }
        if (!is_object($innerObject) || !$innerObject instanceof $type) {
            if (PHP_VERSION_ID >= 50400) {
                $rc = new \ReflectionClass($type);
                $innerObject = $rc->newInstanceWithoutConstructor();
            } else {
                $innerObject = unserialize(sprintf('O:%d:"%s":0:{}', strlen($type), $type));
            }
        }
        return $this->normalizer->denormalize($value, $innerObject);
    }
}
