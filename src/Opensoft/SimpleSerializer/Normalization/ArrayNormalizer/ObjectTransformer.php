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
class ObjectTransformer implements Transformer
{
    /**
     * @var ArrayNormalizer
     */
    private $normalizer;

    /**
     * @param ArrayNormalizer $normalizer
     */
    public function __construct(ArrayNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @return mixed
     */
    public function normalize($value, $property)
    {
        return $this->normalizer->normalize($value);
    }

    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @param object $object
     * @return object
     */
    public function denormalize($value, $property, $object)
    {
        $type = $property->getType();

        if (!is_object($object) || !$object instanceof $type) {
            if (PHP_VERSION_ID >= 50400) {
                $rc = new \ReflectionClass($type);
                $object = $rc->newInstanceWithoutConstructor();
            } else {
                $object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($type), $type));
            }
        }
        return $this->normalizer->denormalize($value, $object);
    }
}
