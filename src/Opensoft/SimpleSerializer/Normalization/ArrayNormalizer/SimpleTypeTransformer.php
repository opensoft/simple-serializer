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

use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class SimpleTypeTransformer implements Transformer
{
    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @return mixed
     */
    public function normalize($value, $property)
    {
        return $this->transform($value, $property);
    }

    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @param mixed $object
     * @return mixed
     */
    public function denormalize($value, $property, $object)
    {
        return $this->transform($value, $property);
    }

    /**
     * @param $value
     * @param $property
     * @return bool|float|int|string
     * @throws InvalidArgumentException
     */
    private function transform($value, $property)
    {
        $type = $property->getType();

        if ($type === 'string') {
            $value = (string)$value;
        } elseif ($type === 'boolean') {
            $value = (boolean)$value;
        } elseif ($type === 'integer') {
            $value = (integer)$value;
        } elseif ($type === 'double') {
            $value = (double)$value;
        }
        else {
            throw new InvalidArgumentException(sprintf('Type "%s" isn\'t simple.', $type));
        }

        return $value;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function supportType($type)
    {
        return in_array(
            $type,
            array('string', 'boolean', 'integer', 'double'),
            true
        );
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function supportValueForNormalization($value)
    {
        return true;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function supportValueForDenormalization($value)
    {
       return true;
    }
}
