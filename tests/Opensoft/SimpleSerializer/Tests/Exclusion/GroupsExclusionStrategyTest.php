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

use Opensoft\SimpleSerializer\Exclusion\GroupsExclusionStrategy;
use Opensoft\SimpleSerializer\Metadata\ClassMetadata;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class GroupsExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $exclusion = new GroupsExclusionStrategy(array('test'));
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Exclusion\ExclusionStrategyInterface', $exclusion);
    }

    /**
     * @expectedException \Opensoft\SimpleSerializer\Exception\RuntimeException
     */
    public function testConstructorException()
    {
        $exclusion = new GroupsExclusionStrategy(array());
    }

    public function testShouldSkipClass()
    {
        $classMetedata = new ClassMetadata('name');
        $exclusion = new GroupsExclusionStrategy(array('test'));
        $this->assertFalse($exclusion->shouldSkipClass($classMetedata));
    }

    public function testShouldSkipProperty()
    {
        $exclusion = new GroupsExclusionStrategy(array('test'));
        $propertyMetadata = new PropertyMetadata('test');
        $this->assertTrue($exclusion->shouldSkipProperty($propertyMetadata));
        $propertyMetadata->setGroups(array('test2'));
        $this->assertTrue($exclusion->shouldSkipProperty($propertyMetadata));
        $propertyMetadata->setGroups(array('test2', 'test'));
        $this->assertFalse($exclusion->shouldSkipProperty($propertyMetadata));
    }
}
