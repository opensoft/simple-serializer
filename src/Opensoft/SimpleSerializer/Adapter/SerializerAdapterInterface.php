<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Adapter;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
interface SerializerAdapterInterface
{
    /**
     * Serialize an array
     *
     * @abstract
     * @param array $data
     * @return mixed
     */
    public function serialize(array $data);

    /**
     * Unserialize an object with data
     *
     * @abstract
     * @param mixed $data Data to unserialize with
     * @return array
     */
    public function unserialize($data);
}
