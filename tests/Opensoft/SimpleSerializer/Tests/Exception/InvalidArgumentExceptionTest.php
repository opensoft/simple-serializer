<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Exception;

use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exceptionClass = new InvalidArgumentException();
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Exception\Exception', $exceptionClass);
        $this->assertInstanceOf('\InvalidArgumentException', $exceptionClass);
    }
}
