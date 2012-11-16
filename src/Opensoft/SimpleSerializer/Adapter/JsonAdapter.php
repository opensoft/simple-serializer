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
use Opensoft\SimpleSerializer\Exception\UnserializedException;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class JsonAdapter implements SerializerAdapterInterface
{
    /**
     * @param mixed $data
     * @return mixed|string
     */
    public function serialize($data)
    {
        return json_encode($data);
    }

    /**
     * @param mixed $data
     * @throws UnserializedException
     * @return mixed
     */
    public function unserialize($data)
    {
        $result = json_decode($data, true);
        if ($result === null && strtolower($data) !== "null") {
            throw new UnserializedException('JSON cannot be decoded');
        }

        return $result;
    }
}
