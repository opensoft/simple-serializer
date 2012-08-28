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
class AChildren extends A
{
    private $float;
    private $dateTime;
    private $null;
    private $array = array();
    private $assocArray = array();

    public function setAssocArray($assocArray)
    {
        $this->assocArray = $assocArray;

        return $this;
    }

    public function getAssocArray()
    {
        return $this->assocArray;
    }

    public function setArray($array)
    {
        $this->array = $array;

        return $this;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function setDateTime($datTime)
    {
        $this->dateTime = $datTime;

        return $this;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }

    public function setFloat($float)
    {
        $this->float = $float;

        return $this;
    }

    public function getFloat()
    {
        return $this->float;
    }

    public function setNull($null)
    {
        $this->null = $null;

        return $this;
    }

    public function getNull()
    {
        return $this->null;
    }
}
