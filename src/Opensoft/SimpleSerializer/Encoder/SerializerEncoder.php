<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Encoder;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
interface SerializerEncoder
{
    /**
     * Encode an array
     *
     * @abstract
     * @param mixed $data
     * @return mixed
     */
    public function encode($data);

    /**
     * Decode an object with data
     *
     * @abstract
     * @param mixed $data Data to decode with
     * @return array
     */
    public function decode($data);
}
