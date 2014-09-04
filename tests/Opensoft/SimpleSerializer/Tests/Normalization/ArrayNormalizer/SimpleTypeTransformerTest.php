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

use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\SimpleTypeTransformer;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class SimpleTypeTransformerTest extends BaseTest
{
    /**
     * @var SimpleTypeTransformer
     */
    private $transformer;

    public function provider()
    {
        return array(
            array('one', 'string'),
            array(true, 'boolean'),
            array(1, 'integer'),
            array(20.05, 'double')
        );
    }

    public function typesProvider()
    {
        return array(
            array('string', 'badString'),
            array('boolean', 'badBoolean'),
            array('integer', 'badInteger'),
            array('double', 'badDouble')
        );
    }

    /**
     * @dataProvider provider
     * @param string|bool|integer|double $value
     * @param string $simpleType
     */
    public function testNormalize($value, $simpleType)
    {
        $normalizedValue = $this->transformer->normalize($value, $this->makeSimpleProperty($simpleType, $simpleType));
        $this->assertSame($value, $normalizedValue);
    }

    /**
     * @dataProvider provider
     * @param string|bool|integer|double $value
     * @param string $simpleType
     */
    public function testDenormalize($value, $simpleType)
    {
        $denormalized = $this->transformer->denormalize($value, $this->makeSimpleProperty($simpleType, $simpleType),  new \stdClass());
        $this->assertSame($value, $denormalized);
    }

    /**
     * @dataProvider typesProvider
     * @param string $goodType
     * @param string $badType
     */
    public function testIsSimpleType($goodType, $badType)
    {
        $this->assertTrue(SimpleTypeTransformer::isSimpleType($goodType));
        $this->assertFalse(SimpleTypeTransformer::isSimpleType($badType));
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testWrongTypeNormalize()
    {
        $this->transformer->normalize(1.02, $this->makeSimpleProperty('', 'fantasticType'));
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testWrongTypeDenormalize()
    {
        $this->transformer->denormalize(1.02, $this->makeSimpleProperty('', 'fantasticType'), new \stdClass());
    }

    public function setUp()
    {
        $this->transformer = new SimpleTypeTransformer();
    }
}
