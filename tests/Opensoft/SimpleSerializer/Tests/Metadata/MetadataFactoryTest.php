<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Metadata;

use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver;
use Opensoft\SimpleSerializer\Metadata\Cache\FileCache;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class MetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YamlDriver
     */
    private $driver;

    /**
     * @var FileCache
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheDir;

    public function testGetMetadataForClass()
    {
        $unitUnderTest = new MetadataFactory($this->driver);
        $result = $unitUnderTest->getMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A');
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Metadata\ClassMetadata', $result);
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

        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getName());
        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getSerializedName());
        $this->assertEquals('boolean', $properties['hiddenStatus']->getType());
        $this->assertFalse($properties['hiddenStatus']->isExpose());
    }

    /**
     * @depends testGetMetadataForClass
     */
    public function testGetMetadataForClassArrayCache()
    {
        $unitUnderTest = new MetadataFactory($this->driver);
        $unitUnderTest->getMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A');
        $result = $unitUnderTest->getMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A');
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Metadata\ClassMetadata', $result);
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

        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getName());
        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getSerializedName());
        $this->assertEquals('boolean', $properties['hiddenStatus']->getType());
        $this->assertFalse($properties['hiddenStatus']->isExpose());
    }

    /**
     * @depends testGetMetadataForClass
     */
    public function testGetMetadataForClassFileCache()
    {
        $unitUnderTest = new MetadataFactory($this->driver);
        $unitUnderTest->setCache($this->cache);

        $unitUnderTestReadCache = new MetadataFactory($this->driver);
        $unitUnderTestReadCache->setCache($this->cache);

        $unitUnderTest->getMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A');
        $result = $unitUnderTestReadCache->getMetadataForClass('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A');
        //die;
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Metadata\ClassMetadata', $result);
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

        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getName());
        $this->assertEquals('hiddenStatus', $properties['hiddenStatus']->getSerializedName());
        $this->assertEquals('boolean', $properties['hiddenStatus']->getType());
        $this->assertFalse($properties['hiddenStatus']->isExpose());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $locator = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\FileLocator',
            array(
                array(
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/../Metadata/Driver/Fixture/A'
                )
            )
        );

        $this->driver = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver',
            array($locator)
        );

        $this->cacheDir = __DIR__ . '/../../../../data/cache';
        mkdir($this->cacheDir, 0777, true);
        $this->cache = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Cache\FileCache',
            array($this->cacheDir)
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        exec('rm -rf ' . $this->cacheDir);
    }
}
