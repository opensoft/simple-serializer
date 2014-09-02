<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Normalization;

use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\D;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\Recursion;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\DateTime as TestDateTime;
use Opensoft\SimpleSerializer\Normalization\Normalizer;
use Opensoft\SimpleSerializer\Normalization\PropertySkipper;
use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Exclusion\GroupsSpecification;
use Opensoft\SimpleSerializer\Exclusion\VersionSpecification;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\HandlerProcessor;
use DateTime;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class ArrayNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Normalizer;
     */
    private $unitUnderTest;

    private $metadataFactory;

    public function testNormalization()
    {
        $object = new A();
        $object->setRid(2)
            ->setName('testName')
            ->setStatus(true)
            ->setHiddenStatus(false);
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('testName', $result['name']);
        $this->assertTrue($result['status']);

        $object = new AChildren();
        $object->setRid(3)
            ->setName('children')
            ->setStatus(false)
            ->setHiddenStatus(true);
        $object->setFloat(3.23)
            ->setArray(array(3, 2, null))
            ->setAssocArray(array('true' => 345, 'false' => 34));
        $time = time();
        $object->setDateTime(new DateTime(date('Y-m-d H:i:s', $time)));
        $object->setNull(null);
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(8, $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('float', $result);
        $this->assertArrayHasKey('null', $result);
        $this->assertArrayHasKey('dateTime', $result);
        $this->assertArrayHasKey('array', $result);
        $this->assertArrayHasKey('assocArray', $result);
        $this->assertEquals(3, $result['id']);
        $this->assertEquals('children', $result['name']);
        $this->assertFalse($result['status']);
        $this->assertNull($result['null']);
        $this->assertEquals(3.23, $result['float']);
        $testTime = new DateTime(date('Y-m-d H:i:s', $time));
        $this->assertEquals($testTime->format(DateTime::ISO8601), $result['dateTime']);
        $this->assertEquals(array(3, 2, null), $result['array']);
        $this->assertEquals(array('true' => 345, 'false' => 34), $result['assocArray']);

        $objectComplex = new E();
        $objectComplex->setRid(434);
        $objectComplex->setObject($object);
        $objectComplex->setArrayOfObjects(array($object, $object));
        $resultComplex = $this->unitUnderTest->normalize($objectComplex);
        $this->assertCount(3, $resultComplex);
        $this->assertArrayHasKey('object', $resultComplex);
        $this->assertEquals($result, $resultComplex['object']);
        $this->assertArrayHasKey('arrayOfObjects', $resultComplex);
        $this->assertEquals(array($result, $result), $resultComplex['arrayOfObjects']);
    }

    public function testDateTimeNormalization()
    {
        $object = new TestDateTime();
        $time = new DateTime();
        $object->setDateTimeConstant($time)
            ->setDateTimeString($time)
            ->setDateTimeStringWithSpace($time)
            ->setEmptyDateTimeFormat($time);

        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(4, $result);
        $this->assertArrayHasKey('dateTimeConstant', $result);
        $this->assertArrayHasKey('dateTimeString', $result);
        $this->assertArrayHasKey('dateTimeStringWithSpace', $result);
        $this->assertArrayHasKey('emptyDateTimeFormat', $result);
        $this->assertEquals($time->format(DateTime::COOKIE), $result['dateTimeConstant']);
        $this->assertEquals($time->format('Y-m-d\TH:i:sP'), $result['dateTimeString']);
        $this->assertEquals($time->format('D, d M y H:i:s O'), $result['dateTimeStringWithSpace']);
        $this->assertEquals($time->format(DateTime::ISO8601), $result['emptyDateTimeFormat']);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\RecursionException
     */
    public function testNormalizationRecursionException()
    {
        $objectComplex = new Recursion();
        $objectComplex->setObject($objectComplex);
        $this->unitUnderTest->normalize($objectComplex);
    }

    public function testNormalizationWithGroupExclusion()
    {
        $object = new A();
        $object->setRid(2)
            ->setName('testName')
            ->setStatus(true)
            ->setHiddenStatus(false);

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new GroupsSpecification(array('support')));
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(0, $result);

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new GroupsSpecification(array('get')));
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertTrue($result['status']);


        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('testName', $result['name']);
        $this->assertTrue($result['status']);
    }

    public function testNormalizationWithVersionExclusion()
    {
        $object = new A();
        $object->setRid(2)
            ->setName('testName')
            ->setStatus(true)
            ->setHiddenStatus(false);

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('0.5'));
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('testName', $result['name']);

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('2.5'));
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('testName', $result['name']);

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('1.5'));
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('testName', $result['name']);


        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $result = $this->unitUnderTest->normalize($object);
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('testName', $result['name']);
        $this->assertTrue($result['status']);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testUndefined()
    {
        $object = new D();
        $object->setRid(4);
        $object->setName('test');
        $this->unitUnderTest->normalize($object);
    }

    public function testDenormalization()
    {
        $object = new E();
        $array = array(
            'rid' => 23,
            'object' => array(
                'id' => 3,
                'name' => 'test',
                'status' => true,
                'hiddenStatus' => false,
                'float' => 3.23,
                'null' => null,
                'array' => array(23, null),
                'assocArray' => array('str' => 34),
                'dateTime' => '2005-08-15T15:52:01+0000'
            ),
            'arrayOfObjects' => array(
                array(
                    'id' => 3,
                    'name' => 'test',
                    'status' => true,
                    'hiddenStatus' => false,
                    'float' => 3.23,
                    'null' => null,
                    'array' => array(23, null),
                    'assocArray' => array('str' => 34),
                    'dateTime' => '2005-08-15T15:52:01+0000'
                )
            )
        );
        $result = $this->unitUnderTest->denormalize($array, $object);

        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E', $result);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $result->getObject());
        $objects = $result->getArrayOfObjects();
        $this->assertCount(1, $objects);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $objects[0]);
        $this->assertEquals(23, $result->getRid());
        $this->assertEquals(3, $result->getObject()->getRid());
        $this->assertEquals('test', $result->getObject()->getName());
        $this->assertTrue($result->getObject()->getStatus());
        $this->assertNull($result->getObject()->getHiddenStatus());
        $this->assertEquals(3.23, $result->getObject()->getFloat());
        $this->assertNull($result->getObject()->getNull());
        $arrayA = $result->getObject()->getArray();
        $this->assertCount(2, $arrayA);
        $this->assertEquals(23, $arrayA[0]);
        $this->assertNull($arrayA[1]);
        $arrayAssoc = $result->getObject()->getAssocArray();
        $this->assertCount(1, $arrayAssoc);
        $this->assertArrayHasKey('str', $arrayAssoc);
        $this->assertEquals(34, $arrayAssoc['str']);
        $this->assertInstanceOf('\DateTime', $result->getObject()->getDateTime());
        $this->assertEquals('2005-08-15T15:52:01+0000', $result->getObject()->getDateTime()->format(DateTime::ISO8601));

        $this->assertEquals(3, $objects[0]->getRid());
        $this->assertEquals('test', $objects[0]->getName());
        $this->assertTrue($objects[0]->getStatus());
        $this->assertNull($objects[0]->getHiddenStatus());
        $this->assertEquals(3.23, $objects[0]->getFloat());
        $this->assertNull($objects[0]->getNull());
        $arrayA = $objects[0]->getArray();
        $this->assertCount(2, $arrayA);
        $this->assertEquals(23, $arrayA[0]);
        $this->assertNull($arrayA[1]);
        $arrayAssoc = $objects[0]->getAssocArray();
        $this->assertCount(1, $arrayAssoc);
        $this->assertArrayHasKey('str', $arrayAssoc);
        $this->assertEquals(34, $arrayAssoc['str']);
        $this->assertInstanceOf('\DateTime', $objects[0]->getDateTime());
        $this->assertEquals('2005-08-15T15:52:01+0000', $objects[0]->getDateTime()->format(DateTime::ISO8601));
    }

    /**
     * @group Test
     */
    public function testDenormalizationWithData()
    {
        $object = new E();
        $objectA = new AChildren();
        $objectA->setRid(11);
        $objectA->setName(23);
        $objectA->setFloat(2.3);
        $objectA->setHiddenStatus(true);
        $object->setRid(2);
        $object->setObject($objectA);
        $object->setArrayOfObjects(array($objectA));
        $array = array(
            'rid' => 23,
            'object' => array(
                'id' => 3,
                'float' => null
            ),
            'arrayOfObjects' => array(
                array(
                    'id' => 3,
                    'float' => null
                )
            )
        );
        $result = $this->unitUnderTest->denormalize($array, $object);

        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E', $result);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $result->getObject());
        $objects = $result->getArrayOfObjects();
        $this->assertCount(1, $objects);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $objects[0]);
        $this->assertEquals(23, $result->getRid());
        $this->assertEquals(3, $result->getObject()->getRid());
        $this->assertEquals(23, $result->getObject()->getName());
        $this->assertNull($result->getObject()->getFloat());
        $this->assertTrue($result->getObject()->getHiddenStatus());
        $this->assertEquals(3, $objects[0]->getRid());
        $this->assertEquals(23, $objects[0]->getName());
        $this->assertNull($objects[0]->getFloat());
        $this->assertTrue($objects[0]->getHiddenStatus());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     * @dataProvider denormalizeDateTimeRejectDataProvider
     *
     * @param string $dateTimeString
     */
    public function testDenormalizationDateTimeReject($dateTimeString)
    {
        $object = new TestDateTime();
        $array = array(
            'emptyDateTimeFormat' => null,
            'dateTimeConstant' => null,
            'dateTimeString' => $dateTimeString, //Y-m-d\TH:i:sP,
            'dateTimeStringWithSpace' => null
        );

        $this->unitUnderTest->denormalize($array, $object);
    }

    public function denormalizeDateTimeRejectDataProvider()
    {
        return array(
            'timezone is lost' => array('2014-01-25T20:00:58'),
            'seconds is lost' => array('2014-01-25T20:00+02:00'),
            'minutes is outbound' => array('2014-01-25T20:88:58+02:00'),
            'month is zero' => array('2014-00-25T20:00:58+02:00'),
            'day is outbound' => array('2014-02-95T20:00:58+02:00'),
            'year is outbound' => array('99999-12-25T20:00:58+02:00'),
            'year is invalid' => array('qwer-00-25T20:00:58+02:00'),
            'month is outbound' => array('2014-18-25T20:00:58+02:00'),
            'date is empty string' => array(''),
            'date is empty string with spaces' => array('     '),
            'date is malformed' => array('12345'),
            'date is zero' => array('0'),
        );
    }

    public function testDenormalisationWithGroupExclusion()
    {
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'hiddenStatus' => false
        );

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new GroupsSpecification(array('support')));
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertNull($result->getRid());
        $this->assertNull($result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new GroupsSpecification(array('get')));
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertNull($result->getRid());
        $this->assertNull($result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testDenormalizationWithVersionExclusion()
    {
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'hiddenStatus' => false
        );

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('0.5'));
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('2.5'));
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $this->unitUnderTest->addExclusionSpecification(new VersionSpecification('1.5'));
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());

        $this->unitUnderTest->cleanUpExclusionSpecifications();
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationDateTimeInvalidArgument()
    {
        $object = new TestDateTime();
        $this->unitUnderTest->denormalize(array('emptyDateTimeFormat' => ""), $object);
    }

    public function testNonStrictUnserializeHasExtraFields()
    {
        $this->unitUnderTest->setUnserializeMode(0);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'hiddenStatus' => false
        );
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testMediumStrictUnserializeHasExtraFields()
    {
        $this->unitUnderTest->setUnserializeMode(1);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'hiddenStatus' => false
        );
        $this->unitUnderTest->denormalize($array, $object);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testUnserializeWithInvalidDateTimeArgument()
    {
        $object = new AChildren();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'dateTime' => 'ssss'
        );
        $this->unitUnderTest->denormalize($array, $object);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testStrictUnserializeHasExtraFields()
    {
        $this->unitUnderTest->setUnserializeMode(2);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'hiddenStatus' => false
        );
        $this->unitUnderTest->denormalize($array, $object);
    }

    public function testNonStrictUnserializeLostField()
    {
        $this->unitUnderTest->setUnserializeMode(0);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test'
        );
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    public function testMedimStrictUnserializeLostField()
    {
        $this->unitUnderTest->setUnserializeMode(1);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test'
        );
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertNull($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testStrictUnserializeLostField()
    {
        $this->unitUnderTest->setUnserializeMode(2);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'extraField' => 'TEST'
        );
        $this->unitUnderTest->denormalize($array, $object);
    }

    public function testNonStrictUnserializeWrongNumberOfFields()
    {
        $this->unitUnderTest->setUnserializeMode(0);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'HAHA' => false
        );
        $result = $this->unitUnderTest->denormalize($array, $object);
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A', $result);
        $this->assertEquals(3, $result->getRid());
        $this->assertEquals('test', $result->getName());
        $this->assertTrue($result->getStatus());
        $this->assertNull($result->getHiddenStatus());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testStrictUnserializeWrongNumberOfFields()
    {
        $this->unitUnderTest->setUnserializeMode(2);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'HAHA' => false
        );
        $this->unitUnderTest->denormalize($array, $object);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testMediumStrictUnserializeWrongNumberOfFields()
    {
        $this->unitUnderTest->setUnserializeMode(1);
        $object = new A();
        $array = array(
            'id' => 3,
            'name' => 'test',
            'status' => true,
            'HAHA' => false
        );
        $this->unitUnderTest->denormalize($array, $object);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testNonAcceptableUnserializeMode()
    {
        $this->unitUnderTest->setUnserializeMode(5);
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
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/../Metadata/Driver/Fixture/A',
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B' => __DIR__ . '/../Metadata/Driver/Fixture/B'
                )
            )
        );

        $driver = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver',
            array($locator)
        );
        $this->metadataFactory = new MetadataFactory($driver);
        $this->unitUnderTest = $this->getMockForAbstractClass('\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer', array($this->metadataFactory, new PropertySkipper(), new HandlerProcessor()));
    }
}
