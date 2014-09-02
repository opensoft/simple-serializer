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

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class InnerObjectHandler
{
    public function handle(ArrayNormalizer $normalizer, $value, $type, $property, $object, $inner)
    {
        if ($inner) {
            $innerObject = $object;
        } else {
            $innerObject = ObjectHandler::serializationHandle($object, $property);
        }
        if (!is_object($innerObject) || !$innerObject instanceof $type) {
            if (PHP_VERSION_ID >= 50400) {
                $rc = new \ReflectionClass($type);
                $innerObject = $rc->newInstanceWithoutConstructor();
            } else {
                $innerObject = unserialize(sprintf('O:%d:"%s":0:{}', strlen($type), $type));
            }
        }
        return $normalizer->denormalize($value, $innerObject);
    }
} 