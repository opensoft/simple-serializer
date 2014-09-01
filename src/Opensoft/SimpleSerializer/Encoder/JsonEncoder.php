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

use Opensoft\SimpleSerializer\Exception\DecodedException;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class JsonEncoder implements SerializerEncoder
{
    /**
     * @param mixed $data
     * @return mixed|string
     */
    public function encode($data)
    {
        return json_encode($data);
    }

    /**
     * @param mixed $data
     * @throws DecodedException
     * @return mixed
     */
    public function decode($data)
    {
        $result = json_decode($data, true);
        if ($result === null && strtolower($data) !== "null") {
            throw new DecodedException('JSON cannot be decoded');
        }

        return $result;
    }
}
