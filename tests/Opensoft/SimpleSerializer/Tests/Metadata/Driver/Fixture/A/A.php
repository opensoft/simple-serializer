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
class A
{
    public $rid;
    private $name;
    private $status;
    private $hiddenStatus;

    public function setHiddenStatus($hiddenStatus)
    {
        $this->hiddenStatus = $hiddenStatus;

        return $this;
    }

    public function getHiddenStatus()
    {
        return $this->hiddenStatus;
    }

    public function setRid($id)
    {
        $this->rid = $id;

        return $this;
    }

    public function getRid()
    {
        return $this->rid;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
