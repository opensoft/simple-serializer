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

use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class HandlerProcessorTest extends BaseTest
{
    /**
     * @dataProvider childrenDataProvider
     * @expectedException  \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testNormalizeProcess($aChildren, $aChildrenAsArray)
    {
        //test null value
        $normalized = $this->processor->normalizeProcess($this->normalizer, null, $this->makeSimpleProperty('test'));
        $this->assertNull($normalized);

        $normalized = $this->processor->normalizeProcess($this->normalizer, '1', $this->makeSimpleProperty('someProperty', 'string'));
        $this->assertEquals('1', $normalized);

        $normalized = $this->processor->normalizeProcess($this->normalizer, '1', $this->makeSimpleProperty('someProperty', 'integer'));
        $this->assertSame(1, $normalized);

        $normalized = $this->processor->normalizeProcess($this->normalizer, 27.0292, $this->makeSimpleProperty('someProperty', 'double'));
        $this->assertEquals(27.0292, $normalized);

        $dateTime = new \DateTime(date('Y-m-d H:i:s', time()));
        $normalized = $this->processor->normalizeProcess($this->normalizer, $dateTime, $this->makeSimpleProperty('someProperty', 'DateTime'));
        $this->assertEquals($dateTime->format(\DateTime::ISO8601), $normalized);

        $normalized = $this->processor->normalizeProcess($this->normalizer, $dateTime, $this->makeSimpleProperty('someProperty', 'DateTime<COOKIE>'));
        $this->assertEquals($dateTime->format(\DateTime::COOKIE), $normalized);

        $array = array(1, 2, 3, 4);
        $normalized = $this->processor->normalizeProcess($this->normalizer, $array, $this->makeSimpleProperty('someProerty', 'array'));
        $this->assertEquals(array(1, 2, 3, 4), $normalized);

        $normalized = $this->processor->normalizeProcess($this->normalizer, $aChildren, $this->makeSimpleProperty('someProperty', 'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren'));
        $this->assertEquals($aChildrenAsArray, $normalized);

        //test unknown type and value exception
        $this->processor->normalizeProcess($this->normalizer, 3, $this->makeSimpleProperty('someProperty', 'unknowType'));
    }

    /**
     * @dataProvider childrenDataProvider
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizeProcess($aChildren, $aChildrenAsArray)
    {
        //test null value
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, null, $this->makeSimpleProperty('test'), new \stdClass);
        $this->assertNull($denormalized);

        $denormalized = $this->processor->denormalizeProcess($this->normalizer, '1', $this->makeSimpleProperty('someProperty', 'string'), new \stdClass);
        $this->assertEquals('1', $denormalized);

        $denormalized = $this->processor->denormalizeProcess($this->normalizer, '1', $this->makeSimpleProperty('someProperty', 'integer'), new \stdClass);
        $this->assertSame(1, $denormalized);

        $denormalized = $this->processor->denormalizeProcess($this->normalizer, 27.0292, $this->makeSimpleProperty('someProperty', 'double'), new \stdClass);
        $this->assertEquals(27.0292, $denormalized);

        $time = time();
        $dateTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $dateTime->format(\DateTime::ISO8601), $this->makeSimpleProperty('someProperty', 'DateTime'), new \stdClass);
        $this->assertEquals($dateTime, $denormalized);

        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $dateTime->format(\DateTime::ATOM), $this->makeSimpleProperty('someProperty', 'DateTime<ATOM>'), new \stdClass());
        $this->assertEquals($dateTime->format(\DateTime::ATOM), $denormalized->format(\DateTime::ATOM));

        $array = array(1, 2, 3, 4);
        $object = new \stdClass();
        $object->someProperty = null;
        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $array, $this->makeSimpleProperty('someProperty', 'array'), $object);
        $this->assertEquals(array(1, 2, 3, 4), $denormalized);

        $denormalized = $this->processor->denormalizeProcess($this->normalizer, $aChildrenAsArray, $this->makeSimpleProperty('', 'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren'), new AChildren(), true);
        $this->assertEquals($aChildren, $denormalized);

        $this->processor->denormalizeProcess($this->normalizer, 3, $this->makeSimpleProperty('', 'unknownType'), new \stdClass());
    }

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->initializeNormalizer();
    }
}
