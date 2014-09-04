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

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
interface Transformer
{
    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @return mixed
     */
    function normalize($value, $property);

    /**
     * @param mixed $value
     * @param PropertyMetadata $property
     * @param mixed $object
     * @return mixed
     */
    function denormalize($value, $property, $object);
} 
