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
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\DateTimeHandler;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class DateTimeHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeHandler
     */
    private $datetimeHandler;

    public function testNormalization()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));

        $property = new PropertyMetadata('dateTime');
        $normalizedDateTime = $this->datetimeHandler->normalizationHandle($testTime, $property);
        $this->assertEquals($testTime->format(\DateTime::ISO8601), $normalizedDateTime);

        $property->setType('DateTime<COOKIE>');
        $normalizedDateTime = $this->datetimeHandler->normalizationHandle($testTime, $property);
        $this->assertEquals($testTime->format(\DateTime::COOKIE), $normalizedDateTime);

        $property->setType('DateTime<Y-m-d>');
        $normalizedDateTime = $this->datetimeHandler->normalizationHandle($testTime, $property);
        $this->assertEquals($testTime->format('Y-m-d'), $normalizedDateTime);
    }

    public function testDenormalization()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));

        $normalized = $testTime->format(\DateTime::COOKIE);
        $property = new PropertyMetadata('dateTime');
        $property->setType('DateTime<COOKIE>');

        $denormalized = $this->datetimeHandler->denormalizationHandle($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);

        $property->setType('DateTime');
        $normalized = $testTime->format(\DateTime::ISO8601);
        $denormalized = $this->datetimeHandler->denormalizationHandle($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);

        $property->setType('DateTime<Y-m-d>');
        $normalized = '    ' . $testTime->format('Y-m-d');
        $denormalized = $this->datetimeHandler->denormalizationHandle($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationExceptionEmptyValue()
    {
        $property = new PropertyMetadata('dateTime');
        $this->datetimeHandler->denormalizationHandle('  ', $property, new \stdClass());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationExceptionWrongValue()
    {
        $property = new PropertyMetadata('dateTime');
        $this->datetimeHandler->denormalizationHandle('argentina-jamaica50', $property, new \stdClass());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationExceptionWrongFormat()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));

        $property = new PropertyMetadata('dateTime');
        $property->setType('DateTime<Y-m-d H:i>');

        $this->datetimeHandler->denormalizationHandle($testTime->format(\DateTime::COOKIE), $property, new \stdClass());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->datetimeHandler = new DateTimeHandler();
    }
}
