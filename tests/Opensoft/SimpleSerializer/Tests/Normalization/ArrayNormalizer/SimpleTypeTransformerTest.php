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
            array('one', true, 1, 20.05)
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
     * @param $string
     * @param $boolean
     * @param $integer
     * @param $double
     */
    public function testNormalize($string, $boolean, $integer, $double)
    {
        $value = $this->transformer->normalize($string, $this->makeSimpleProperty('string', 'string'));
        $this->assertSame($string, $value);

        $value = $this->transformer->normalize($boolean, $this->makeSimpleProperty('boolean', 'boolean'));
        $this->assertSame($boolean, $value);

        $value = $this->transformer->normalize($integer, $this->makeSimpleProperty('integer', 'integer'));
        $this->assertSame($integer, $value);

        $value = $this->transformer->normalize($double, $this->makeSimpleProperty('double', 'double'));
        $this->assertSame($double, $value);
    }

    /**
     * @dataProvider provider
     * @param $string
     * @param $boolean
     * @param $integer
     * @param $double
     */
    public function testDenormalize($string, $boolean, $integer, $double)
    {
        $value = $this->transformer->denormalize($string, $this->makeSimpleProperty('string', 'string'),  new \stdClass());
        $this->assertSame($string, $value);

        $value = $this->transformer->denormalize($boolean, $this->makeSimpleProperty('boolean', 'boolean'),  new \stdClass());
        $this->assertSame($boolean, $value);

        $value = $this->transformer->denormalize($integer, $this->makeSimpleProperty('integer', 'integer'),  new \stdClass());
        $this->assertSame($integer, $value);

        $value = $this->transformer->denormalize($double, $this->makeSimpleProperty('double', 'double'),  new \stdClass());
        $this->assertSame($double, $value);
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
