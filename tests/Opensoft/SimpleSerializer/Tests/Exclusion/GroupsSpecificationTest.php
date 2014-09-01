<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Exclusion;

use Opensoft\SimpleSerializer\Exclusion\GroupsSpecification;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class GroupsSpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $exclusion = new GroupsSpecification(array('test'));
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Exclusion\Specification', $exclusion);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\RuntimeException
     */
    public function testConstructorException()
    {
        $exclusion = new GroupsSpecification(array());
    }

    public function testIsSatisfiedBy()
    {
        $exclusion = new GroupsSpecification(array('test'));
        $propertyMetadata = new PropertyMetadata('test');
        $this->assertTrue($exclusion->isSatisfiedBy($propertyMetadata));
        $propertyMetadata->setGroups(array('test2'));
        $this->assertTrue($exclusion->isSatisfiedBy($propertyMetadata));
        $propertyMetadata->setGroups(array('test2', 'test'));
        $this->assertFalse($exclusion->isSatisfiedBy($propertyMetadata));
    }
}
