<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests;

use Opensoft\SimpleSerializer\Serializer;
use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use DateTime;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var Serializer
     */
    private $unitUnderTest;

    public function testSerialize()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $aChildren = new AChildren();
        $aChildren->setRid(1);
        $aChildren->setStatus(true);
        $aChildren->setHiddenStatus(true);
        $aChildren->setFloat(3.23);
        $aChildren->setArray(array(3,4));
        $aChildren->setAssocArray(array('tr' => 2));
        $aChildren->setDateTime($testTime);
        $aChildren->setNull(null);
        $aChildren->setName('name');
        $e = new E();
        $e->setRid(3);
        $e->setObject($aChildren);
        $e->setArrayOfObjects(array($aChildren));
        $result = $this->unitUnderTest->serialize($e);
        $expectedString = '{"rid":3,"object":{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,4],"assocArray":{"tr":2}},"arrayOfObjects":[{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,4],"assocArray":{"tr":2}}]}';
        $this->assertEquals($expectedString, $result);
    }

    public function testUnserialize()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $data = '{"rid":3,"object":{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,4],"assocArray":{"tr":2}},"arrayOfObjects":[{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,4],"assocArray":{"tr":2}}]}';
        $emptyObject = new E();
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E', $result);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $result->getObject());
        $objects = $result->getArrayOfObjects();
        $this->assertCount(1, $objects);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $objects[0]);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals(1, $result->getObject()->getRid());
        $this->assertEquals('name', $result->getObject()->getName());
        $this->assertTrue($result->getObject()->getStatus());
        $this->assertNull($result->getObject()->getHiddenStatus());
        $this->assertEquals(3.23, $result->getObject()->getFloat());
        $this->assertNull($result->getObject()->getNull());
        $arrayA = $result->getObject()->getArray();
        $this->assertCount(2, $arrayA);
        $this->assertEquals(3, $arrayA[0]);
        $this->assertEquals(4, $arrayA[1]);
        $arrayAssoc = $result->getObject()->getAssocArray();
        $this->assertCount(1, $arrayAssoc);
        $this->assertArrayHasKey('tr', $arrayAssoc);
        $this->assertEquals(2, $arrayAssoc['tr']);
        $this->assertInstanceOf('\DateTime', $result->getObject()->getDateTime());
        $this->assertEquals($testTime->format(\DateTime::ISO8601), $result->getObject()->getDateTime()->format(DateTime::ISO8601));
    }

    protected function setUp()
    {
        $locator = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\FileLocator',
            array(
                array(
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/Metadata/Driver/Fixture/A',
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B' => __DIR__ . '/Metadata/Driver/Fixture/B'
                )
            )
        );

        $driver = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver',
            array($locator)
        );
        $this->metadataFactory = new MetadataFactory($driver);
        $arrayAdapter = $this->getMockForAbstractClass('\Opensoft\SimpleSerializer\Adapter\ArrayAdapter', array($this->metadataFactory));
        $serializerAdapter = $this->getMockForAbstractClass('Opensoft\SimpleSerializer\Adapter\JsonAdapter');
        $this->unitUnderTest = new Serializer($arrayAdapter, $serializerAdapter);
    }
}
