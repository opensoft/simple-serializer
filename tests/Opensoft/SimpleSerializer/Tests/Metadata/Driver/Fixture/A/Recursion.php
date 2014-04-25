<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class Recursion
{
    public $object;

    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }
}
