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
class E
{
    private $rid;
    public $object;
    private $arrayOfObjects;

    public function setArrayOfObjects($arrayOfObjects)
    {
        $this->arrayOfObjects = $arrayOfObjects;

        return $this;
    }

    public function getArrayOfObjects()
    {
        return $this->arrayOfObjects;
    }

    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setRid($rid)
    {
        $this->rid = $rid;

        return $this;
    }

    public function getRid()
    {
        return $this->rid;
    }
}
