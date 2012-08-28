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

use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class PropertyMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $unitUnderTest = new PropertyMetadata('testProperty');
        $this->assertEquals(false, $unitUnderTest->isExpose());
        $this->assertInstanceOf('\Serializable', $unitUnderTest);
        $this->assertEquals('testProperty', $unitUnderTest->getName());
        $this->assertEquals('testProperty', $unitUnderTest->getSerializedName());
    }

    /**
     * @depends testConstructor
     */
    public function testSettersGetters()
    {
        $unitUnderTest = new PropertyMetadata('testProperty');
        $this->assertEquals(false, $unitUnderTest->isExpose());
        $unitUnderTest->setExpose(true);
        $this->assertEquals(true, $unitUnderTest->isExpose());
        $unitUnderTest->setSerializedName('serialized');
        $this->assertEquals('serialized', $unitUnderTest->getSerializedName());
        $unitUnderTest->setType('string');
        $this->assertEquals('string', $unitUnderTest->getType());
    }

    /**
     * @depends testConstructor
     * @depends testSettersGetters
     */
    public function testSerializeUnserialize()
    {
        $unitUnderTest = new PropertyMetadata('test');
        $unitUnderTest->setExpose(true)
            ->setSerializedName('serialize')
            ->setType('string');

        $serializedString = serialize($unitUnderTest);
        $unserializedObject = unserialize($serializedString);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Metadata\PropertyMetadata', $unserializedObject);
        $this->assertEquals('test', $unserializedObject->getName());
        $this->assertTrue($unserializedObject->isExpose());
        $this->assertEquals('string', $unserializedObject->getType());
        $this->assertEquals('serialize', $unserializedObject->getSerializedName());
    }
}
