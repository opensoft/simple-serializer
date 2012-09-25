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

use Opensoft\SimpleSerializer\Exclusion\VersionExclusionStrategy;
use Opensoft\SimpleSerializer\Metadata\ClassMetadata;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class VersionExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $exclusion = new VersionExclusionStrategy('1.1');
        $this->assertInstanceOf('Opensoft\SimpleSerializer\Exclusion\ExclusionStrategyInterface', $exclusion);
    }

    public function testShouldSkipClass()
    {
        $classMetedata = new ClassMetadata('name');
        $exclusion = new VersionExclusionStrategy('1.1');
        $this->assertFalse($exclusion->shouldSkipClass($classMetedata));
    }

    public function testShouldSkipProperty()
    {
        $exclusion = new VersionExclusionStrategy('1.1');
        $propertyMetadata = new PropertyMetadata('test');
        $this->assertFalse($exclusion->shouldSkipProperty($propertyMetadata));
        $propertyMetadata->setSinceVersion('2.0');
        $this->assertTrue($exclusion->shouldSkipProperty($propertyMetadata));
        $propertyMetadata->setSinceVersion('1.0');
        $this->assertFalse($exclusion->shouldSkipProperty($propertyMetadata));
        $propertyMetadata->setUntilVersion('0.9');
        $this->assertTrue($exclusion->shouldSkipProperty($propertyMetadata));
        $propertyMetadata->setUntilVersion('1.9');
        $this->assertFalse($exclusion->shouldSkipProperty($propertyMetadata));
    }
}
