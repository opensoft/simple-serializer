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
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\DateTimeTransformer;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class DateTimeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeTransformer
     */
    private $datetimeTransformer;

    /**
     * @dataProvider dateTimeProvider
     * @param \DateTime $testDateTime
     */
    public function testNormalize($testDateTime)
    {
        $property = new PropertyMetadata('dateTime');
        $normalizedDateTime = $this->datetimeTransformer->normalize($testDateTime, $property);
        $this->assertEquals($testDateTime->format(\DateTime::ISO8601), $normalizedDateTime);

        $property->setType('DateTime<COOKIE>');
        $normalizedDateTime = $this->datetimeTransformer->normalize($testDateTime, $property);
        $this->assertEquals($testDateTime->format(\DateTime::COOKIE), $normalizedDateTime);

        $property->setType('DateTime<Y-m-d>');
        $normalizedDateTime = $this->datetimeTransformer->normalize($testDateTime, $property);
        $this->assertEquals($testDateTime->format('Y-m-d'), $normalizedDateTime);
    }

    /**
     * @dataProvider dateTimeProvider
     * @param \DateTime $testDateTime
     */
    public function testDenormalize($testDateTime)
    {
        $normalized = $testDateTime->format(\DateTime::COOKIE);
        $property = new PropertyMetadata('dateTime');
        $property->setType('DateTime<COOKIE>');

        $denormalized = $this->datetimeTransformer->denormalize($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);

        $property->setType('DateTime');
        $normalized = $testDateTime->format(\DateTime::ISO8601);
        $denormalized = $this->datetimeTransformer->denormalize($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);

        $property->setType('DateTime<Y-m-d>');
        $normalized = '    ' . $testDateTime->format('Y-m-d');
        $denormalized = $this->datetimeTransformer->denormalize($normalized, $property, new \stdClass());
        $this->assertEquals(new \DateTime($normalized), $denormalized);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationExceptionEmptyValue()
    {
        $property = new PropertyMetadata('dateTime');
        $this->datetimeTransformer->denormalize('  ', $property, new \stdClass());
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationExceptionWrongValue()
    {
        $property = new PropertyMetadata('dateTime');
        $this->datetimeTransformer->denormalize('argentina-jamaica50', $property, new \stdClass());
    }

    /**
     * @dataProvider dateTimeProvider
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testDenormalizationExceptionWrongFormat($testDateTime)
    {
        $property = new PropertyMetadata('dateTime');
        $property->setType('DateTime<Y-m-d H:i>');

        $this->datetimeTransformer->denormalize($testDateTime->format(\DateTime::COOKIE), $property, new \stdClass());
    }

    /**
     * @return array
     */
    public function dateTimeProvider()
    {
        return array(
            array(
                new \DateTime(date('Y-m-d H:i:s', time()))
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->datetimeTransformer = new DateTimeTransformer();
    }
}
