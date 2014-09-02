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

        $skipExclusionGroupSpecification = new GroupsSpecification(array('test1'));
        $this->propertySkipper->registerSpecification($skipExclusionGroupSpecification);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $nonSkipExclusionGroupSpecification = new GroupsSpecification(array('test'));
        $this->propertySkipper->registerSpecification($nonSkipExclusionGroupSpecification);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->cleanUpSpecifications();

        $this->propertySkipper->registerSpecification($nonSkipExclusionGroupSpecification);
        $this->assertFalse($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $skipExclusionVersionSpecification = new VersionSpecification('5.5');
        $this->propertySkipper->registerSpecification($skipExclusionVersionSpecification);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $nonSkipExclusionVersionSpecification = new VersionSpecification('5.1');
        $this->propertySkipper->registerSpecification($nonSkipExclusionVersionSpecification);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->cleanUpSpecifications();

        $this->propertySkipper->registerSpecification($nonSkipExclusionGroupSpecification);
        $this->assertFalse($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->registerSpecification($skipExclusionGroupSpecification);
        $this->assertTrue($this->propertySkipper->shouldSkip($this->propertyMetadata));

        $this->propertySkipper->cleanUpSpecifications();
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
