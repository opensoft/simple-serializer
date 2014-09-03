<?php
/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2014 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Normalization\ArrayNormalizer;

use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\HandlerProcessor;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;
use Opensoft\SimpleSerializer\Normalization\PropertySkipper;
use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class HandlerProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HandlerProcessor;
     */
    private $processor;

    /**
     * @var ArrayNormalizer
     */
    private $normalizer;

    /**
     * @expectedException  \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testNormalizeProcess()
    {
        //test null value
        $normalized = $this->processor->normalizeProcess($this->normalizer, null, new PropertyMetadata('test'));
        $this->assertNull($normalized);

        $property = new PropertyMetadata('someProperty');

        $property->setType('string');
        $normalized = $this->processor->normalizeProcess($this->normalizer, '1', $property);
        $this->assertEquals('1', $normalized);

        $property->setType('integer');
        $normalized = $this->processor->normalizeProcess($this->normalizer, '1', $property);
        $this->assertEquals(1, $normalized);

        $property->setType('double');
        $normalized = $this->processor->normalizeProcess($this->normalizer, 27.0292, $property);
        $this->assertEquals(27.0292, $normalized);

        $time = time();
        $dateTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $property->setType('DateTime');
        $normalized = $this->processor->normalizeProcess($this->normalizer, $dateTime, $property);
        $this->assertEquals($dateTime->format(\DateTime::ISO8601), $normalized);

        $property->setType('DateTime<COOKIE>');
        $normalized = $this->processor->normalizeProcess($this->normalizer, $dateTime, $property);
        $this->assertEquals($dateTime->format(\DateTime::COOKIE), $normalized);

        $array = array(1, 2, 3, 4);
        $property->setType('array');
        $normalized = $this->processor->normalizeProcess($this->normalizer, $array, $property);
        $this->assertEquals(array(1, 2, 3, 4), $normalized);

        $aChildren = new AChildren();
        $aChildren->setRid(1);
        $aChildren->setStatus(true);
        $aChildren->setFloat(3.23);
        $aChildren->setArray(array(3, null));
        $aChildren->setAssocArray(array('tr' => 2));
        $aChildren->setDateTime($dateTime);
        $aChildren->setNull(null);
        $aChildren->setName('name');

        $expectedData = array(
            'id' => 1,
            'name' => "name",
            'status' => true,
            'float' => 3.23,
            'dateTime' => $dateTime->format(\DateTime::ISO8601),
            'null' => null,
            'array' => array(3, null),
            'assocArray' => array('tr' => 2)
        );

        $property->setType('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren');
        $normalized = $this->processor->normalizeProcess($this->normalizer, $aChildren, $property);
        $this->assertEquals($expectedData, $normalized);

        //test unknown type and value exception
        $property->setType('unknownType');
        $this->processor->normalizeProcess($this->normalizer, 3, $property);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizeProcess()
    {
        //test null value
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, null, new PropertyMetadata('test'), new \stdClass);
        $this->assertNull($denormalized);

        $property = new PropertyMetadata('someProperty');

        $property->setType('string');
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, '1', $property, new \stdClass);
        $this->assertEquals('1', $denormalized);

        $property->setType('integer');
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, '1', $property, new \stdClass);
        $this->assertEquals(1, $denormalized);

        $property->setType('double');
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, 27.0292, $property, new \stdClass);
        $this->assertEquals(27.0292, $denormalized);

        $time = time();
        $dateTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $property->setType('DateTime');
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $dateTime->format(\DateTime::ISO8601), $property, new \stdClass);
        $this->assertEquals($dateTime, $denormalized);

        $property->setType('DateTime<ATOM>');
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $dateTime->format(\DateTime::ATOM), $property, new \stdClass());
        $this->assertEquals($dateTime, $denormalized);

        $array = array(1, 2, 3, 4);
        $property->setType('array');
        $object = new \stdClass();
        $object->someProperty = null;
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $array, $property, $object);
        $this->assertEquals(array(1, 2, 3, 4), $denormalized);

        $expectedChildren = new AChildren();
        $expectedChildren->setRid(1);
        $expectedChildren->setStatus(true);
        $expectedChildren->setFloat(3.23);
        $expectedChildren->setArray(array(3, null));
        $expectedChildren->setAssocArray(array('tr' => 2));
        $expectedChildren->setDateTime($dateTime);
        $expectedChildren->setNull(null);
        $expectedChildren->setName('name');

        $data = array(
            'id' => 1,
            'name' => "name",
            'status' => true,
            'float' => 3.23,
            'dateTime' => $dateTime->format(\DateTime::ISO8601),
            'null' => null,
            'array' => array(3, null),
            'assocArray' => array('tr' => 2)
        );

        $property = new PropertyMetadata('');
        $property->setType('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren');
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $data, $property, new AChildren(), true);
        $this->assertEquals($expectedChildren, $denormalized);

        //test unknown type and value exception
        $property->setType('unknownType');
        $this->processor->denormalizeProcess($this->normalizer, 3, $property, new \stdClass());
    }

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $locator = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\FileLocator',
            array(
                array(
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/../../Metadata/Driver/Fixture/A'
                )
            )
        );

        $driver = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver',
            array($locator)
        );

        $this->processor = new HandlerProcessor();
        $this->normalizer = new ArrayNormalizer(new MetadataFactory($driver), new PropertySkipper(), $this->processor);
    }
}
