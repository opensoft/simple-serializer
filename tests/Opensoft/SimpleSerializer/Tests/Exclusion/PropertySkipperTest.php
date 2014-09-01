<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2014 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Exclusion;

use Opensoft\SimpleSerializer\Normalization\PropertySkipper;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Exclusion\GroupsSpecification;
use Opensoft\SimpleSerializer\Exclusion\VersionSpecification;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class PropertySkipperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyMetadata
     */
    private $propertyMetadata;

    /**
     * @var PropertySkipper
     */
    private $propertySkipper;

    public function testConstructor()
    {
        $newSkipper = new PropertySkipper(array(
            new GroupsSpecification(array('test1'))
        ));
        $this->assertTrue($newSkipper->shouldSkip($this->propertyMetadata));
    }

    public function testShouldSkip()
    {
        $this->assertFalse($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $skipExclusionGroupStrategy = new GroupsSpecification(array('test1'));
        $this->propertySkipper->registerStrategy($skipExclusionGroupStrategy);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $nonSkipExclusionGroupStrategy = new GroupsSpecification(array('test'));
        $this->propertySkipper->registerStrategy($nonSkipExclusionGroupStrategy);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->cleanUpStrategies();

        $this->propertySkipper->registerStrategy($nonSkipExclusionGroupStrategy);
        $this->assertFalse($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $skipExclusionVersionStrategy = new VersionSpecification('5.5');
        $this->propertySkipper->registerStrategy($skipExclusionVersionStrategy);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $nonSkipExclusionVersionStrategy = new VersionSpecification('5.1');
        $this->propertySkipper->registerStrategy($nonSkipExclusionVersionStrategy);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->cleanUpStrategies();

        $this->propertySkipper->registerStrategy($nonSkipExclusionGroupStrategy);
        $this->assertFalse($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->registerStrategy($skipExclusionGroupStrategy);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->cleanUpStrategies();
        $this->assertFalse($this->propertySkipper->shouldSkip($this->propertyMetadata));
    }

    protected function setUp()
    {
        $this->propertyMetadata = new PropertyMetadata('test');
        $this->propertyMetadata->setSinceVersion('5.0');
        $this->propertyMetadata->setUntilVersion('5.4');
        $this->propertyMetadata->setGroups(array('test', 'foo', 'bar'));

        $this->propertySkipper = new PropertySkipper();
    }
}
