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
use Opensoft\SimpleSerializer\Metadata\ClassHierarchyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class ClassHierarchyMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testAddClassMetadata()
    {
        $metadata = new ClassMetadata('testClass');
        sleep(2);
        $unitUnderTest = new ClassHierarchyMetadata();
        $property = new PropertyMetadata('property');
        $property->setType('string');
        $metadata->addFileResource('/tmp/fileNotExists')
            ->addPropertyMetadata($property);

        $unitUnderTest->addFileResource('/tmp/fileNotExists2');
        $property = new PropertyMetadata('property2');
        $property->setType('integer');
        $unitUnderTest->addPropertyMetadata($property);

        $unitUnderTest->addClassMetadata($metadata);
        $this->assertEquals($metadata->getName(), $unitUnderTest->getName());
        $this->assertEquals($metadata->getCreatedAt(), $unitUnderTest->getCreatedAt());
        $this->assertCount(2, $unitUnderTest->getFileResources());
        $this->assertCount(2, $unitUnderTest->getProperties());
        $this->assertEquals(array('/tmp/fileNotExists2', '/tmp/fileNotExists'), $unitUnderTest->getFileResources());
        $this->assertArrayHasKey('property', $unitUnderTest->getProperties());
        $this->assertArrayHasKey('property2', $unitUnderTest->getProperties());
    }
}
