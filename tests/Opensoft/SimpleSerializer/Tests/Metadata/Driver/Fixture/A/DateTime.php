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
class DateTime
{
    private $emptyDateTimeFormat;
    private $dateTimeConstant;
    private $dateTimeString;
    private $dateTimeStringWithSpace;

    public function setDateTimeStringWithSpace($dateTimeStringWithSpace)
    {
        $this->dateTimeStringWithSpace = $dateTimeStringWithSpace;

        return $this;
    }

    public function getDateTimeStringWithSpace()
    {
        return $this->dateTimeStringWithSpace;
    }

    public function setDateTimeConstant($dateTimeConstant)
    {
        $this->dateTimeConstant = $dateTimeConstant;

        return $this;
    }

    public function getDateTimeConstant()
    {
        return $this->dateTimeConstant;
    }

    public function setDateTimeString($dateTimeString)
    {
        $this->dateTimeString = $dateTimeString;

        return $this;
    }

    public function getDateTimeString()
    {
        return $this->dateTimeString;
    }

    public function setEmptyDateTimeFormat($emptyDateTimeFormat)
    {
        $this->emptyDateTimeFormat = $emptyDateTimeFormat;

        return $this;
    }

    public function getEmptyDateTimeFormat()
    {
        return $this->emptyDateTimeFormat;
    }
}
