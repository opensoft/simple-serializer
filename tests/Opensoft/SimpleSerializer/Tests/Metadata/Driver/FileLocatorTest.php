<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Metadata\Driver;

use Opensoft\SimpleSerializer\Metadata\Driver\FileLocator;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class FileLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testFindFileForClass()
    {
        $locator = new FileLocator(array(
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/Fixture/A',
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B' => __DIR__ . '/Fixture/B'
        ));

        $this->assertEquals(
            realpath(__DIR__ . '/Fixture/A/A.yml'),
            realpath($locator->findFileForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', 'yml'))
        );

        $this->assertEquals(
            realpath(__DIR__.'/Fixture/B/SubDir.B.yml'),
            realpath($locator->findFileForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B\SubDir\B', 'yml'))
        );
    }

    public function testFindFileForClassNullCase()
    {
        $locator = new FileLocator(array());
        $this->assertNull($locator->findFileForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B\SubDir\B', 'yml'));
    }
}
