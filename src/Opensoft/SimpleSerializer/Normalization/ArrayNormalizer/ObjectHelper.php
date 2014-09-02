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

use Opensoft\SimpleSerializer\Exception\RecursionException;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class ObjectHelper
{
    /**
     * @param $object
     * @param $property
     * @return mixed
     * @throws RecursionException
     */
    public static function expose($object, $property)
    {
        $attributes = get_object_vars($object);
        if (array_key_exists($propertyName = $property->getName(), $attributes)) {
            $value =  $object->$propertyName;
        } else {
            $value = call_user_func(array($object, 'get' . ucfirst($property->getName())));
        }

        if ($value === $object) {
            throw new RecursionException(sprintf('Invalid self reference detected. %s::%s', self::getFullClassName($object), $property->getName()));
        }

        return $value;
    }

    /**
     * @param $object
     * @param $property
     * @param $value
     */
    public static function involve($object, $property, $value)
    {
        $attributes = get_object_vars($object);
        if (array_key_exists($propertyName = $property->getName(), $attributes)) {
            $object->$propertyName = $value;
        } else {
            call_user_func_array(array($object, 'set' . ucfirst($property->getName())), array($value));
        }
    }

    /**
     * @param object $object
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getFullClassName($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Argument is not an object');
        }

        return get_class($object);
    }
}
