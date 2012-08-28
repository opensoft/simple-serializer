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

use Opensoft\SimpleSerializer\Adapter\SerializerAdapterInterface;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class JsonAdapter implements SerializerAdapterInterface
{
    /**
     * @param array $data
     * @return mixed|string
     */
    public function serialize(array $data)
    {
        return json_encode($data);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function unserialize($data)
    {
        return json_decode($data, true);
    }
}
