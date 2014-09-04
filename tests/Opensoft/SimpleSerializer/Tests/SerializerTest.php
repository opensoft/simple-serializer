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
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A;
use Opensoft\SimpleSerializer\Exclusion\VersionSpecification;
use Opensoft\SimpleSerializer\Exclusion\GroupsSpecification;
use Opensoft\SimpleSerializer\Normalization\PropertySkipper;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\DataProcessor;
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
        $aChildren->setArray(array(3, null));
        $aChildren->setAssocArray(array('tr' => 2));
        $aChildren->setDateTime($testTime);
        $aChildren->setNull(null);
        $aChildren->setName('name');
        $e = new E();
        $e->setRid(3);
        $e->setObject($aChildren);
        $e->setArrayOfObjects(array($aChildren));
        $result = $this->unitUnderTest->serialize($e);
        $expectedString = '{"rid":3,"object":{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}},"arrayOfObjects":[{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}}]}';
        $this->assertEquals($expectedString, $result);
        $this->assertEquals('[]', $this->unitUnderTest->serialize(array()));
    }

    public function testSerializeGroup()
    {
        $a = new A();
        $a->setRid(1);
        $a->setName('name');
        $a->setStatus(true);
        $a->setHiddenStatus(true);

        $this->unitUnderTest->setGroups(array('test'));
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '[]';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->setGroups(array('get'));
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"status":true}';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->setGroups(array());
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"id":1,"name":"name","status":true}';
        $this->assertEquals($expectedString, $result);
    }

    public function testSerializeVersion()
    {
        $a = new A();
        $a->setRid(1);
        $a->setName('name');
        $a->setStatus(true);
        $a->setHiddenStatus(true);

        $this->unitUnderTest->setVersion('0.5');
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"id":1,"name":"name"}';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->setVersion('2.5');
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"id":1,"name":"name"}';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->setVersion('1.5');
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"id":1,"name":"name","status":true}';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->setVersion(null);
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"id":1,"name":"name","status":true}';
        $this->assertEquals($expectedString, $result);
    }

    public function testSerializeWithExclusions()
    {
        $a = new A();
        $a->setRid(1);
        $a->setName('name');
        $a->setStatus(true);
        $a->setHiddenStatus(true);

        $this->unitUnderTest->addExclusionSpecification(new GroupsSpecification(array('get')));
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"status":true}';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('1.5'));
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"status":true}';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('2.5'));
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '[]';
        $this->assertEquals($expectedString, $result);

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $result = $this->unitUnderTest->serialize($a);
        $expectedString = '{"id":1,"name":"name","status":true}';
        $this->assertEquals($expectedString, $result);
    }

    public function testArraySerialize()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $aChildren = new AChildren();
        $aChildren->setRid(1);
        $aChildren->setStatus(true);
        $aChildren->setHiddenStatus(true);
        $aChildren->setFloat(3.23);
        $aChildren->setArray(array(3, null));
        $aChildren->setAssocArray(array('tr' => 2));
        $aChildren->setDateTime($testTime);
        $aChildren->setNull(null);
        $aChildren->setName('name');
        $e = new E();
        $e->setRid(3);
        $e->setObject($aChildren);
        $e->setArrayOfObjects(array($aChildren));
        $result = $this->unitUnderTest->serialize(array($e));
        $expectedString = '[{"rid":3,"object":{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}},"arrayOfObjects":[{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}}]}]';
        $this->assertEquals($expectedString, $result);
    }

    public function testSerializeSimpleValues()
    {
        $data = null;
        $result = $this->unitUnderTest->serialize($data);
        $this->assertEquals('null', $result);

        $data = 'null';
        $result = $this->unitUnderTest->serialize($data);
        $this->assertEquals('"null"', $result);

        $data = array(null);
        $result = $this->unitUnderTest->serialize($data, null);
        $this->assertEquals('[null]', $result);

        $data = array(1,2);
        $result = $this->unitUnderTest->serialize($data);
        $this->assertEquals('[1,2]', $result);

        $data = array(array(1),2);
        $result = $this->unitUnderTest->serialize($data);
        $this->assertEquals('[[1],2]', $result);

        $data = array('value' => 12, 'array' => array(1, 2, 3));
        $result = $this->unitUnderTest->serialize($data);
        $this->assertEquals('{"value":12,"array":[1,2,3]}', $result);
    }

    public function testUnserialize()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $data = '{"rid":3,"object":{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}},"arrayOfObjects":[{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}}]}';
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
        $this->assertNull($arrayA[1]);
        $arrayAssoc = $result->getObject()->getAssocArray();
        $this->assertCount(1, $arrayAssoc);
        $this->assertArrayHasKey('tr', $arrayAssoc);
        $this->assertEquals(2, $arrayAssoc['tr']);
        $this->assertInstanceOf('\DateTime', $result->getObject()->getDateTime());
        $this->assertEquals($testTime->format(\DateTime::ISO8601), $result->getObject()->getDateTime()->format(DateTime::ISO8601));

        $this->assertEquals(1, $objects[0]->getRid());
        $this->assertEquals('name', $objects[0]->getName());
        $this->assertTrue($objects[0]->getStatus());
        $this->assertNull($objects[0]->getHiddenStatus());
        $this->assertEquals(3.23, $objects[0]->getFloat());
        $this->assertNull($objects[0]->getNull());
        $arrayA = $objects[0]->getArray();
        $this->assertCount(2, $arrayA);
        $this->assertEquals(3, $arrayA[0]);
        $this->assertNull($arrayA[1]);
        $arrayAssoc = $objects[0]->getAssocArray();
        $this->assertCount(1, $arrayAssoc);
        $this->assertArrayHasKey('tr', $arrayAssoc);
        $this->assertEquals(2, $arrayAssoc['tr']);
        $this->assertInstanceOf('\DateTime', $objects[0]->getDateTime());
        $this->assertEquals($testTime->format(\DateTime::ISO8601), $objects[0]->getDateTime()->format(DateTime::ISO8601));
    }

    public function testUnserializeGroup()
    {
        $data = '{"id":1,"name":"name","status":true}';
        $emptyObject = new A();

        $this->unitUnderTest->setGroups(array('test'));
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertNull($result->getRid());
        $this->assertNull($result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->setGroups(array('get'));
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertNull($result->getRid());
        $this->assertNull($result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->setGroups(array());
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testUnserializeVersion()
    {
        $data = '{"id":1,"name":"name","status":true}';
        $emptyObject = new A();

        $this->unitUnderTest->setVersion('0.5');
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->setVersion('2.5');
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->setVersion('1.5');
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->setVersion(null);
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testUnserializeArray()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $data = '[{"rid":3,"object":{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}},"arrayOfObjects":[{"id":1,"name":"name","status":true,"float":3.23,"dateTime":"' . $testTime->format(\DateTime::ISO8601) . '","null":null,"array":[3,null],"assocArray":{"tr":2}}]}]';
        $emptyObject = new E();
        $result = $this->unitUnderTest->unserialize($data, array($emptyObject));
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E', $result[0]);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $result[0]->getObject());
        $objects = $result[0]->getArrayOfObjects();
        $this->assertCount(1, $objects);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $objects[0]);
        $this->assertEquals(3, $result[0]->getRid());
        $this->assertEquals(1, $result[0]->getObject()->getRid());
        $this->assertEquals('name', $result[0]->getObject()->getName());
        $this->assertTrue($result[0]->getObject()->getStatus());
        $this->assertNull($result[0]->getObject()->getHiddenStatus());
        $this->assertEquals(3.23, $result[0]->getObject()->getFloat());
        $this->assertNull($result[0]->getObject()->getNull());
        $arrayA = $result[0]->getObject()->getArray();
        $this->assertCount(2, $arrayA);
        $this->assertEquals(3, $arrayA[0]);
        $this->assertNull($arrayA[1]);
        $arrayAssoc = $result[0]->getObject()->getAssocArray();
        $this->assertCount(1, $arrayAssoc);
        $this->assertArrayHasKey('tr', $arrayAssoc);
        $this->assertEquals(2, $arrayAssoc['tr']);
        $this->assertInstanceOf('\DateTime', $result[0]->getObject()->getDateTime());
        $this->assertEquals($testTime->format(\DateTime::ISO8601), $result[0]->getObject()->getDateTime()->format(DateTime::ISO8601));

        $this->assertEquals(1, $objects[0]->getRid());
        $this->assertEquals('name', $objects[0]->getName());
        $this->assertTrue($objects[0]->getStatus());
        $this->assertNull($objects[0]->getHiddenStatus());
        $this->assertEquals(3.23, $objects[0]->getFloat());
        $this->assertNull($objects[0]->getNull());
        $arrayA = $objects[0]->getArray();
        $this->assertCount(2, $arrayA);
        $this->assertEquals(3, $arrayA[0]);
        $this->assertNull($arrayA[1]);
        $arrayAssoc = $objects[0]->getAssocArray();
        $this->assertCount(1, $arrayAssoc);
        $this->assertArrayHasKey('tr', $arrayAssoc);
        $this->assertEquals(2, $arrayAssoc['tr']);
        $this->assertInstanceOf('\DateTime', $objects[0]->getDateTime());
        $this->assertEquals($testTime->format(\DateTime::ISO8601), $objects[0]->getDateTime()->format(DateTime::ISO8601));
    }

    public function testStrictUnserialize()
    {
        $data = '{"id":1,"name":"name","status":true}';
        $emptyObject = new A();

        $this->unitUnderTest->setUnserializeMode(2);
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testNonStrictUnserialize()
    {
        $data = '{"id":1,"name":"name","status":true}';
        $emptyObject = new A();

        $this->unitUnderTest->setUnserializeMode(0);
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testMediumStrictUnserialize()
    {
        $data = '{"id":1,"name":"name","status":true}';
        $emptyObject = new A();

        $this->unitUnderTest->setUnserializeMode(1);
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testNonStrictUnserializeFailed()
    {
        $data = '{"id":1,"name":"name"}';
        $emptyObject = new A();

        $this->unitUnderTest->setUnserializeMode(0);
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testMediumStrictUnserializeFailed()
    {
        $data = '{"id":1,"name":"name"}';
        $emptyObject = new A();

        $this->unitUnderTest->setUnserializeMode(1);
        $result = $this->unitUnderTest->unserialize($data, $emptyObject);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(1, $result->getRid());
        $this->assertEquals('name', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testStrictUnserializeFailed()
    {
        $data = '{"id":1,"name":"name"}';
        $emptyObject = new A();

        $this->unitUnderTest->setUnserializeMode(2);
        $this->unitUnderTest->unserialize($data, $emptyObject);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testNonAcceptableUnserializeMode()
    {
        $this->unitUnderTest->setUnserializeMode(5);
    }

    public function testUnserializeSimpleValues()
    {
        $data = 'null';
        $result = $this->unitUnderTest->unserialize($data, null);
        $this->assertNull($result);

        $data = '"null"';
        $result = $this->unitUnderTest->unserialize($data, null);
        $this->assertEquals('null', $result);

        $data = '[null]';
        $result = $this->unitUnderTest->unserialize($data, null);
        $this->assertEquals(array(null), $result);

        $data = '[1,2]';
        $result = $this->unitUnderTest->unserialize($data, array());
        $this->assertEquals(array(1,2), $result);

        $data = '[1,2]';
        $result = $this->unitUnderTest->unserialize($data, array(2,1));
        $this->assertEquals(array(1,2), $result);

        $data = '[[1],2]';
        $result = $this->unitUnderTest->unserialize($data);
        $this->assertEquals(array(array(1),2), $result);

        $data = '{"value": 12, "array":[1,2,3]}';
        $result = $this->unitUnderTest->unserialize($data);
        $this->assertEquals(array('value' => 12, 'array' => array(1, 2, 3)), $result);
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
        $arrayNormalizer = $this->getMockForAbstractClass('\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer', array($this->metadataFactory, new PropertySkipper(), new DataProcessor()));
        $serializerEncoder = $this->getMockForAbstractClass('Opensoft\SimpleSerializer\Encoder\JsonEncoder');
        $this->unitUnderTest = new Serializer($arrayNormalizer, $serializerEncoder);
    }
}
