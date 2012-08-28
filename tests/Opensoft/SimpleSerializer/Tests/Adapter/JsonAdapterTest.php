<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Adapter;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class JsonAdapterTest extends \PHPUnit_Framework_TestCase
{
    private $mockAdapter;

    /**
     * @param array $data
     * @param $expectedResult
     * @dataProvider providerSerialize
     */
    public function testSerialize(array $data, $expectedResult)
    {
        $result = $this->mockAdapter->serialize($data);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providerSerialize()
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
     * @dataProvider providerUnserialize
     */
    public function testUnserialize($data, $expectedResult)
    {
        $result = $this->mockAdapter->unserialize($data);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function providerUnserialize()
    {
        return array(
            array('[]', array()),
            array('[1]', array(1)),
            array('{"true":true}', array('true' => true)),
            array('{"false":false}', array('false' => false)),
            array('{"string":"string"}', array('string' => 'string')),
            array('{"null":null}', array('null' => null)),
            array('{"emptyString":""}', array('emptyString' => '')),
            array('{"nestedArray":{"test":"nestedTest"}}', array('nestedArray' => array('test' => 'nestedTest')))
        );
    }

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockForAbstractClass('Opensoft\SimpleSerializer\Adapter\JsonAdapter');
    }
}
