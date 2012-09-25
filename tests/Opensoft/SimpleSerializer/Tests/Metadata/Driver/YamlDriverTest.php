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
use Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class YamlDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadMetadataForClass()
    {
        $locator = new FileLocator(array(
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/Fixture/A',
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B' => __DIR__ . '/Fixture/B'
        ));

        $unitUnderTest = new YamlDriver($locator);

        $result = $unitUnderTest->loadMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A');

        $this->assertInstanceOf('Opensoft\SimpleSerializer\Metadata\ClassMetadata', $result);
        $this->assertEquals('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result->getName());
        $properties = $result->getProperties();
        $this->assertCount(4, $properties);
        $this->assertCount(1, $result->getFileResources());

        $this->assertEquals('rid', $properties['rid']->getName());
        $this->assertEquals('id', $properties['rid']->getSerializedName());
        $this->assertEquals('integer', $properties['rid']->getType());
        $this->assertTrue($properties['rid']->isExpose());

        $this->assertEquals('name', $properties['name']->getName());
        $this->assertEquals('name', $properties['name']->getSerializedName());
        $this->assertEquals('string', $properties['name']->getType());
        $this->assertTrue($properties['name']->isExpose());

        $this->assertEquals('status', $properties['status']->getName());
        $this->assertEquals('status', $properties['status']->getSerializedName());
        $this->assertEquals('boolean', $properties['status']->getType());
        $this->assertTrue($properties['status']->isExpose());
        $this->assertEquals('1.1', $properties['status']->getSinceVersion());
        $this->assertEquals('2.1', $properties['status']->getUntilVersion());
        $this->assertEquals(array('post', 'patch', 'get'), $properties['status']->getGroups());

        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getName());
        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getSerializedName());
        $this->assertEquals('boolean', $properties['hiddenStatus']->getType());
        $this->assertFalse($properties['hiddenStatus']->isExpose());

    }

    public function testLoadMetadataForClassNotFoundCase()
    {
        $locator = new FileLocator(array(
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/Fixture/A',
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B' => __DIR__ . '/Fixture/B'
        ));

        $unitUnderTest = new YamlDriver($locator);

        $result = $unitUnderTest->loadMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A');
        $this->assertNull($result);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\RuntimeException
     */
    public function testLoadMetadataForClassUnexpectedClassCase()
    {
        $locator = new FileLocator(array(
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/Fixture/A',
            'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B' => __DIR__ . '/Fixture/B'
        ));

        $unitUnderTest = new YamlDriver($locator);

        $unitUnderTest->loadMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B\SubDir\B');
    }
}
