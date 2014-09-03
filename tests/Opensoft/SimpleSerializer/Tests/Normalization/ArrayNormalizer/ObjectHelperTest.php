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

use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ObjectHelper;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class ObjectHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testExpose()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $aChildren = new AChildren();
        $aChildren->setRid(1);
        $aChildren->setStatus(true);
        $aChildren->setFloat(3.23);
        $aChildren->setArray(array(3, null));
        $aChildren->setAssocArray(array('tr' => 2));
        $aChildren->setDateTime($testTime);
        $aChildren->setNull(null);
        $aChildren->setName('name');

        $property = new PropertyMetadata('name');

        $name = ObjectHelper::expose($aChildren, $property);
        $this->assertEquals('name', $name);

        $stdClass = new \stdClass();
        $stdClass->someProperty = 'someProperty';
        $someProperty = ObjectHelper::expose($stdClass, new PropertyMetadata('someProperty'));
        $this->assertEquals('someProperty', $someProperty);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\RecursionException
     */
    public function testExposeException()
    {
        $stdClass = new \stdClass();
        $stdClass->recursion = $stdClass;

        ObjectHelper::expose($stdClass, new PropertyMetadata('recursion'));
    }

    public function testInvolve()
    {
        $aChildren = new AChildren();
        ObjectHelper::involve($aChildren, new PropertyMetadata('float'), 3.14);
        $this->assertEquals(3.14, $aChildren->getFloat());

        $stdClass = new \stdClass();
        $stdClass->property = 'cat';
        ObjectHelper::involve($stdClass, new PropertyMetadata('property'), 'dog');
        $this->assertEquals('dog', $stdClass->property);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\RecursionException
     */
    public function testInvolveException()
    {
        $stdClass = new \stdClass();
        $stdClass->recursion = null;

        ObjectHelper::involve($stdClass, new PropertyMetadata('recursion'), $stdClass);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     */
    public function testClassFullName()
    {
        $object = new AChildren();
        $className = ObjectHelper::getFullClassName($object);
        $this->assertEquals('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren', $className);

        ObjectHelper::getFullClassName('notClass');
    }
}
