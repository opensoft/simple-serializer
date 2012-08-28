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
use Opensoft\SimpleSerializer\Metadata\ClassMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $unitUnderTest = new ClassMetadata('testClass');
        $this->assertInstanceOf('\Serializable', $unitUnderTest);
        $this->assertEquals('testClass', $unitUnderTest->getName());
        $this->assertNotNull($unitUnderTest->getCreatedAt());
        $this->assertEmpty($unitUnderTest->getFileResources());
        $this->assertEmpty($unitUnderTest->getProperties());
        $this->assertTrue($unitUnderTest->isFresh());
    }

    /**
     * @depends testConstructor
     */
    public function testSettersGetters()
    {
        $unitUnderTest = new ClassMetadata('testClass');
        $unitUnderTest->addFileResource(__FILE__);

        $this->assertCount(1, $unitUnderTest->getFileResources());
        $this->assertContains(__FILE__, $unitUnderTest->getFileResources());
        $this->assertTrue($unitUnderTest->isFresh());
        $this->assertFalse($unitUnderTest->isFresh(0));

        $unitUnderTest->addFileResource('/tmp/NotExistsFile');
        $this->assertCount(2, $unitUnderTest->getFileResources());
        $this->assertFalse($unitUnderTest->isFresh());

        $property = new PropertyMetadata('first_property');
        $unitUnderTest->addPropertyMetadata($property);

        $this->assertCount(1, $unitUnderTest->getProperties());
        $this->assertArrayHasKey('first_property', $unitUnderTest->getProperties());
        $this->assertContains($property, $unitUnderTest->getProperties());
    }

    /**
     * @depends testConstructor
     * @depends testSettersGetters
     */
    public function testSerializeUnserialize()
    {
        $unitUnderTest = new ClassMetadata('testClass');
        $property = new PropertyMetadata('property');
        $property->setType('string');
        $unitUnderTest->addFileResource('/tmp/fileNotExists')
            ->addPropertyMetadata($property);

        $serializedString = serialize($unitUnderTest);
        $unserializedObject = unserialize($serializedString);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Metadata\ClassMetadata', $unserializedObject);
        $this->assertEquals('testClass', $unserializedObject->getName());
        $this->assertNotNull($unserializedObject->getCreatedAt());

        $fileResources = $unserializedObject->getFileResources();
        $this->assertCount(1, $fileResources);
        $this->assertEquals('/tmp/fileNotExists', $fileResources[0]);
        $properties = $unserializedObject->getProperties();
        $this->assertCount(1, $properties);
        $this->assertArrayHasKey('property', $properties);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Metadata\PropertyMetadata', $properties['property']);
        $this->assertEquals('property', $properties['property']->getName());
        $this->assertEquals('string', $properties['property']->getType());
    }
}
