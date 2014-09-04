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

    public function dateTimeProvider()
    {
        return array(
            array(
                new \DateTime(date('Y-m-d H:i:s', time()))
            )
        );
    }

    /**
     * @dataProvider dateTimeProvider
     * @param \DateTime $testDateTime
     */
    public function testNormalization($testDateTime)
    {
        $property = new PropertyMetadata('dateTime');
        $normalizedDateTime = $this->datetimeHandler->normalizationHandle($testDateTime, $property);
        $this->assertEquals($testDateTime->format(\DateTime::ISO8601), $normalizedDateTime);

        $property->setType('DateTime<COOKIE>');
        $normalizedDateTime = $this->datetimeHandler->normalizationHandle($testDateTime, $property);
        $this->assertEquals($testDateTime->format(\DateTime::COOKIE), $normalizedDateTime);

        $property->setType('DateTime<Y-m-d>');
        $normalizedDateTime = $this->datetimeHandler->normalizationHandle($testDateTime, $property);
        $this->assertEquals($testDateTime->format('Y-m-d'), $normalizedDateTime);
    }

    /**
     * @dataProvider dateTimeProvider
     * @param \DateTime $testDateTime
     */
    public function testDenormalization($testDateTime)
    {
        $normalized = $testDateTime->format(\DateTime::COOKIE);
        $property = new PropertyMetadata('dateTime');
        $property->setType('DateTime<COOKIE>');

        $denormalized = $this->datetimeHandler->denormalizationHandle($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);

        $property->setType('DateTime');
        $normalized = $testDateTime->format(\DateTime::ISO8601);
        $denormalized = $this->datetimeHandler->denormalizationHandle($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);

        $property->setType('DateTime<Y-m-d>');
        $normalized = '    ' . $testDateTime->format('Y-m-d');
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
     * @dataProvider dateTimeProvider
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationExceptionWrongFormat($testDateTime)
    {
        $property = new PropertyMetadata('dateTime');
        $property->setType('DateTime<Y-m-d H:i>');

        $this->datetimeHandler->denormalizationHandle($testDateTime->format(\DateTime::COOKIE), $property, new \stdClass());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->datetimeHandler = new DateTimeHandler();
    }
}
