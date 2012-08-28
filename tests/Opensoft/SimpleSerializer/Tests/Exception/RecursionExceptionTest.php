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

use Opensoft\SimpleSerializer\Exception\RecursionException;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class RecursionExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exceptionClass = new RecursionException();
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Exception\Exception', $exceptionClass);
        $this->assertInstanceOf('\LogicException', $exceptionClass);
    }
}
