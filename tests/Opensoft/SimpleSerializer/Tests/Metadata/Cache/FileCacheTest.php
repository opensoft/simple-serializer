<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Metadata\Cache;

use Opensoft\SimpleSerializer\Metadata\ClassMetadata;
use Opensoft\SimpleSerializer\Metadata\Cache\FileCache;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class FileCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileCache
     */
    private $unitUderTest;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testConstructorDirectoryNotExists()
    {
        $fileCache = new FileCache('/NotExistsDir');
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testConstructorDeniedDirectory()
    {
        $fileCache = new FileCache('/');
    }

    public function testPutClassMetadataInCache()
    {
        $metadata = new ClassMetadata('test');
        $metadata->addFileResource(__FILE__);
        $this->unitUderTest->putClassMetadataInCache($metadata);
        $this->assertFileExists($this->cacheDir . '/test.cache.php');
    }

    /**
     * @depends testPutClassMetadataInCache
     */
    public function testLoadClassMetadataFromCache()
    {
        $metadata = new ClassMetadata('test');
        $metadata->addFileResource(__FILE__);
        $this->unitUderTest->putClassMetadataInCache($metadata);
        $result = $this->unitUderTest->loadClassMetadataFromCache('test');
        $this->assertInstanceOf('\Opensoft\SimpleSerializer\Metadata\ClassMetadata', $result);
        $this->assertCount(1, $result->getFileResources());
        $this->assertEquals('test', $result->getName());
        $this->assertEquals($metadata, $result);

        $result = $this->unitUderTest->loadClassMetadataFromCache('testNotExistClass');
        $this->assertNull($result);
    }

    /**
     * @depends testPutClassMetadataInCache
     */
    public function testRemoveClassMetadataFromCache()
    {
        $metadata = new ClassMetadata('test');
        $metadata->addFileResource(__FILE__);
        $this->unitUderTest->putClassMetadataInCache($metadata);
        $this->unitUderTest->removeClassMetadataFromCache('test');
        $this->assertFileNotExists($this->cacheDir . '/test.cache.php');
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->cacheDir = __DIR__ . '/../../../../../data/cache';
        mkdir($this->cacheDir, 0777, true);
        $this->unitUderTest = $this->getMockForAbstractClass(
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
