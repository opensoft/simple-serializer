<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Encoder;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class JsonEncoderTest extends \PHPUnit_Framework_TestCase
{
    private $mockEncoder;

    /**
     * @param array $data
     * @param $expectedResult
     * @dataProvider providerEncode
     */
    public function testEncode(array $data, $expectedResult)
    {
        $result = $this->mockEncoder->encode($data);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providerEncode()
    {
        return array(
            array(array(), '[]'),
            array(array(1), '[1]'),
            array(array('true' => true), '{"true":true}'),
            array(array('false' => false), '{"false":false}'),
            array(array('string' => 'string'), '{"string":"string"}'),
            array(array('null' => null), '{"null":null}'),
            array(array('emptyString' => ''), '{"emptyString":""}'),
            array(array('nestedArray' => array('test' => 'nestedTest')), '{"nestedArray":{"test":"nestedTest"}}')
        );
    }

    /**
     * @param mixed $data
     * @param $expectedResult
     * @dataProvider providerDecode
     */
    public function testDecode($data, $expectedResult)
    {
        $result = $this->mockEncoder->decode($data);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providerDecode()
    {
        return array(
            array('[]', array()),
            array('null', null),
            array('"null"', 'null'),
            array('[1]', array(1)),
            array('{"true":true}', array('true' => true)),
            array('{"false":false}', array('false' => false)),
            array('{"string":"string"}', array('string' => 'string')),
            array('{"null":null}', array('null' => null)),
            array('{"emptyString":""}', array('emptyString' => '')),
            array('{"nestedArray":{"test":"nestedTest"}}', array('nestedArray' => array('test' => 'nestedTest')))
        );
    }

    /**
     * @return array
     * @expectedException \Opensoft\SimpleSerializer\Exception\DecodedException
     */
    public function testDecodedException()
    {
        $this->mockEncoder->decode('{"wrongJson":"}');
    }

    protected function setUp()
    {
        $this->mockEncoder = $this->getMockForAbstractClass('Opensoft\SimpleSerializer\Encoder\JsonEncoder');
    }
}
